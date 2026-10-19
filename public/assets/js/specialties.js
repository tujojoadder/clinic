(function () {
    const table = document.getElementById("specTable");
    const dlg = document.getElementById("specDialog");
    if (!table || !dlg) return;

    const form = document.getElementById("specForm");
    const title = document.getElementById("specTitle");
    const input = document.getElementById("sp_name");
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

    function fillRow(tr, d) {
        tr.dataset.id = d.id;
        tr.innerHTML = "";
        const c1 = document.createElement("td");
        c1.textContent = "#" + d.id;
        const c2 = document.createElement("td");
        c2.className = "sp-name";
        c2.textContent = d.name;
        const c3 = document.createElement("td");
        c3.className = "sp-count";
        c3.textContent = d.doctors_count;
        const c4 = document.createElement("td");
        c4.appendChild(
            btn("data-spec-edit", "edit", "fa-pen-to-square", "এডিট"),
        );
        c4.appendChild(document.createTextNode(" "));
        c4.appendChild(btn("data-spec-del", "danger", "fa-trash", "ডিলেট"));
        [c1, c2, c3, c4].forEach(function (c) {
            tr.appendChild(c);
        });
    }

    function openDialog(id, name) {
        editId = id;
        title.textContent = id ? "বিভাগ সম্পাদনা" : "নতুন বিভাগ";
        input.value = name || "";
        box.hidden = true;
        dlg.showModal();
        input.focus();
    }

    document.addEventListener("click", function (e) {
        if (e.target.closest("[data-spec-add]")) {
            openDialog(null, "");
            return;
        }
        const edit = e.target.closest("[data-spec-edit]");
        if (edit) {
            const tr = edit.closest("tr");
            openDialog(tr.dataset.id, tr.querySelector(".sp-name").textContent);
            return;
        }
        const del = e.target.closest("[data-spec-del]");
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
                        const empty = tbody.querySelector(".sp-empty");
                        if (empty) empty.remove();
                        tr = document.createElement("tr");
                        tbody.insertBefore(tr, tbody.firstChild);
                    }
                    fillRow(tr, res.d);
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
