<?php

declare(strict_types=1);

/**
 * Model for SVG outputs
 */
class RendererModel
{
    /** @var array<string> $lines text to display */
    public $lines;

    /** @var array<array<string, string>> $segments per-line colored segments for rendering */
    public $segments;

    /** @var string $font Font family */
    public $font;

    /** @var string $font Font weight */
    public $weight;

    /** @var string $color Font color */
    public $color;

    /** @var string $background Background color */
    public $background;

    /** @var int $size Font size */
    public $size;

    /** @var bool $center Whether or not to center text horizontally */
    public $center;

    /** @var bool $vCenter Whether or not to center text vertically */
    public $vCenter;

    /** @var int $width SVG width (px) */
    public $width;

    /** @var int $height SVG height (px) */
    public $height;

    /** @var bool $multiline True = wrap to new lines, False = retype on same line */
    public $multiline;

    /** @var int $duration print duration in milliseconds */
    public $duration;

    /** @var int $pause pause duration between lines in milliseconds */
    public $pause;

    /** @var bool $repeat Whether to loop around to the first line after the last */
    public $repeat;

    /** @var string $separator Line separator */
    public $separator;

    /** @var bool $random True = Sort lines in random order */
    public $random;

    /** @var string $fontCSS CSS required for displaying the selected font */
    public $fontCSS;

    /** Directory where user-uploaded font files are stored (fixed path) */
    private const LOCAL_FONT_DIR = __DIR__ . "/../fonts/";

    /** @var array<string, string> $localFonts name => filename (within LOCAL_FONT_DIR) */
    private $localFonts = [];

    /** @var string $fontSourceBase Base URL of the Google Fonts CSS API (font source) */
    private $fontSourceBase = GoogleFontConverter::DEFAULT_CSS_BASE;

    /** @var string $letterSpacing Letter spacing */
    public $letterSpacing;

    /** @var string $template Path to template file */
    public $template;

    /** @var array<string, string> $DEFAULTS */
    private $DEFAULTS = [
        "font" => "monospace",
        "weight" => "400",
        "color" => "#36BCF7",
        "background" => "#00000000",
        "size" => "20",
        "center" => "false",
        "vCenter" => "false",
        "width" => "400",
        "height" => "50",
        "multiline" => "false",
        "duration" => "5000",
        "pause" => "0",
        "repeat" => "true",
        "separator" => ";",
        "random" => "false",
        "letterSpacing" => "normal",
    ];

