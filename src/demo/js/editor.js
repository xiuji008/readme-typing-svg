/*
 * 交互式文字格式编辑器 + 字体速查表
 * - 字体速查表：精选 Google 字体，点击复制字体名并可填入字体框
 * - 可视化编辑器：点击单词或拖选词语，弹出工具栏设置 颜色/加粗/字号/字体
 *   底层通过 [[...]] token 写入每行输入框，与后端 RendererModel::parseLine 语法一致
 */
(function () {
  "use strict";

  const $ = (sel, el = document) => el.querySelector(sel);

  // 字体配置：由后端从 src/fonts/fonts.json 注入
  //   google: Google 字体名称数组（含中文）
  //   local:  本地上传字体映射 { 显示名: 文件名 }
  const FONT_CONFIG =
    (typeof window.FONT_CONFIG === "object" && window.FONT_CONFIG)
      ? window.FONT_CONFIG
      : { google: [], local: {} };
  const GOOGLE_FONTS = Array.isArray(FONT_CONFIG.google) ? FONT_CONFIG.google : [];
  const LOCAL_FONTS =
    (typeof FONT_CONFIG.local === "object" && FONT_CONFIG.local) ? FONT_CONFIG.local : {};
  // 上传字体文件可通过该 URL 访问（docroot 为 src，故 /fonts/...）
  const LOCAL_FONT_URL = "/fonts/";

  // 用于字体下拉与速查表合并后的完整字体列表
  function allFonts() {
    return GOOGLE_FONTS.concat(Object.keys(LOCAL_FONTS));
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, (c) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;",
    }[c]));
  }

  /* ------------------------------------------------------------------ *
   * 行文本 <-> 标记（marks）互相转换，语法与后端保持一致
   * mark: { start, end, color?, size?, font?, weight? }
   * 仅在属性偏离行默认值（line default）时才写入 mark
   * ------------------------------------------------------------------ */
  function applyTokenToState(inner, state) {
    const lower = inner.toLowerCase();
    if (lower === "default") { state.color = null; return; }
    if (lower === "reset") {
      state.color = null; state.font = null; state.size = null; state.weight = null;
      return;
    }
    if (lower === "bold") { state.weight = 700; return; }
    if (lower === "normal") { state.weight = null; return; }
    let m;
    if ((m = inner.match(/^font=(.*)$/i))) { state.font = m[1].toLowerCase() === "default" ? null : m[1]; return; }
    if ((m = inner.match(/^size=(\d+)$/i))) { state.size = m[1].toLowerCase() === "default" ? null : parseInt(m[1], 10); return; }
    if ((m = inner.match(/^weight=(\d{1,3})$/i))) { state.weight = m[1].toLowerCase() === "default" ? null : parseInt(m[1], 10); return; }
    if (/^[0-9a-fA-F]{3,8}$/.test(inner)) { state.color = "#" + inner.toLowerCase(); return; }
  }

  function parseLineToMarks(str) {
    const re = /(\[\[[^\]]+\]\])/g;
    const segments = [];
    const eff = { color: null, font: null, size: null, weight: null };
    let buf = "";
    let last = 0;
    let m;
    const flush = () => {
      if (buf !== "") { segments.push({ ...eff, text: buf }); buf = ""; }
    };
    while ((m = re.exec(str)) !== null) {
      buf += str.slice(last, m.index);
      last = re.lastIndex;
      flush();
      applyTokenToState(m[1].slice(2, -2), eff);
    }
    buf += str.slice(last);
    flush();

    // 合并相邻且属性完全相同的片段
    const merged = [];
    for (const s of segments) {
      const lastM = merged[merged.length - 1];
      if (lastM && lastM.color === s.color && lastM.font === s.font &&
          lastM.size === s.size && lastM.weight === s.weight) {
        lastM.text += s.text;
      } else {
        merged.push({ ...s });
      }
    }

    let text = "";
    const marks = [];
    for (const s of merged) {
      const start = text.length;
      text += s.text;
      const end = text.length;
      const attrs = {};
      if (s.color) attrs.color = s.color;
      if (s.font) attrs.font = s.font;
      if (s.size != null) attrs.size = s.size;
      if (s.weight != null) attrs.weight = s.weight;
      if (Object.keys(attrs).length) marks.push(Object.assign({ start, end }, attrs));
    }
    return { text, marks };
  }

  function serializeMarks(text, marks) {
    if (!marks || !marks.length) return text;
    const bounds = new Set([0, text.length]);
    marks.forEach((mk) => { bounds.add(mk.start); bounds.add(mk.end); });
    const pts = [...bounds].sort((a, b) => a - b);
    let out = "";
    let prevHad = false;
    for (let i = 0; i < pts.length - 1; i++) {
      const s = pts[i];
      const e = pts[i + 1];
      if (s >= e) continue;
      const seg = text.slice(s, e);
      const mk = marks.find((x) => x.start <= s && x.end >= e);
      if (mk) {
        let t = "";
        if (mk.color) t += `[[${mk.color.replace(/^#/, "")}]]`;
        if (mk.font) t += `[[font=${mk.font}]]`;
        if (mk.size != null) t += `[[size=${mk.size}]]`;
        if (mk.weight != null) t += `[[weight=${mk.weight}]]`;
        out += t + seg;
        prevHad = true;
      } else {
        if (prevHad) out += "[[reset]]";
        out += seg;
        prevHad = false;
      }
    }
    return out;
  }

  /* ------------------------------------------------------------------ *
   * 可视化编辑器
   * ------------------------------------------------------------------ */
  const Editors = (function () {
    const state = {}; // index -> { input, editor, text, marks }

    function renderEditor(text, marks) {
      if (!marks.length) return escapeHtml(text) || " ";
      const bounds = new Set([0, text.length]);
      marks.forEach((mk) => { bounds.add(mk.start); bounds.add(mk.end); });
      const pts = [...bounds].sort((a, b) => a - b);
      let html = "";
      for (let i = 0; i < pts.length - 1; i++) {
        const s = pts[i];
        const e = pts[i + 1];
        if (s >= e) continue;
        const seg = text.slice(s, e);
        const mk = marks.find((m) => m.start <= s && m.end >= e);
        if (mk) {
          const style = [];
          if (mk.color) style.push(`color:${mk.color}`);
          if (mk.weight) style.push(`font-weight:${mk.weight}`);
          if (mk.size) style.push(`font-size:${mk.size}px`);
          if (mk.font) style.push(`font-family:'${mk.font}', monospace`);
          html += `<span style="${style.join(";")}">${escapeHtml(seg)}</span>`;
        } else {
          html += escapeHtml(seg);
        }
      }
      return html || " ";
    }

    function refresh(idx) {
      const st = state[idx];
      if (!st) return;
      const parsed = parseLineToMarks(st.input.value);
      st.text = parsed.text;
      st.marks = parsed.marks;
      st.editor.innerHTML = renderEditor(st.text, st.marks);
    }

    function sync() {
      const form = $(".lines");
      if (!form) return;
      // 移除已删除行对应的编辑器
      form.querySelectorAll(".line-editor").forEach((el) => {
        const idx = el.dataset.editorIndex;
        if (!form.querySelector(`.param[data-index="${idx}"]`)) {
          delete state[idx];
          el.remove();
        }
      });
      // 为新增的行创建编辑器
      form.querySelectorAll(".param[data-index]").forEach((input) => {
        const idx = input.dataset.index;
        if (state[idx]) return;
        const del = form.querySelector(`.delete-line.btn[data-index="${idx}"]`);
        const editor = document.createElement("div");
        editor.className = "line-editor";
        editor.dataset.editorIndex = idx;
        editor.setAttribute("contenteditable", "false");
        editor.title = "点击单词或拖选文字以设置格式";
        if (del) del.insertAdjacentElement("afterend", editor);
        else form.appendChild(editor);
        state[idx] = { input, editor, text: "", marks: [] };
        input.addEventListener("input", () => refresh(idx));
        editor.addEventListener("mouseup", onEditorInteraction);
        editor.addEventListener("click", onEditorInteraction);
        refresh(idx);
      });
    }

    return { sync, refresh, state, renderEditor };
  })();

  /* ------------------------------------------------------------------ *
   * 选区偏移工具（在 .line-editor 内将 DOM 选区映射为纯文本偏移）
   * ------------------------------------------------------------------ */
  function getTextNodes(container) {
    const walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null);
    const nodes = [];
    let n;
    while ((n = walker.nextNode())) nodes.push(n);
    return nodes;
  }
  function getCharOffset(container, node, offset) {
    let total = 0;
    for (const t of getTextNodes(container)) {
      if (t === node) return total + offset;
      total += t.length;
    }
    return total + offset;
  }
  function locateOffset(container, target) {
    const nodes = getTextNodes(container);
    let acc = 0;
    for (const t of nodes) {
      const len = t.length;
      if (acc + len >= target) return { node: t, offset: target - acc };
      acc += len;
    }
    const last = nodes[nodes.length - 1];
    return { node: last, offset: last ? last.length : 0 };
  }
  function setSelectionRange(container, start, end) {
    const s = locateOffset(container, start);
    const e = locateOffset(container, end);
    const range = document.createRange();
    range.setStart(s.node, s.offset);
    range.setEnd(e.node, e.offset);
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
  }
  function getSelectionOffsets(container) {
    const sel = window.getSelection();
    if (!sel.rangeCount) return null;
    const range = sel.getRangeAt(0);
    if (!container.contains(range.startContainer) || !container.contains(range.endContainer)) return null;
    if (range.collapsed) return null;
    return {
      start: getCharOffset(container, range.startContainer, range.startOffset),
      end: getCharOffset(container, range.endContainer, range.endOffset),
    };
  }
  function wordBounds(text, pos) {
    let s = pos;
    let e = pos;
    while (s > 0 && !/\s/.test(text[s - 1])) s--;
    while (e < text.length && !/\s/.test(text[e])) e++;
    return [s, e];
  }

  /* ------------------------------------------------------------------ *
   * 浮动工具栏
   * ------------------------------------------------------------------ */
  let currentIdx = null;
  let currentSel = null;
  let toolbarEl = null;

  function buildToolbar() {
    const tb = document.createElement("div");
    tb.id = "fmt-toolbar";
    tb.className = "fmt-toolbar";
    tb.style.display = "none";
    tb.innerHTML = `
      <span class="fmt-group"><input type="color" id="fmt-color" value="#36BCF7" title="颜色"></span>
      <span class="fmt-group"><label class="fmt-check"><input type="checkbox" id="fmt-bold"> 加粗</label></span>
      <span class="fmt-group">字号 <input type="number" id="fmt-size" value="20" min="1" max="300" style="width:64px"></span>
      <span class="fmt-group">字体 <select id="fmt-font"></select></span>
      <button type="button" class="btn fmt-apply" id="fmt-apply">应用</button>
      <button type="button" class="btn fmt-clear" id="fmt-clear">清除</button>
      <button type="button" class="btn fmt-close" id="fmt-close" title="关闭">✕</button>
    `;
    document.body.appendChild(tb);
    toolbarEl = tb;

    const fontSel = $("#fmt-font", tb);
    fontSel.innerHTML = `<option value="">&lt;默认&gt;</option>` +
      allFonts().map((f) => `<option value="${escapeHtml(f)}">${escapeHtml(f)}</option>`).join("");

    $("#fmt-apply", tb).addEventListener("click", applyFormat);
    $("#fmt-clear", tb).addEventListener("click", clearFormat);
    $("#fmt-close", tb).addEventListener("click", hideToolbar);
  }

  function prefillToolbar(editor, range) {
    const st = Editors.state[editor.dataset.editorIndex];
    const mk = st.marks.find((m) => m.start <= range.start && m.end > range.start) ||
      st.marks.find((m) => range.start < m.end && range.end > m.start);
    const lineColor = ($("#color") && $("#color").value) || "#36BCF7";
    const lineFont = (($("#font") && $("#font").value) || "monospace").toLowerCase();
    const lineSize = parseInt(($("#size") && $("#size").value) || "20", 10);

    $("#fmt-color").value = (mk && mk.color) ? mk.color : lineColor;
    $("#fmt-bold").checked = !!(mk && mk.weight === 700);
    $("#fmt-size").value = (mk && mk.size != null) ? mk.size : lineSize;
    $("#fmt-font").value = (mk && mk.font) ? mk.font : (lineFont === "monospace" ? "" : lineFont);
  }

  function showToolbar(editor, range) {
    prefillToolbar(editor, range);
    toolbarEl.style.display = "flex";
    const rect = window.getSelection().getRangeAt(0).getBoundingClientRect();
    const top = rect.top - toolbarEl.offsetHeight - 8;
    let left = rect.left + rect.width / 2 - toolbarEl.offsetWidth / 2;
    left = Math.max(8, Math.min(left, window.innerWidth - toolbarEl.offsetWidth - 8));
    toolbarEl.style.top = Math.max(8, top) + "px";
    toolbarEl.style.left = left + "px";
  }

  function hideToolbar() {
    if (toolbarEl) toolbarEl.style.display = "none";
    currentIdx = null;
    currentSel = null;
  }

  function onEditorInteraction(e) {
    const editor = e.currentTarget;
    const idx = editor.dataset.editorIndex;
    const st = Editors.state[idx];
    if (!st) return;
    let range = getSelectionOffsets(editor);
    if (!range) {
      // 单击：以光标位置扩展为整个单词
      const sel = window.getSelection();
      if (!sel.rangeCount) return;
      const r = sel.getRangeAt(0);
      if (!editor.contains(r.startContainer)) return;
      const pos = getCharOffset(editor, r.startContainer, r.startOffset);
      const [ws, we] = wordBounds(st.text, pos);
      if (ws === we) return; // 点在空白上
      setSelectionRange(editor, ws, we);
      range = { start: ws, end: we };
    }
    currentIdx = idx;
    currentSel = range;
    showToolbar(editor, range);
  }

  function applyFormat() {
    if (!currentIdx || !currentSel) return;
    const st = Editors.state[currentIdx];
    const sel = currentSel;

    // 移除与选区重叠的已有标记
    st.marks = st.marks.filter((m) => m.end <= sel.start || m.start >= sel.end);

    const lineColor = ($("#color") && $("#color").value || "#36BCF7").toLowerCase();
    const lineFont = (($("#font") && $("#font").value) || "monospace").toLowerCase();
    const lineSize = parseInt(($("#size") && $("#size").value) || "20", 10);

    const attrs = {};
    const color = $("#fmt-color").value;
    if (color && color.toLowerCase() !== lineColor) attrs.color = color;
    if ($("#fmt-bold").checked) attrs.weight = 700;
    const size = parseInt($("#fmt-size").value, 10);
    if (size && size !== lineSize) attrs.size = size;
    const font = $("#fmt-font").value;
    if (font && font.toLowerCase() !== lineFont) attrs.font = font;

    if (Object.keys(attrs).length) {
      st.marks.push(Object.assign({ start: sel.start, end: sel.end }, attrs));
      st.marks.sort((a, b) => a.start - b.start);
    }

    st.input.value = serializeMarks(st.text, st.marks);
    st.input.dispatchEvent(new Event("input", { bubbles: true }));
    if (window.preview && preview.update) preview.update();
    hideToolbar();
  }

  function clearFormat() {
    if (!currentIdx || !currentSel) return;
    const st = Editors.state[currentIdx];
    const sel = currentSel;
    st.marks = st.marks.filter((m) => m.end <= sel.start || m.start >= sel.end);
    st.input.value = serializeMarks(st.text, st.marks);
    st.input.dispatchEvent(new Event("input", { bubbles: true }));
    if (window.preview && preview.update) preview.update();
    hideToolbar();
  }

  /* ------------------------------------------------------------------ *
   * 字体速查表
   * ------------------------------------------------------------------ */
  function formatFromExt(ext) {
    switch ((ext || "").toLowerCase()) {
      case "ttf": return "truetype";
      case "otf": return "opentype";
      case "woff": return "woff";
      case "woff2": return "woff2";
      default: return "truetype";
    }
  }

  // Google 中文字体集合（与 src/fonts/fonts.json 保持一致，含简/繁/港台及常用 CJK）
  const CN_FONTS = [
    "Noto Sans SC", "Noto Serif SC", "Ma Shan Zheng", "ZCOOL XiaoWei",
    "ZCOOL KuaiLe", "ZCOOL QingKe HuangYou", "Liu Jian Mao Cao", "Long Cang", "Zhi Mang Xing",
    "Noto Sans TC", "Noto Serif TC", "Noto Sans HK", "Noto Serif HK",
    "LXGW WenKai TC", "LXGW WenKai Mono TC",
    "Klee One", "Zen Maru Gothic", "Zen Old Mincho", "Zen Kaku Gothic New", "Zen Kaku Gothic Antique",
    "IBM Plex Sans JP", "Iansui", "Yusei Magic", "Dela Gothic One", "Huninn",
    "Shippori Mincho", "Shippori Mincho B1", "Sawarabi Mincho", "Sawarabi Gothic",
    "Noto Sans JP", "Noto Serif JP", "Yuji Syuku", "Yuji Boku",
    "RocknRoll One", "Reggae One", "M PLUS Rounded 1c", "M PLUS 1p",
    "Kosugi", "Kosugi Maru", "BIZ UDPGothic", "BIZ UDPMincho",
  ];

  // 为本地上传字体注入 @font-face（仅一次）
  function injectLocalFontFaces() {
    if (window.__localFacesInjected) return;
    window.__localFacesInjected = true;
    let localStyle = "";
    Object.keys(LOCAL_FONTS).forEach((name) => {
      const file = LOCAL_FONTS[name];
      const fmt = formatFromExt(file.split(".").pop());
      localStyle += `@font-face{font-family:'${name.replace(/'/g, "")}';` +
        `src:url('${LOCAL_FONT_URL}${escapeHtml(file)}') format('${fmt}');}\n`;
    });
    if (localStyle) {
      const styleEl = document.createElement("style");
      styleEl.textContent = localStyle;
      document.head.appendChild(styleEl);
    }
  }

  function chipHtml(name) {
    return `<button type="button" class="font-chip" data-font="${escapeHtml(name)}" ` +
      `style="font-family:'${escapeHtml(name)}', sans-serif" title="点击复制：${escapeHtml(name)}">${escapeHtml(name)}</button>`;
  }

  function bindChip(chip) {
    chip.addEventListener("click", () => {
      const name = chip.dataset.font;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(name).then(() => {
          chip.classList.add("copied");
          setTimeout(() => chip.classList.remove("copied"), 800);
        });
      }
      const fontInput = $("#font");
      if (fontInput) fontInput.value = name;
      const fs = $("#fmt-font");
      if (fs) fs.value = name;
      if (window.preview && preview.update) preview.update();
    });
  }

  function buildCheatSheet() {
    const container = $("#font-cheatsheet");
    if (!container) return;

    injectLocalFontFaces();

    // 联网时加载 Google 字体的 CSS（仅一次，离线自动回退）
    if (GOOGLE_FONTS.length && !window.__googleFontsLinked) {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = "https://fonts.googleapis.com/css2?family=" +
        GOOGLE_FONTS.map((f) => f.replace(/ /g, "+")).join("&family=") + "&display=swap";
      document.head.appendChild(link);
      window.__googleFontsLinked = true;
    }

    // 搜索框 + 分类按钮（仅构建一次）
    if (!container.dataset.built) {
      const wrap = document.createElement("div");
      wrap.className = "cheatsheet-controls";
      wrap.innerHTML =
        `<input type="search" id="font-search" class="font-search" placeholder="搜索字体…" />` +
        `<div class="font-cats" id="font-cats">` +
          `<button type="button" class="font-cat active" data-cat="all">全部</button>` +
          `<button type="button" class="font-cat" data-cat="latin">English</button>` +
          `<button type="button" class="font-cat" data-cat="cn">中文</button>` +
          `<button type="button" class="font-cat" data-cat="local">本地</button>` +
        `</div>`;
      container.parentNode.insertBefore(wrap, container);
      const search = $("#font-search", wrap);
      search.addEventListener("input", renderChips);
      wrap.querySelectorAll(".font-cat").forEach((btn) => {
        btn.addEventListener("click", () => {
          wrap.querySelectorAll(".font-cat").forEach((b) => b.classList.remove("active"));
          btn.classList.add("active");
          renderChips();
        });
      });
      container.dataset.built = "1";
    }

    function matches(name) {
      const isLocal = Object.prototype.hasOwnProperty.call(LOCAL_FONTS, name);
      const isCn = CN_FONTS.indexOf(name) !== -1;
      const active = document.querySelector(".font-cat.active");
      const cat = (active && active.dataset) ? active.dataset.cat : "all";
      if (cat === "local" && !isLocal) return false;
      if (cat === "cn" && !isCn) return false;
      if (cat === "latin" && (isLocal || isCn)) return false;
      const q = ($("#font-search") ? $("#font-search").value : "").toLowerCase().trim();
      if (q && name.toLowerCase().indexOf(q) === -1) return false;
      return true;
    }

    function renderChips() {
      const names = allFonts().filter(matches);
      if (!names.length) {
        container.innerHTML = `<span class="hint">没有匹配的字体。</span>`;
        return;
      }
      container.innerHTML = names.map(chipHtml).join("");
      container.querySelectorAll(".font-chip").forEach(bindChip);
    }

    renderChips();
    renderLocalFontList();
  }

  /* 展示已上传的本地字体清单 */
  function renderLocalFontList() {
    const box = $("#local-font-list");
    if (!box) return;
    const names = Object.keys(LOCAL_FONTS);
    if (!names.length) {
      box.innerHTML = `<p class="hint">尚未上传本地字体。</p>`;
      return;
    }
    box.innerHTML = `<h3>已上传字体</h3><ul>` +
      names.map((n) =>
        `<li><code>${escapeHtml(n)}</code> <span class="muted">(${escapeHtml(LOCAL_FONTS[n])})</span></li>`
      ).join("") + `</ul>`;
  }

  /* ------------------------------------------------------------------ *
   * 初始化
   * ------------------------------------------------------------------ */
  function init() {
    buildToolbar();
    buildCheatSheet();
    // 监听行的增删，自动同步可视化编辑器
    const form = $(".lines");
    if (form) {
      const observer = new MutationObserver(() => Editors.sync());
      observer.observe(form, { childList: true });
    }
    Editors.sync();

    // restore() 在 load 阶段才会把每行的最终值写入输入框，
    // 这里在 load 后再刷新一次，确保编辑器显示已还原的文字与格式
    window.addEventListener("load", () => {
      Object.keys(Editors.state).forEach((idx) => Editors.refresh(idx));
    });

    // 点击空白处关闭工具栏
    document.addEventListener("click", (e) => {
      if (toolbarEl && toolbarEl.style.display !== "none" &&
          !toolbarEl.contains(e.target) && !(e.target.closest && e.target.closest(".line-editor"))) {
        hideToolbar();
      }
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
