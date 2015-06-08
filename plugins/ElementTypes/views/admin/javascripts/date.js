jQuery(document).bind("omeka:elementformload", function() {

	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

	$("input[data-type='date']").each(function() {

		var thisHere=$(this);

		thisHere.closest(".input-block").css("width","100%");
		thisHere.css("width","50%");

		var format = thisHere.attr("data-format") || "yy-mm-dd";
		thisHere.calendarsPicker({
			dateFormat: format,
			firstDay: 1,
			rangeSelect: true,
			calendar: $.calendars.instance('gregorian', 'de'),
			showTrigger: '<button type="button" class="trigger">…</button>',
		});

		var gredJulId = "gregJul"+thisHere.attr("id");

		thisHere.parent().children("#"+gredJulId).remove();
		thisHere.parent().append("<select id='"+gredJulId+"' style='width:30%'>"+
														"<option value='gregorian'>Gregorianisch</option>"+
														"<option value='julian'>Julianisch</option>"+
														"</select>");

		$("#"+gredJulId).change(
			function() {
				var calendar = $.calendars.instance($(this).val(), 'de'); 
				var convert = function(value) { 
					return (!value || typeof value != 'object' ?
										value : calendar.fromJD(value.toJD())); 
				}; 

				var myPicker = $(this).parent().find(".is-calendarsPicker");
				var current = myPicker.calendarsPicker('option'); 
				myPicker.calendarsPicker('option', {calendar: calendar, 
								onSelect: null, onChangeMonthYear: null, 
								defaultDate: convert(current.defaultDate), 
								minDate: convert(current.minDate), 
								maxDate: convert(current.maxDate)}). 
						calendarsPicker('option', 
								{onSelect: current.onSelect, 
								onChangeMonthYear: current.onChangeMonthYear}); 
			}
		);

	});
});
