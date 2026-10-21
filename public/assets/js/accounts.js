(function () {
    const table = document.getElementById("accTable");
    const dlg = document.getElementById("accDialog");
    if (!table || !dlg) return;

    const form = document.getElementById("accForm");
    const title = document.getElementById("accTitle");
    const fName = document.getElementById("ac_name");
    const fHolder = document.getElementById("ac_holder");
    const fNumber = document.getElementById("ac_number");
    const fDefault = document.getElementById("ac_default");
    const box = dlg.querySelector(".qa-errors");
    const base = table.dataset.base;
    const tbody = table.querySelector("tbody");
    let editId = null;

    function csrf() {
        return form.querySelector('input[name="_token"]').value;
    }

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

    function btn(attr, cls, icon, label) {
        const b = document.createElement("button");
        b.type = "button";
        b.className = "btn abtn " + cls;
        b.setAttribute(attr, "");
        const i = document.createElement("i");
        i.className = "fa-solid " + icon;
        b.appendChild(i);
        b.appendChild(document.createTextNode(" " + label));
        return b;
    }

    function cell(cls, text) {
        const td = document.createElement("td");
        if (cls) td.className = cls;
        td.textContent = text;
        return td;
    }

    function fillRow(tr, d) {
        tr.dataset.id = d.id;
        tr.dataset.default = d.is_default ? "1" : "0";
        tr.innerHTML = "";
        const def = cell("ac-default", "");
        if (d.is_default) {
            const s = document.createElement("span");
            s.className = "badge";
            s.textContent = "ডিফল্ট";
            def.appendChild(s);
        }
        const act = cell("noprint", "");
        act.appendChild(
            btn("data-acc-edit", "edit", "fa-pen-to-square", "এডিট"),
        );
        act.appendChild(document.createTextNode(" "));
        act.appendChild(btn("data-acc-del", "danger", "fa-trash", "ডিলেট"));
        [
            cell("", "#" + d.id),
            cell("ac-name", d.name),
            cell("ac-holder", d.holder_name),
            cell("ac-number", d.account_number),
            def,
            act,
        ].forEach(function (c) {
            tr.appendChild(c);
        });
    }

    // নতুন default হলে অন্য সারি থেকে ব্যাজ সরাবে
    function clearOtherDefaults(exceptId) {
        tbody.querySelectorAll("tr[data-id]").forEach(function (tr) {
            if (String(tr.dataset.id) === String(exceptId)) return;
            tr.dataset.default = "0";
            const c = tr.querySelector(".ac-default");
            if (c) c.innerHTML = "";
        });
    }

    function openDialog(tr) {
        editId = tr ? tr.dataset.id : null;
        title.textContent = tr ? "অ্যাকাউন্ট সম্পাদনা" : "নতুন অ্যাকাউন্ট";
        fName.value = tr ? tr.querySelector(".ac-name").textContent : "";
        fHolder.value = tr ? tr.querySelector(".ac-holder").textContent : "";
        fNumber.value = tr ? tr.querySelector(".ac-number").textContent : "";
        fDefault.checked = tr ? tr.dataset.default === "1" : false;
        box.hidden = true;
        dlg.showModal();
        fName.focus();
    }

    document.addEventListener("click", function (e) {
        if (e.target.closest("[data-acc-add]")) {
            openDialog(null);
            return;
        }
        const edit = e.target.closest("[data-acc-edit]");
        if (edit) {
            openDialog(edit.closest("tr"));
            return;
        }
        const del = e.target.closest("[data-acc-del]");
        if (del) {
            const tr = del.closest("tr");
            if (!confirm("রেকর্ড মুছবেন?")) return;
            const fd = new FormData();
            fd.append("_token", csrf());
            fd.append("_method", "DELETE");
            fetch(base + "/" + tr.dataset.id, {
                method: "POST",
                body: fd,
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
                            return { ok: r.ok, d: d };
                        });
                })
                .then(function (res) {
                    if (res.ok) tr.remove();
                    else alert(res.d.message || "মোছা যায়নি।");
                })
                .catch(function () {
                    alert("নেটওয়ার্ক সমস্যা, আবার চেষ্টা করুন।");
                });
        }
    });

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const submit = form.querySelector("button:not([type=button])");
        const fd = new FormData(form);
        let url = base;
        if (editId) {
            url = base + "/" + editId;
            fd.append("_method", "PUT");
        }
        box.hidden = true;
        submit.disabled = true;

        fetch(url, {
            method: "POST",
            body: fd,
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
                    let tr = tbody.querySelector(
                        'tr[data-id="' + res.d.id + '"]',
                    );
                    if (!tr) {
                        const empty = tbody.querySelector(".ac-empty");
                        if (empty) empty.remove();
                        tr = document.createElement("tr");
                        tbody.insertBefore(tr, tbody.firstChild);
                    }
                    fillRow(tr, res.d);
                    if (res.d.is_default) clearOtherDefaults(res.d.id);
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
                submit.disabled = false;
            });
    });
})();
