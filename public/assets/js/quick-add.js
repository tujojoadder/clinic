(function () {
    document.addEventListener("click", function (e) {
        const open = e.target.closest("[data-quick-open]");
        if (open) {
            const dlg = document.getElementById(open.dataset.quickOpen);
            if (dlg) {
                dlg.showModal();
                const first = dlg.querySelector(
                    "input:not([type=hidden]), select, textarea",
                );
                if (first) first.focus();
            }
            return;
        }
        if (e.target.closest("[data-quick-close]")) {
            e.target.closest("dialog").close();
            return;
        }
        if (e.target.matches("dialog[data-quick-modal]")) e.target.close(); // backdrop click
    });

    // modal বন্ধ হলে error মুছে দাও
    document.addEventListener(
        "close",
        function (e) {
            const box =
                e.target.querySelector && e.target.querySelector(".qa-errors");
            if (box) box.hidden = true;
        },
        true,
    );

    document.addEventListener("submit", function (e) {
        const form = e.target.closest("form[data-quick-form]");
        if (!form) return;
        e.preventDefault();

        const dlg = form.closest("dialog");
        const box = form.querySelector(".qa-errors");
        const btn = form.querySelector("button:not([type=button])");

        function showErr(msgs) {
            box.innerHTML = "";
            const ul = document.createElement("ul");
            msgs.forEach(function (m) {
                const li = document.createElement("li");
                li.textContent = m;
                ul.appendChild(li);
            });
            box.appendChild(ul);
            box.hidden = false;
        }

        box.hidden = true;
        btn.disabled = true;

        fetch(form.action, {
            method: "POST",
            body: new FormData(form),
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
        })
            .then(function (r) {
                return r
                    .json()
                    .catch(function () {
                        return {};
                    })
                    .then(function (d) {
                        return { ok: r.ok, status: r.status, d: d };
                    });
            })
            .then(function (res) {
                if (res.ok && res.d.id) {
                    const select = document.getElementById(form.dataset.target);
                    if (select) {
                        const idx =
                            select.options.length &&
                            select.options[0].value === ""
                                ? 1
                                : 0;
                        select.add(
                            new Option(res.d.label, res.d.id, true, true),
                            idx,
                        );
                        select.value = res.d.id;
                        select.dispatchEvent(
                            new Event("change", { bubbles: true }),
                        );
                    }
                    form.reset();
                    dlg.close();
                } else if (res.status === 422 && res.d.errors) {
                    showErr(Object.values(res.d.errors).flat());
                } else if (res.status === 419 || res.status === 401) {
                    showErr(["সেশন শেষ হয়ে গেছে, পেজ রিফ্রেশ করুন।"]);
                } else {
                    showErr([res.d.message || "সংরক্ষণ করা যায়নি।"]);
                }
            })
            .catch(function () {
                showErr(["নেটওয়ার্ক সমস্যা, আবার চেষ্টা করুন।"]);
            })
            .finally(function () {
                btn.disabled = false;
            });
    });
})();
