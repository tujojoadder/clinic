/* =====================================================================
   list_print.js — তালিকা প্রিন্টের সময় ব্যবহৃত ফিল্টারগুলো (ড্রপডাউনসহ) প্রিন্টের বক্সে বসায়
   - ".filters form" বা "form.filters" এর সব ইনপুট/সিলেক্ট পড়ে, নতুন করে কিছু লিখতে হয় না
   - লেবেল পায় যেভাবে: data-label → <label for> → ঘিরে থাকা <label> → placeholder → title → name
   - [data-list-print] বাটনে ক্লিক করলে প্রিন্ট হয়
   ===================================================================== */
(function () {
    "use strict";

    var SKIP_TYPES = [
        "hidden",
        "submit",
        "button",
        "reset",
        "file",
        "password",
        "image",
    ];
    var SKIP_NAMES = ["per_page", "page", "_token", "_method"];

    function clean(text) {
        return (text || "").replace(/\s+/g, " ").trim();
    }

    // কন্ট্রোলের লেবেল
    function labelFor(el) {
        if (el.dataset && el.dataset.label) return el.dataset.label;

        if (el.id && window.CSS && CSS.escape) {
            var linked = document.querySelector(
                'label[for="' + CSS.escape(el.id) + '"]',
            );
            if (linked) return clean(linked.textContent);
        }

        var wrap = el.closest("label");
        if (wrap) {
            var copy = wrap.cloneNode(true);
            copy.querySelectorAll(
                "select, input, textarea, button, option",
            ).forEach(function (n) {
                n.remove();
            });
            var t = clean(copy.textContent);
            if (t) return t;
        }

        if (el.name === "q" || el.name === "search") return "খোঁজ";
        if (el.placeholder) return clean(el.placeholder);
        if (el.title) return clean(el.title);
        return el.name || "";
    }

    // কন্ট্রোলের দেখানোর মতো মান (ফাঁকা হলে "")
    function valueOf(el) {
        var tag = el.tagName.toLowerCase();

        if (tag === "select") {
            return Array.prototype.map
                .call(el.selectedOptions || [], function (o) {
                    return o.value === "" ? "" : clean(o.text);
                })
                .filter(Boolean)
                .join(", ");
        }

        if (el.type === "checkbox" || el.type === "radio") {
            if (!el.checked) return "";
            var wrap = el.closest("label");
            return wrap ? clean(wrap.textContent) || "হ্যাঁ" : "হ্যাঁ";
        }

        return clean(el.value);
    }

    // পেজের সব ফিল্টার ফর্ম থেকে [{label, value}] তালিকা
    function collect() {
        var list = [];
        var seen = {};

        document
            .querySelectorAll(".filters form, form.filters")
            .forEach(function (form) {
                Array.prototype.forEach.call(form.elements, function (el) {
                    if (!el.name || el.disabled) return;
                    if (SKIP_TYPES.indexOf(el.type) !== -1) return;
                    if (SKIP_NAMES.indexOf(el.name) !== -1) return;

                    var value = valueOf(el);
                    if (!value) return;

                    var label = labelFor(el);
                    var key = el.name + "|" + value;
                    if (seen[key]) return;
                    seen[key] = true;

                    list.push({ label: label, value: value });
                });
            });

        return list;
    }

    // প্রিন্টের বক্সে চিপ বসানো
    function render() {
        document
            .querySelectorAll("[data-lp-filterbox]")
            .forEach(function (box) {
                var chips = box.querySelector("[data-lp-chips]");
                if (!chips) return;

                chips.querySelectorAll(".lp-auto").forEach(function (n) {
                    n.remove();
                });

                collect().forEach(function (item) {
                    var chip = document.createElement("span");
                    chip.className = "lp-chip lp-auto";

                    if (item.label) {
                        var b = document.createElement("b");
                        b.textContent = item.label + ": ";
                        chip.appendChild(b);
                    }
                    chip.appendChild(document.createTextNode(item.value));
                    chips.appendChild(chip);
                });

                box.classList.toggle(
                    "has-filter",
                    chips.querySelectorAll(".lp-chip").length > 0,
                );
            });
    }

    // Ctrl+P বা প্রিন্ট বাটন — যেভাবেই হোক প্রিন্টের ঠিক আগে চলে
    window.addEventListener("beforeprint", render);

    document.addEventListener("click", function (e) {
        var btn = e.target.closest("[data-list-print]");
        if (!btn) return;
        e.preventDefault();
        render();
        window.print();
    });
})();
