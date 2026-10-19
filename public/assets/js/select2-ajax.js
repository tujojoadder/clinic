(function ($) {
    window.initAjaxSelects = function (root) {
        $(root || document)
            .find("select[data-ajax-url]")
            .each(function () {
                var $s = $(this);
                if ($s.hasClass("select2-hidden-accessible")) return;
                $s.select2({
                    width: "100%",
                    placeholder: $s.data("placeholder") || "নির্বাচন করুন",
                    allowClear: !$s.prop("required"),
                    ajax: {
                        url: $s.data("ajax-url"),
                        dataType: "json",
                        delay: 250,
                        data: function (p) {
                            var params = { q: p.term || "", page: p.page || 1 };
                            // অন্য ইনপুটের মান সাথে পাঠানোর জন্য: data-extra-date-from="#appointment_at"
                            var from = $s.data("extra-date-from");
                            if (from) params.date = $(from).val() || "";
                            return params;
                        },
                        processResults: function (d) {
                            return d;
                        },
                    },
                });
            });
    };
    $(function () {
        window.initAjaxSelects(document);
    });
})(jQuery);
