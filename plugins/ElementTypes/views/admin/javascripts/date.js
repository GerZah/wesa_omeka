jQuery(document).bind("omeka:elementformload", function() {
    jQuery("input[data-type='date']").each(function() {
        var format = jQuery(this).attr("data-format") || "yy-mm-dd";
        jQuery(this).calendarsPicker({
            dateFormat: format,
						useMouseWheel: true,
						firstDay: 1,
						rangeSelect: true,
        });
    });
});