    /**
     * Construct RendererModel
     *
     * @param string $template Path to the template file
     * @param array<string, string> $params request parameters
     */
    public function __construct($template, $params)
    {
        $this->template = $template;
        $this->separator = $params["separator"] ?? $this->DEFAULTS["separator"];
        $this->random = $this->checkBoolean($params["random"] ?? $this->DEFAULTS["random"]);
        $this->font = $this->checkFont($params["font"] ?? $this->DEFAULTS["font"]);
        $this->weight = $this->checkNumberPositive($params["weight"] ?? $this->DEFAULTS["weight"], "Font weight");
        $this->color = $this->checkColor($params["color"] ?? $this->DEFAULTS["color"], "color");
        $this->background = $this->checkColor($params["background"] ?? $this->DEFAULTS["background"], "background");
        $rawLines = $params["lines"] ?? "";
        if (!$rawLines) {
            throw new UnprocessableEntityException("Lines parameter must be set.");
        }
        $exploded = $this->explodeLines($rawLines);
        $this->lines = array_map("htmlspecialchars", $exploded);
        $this->segments = array_map(fn($line) => $this->parseLine($line, $this->color), $exploded);
        $this->size = $this->checkNumberPositive($params["size"] ?? $this->DEFAULTS["size"], "Font size");
        $this->center = $this->checkBoolean($params["center"] ?? $this->DEFAULTS["center"]);
        $this->vCenter = $this->checkBoolean($params["vCenter"] ?? $this->DEFAULTS["vCenter"]);
        $this->width = $this->checkNumberPositive($params["width"] ?? $this->DEFAULTS["width"], "Width");
        $this->height = $this->checkNumberPositive($params["height"] ?? $this->DEFAULTS["height"], "Height");
        $this->multiline = $this->checkBoolean($params["multiline"] ?? $this->DEFAULTS["multiline"]);
        $this->duration = $this->checkNumberPositive($params["duration"] ?? $this->DEFAULTS["duration"], "duration");
        $this->pause = $this->checkNumberNonNegative($params["pause"] ?? $this->DEFAULTS["pause"], "pause");
        $this->repeat = $this->checkBoolean($params["repeat"] ?? $this->DEFAULTS["repeat"]);
        // 加载字体配置（本地上传字体映射： 名称 => 文件名 + css_base）
        $this->loadLocalFonts();
        // 允许通过 URL 参数 font_source 覆盖字体源（缺省用配置文件 / 转换器默认）
        $fontSourceParam = $params["font_source"] ?? "";
        if (is_string($fontSourceParam) && $fontSourceParam !== "") {
            $this->fontSourceBase = rtrim($fontSourceParam, "/");
        }
        // 收集所有用到的（字体, 字重）组合：含整行默认字体，以及每个分段字体。
        // 本地字体从固定路径读取并内嵌；Google 字体实时抓取并内嵌。
        $fontSpecs = [];
        $lineFont = $this->font;
        $lineWeight = $this->weight;
        if ($lineFont !== $this->DEFAULTS["font"]) {
            $fontSpecs["{$lineFont}|{$lineWeight}"] = [$lineFont, $lineWeight];
        }
        foreach ($this->segments as $lineSegments) {
            foreach ($lineSegments as $segment) {
                if (!empty($segment["font"])) {
                    $w = $segment["weight"] ?? $lineWeight;
                    $fontSpecs["{$segment["font"]}|{$w}"] = [$segment["font"], $w];
                }
            }
        }
        $fontCSS = "";
        foreach ($fontSpecs as [$f, $w]) {
            if (isset($this->localFonts[$f])) {
                $fontCSS .= GoogleFontConverter::embedLocalFont(
                    $f,
                    self::LOCAL_FONT_DIR . $this->localFonts[$f],
                    (int) $w
                );
            } else {
                $fontCSS .= $this->fetchFontCSS($f, $w, $params["lines"]);
            }
        }
        $this->fontCSS = $fontCSS;
        $this->letterSpacing = $this->checkLetterSpacing($params["letterSpacing"] ?? $this->DEFAULTS["letterSpacing"]);
    }

    /**
     * Split the raw lines parameter into an array of raw line strings.
     *
     * @param string $lines Semicolon-separated lines parameter
     * @return array<string> raw line strings (shuffled if random is enabled)
     */
    private function explodeLines($lines)
    {
        if (strlen($this->separator) === 1) {
            $lines = rtrim($lines, $this->separator);
        }
        $exploded = explode($this->separator, $lines);
        if ($this->random) {
            shuffle($exploded);
        }
        return $exploded;
    }

