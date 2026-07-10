<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Readme Typing SVG - 演示站点</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/loader.css">
    <link rel="stylesheet" href="./css/toggle-dark.css">
    <script type="text/javascript" src="./js/script.js" defer></script>
    <script type="text/javascript" src="./js/editor.js" defer></script>
    <script type="text/javascript" src="./js/toggle-dark.js" defer></script>
    <script type="text/javascript" src="./js/jscolor.min.js" defer></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <?php
    // 读取字体配置并注入前端，供速查表与字体下拉使用
    $fontConfigPath = __DIR__ . "/../fonts/fonts.json";
    $fontConfig = file_exists($fontConfigPath)
        ? json_decode(ltrim((string) file_get_contents($fontConfigPath), "\xEF\xBB\xBF"), true)
        : ["google" => [], "local" => []];
    if (!isset($fontConfig["google"]) || !is_array($fontConfig["google"])) {
        $fontConfig["google"] = [];
    }
    if (!isset($fontConfig["local"]) || !is_array($fontConfig["local"])) {
        $fontConfig["local"] = [];
    }
    ?>
    <script>window.FONT_CONFIG = <?= json_encode($fontConfig, JSON_UNESCAPED_UNICODE) ?>;</script>
    <link rel="icon" type="image/png" href="favicon.png">
</head>

