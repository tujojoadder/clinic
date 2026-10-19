/*
 * Reusable pickers — যেকোনো পেজে শুধু class দিলেই কাজ করবে:
 *   <input class="js-date">      → শুধু তারিখ     (15-10-2026)
 *   <input class="js-datetime">  → তারিখ + সময়  (15-10-2026 14:30)
 * Modal / ajax দিয়ে HTML লোড হলে: window.initPickers(modalElement)
 *
 * ইনপুটে ডাটাবেসের ফরম্যাট (2026-10-15 বা 2026-10-15 14:30:00) থাকলেও
 * এখানে স্বয়ংক্রিয়ভাবে dd-mm-yyyy তে রূপান্তর হয়, তাই এডিট পেজে ভ্যালু ফাঁকা হয় না।
 */
(function () {
    // 2026-10-15 / 2026-10-15 14:30:00 / 2026-10-15T14:30  →  15-10-2026 / 15-10-2026 14:30
    function toDisplay(value, withTime) {
        var m = /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}))?/.exec(
            (value || "").trim(),
        );
        if (!m) return value;
        var out = m[3] + "-" + m[2] + "-" + m[1];
        if (withTime) out += " " + (m[4] || "00") + ":" + (m[5] || "00");
        return out;
    }

    function normalize($inputs, withTime) {
        $inputs.each(function () {
            var converted = toDisplay(this.value, withTime);
            if (converted !== this.value) this.value = converted;
        });
    }

    window.initPickers = function (root) {
        var $root = $(root || document);

        var $dates = $root.find(".js-date").not(".hasDatepicker");
        normalize($dates, false);
        $dates.attr("autocomplete", "off").datepicker({
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear: true,
        });

        var $datetimes = $root.find(".js-datetime").not(".hasDatepicker");
        normalize($datetimes, true);
        $datetimes.attr("autocomplete", "off").datetimepicker({
            dateFormat: "dd-mm-yy",
            timeFormat: "HH:mm",
            controlType: "select",
            oneLine: true,
            changeMonth: true,
            changeYear: true,
        });
    };

    $(function () {
        window.initPickers(document);
    });
})();