    /**
     * Parse a single line into an array of colored segments.
     *
     * Inline color tokens of the form "[[RRGGBB]]" or "[[RRGGBBAA]]" switch the
     * color for the following text. "[[default]]" resets to the base color.
     *
     * @param string $line The raw line text
     * @param string $baseColor Base color applied before any token
     * @return array<string, string> Array of ["color" => "#...", "text" => "..."]
     */
    private function parseLine($line, $baseColor)
    {
        $segments = [];
        // split while keeping the captured "[[...]]" tokens
        $pattern = "/(\[\[[^\]]+\]\])/";
        $parts = preg_split($pattern, $line, -1, PREG_SPLIT_DELIM_CAPTURE);

        // current effective attributes (null = inherit line default)
        $state = [
            "color" => $baseColor,
            "font" => null,
            "size" => null,
            "weight" => null,
        ];

        // append a text portion using the current state, merging with the
        // previous segment when all attributes match to keep output minimal
        $push = function ($text) use (&$segments, &$state) {
            $text = htmlspecialchars($text, ENT_QUOTES);
            if ($text === "") {
                return;
            }
            $last = count($segments) - 1;
            if (
                $last >= 0
                && $segments[$last]["color"] === $state["color"]
                && $segments[$last]["font"] === $state["font"]
                && $segments[$last]["size"] === $state["size"]
                && $segments[$last]["weight"] === $state["weight"]
            ) {
                $segments[$last]["text"] .= $text;
                return;
            }
            $segments[] = [
                "color" => $state["color"],
                "font" => $state["font"],
                "size" => $state["size"],
                "weight" => $state["weight"],
                "text" => $text,
            ];
        };

        // apply a single "[[...]]" token to the current state
        $applyToken = function ($token) use (&$state, $baseColor) {
            $inner = substr($token, 2, -2);
            $lower = strtolower($inner);
            if ($lower === "default") {
                // reset color to the line default color
                $state["color"] = $baseColor;
                return;
            }
            if ($lower === "reset") {
                // reset every attribute back to the line defaults
                $state = ["color" => $baseColor, "font" => null, "size" => null, "weight" => null];
                return;
            }
            if ($lower === "bold") {
                $state["weight"] = 700;
                return;
            }
            if ($lower === "normal") {
                $state["weight"] = null;
                return;
            }
            if (preg_match("/^font=(.*)$/i", $inner, $m)) {
                $state["font"] = strtolower($m[1]) === "default" ? null : $m[1];
                return;
            }
            if (preg_match("/^size=(\d+)$/i", $inner, $m)) {
                $state["size"] = strtolower($m[1]) === "default" ? null : (int) $m[1];
                return;
            }
            if (preg_match("/^weight=(\d{1,3})$/i", $inner, $m)) {
                $state["weight"] = strtolower($m[1]) === "default" ? null : (int) $m[1];
                return;
            }
            if (preg_match("/^[0-9A-Fa-f]{3,8}$/", $inner)) {
                $state["color"] = "#" . strtolower($inner);
                return;
            }
            // unknown token: ignore so it is rendered as literal text
        };

        foreach ($parts as $index => $part) {
            if ($index % 2 === 1) {
                $applyToken($part);
            } else {
                $push($part);
            }
        }
        return $segments;
    }

    /**
     * Validate font family and return valid string
     *
     * @param string $font Font name parameter
     * @return string Sanitized font name
     */
    private function checkFont($font)
    {
        // 允许字母（含中文等 Unicode）、数字、连字符与空格；
        // 去除可能破坏 SVG 属性的引号等字符
        return preg_replace("/[^\p{L}\p{N}\- ]/u", "", $font);
    }

    /**
     * Load the local (uploaded) font map from the font config file.
     */
    private function loadLocalFonts(): void
    {
        $configPath = __DIR__ . "/../fonts/fonts.json";
        if (!is_file($configPath)) {
            $this->localFonts = [];
            return;
        }
        $raw = ltrim((string) file_get_contents($configPath), "\xEF\xBB\xBF");
        $config = json_decode($raw, true);
        $this->localFonts = (isset($config["local"]) && is_array($config["local"]))
            ? $config["local"]
            : [];
        // 字体源（CSS 接口地址）。缺省时使用转换器的 DEFAULT_CSS_BASE
        $this->fontSourceBase = (isset($config["css_base"]) && is_string($config["css_base"]) && $config["css_base"] !== "")
            ? rtrim($config["css_base"], "/")
            : GoogleFontConverter::DEFAULT_CSS_BASE;
    }

