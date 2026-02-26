(function () {
    var JODIT_JS = "https://cdn.jsdelivr.net/npm/jodit@4.9.6/es2021/jodit.fat.min.js";
    var JODIT_CSS = "https://cdn.jsdelivr.net/npm/jodit@4.9.6/es2021/jodit.fat.min.css";

    var EDITOR_CONFIG = {
        language: "fr",
        height: 450,
        toolbarAdaptive: false,
        askBeforePasteHTML: false,
        askBeforePasteFromWord: false,
        defaultActionOnPaste: "insert_clear_html",
        buttons: [
            "bold", "italic", "underline", "strikethrough", "|",
            "font", "fontsize", "brush", "|",
            "ul", "ol", "indent", "outdent", "|",
            "left", "center", "right", "justify", "|",
            "link", "table", "hr", "|",
            "undo", "redo", "|",
            "eraser", "source", "fullsize"
        ],
        removeButtons: ["image", "video", "file", "print", "about", "ai-assistant"],
        showCharsCounter: false,
        showWordsCounter: false,
        showXPathInStatusbar: false
    };

    function loadCSS(url) {
        if (document.querySelector('link[href="' + url + '"]')) return;
        var link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = url;
        document.head.appendChild(link);
    }

    function loadScript(url, callback) {
        if (typeof Jodit !== "undefined") { callback(); return; }
        var existing = document.querySelector('script[src="' + url + '"]');
        if (existing) { existing.onload = callback; return; }
        var s = document.createElement("script");
        s.src = url;
        s.onload = callback;
        document.head.appendChild(s);
    }

    function initEditors() {
        var textareas = document.querySelectorAll("form[name] textarea");
        if (textareas.length === 0) return;

        loadCSS(JODIT_CSS);
        loadScript(JODIT_JS, function () {
            if (typeof Jodit === "undefined") return;
            textareas.forEach(function (el) {
                if (el.dataset.joditDone) return;
                el.dataset.joditDone = "1";
                Jodit.make(el, EDITOR_CONFIG);
            });
        });
    }

    function cleanAndInit() {
        document.querySelectorAll(".jodit-container").forEach(function (el) {
            el.remove();
        });
        document.querySelectorAll("textarea[data-jodit-done]").forEach(function (el) {
            el.removeAttribute("data-jodit-done");
            el.style.display = "";
        });
        initEditors();
    }

    document.addEventListener("DOMContentLoaded", initEditors);
    document.addEventListener("turbo:load", cleanAndInit);
    document.addEventListener("turbo:render", cleanAndInit);
})();