<body <?= isset($_COOKIE["darkmode"]) && $_COOKIE["darkmode"] == "on" ? 'data-theme="dark"' : "" ?>>
    <h1>⌨️ Readme Typing SVG</h1>

    <!-- GitHub 徽章/链接 -->
    <div class="github">
        <!-- GitHub Sponsors -->
        <a class="github-button" href="https://github.com/sponsors/denvercoder1" data-color-scheme="no-preference: light; light: light; dark: dark;" data-icon="octicon-heart" data-size="large" aria-label="Sponsor @denvercoder1 on GitHub">Sponsor</a>
        <!-- View on GitHub -->
        <a class="github-button" href="https://github.com/xiuji008/readme-typing-svg" data-color-scheme="no-preference: light; light: light; dark: dark;" data-size="large" aria-label="View xiuji008/readme-typing-svg on GitHub">View on GitHub</a>
        <!-- GitHub Star -->
        <a class="github-button" href="https://github.com/xiuji008/readme-typing-svg" data-color-scheme="no-preference: light; light: light; dark: dark;" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star xiuji008/readme-typing-svg on GitHub">Star</a>
    </div>

    <!-- 配置说明按钮 -->
    <div class="toolbar">
        <button type="button" class="btn" id="config-help-btn">配置说明</button>
    </div>

    <div class="container">
        <div class="properties">
            <h2>添加文字</h2>
            <form class="parameters three-columns lines">
                <!-- Lines are added in JavaScript -->
            </form>
            <button class="add-line btn" onclick="return preview.addLines(1);">+ 添加一行</button>

            <h2>选项</h2>
            <form class="parameters two-columns options">
                <div class="label-group">
                    <label for="font">字体</label>
                    <a href="https://fonts.google.com/" target="_blank" class="icon tooltip" title="输入来自 Google Fonts 的字体名称">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 6C9.831 6 8.066 7.765 8.066 9.934h2C10.066 8.867 10.934 8 12 8s1.934.867 1.934 1.934c0 .598-.481 1.032-1.216 1.626-.255.207-.496.404-.691.599C11.029 13.156 11 14.215 11 14.333V15h2l-.001-.633c.001-.016.033-.386.441-.793.15-.15.339-.3.535-.458.779-.631 1.958-1.584 1.958-3.182C15.934 7.765 14.169 6 12 6zM11 16H13V18H11z"></path>
                            <path d="M12,2C6.486,2,2,6.486,2,12s4.486,10,10,10s10-4.486,10-10S17.514,2,12,2z M12,20c-4.411,0-8-3.589-8-8s3.589-8,8-8 s8,3.589,8,8S16.411,20,12,20z"></path>
                        </svg>
                    </a>
                </div>
                <input class="param" type="text" id="font" name="font" alt="字体名称" placeholder="Fira Code" value="Fira Code" pattern="^[\u4e00-\u9fffA-Za-z0-9\- ]*$" title="字体可来自 Google Fonts，也可是在“上传字体”中添加的本地字体（支持中文名称）。">

                <label for="weight">字体粗细</label>
                <input class="param" type="number" id="weight" name="weight" alt="字体粗细" placeholder="400" value="400" min="100" max="900" step="100">

                <label for="size">字体大小</label>
                <input class="param" type="number" id="size" name="size" alt="字体大小" placeholder="20" value="20">

                <div class="label-group">
                    <label for="letterSpacing">字间距</label>
                    <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/letter-spacing" target="_blank" class="icon tooltip" title="输入 CSS letter-spacing 属性支持的任意值">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 6C9.831 6 8.066 7.765 8.066 9.934h2C10.066 8.867 10.934 8 12 8s1.934.867 1.934 1.934c0 .598-.481 1.032-1.216 1.626-.255.207-.496.404-.691.599C11.029 13.156 11 14.215 11 14.333V15h2l-.001-.633c.001-.016.033-.386.441-.793.15-.15.339-.3.535-.458.779-.631 1.958-1.584 1.958-3.182C15.934 7.765 14.169 6 12 6zM11 16H13V18H11z"></path>
                            <path d="M12,2C6.486,2,2,6.486,2,12s4.486,10,10,10s10-4.486,10-10S17.514,2,12,2z M12,20c-4.411,0-8-3.589-8-8s3.589-8,8-8 s8,3.589,8,8S16.411,20,12,20z"></path>
                        </svg>
                    </a>
                </div>
                <input class="param" type="text" id="letterSpacing" name="letterSpacing" alt="字间距" placeholder="normal" value="normal">

                <label for="duration">每行时长（毫秒）</label>
                <input class="param" type="number" id="duration" name="duration" alt="打印时长（毫秒）" placeholder="5000" value="5000">

                <label for="pause">行间隔（毫秒）</label>
                <input class="param" type="number" id="pause" name="pause" alt="停顿时长（毫秒）" placeholder="1000" value="1000">

                <label for="color">字体颜色</label>
                <input class="param jscolor jscolor-active" id="color" name="color" alt="字体颜色" data-jscolor="{ format: 'hexa' }" value="#36BCF7">
                <div class="color-presets" data-target="color" style="grid-column: 1 / -1;">
                    <button type="button" class="swatch" style="background:#36BCF7" data-color="#36BCF7" title="#36BCF7"></button>
                    <button type="button" class="swatch" style="background:#F724A9" data-color="#F724A9" title="#F724A9"></button>
                    <button type="button" class="swatch" style="background:#2ECC71" data-color="#2ECC71" title="#2ECC71"></button>
                    <button type="button" class="swatch" style="background:#FFA500" data-color="#FFA500" title="#FFA500"></button>
                    <button type="button" class="swatch" style="background:#E74C3C" data-color="#E74C3C" title="#E74C3C"></button>
                    <button type="button" class="swatch" style="background:#9B59B6" data-color="#9B59B6" title="#9B59B6"></button>
                    <button type="button" class="swatch" style="background:#F1C40F" data-color="#F1C40F" title="#F1C40F"></button>
                    <button type="button" class="swatch" style="background:#000000" data-color="#000000" title="#000000"></button>
                    <button type="button" class="swatch" style="background:#FFFFFF" data-color="#FFFFFF" title="#FFFFFF"></button>
                </div>

                <label for="background">背景颜色</label>
                <input class="param jscolor jscolor-active" id="background" name="background" alt="背景颜色" data-jscolor="{ format: 'hexa' }" value="#00000000">
                <div class="color-presets" data-target="background" style="grid-column: 1 / -1;">
                    <button type="button" class="swatch transparent" data-color="#00000000" title="透明"></button>
                    <button type="button" class="swatch" style="background:#FFFFFF" data-color="#FFFFFF" title="#FFFFFF"></button>
                    <button type="button" class="swatch" style="background:#000000" data-color="#000000" title="#000000"></button>
                    <button type="button" class="swatch" style="background:#36BCF7" data-color="#36BCF7" title="#36BCF7"></button>
                    <button type="button" class="swatch" style="background:#F724A9" data-color="#F724A9" title="#F724A9"></button>
                    <button type="button" class="swatch" style="background:#2ECC71" data-color="#2ECC71" title="#2ECC71"></button>
                    <button type="button" class="swatch" style="background:#FFA500" data-color="#FFA500" title="#FFA500"></button>
                    <button type="button" class="swatch" style="background:#1A1A1A" data-color="#1A1A1A" title="#1A1A1A"></button>
                </div>

                <label for="center">水平居中</label>
                <select class="param" id="center" name="center" alt="水平居中">
                    <option value="false">否</option>
                    <option value="true">是</option>
                </select>

                <label for="vCenter">垂直居中</label>
                <select class="param" id="vCenter" name="vCenter" alt="垂直居中">
                    <option value="false">否</option>
                    <option value="true">是</option>
                </select>

                <label for="multiline">多行显示</label>
                <select class="param" id="multiline" name="multiline" alt="多行显示">
                    <option value="false">单行输入句子</option>
                    <option value="true">每行一个句子</option>
                </select>

                <label for="repeat">循环</label>
                <select class="param" id="repeat" name="repeat" alt="循环">
                    <option value="true">是</option>
                    <option value="false">否</option>
                </select>

                <label for="random">随机</label>
                <select class="param" id="random" name="random" alt="随机">
                    <option value="false">否</option>
                    <option value="true">是</option>
                </select>

                <label for="dimensions" title="宽度 ✕ 高度">宽度 ✕ 高度</label>
                <span id="dimensions">
                    <input class="param inline" type="number" id="width" name="width" alt="宽度（像素）" placeholder="435" value="435">
                    <label>✕</label>
                    <input class="param inline" type="number" id="height" name="height" alt="高度（像素）" placeholder="50" value="50">
                </span>

                <input type="button" class="btn" value="重置" onclick="preview.reset();">

                <button type="button" class="copy-button btn tooltip" onclick="clipboard.copyPermalink(this);" onmouseout="tooltip.reset(this);" disabled>复制链接</button>
            </form>
        </div>

        <div class="output top-bottom-split">
            <div class="top">
                <h2>预览</h2>

                <img alt="Readme Typing SVG" src="/?lines=The+five+boxing+wizards+jump+quickly" onload="this.classList.remove('loading')" onerror="this.classList.remove('loading')" />
                <div class="loader">加载中...</div>

                <label class="show-border">
                    <input type="checkbox">
                    显示边框
                </label>

                <a id="download-svg" class="btn" href="/?lines=The+five+boxing+wizards+jump+quickly" download="typing.svg" title="下载当前预览的 SVG（已内嵌字体）">下载 SVG</a>

                <div>
                    <h2>Markdown</h2>
                    <div class="code-container md">
                        <code></code>
                    </div>

                    <button class="copy-button btn tooltip" onclick="clipboard.copyCode(this);" onmouseout="tooltip.reset(this);" disabled>
                        复制到剪贴板
                    </button>
                </div>

                <div>
                    <h2>HTML</h2>
                    <div class="code-container html">
                        <code></code>
                    </div>

                    <button class="copy-button btn tooltip" onclick="clipboard.copyCode(this);" onmouseout="tooltip.reset(this);" disabled>
                        复制到剪贴板
                    </button>
                </div>
            </div>
            <div class="bottom">
                <a href="https://github.com/xiuji008/readme-typing-svg/blob/main/docs/faq.md" target="_blank" class="underline-hover faq">
                    常见问题
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M10 6v2H5v11h11v-5h2v6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6zm11-3v9l-3.794-3.793-5.999 6-1.414-1.414 5.999-6L12 3h9z"></path>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- 字体速查表 -->
    <div class="properties font-cheatsheet-card">
        <h2>字体速查表</h2>
        <p class="hint">点击任意字体即可复制其名称并填入“字体”框（联网时以本字体预览）。</p>
        <div id="font-cheatsheet" class="font-cheatsheet"></div>
    </div>

    <!-- 上传字体 -->
    <div class="properties font-upload-card">
        <h2>上传字体</h2>
        <p class="hint">支持 .ttf / .otf / .woff / .woff2 单个字体文件，或包含这些字体的 .zip。
            上传后字体保存在固定目录 <code>src/fonts/</code>，名称自动记入字体配置文件。</p>
        <form id="font-upload-form" class="font-upload-form" action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" id="fontfile" name="fontfile" multiple
                accept=".ttf,.otf,.woff,.woff2,.zip" />
            <button type="submit" class="btn">上传</button>
        </form>
        <p id="font-upload-msg" class="font-upload-msg" role="status"></p>
        <div id="local-font-list" class="local-font-list"></div>
    </div>

    <script type="text/javascript">
        (function () {
            var form = document.getElementById("font-upload-form");
            var msg = document.getElementById("font-upload-msg");
            if (!form) return;
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                var input = document.getElementById("fontfile");
                if (!input.files.length) {
                    msg.textContent = "请先选择一个字体文件。";
                    msg.className = "font-upload-msg error";
                    return;
                }
                var data = new FormData();
                for (var i = 0; i < input.files.length; i++) {
                    data.append("fontfile", input.files[i]);
                }
                msg.textContent = "上传中…";
                msg.className = "font-upload-msg";
                fetch("upload.php", { method: "POST", body: data })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res.success) {
                            msg.textContent = res.message + "（即将刷新字体列表）";
                            msg.className = "font-upload-msg success";
                            setTimeout(function () { location.reload(); }, 800);
                        } else {
                            msg.textContent = res.message;
                            msg.className = "font-upload-msg error";
                        }
                    })
                    .catch(function (err) {
                        msg.textContent = "上传失败：" + err;
                        msg.className = "font-upload-msg error";
                    });
            });
        })();
    </script>

    <!-- 配置说明弹框 -->
    <div class="modal-overlay" id="config-modal-overlay">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="config-modal-title">
            <button class="modal-close" id="config-modal-close" aria-label="关闭">&times;</button>
            <h2 id="config-modal-title">配置说明</h2>
            <p>将下方 Markdown 复制到你的 GitHub 个人主页 README 中即可使用。把 <code>lines=</code> 后的内容替换成你自己的文字，多行用 <code>;</code> 分隔，空格用 <code>+</code> 或 <code>%20</code> 表示。</p>
            <pre class="modal-example">![Typing SVG](http://192.168.31.195:2800?font=Fira+Code&amp;pause=1000&amp;width=435&amp;lines=The+five+boxing+wizards+jump+quickly)</pre>
            <p><strong>按单词设置不同颜色：</strong>在文字中用 <code>[[颜色]]</code>（不含 <code>#</code> 的十六进制，支持 3/4/6/8 位）切换其后文字的颜色，<code>[[default]]</code> 可恢复默认颜色。例如下面这行会让 <code>world</code> 显示为粉色：</p>
            <pre class="modal-example">lines=Hello+[[F724A9]]world[[default]]!</pre>

            <p><strong>字体（含中文与本地上传字体）：</strong><code>font=</code> 现在除了 Google 英文字体，还支持 <b>Google 中文字体</b>（如 <code>Noto Sans SC</code>、<code>Ma Shan Zheng</code>、<code>ZCOOL KuaiLe</code> 等，见下方字体速查表“中文”分类），以及通过页面“<b>上传字体</b>”卡片添加的<b>本地字体</b>（名称支持中文）。</p>
            <p><strong>按单词设置字体 / 字号 / 粗细：</strong>在文字中插入以下 token 即可对后续文字生效（这也是“点击单词或拖选→浮条”功能的底层语法）：</p>
            <pre class="modal-example">[[font=名称]]  切换字体（如 Noto Sans SC、或上传的本地字体名）
[[size=28]]   切换字号（像素）
[[weight=700]] 切换字重（100–900）
[[bold]]      加粗（等价于 [[weight=700]]）
[[normal]]     恢复正常字重
[[reset]]     清除前面所有格式（颜色/字体/字号/字重）回到默认</pre>
            <p>例如下面这行会让 <code>世界</code> 用 <code>Noto Sans SC</code> 28 号加粗显示：</p>
            <pre class="modal-example">lines=Hello+[[font=Noto Sans SC]][[size=28]][[bold]]世界[[reset]]!</pre>
            <p><strong>上传字体：</strong>页面“上传字体”卡片支持 <code>.ttf / .otf / .woff / .woff2</code> 单文件或包含它们的 <code>.zip</code>。上传后字体保存在固定目录 <code>src/fonts/</code>，名称自动写入字体配置文件 <code>src/fonts/fonts.json</code> 的 <code>local</code> 字段，并出现在速查表的“本地”分类中。</p>
            <p><strong>字体源可配置：</strong>抓取 Google 字体使用的 CSS 接口地址可在字体配置文件 <code>src/fonts/fonts.json</code> 的 <code>css_base</code> 字段中配置，<b>默认值为 <code>https://fonts.googleapis.cn</code></b>（国内镜像，方便国内网络与 Docker 部署）。也可在生成 SVG 的 URL 上用 <code>font_source=</code> 参数按需覆盖（例如 <code>font_source=https://fonts.googleapis.com</code>）。若配置/指定的字体源不可达，会自动回退到官方 <code>https://fonts.googleapis.com</code>。</p>
            <table>
                <thead>
                    <tr>
                        <th>参数</th>
                        <th>说明</th>
                        <th>类型</th>
                        <th>默认值</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>lines</code></td><td>显示的文字，多行用 <code>;</code> 分隔，空格用 <code>+</code> 表示</td><td>字符串</td><td>-</td></tr>
                    <tr><td><code>height</code></td><td>输出 SVG 的高度（像素）</td><td>整数</td><td>50</td></tr>
                    <tr><td><code>width</code></td><td>输出 SVG 的宽度（像素）</td><td>整数</td><td>400</td></tr>
                    <tr><td><code>size</code></td><td>字体大小（像素）</td><td>整数</td><td>20</td></tr>
                    <tr><td><code>font</code></td><td>字体（来自 Google Fonts 的字体名）</td><td>字符串</td><td>monospace</td></tr>
                    <tr><td><code>color</code></td><td>文字颜色（不含 <code>#</code> 的十六进制）</td><td>字符串</td><td>36BCF7</td></tr>
                    <tr><td><code>background</code></td><td>背景颜色（不含 <code>#</code> 的十六进制，<code>00000000</code> 为透明）</td><td>字符串</td><td>00000000</td></tr>
                    <tr><td><code>center</code></td><td>是否水平居中</td><td>布尔</td><td>false</td></tr>
                    <tr><td><code>vCenter</code></td><td>是否垂直居中</td><td>布尔</td><td>false</td></tr>
                    <tr><td><code>multiline</code></td><td>是否换行（false 为单行循环输入）</td><td>布尔</td><td>false</td></tr>
                    <tr><td><code>duration</code></td><td>单行打印时长（毫秒）</td><td>整数</td><td>5000</td></tr>
                    <tr><td><code>pause</code></td><td>行间停顿时长（毫秒）</td><td>整数</td><td>0</td></tr>
                    <tr><td><code>repeat</code></td><td>是否在最后一行后循环回第一行</td><td>布尔</td><td>true</td></tr>
                    <tr><td><code>separator</code></td><td>行与行之间的分隔符</td><td>字符串</td><td>;</td></tr>
                    <tr><td><code>letterSpacing</code></td><td>字间距（CSS <code>letter-spacing</code> 取值）</td><td>字符串</td><td>normal</td></tr>
                    <tr><td><code>font_source</code></td><td>字体源 CSS 接口地址，覆盖 <code>fonts.json</code> 的 <code>css_base</code></td><td>URL</td><td>https://fonts.googleapis.cn</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <a href="javascript:toggleTheme()" class="darkmode" title="切换深色模式" aria-label="切换深色模式">
        <span class="icon-moon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </span>
        <span class="icon-sun" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
            </svg>
        </span>
    </a>
</body>

</html>
