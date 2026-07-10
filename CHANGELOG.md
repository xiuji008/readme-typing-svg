# 修改记录

本项目基于 [readme-typing-svg](https://github.com/xiuji008/readme-typing-svg) 的本地定制版本，以下为相对上游所做的改动记录。

## 2026-07-10

- **演示站点中文化**：`src/demo/index.php` 全面翻译为中文界面（标题、标签、按钮、提示等）。
- **新增「配置说明」弹框**：演示页顶部新增「配置说明」按钮，点击弹出模态框，内含使用示例与完整参数说明表格；支持点击遮罩、`×` 按钮、`Esc` 键关闭。
- **颜色选择器内置常用颜色**：字体颜色、背景颜色下方各增加一排常用色块（含透明棋盘格），点击即可写入颜色并刷新预览。
- **生成的 SVG 代码去除跳转链接**：复制出的 Markdown / HTML 改为纯图片格式，不再包裹 `<a>` 跳转链接：
  ```md
  ![Typing SVG](http://192.168.31.195:2800?font=Fira+Code&pause=1000&width=435&lines=The+five+boxing+wizards+jump+quickly)
  ```
  ```html
  <img src="http://192.168.31.195:2800?font=Fira+Code&pause=1000&width=435&lines=The+five+boxing+wizards+jump+quickly" alt="Typing SVG" />
  ```
- **新增「按单词设置不同颜色」功能**：在 `lines` 文本中使用 `[[颜色]]` 标记切换其后文字颜色，支持 3/4/6/8 位十六进制（不含 `#`），`[[default]]` 恢复默认颜色。
  - 渲染器：`src/models/RendererModel.php` 解析每行彩色片段，`src/templates/main.php` 使用 `<tspan>` 逐段上色，打字动画不受影响。
  - 示例：`lines=Hello+[[F724A9]]world[[default]]!`
- **README 示例地址本地化**：`README.md` 中的示例图片、快速设置 Markdown、演示站点链接均改为 `http://192.168.31.195:2800`。