    /**
     * Validate font color and return valid string
     *
     * @param string $color Color parameter
     * @param string $field Field name for displaying in case of error
     * @return string Sanitized color with preceding hash symbol
     */
    private function checkColor($color, $field)
    {
        $sanitized = (string) preg_replace("/[^0-9A-Fa-f]/", "", $color);
        // if color is not a valid length, use the default
        if (!in_array(strlen($sanitized), [3, 4, 6, 8])) {
            return $this->DEFAULTS[$field];
        }
        // return sanitized color
        return "#" . $sanitized;
    }

    /**
     * Validate positive numeric parameter and return valid integer
     *
     * @param string $num Parameter to validate
     * @param string $field Field name for displaying in case of error
     * @return int Sanitized digits and int
     */
    private function checkNumberPositive($num, $field)
    {
        $digits = intval(preg_replace("/[^0-9\-]/", "", $num));
        if ($digits <= 0) {
            throw new UnprocessableEntityException("$field must be a positive number.");
        }
        return $digits;
    }

    /**
     * Validate non-negative numeric parameter and return valid integer
     *
     * @param string $num Parameter to validate
     * @param string $field Field name for displaying in case of error
     * @return int Sanitized digits and int
     */
    private function checkNumberNonNegative($num, $field)
    {
        $digits = intval(preg_replace("/[^0-9\-]/", "", $num));
        if ($digits < 0) {
            throw new UnprocessableEntityException("$field must be a non-negative number.");
        }
        return $digits;
    }

    /**
     * Validate "true" or "false" value as string and return boolean
     *
     * @param string $bool Boolean parameter as string
     * @return boolean Whether or not $bool is set to "true"
     */
    private function checkBoolean($bool)
    {
        return strtolower($bool) == "true";
    }

    /**
     * Fetch CSS with Base-64 encoding from Google Fonts
     *
     * @param string $font Google Font to fetch
     * @param string $text Text to display in font
     * @return string The CSS for displaying the font
     */
    private function fetchFontCSS($font, $weight, $text)
    {
        // skip checking if left as default
        if ($font != $this->DEFAULTS["font"]) {
            // fetch and convert from Google Fonts (字体源可配置)
            $from_google_fonts = GoogleFontConverter::fetchFontCSS($font, $weight, $text, $this->fontSourceBase);
            if ($from_google_fonts) {
                // return the CSS for displaying the font
                return "<style>\n{$from_google_fonts}</style>\n";
            }
        }
        // font is not found
        return "";
    }

    /**
     * Validate unit for size properties
     *
     * This method validates if the given unit is a valid CSS size unit.
     * It supports various units such as px, em, rem, pt, pc, in, cm, mm,
     * ex, ch, vh, vw, vmin, vmax, and percentages.
     *
     * @param string $unit Unit for validation
     * @return bool True if valid, false otherwise
     */
    private function isValidUnit($unit)
    {
        return (bool) preg_match("/^(-?\\d+(\\.\\d+)?(px|em|rem|pt|pc|in|cm|mm|ex|ch|vh|vw|vmin|vmax|%))$/", $unit);
    }

    /**
     * Validate letter spacing
     *
     * This method validates the letter spacing property for fonts.
     * It allows specific keywords (normal, inherit, initial, revert, revert-layer, unset)
     * and valid CSS size units.
     *
     * @param string $letterSpacing Letter spacing for validation
     * @return string Validated letter spacing
     */
    private function checkLetterSpacing($letterSpacing)
    {
        // List of valid keywords for letter-spacing
        $keywords = "normal|inherit|initial|revert|revert-layer|unset";

        // Check if the input matches one of the keywords or a valid unit
        if (preg_match("/^($keywords)$/", $letterSpacing) || $this->isValidUnit($letterSpacing)) {
            return $letterSpacing;
        }

        // Return the default letter spacing value if the input is invalid
        return $this->DEFAULTS["letterSpacing"];
    }
}
