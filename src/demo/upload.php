<?php declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

/**
 * 字体上传端点
 * - 接收 .ttf / .otf / .woff / .woff2 单文件，或包含这些字体的 .zip
 * - 上传的字体统一存放到固定目录 src/fonts/
 * - 把字体名称写入字体配置文件 src/fonts/fonts.json 的 local 映射
 */

function uploadResponse(bool $success, string $message, array $extra = []): void
{
    echo json_encode(array_merge(["success" => $success, "message" => $message], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    uploadResponse(false, "请使用 POST 方式上传字体");
}

if (empty($_FILES["fontfile"])) {
    uploadResponse(false, "未接收到文件");
}

$configPath = __DIR__ . "/../fonts/fonts.json";
$localDir = __DIR__ . "/../fonts/"; // src/fonts/
if (!is_dir($localDir)) {
    mkdir($localDir, 0755, true);
}

// 允许的内嵌字体格式
$allowedExt = [
    "ttf" => "truetype",
    "otf" => "opentype",
    "woff" => "woff",
    "woff2" => "woff2",
];

// 读取现有配置
$config = file_exists($configPath)
    ? json_decode((string) file_get_contents($configPath), true)
    : ["google" => [], "local" => []];
if (!is_array($config)) {
    $config = ["google" => [], "local" => []];
}
if (!isset($config["local"]) || !is_array($config["local"])) {
    $config["local"] = [];
}

// 规范化上传文件为条目数组
$files = $_FILES["fontfile"];
$entries = [];
if (is_array($files["name"])) {
    $count = count($files["name"]);
    for ($i = 0; $i < $count; $i++) {
        $entries[] = [
            "name" => $files["name"][$i],
            "tmp" => $files["tmp_name"][$i],
            "error" => $files["error"][$i],
        ];
    }
} else {
    $entries[] = [
        "name" => $files["name"],
        "tmp" => $files["tmp_name"],
        "error" => $files["error"],
    ];
}

// 仅保留文件系统安全的文件名（保留 Unicode 显示名，磁盘名转 ASCII 避免兼容问题）
function sanitizeDiskName(string $name): string
{
    $name = preg_replace("/[\/\\\\]/", "_", $name);
    return preg_replace("/[^A-Za-z0-9_.\-]/", "_", $name);
}

// 计算不冲突的磁盘目标路径
function uniqueTarget(string $dir, string $base, string $ext): string
{
    $target = $dir . $base . "." . $ext;
    $k = 1;
    while (file_exists($target)) {
        $target = $dir . $base . "_" . ($k++) . "." . $ext;
    }
    return $target;
}

$added = [];

foreach ($entries as $entry) {
    if ($entry["error"] !== UPLOAD_ERR_OK) {
        continue;
    }
    if (!is_uploaded_file($entry["tmp"])) {
        continue;
    }
    $original = basename($entry["name"]);
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $displayName = pathinfo($original, PATHINFO_FILENAME); // 用于 font-family / 配置名

    // 处理 zip：解压其中所有受支持的字体文件
    if ($ext === "zip") {
        $zip = new ZipArchive();
        if ($zip->open($entry["tmp"]) !== true) {
            continue;
        }
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $zName = $zip->getNameIndex($i);
            if ($zName === false) {
                continue;
            }
            $zBase = basename($zName);
            if ($zBase === "" || preg_match("/(\/|\\\\)/", $zName)) {
                continue; // 跳过目录与含路径的条目（防路径穿越）
            }
            $zExt = strtolower(pathinfo($zBase, PATHINFO_EXTENSION));
            if (!isset($allowedExt[$zExt])) {
                continue;
            }
            $content = $zip->getFromIndex($i);
            if ($content === false) {
                continue;
            }
            $zStem = pathinfo($zBase, PATHINFO_FILENAME);
            $target = uniqueTarget($localDir, sanitizeDiskName($zStem), $zExt);
            file_put_contents($target, $content);
            $stored = basename($target);
            if (!isset($config["local"][$zStem])) {
                $config["local"][$zStem] = $stored;
            }
            $added[] = $zStem;
        }
        $zip->close();
        continue;
    }

    if (!isset($allowedExt[$ext])) {
        continue;
    }
    $target = uniqueTarget($localDir, sanitizeDiskName($displayName), $ext);
    if (!move_uploaded_file($entry["tmp"], $target)) {
        continue;
    }
    $stored = basename($target);
    if (!isset($config["local"][$displayName])) {
        $config["local"][$displayName] = $stored;
    }
    $added[] = $displayName;
}

// 写回配置文件
file_put_contents(
    $configPath,
    json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

if (empty($added)) {
    uploadResponse(false, "没有有效的字体文件被添加（仅支持 .ttf/.otf/.woff/.woff2 及包含它们的 .zip）");
}
uploadResponse(true, "已添加 " . count($added) . " 个字体", ["added" => $added]);
