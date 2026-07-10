<?php

declare(strict_types=1);

/**
 * Class for converting Google Fonts to base 64 for displaying through SVG image
 */
class GoogleFontConverter
{
    /**
     * 默认字体源（CSS 接口）。可在 src/fonts/fonts.json 的 css_base 覆盖，
     * 例如国内环境设为 https://fonts.googleapis.cn
     */
    public const DEFAULT_CSS_BASE = "https://fonts.googleapis.cn";

    /**
     * Fetch CSS from Google Fonts
     *
     * @param string $font    Google Font to fetch
     * @param string $weight  Font weight
     * @param string $text    Text to display in font (subsets the glyphs)
     * @param string $cssBase Base URL of the Google Fonts CSS API
     * @return string The CSS for displaying the font
     */
    public static function fetchFontCSS($font, $weight, $text, $cssBase = self::DEFAULT_CSS_BASE): string
    {
        $cssBase = rtrim($cssBase, "/");
        $url =
            $cssBase . "/css2?" .
            http_build_query([
                "family" => $font . ":wght@" . $weight,
                "text" => $text,
                "display" => "fallback",
            ]);
        try {
            // get the CSS for the font
            $response = self::curlGetContents($url);
            // find all font files and convert them to base64 Data URIs
            return self::encodeFonts($response);
        } catch (InvalidArgumentException $error) {
            // 字体源不可达时，自动回退到官方 Google Fonts（除非本就是官方源）
            $canonical = "https://fonts.googleapis.com";
            if ($cssBase !== $canonical) {
                try {
                    $response = self::curlGetContents(
                        $canonical . "/css2?" . http_build_query([
                            "family" => $font . ":wght@" . $weight,
                            "text" => $text,
                            "display" => "fallback",
                        ])
                    );
                    return self::encodeFonts($response);
                } catch (InvalidArgumentException $e) {
                    return "";
                }
            }
            return "";
        }
    }

    /**
     * Embed a locally stored font file as a base64 data URI @font-face rule.
     *
     * Used for user-uploaded fonts so the SVG is fully self-contained and
     * renders without any external network request.
     *
     * @param string $name     Font family name to expose (must match usage)
     * @param string $filePath Absolute path to the local font file
     * @param int    $weight   Font weight to declare in the @font-face rule
     * @return string <style>…</style> block, or "" when the file is missing
     */
    public static function embedLocalFont(string $name, string $filePath, int $weight): string
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return "";
        }
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $formatMap = [
            "ttf" => "truetype",
            "otf" => "opentype",
            "woff" => "woff",
            "woff2" => "woff2",
        ];
        $format = $formatMap[$ext] ?? "truetype";
        $data = file_get_contents($filePath);
        if ($data === false) {
            return "";
        }
        $dataURI = "data:font/{$ext};base64," . base64_encode($data);
        return "<style>\n@font-face{font-family:'{$name}';font-weight:{$weight};" .
            "src:url('{$dataURI}') format('{$format}');}\n</style>\n";
    }

    /**
     * Encode font urls in string as base 64
     *
     * 匹配 CSS 中出现的任意 https 字体文件地址（与具体镜像域名无关，
     * 无论是 fonts.gstatic.com 还是 fonts.gstatic.cn 等都能处理）。
     *
     * @param string $css The CSS from Google Fonts
     * @return string CSS with urls replaced with base 64 Data URIs
     */
    private static function encodeFonts($css)
    {
        $urlRegex = '/url\((https\:\/\/[^\n\)]+)\)\s+format\(\'([^\']+)\'\)/';
        preg_match_all($urlRegex, $css, $matches);
        $urls = array_combine($matches[1], $matches[2]);
        // go over all links and replace with data URI
        foreach ($urls as $url => $fontType) {
            $response = self::curlGetContents($url);
            $dataURI = "data:font/{$fontType};base64," . base64_encode($response);
            $css = str_replace($url, $dataURI, $css);
        }
        return $css;
    }

    /**
     * Get the contents of a URL
     *
     * @param string $url The URL to fetch
     * @return string Response from URL
     */
    private static function curlGetContents($url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        // 设置超时：避免字体源不可达时长时间挂起（PHP 内置服务器为单线程，
        // 一次长时间请求会阻塞整个预览页面）。连接超时 5s，总超时 10s。
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        // 提供现代浏览器 UA，否则 Google Fonts 可能返回不兼容格式或拒绝
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode != ResponseEnum::HTTP_OK->value) {
            throw new InvalidArgumentException("Failed to fetch Google Font from API.");
        }
        return $response;
    }
}
