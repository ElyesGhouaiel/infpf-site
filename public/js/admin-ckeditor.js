(function () {
    var CK_URL = "https://cdn.ckeditor.com/4.22.1/full/ckeditor.js";
    var CK_CONFIG = {
        language: "fr",
        height: 400,
        removePlugins: "elementspath",
        format_tags: "p;h2;h3;h4;h5",
        allowedContent: true,
        toolbar: [
            { name: "styles", items: ["Format", "FontSize"] },
            { name: "basicstyles", items: ["Bold", "Italic", "Underline", "Strike", "Subscript", "Superscript", "-", "RemoveFormat"] },
            { name: "colors", items: ["TextColor", "BGColor"] },
            { name: "paragraph", items: ["NumberedList", "BulletedList", "-", "Outdent", "Indent", "-", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock"] },
            "/",
            { name: "links", items: ["Link", "Unlink"] },
            { name: "insert", items: ["Table", "HorizontalRule", "SpecialChar"] },
            { name: "clipboard", items: ["Undo", "Redo"] },
            { name: "tools", items: ["Maximize", "Source"] }
        ]
    };

    function loadScript(url, callback) {
        if (typeof CKEDITOR !== "undefined") { callback(); return; }
        var s = document.createElement("script");
        s.src = url;
        s.onload = callback;
        document.head.appendChild(s);
    }

    function initEditors() {
        var textareas = document.querySelectorAll("form[name] textarea");
        if (textareas.length === 0) return;

        loadScript(CK_URL, function () {
            if (typeof CKEDITOR === "undefined") return;
            for (var name in CKEDITOR.instances) {
                try { CKEDITOR.instances[name].destroy(true); } catch (e) {}
            }
            textareas.forEach(function (el) {
                if (!el.id || el.dataset.ckeditorDone) return;
                el.dataset.ckeditorDone = "1";
                CKEDITOR.replace(el, CK_CONFIG);
            });
        });
    }

    document.addEventListener("DOMContentLoaded", initEditors);
    document.addEventListener("turbo:load", initEditors);
    document.addEventListener("turbo:render", initEditors);
    document.addEventListener("turbo:frame-render", initEditors);
    setTimeout(initEditors, 1000);
})();
