/* প্রতি পেজে কতটি — select বদলালেই per_page সহ পেজ রিলোড, বাকি সব ফিল্টার থাকে */
document.addEventListener("change", function (e) {
    var select = e.target.closest("select[data-per-page]");
    if (!select) return;

    var url = new URL(window.location.href);

    // পেজ নম্বর রিসেট (page, admitted_page, discharged_page ... সব)
    Array.from(url.searchParams.keys()).forEach(function (key) {
        if (key === "page" || key.slice(-5) === "_page") {
            url.searchParams.delete(key);
        }
    });

    // রিসেটের পরে বসাতে হবে, কারণ per_page নামটাও "_page" দিয়ে শেষ
    url.searchParams.set("per_page", select.value);
    window.location.href = url.toString();
});
