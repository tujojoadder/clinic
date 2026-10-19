/* public/assets/sidebar.js - সাইডবারের স্ক্রল পজিশন ও গ্রুপ খোলা/বন্ধ মনে রাখে */

/* ---------- গ্রুপ মেনু খোলা/বন্ধ — একসাথে শুধু একটি গ্রুপ খোলা থাকবে ---------- */
(function () {
    var KEY = "clinicSidebarGroups";
    var groups = [].slice.call(document.querySelectorAll("#sideNav .navgroup"));
    if (!groups.length) return;

    var saved = [];
    try {
        saved = JSON.parse(sessionStorage.getItem(KEY)) || [];
    } catch (e) {}

    function setOpen(g, open) {
        g.classList.toggle("open", open);
        var b = g.querySelector(".navgroup-head");
        if (b) b.setAttribute("aria-expanded", open ? "true" : "false");
    }

    function store() {
        try {
            var open = groups
                .filter(function (g) {
                    return g.classList.contains("open");
                })
                .map(function (g) {
                    return g.getAttribute("data-group");
                });
            sessionStorage.setItem(KEY, JSON.stringify(open));
        } catch (e) {}
    }

    // শুরুতে শুধু বর্তমান পেজের গ্রুপটি খুলবে।
    // ড্যাশবোর্ড/রেফারেন্সের মতো আলাদা মেনুতে থাকলে কোনো গ্রুপই খুলবে না।
    var initial = groups.filter(function (g) {
        return g.classList.contains("has-active");
    })[0];

    groups.forEach(function (g) {
        setOpen(g, g === initial);

        g.querySelector(".navgroup-head").addEventListener(
            "click",
            function () {
                var willOpen = !g.classList.contains("open");

                // অন্য সব গ্রুপ বন্ধ করা
                groups.forEach(function (other) {
                    if (other !== g) setOpen(other, false);
                });

                setOpen(g, willOpen);
                store();
            },
        );
    });
    store();
})();

/* ---------- সাইডবারের স্ক্রল পজিশন মনে রাখে ---------- */
(function () {
    var KEY = "clinicSidebarScroll";
    var aside =
        document.getElementById("sideBar") || document.querySelector("aside");
    if (!aside) return;

    // aside এবং তার ভেতরের যেসব এলিমেন্টে স্ক্রল হতে পারে সেগুলো খুঁজে বের করা
    function scrollers() {
        var list = [aside].concat([].slice.call(aside.querySelectorAll("*")));
        return list.filter(function (el) {
            var o = getComputedStyle(el).overflowY;
            return o === "auto" || o === "scroll" || o === "overlay";
        });
    }

    function save() {
        try {
            var data = scrollers().map(function (el) {
                return el.scrollTop;
            });
            sessionStorage.setItem(KEY, JSON.stringify(data));
        } catch (e) {}
    }

    function restore() {
        var data = null;
        try {
            data = JSON.parse(sessionStorage.getItem(KEY));
        } catch (e) {}
        if (!data) return false;
        var list = scrollers();
        list.forEach(function (el, i) {
            if (data[i] != null) el.scrollTop = data[i];
        });
        return true;
    }

    // সেভ করা পজিশন না থাকলে সক্রিয় মেনুটি মাঝামাঝি দেখানো
    function showActive() {
        var link = aside.querySelector("a.primary");
        if (!link) return;
        scrollers().forEach(function (sc) {
            if (sc.scrollHeight > sc.clientHeight + 1) {
                var l = link.getBoundingClientRect();
                var s = sc.getBoundingClientRect();
                sc.scrollTop +=
                    l.top - s.top - sc.clientHeight / 2 + l.height / 2;
            }
        });
    }

    // স্মুথ স্ক্রল থাকলে পজিশন বসানোর সময় সাময়িকভাবে বন্ধ রাখা
    function instant(fn) {
        var els = scrollers();
        var old = els.map(function (el) {
            return el.style.scrollBehavior;
        });
        els.forEach(function (el) {
            el.style.scrollBehavior = "auto";
        });
        fn();
        els.forEach(function (el, i) {
            el.style.scrollBehavior = old[i];
        });
    }

    var hadSaved = false;
    function apply() {
        instant(function () {
            var ok = restore();
            if (ok) hadSaved = true;
            else showActive();
        });
    }

    apply();
    document.addEventListener("DOMContentLoaded", apply);
    window.addEventListener("load", apply);
    setTimeout(apply, 150);

    var t;
    function later() {
        clearTimeout(t);
        t = setTimeout(save, 60);
    }
    scrollers().forEach(function (el) {
        el.addEventListener("scroll", later, { passive: true });
    });
    aside.addEventListener("click", save);
    window.addEventListener("pagehide", save);
})();
