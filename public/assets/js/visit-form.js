(function () {
    const form = document.getElementById("visitForm");
    if (!form) return;

    const doctor = document.getElementById("doctor_id");
    const type = document.getElementById("visit_type");
    const fee = document.getElementById("fee");
    const url = form.dataset.feeUrl;

    function loadFee() {
        if (!doctor.value) return;
        fetch(url.replace("__ID__", doctor.value), {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
            credentials: "same-origin",
        })
            .then((r) => r.json())
            .then((d) => {
                fee.value = type.value === "followup" ? d.followup_fee : d.fee;
            })
            .catch(console.error);
    }

    $(doctor).on("change", loadFee);
    $(type).on("change", loadFee);

    // তারিখ (দিন) বদলালে আগের বেছে নেওয়া ডাক্তার মুছে দেওয়া হয়,
    // কারণ নতুন দিনে সে নাও বসতে পারে। শুধু সময় বদলালে কিছু হয় না।
    const appt = document.getElementById("appointment_at");
    if (appt) {
        let lastDay = (appt.value || "").slice(0, 10);
        $(appt).on("change", function () {
            const day = (appt.value || "").slice(0, 10);
            if (day === lastDay) return;
            lastDay = day;
            $(doctor).val(null).trigger("change");
        });
    }

    if (form.dataset.autoload === "1") loadFee();
})();
