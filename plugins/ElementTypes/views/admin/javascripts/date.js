jQuery(document).bind("omeka:elementformload", function() {

	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

	$("input[data-type='date']").each(function() {

		var thisHere=$(this);
		var isSpan=(thisHere.val().indexOf(" - ")!=-1);

		var gregFirst=elTypesGregorian.substr(0,1); // "G"
		var julFirst=elTypesJulian.substr(0,1); // "J"

		var gregPrefix="["+gregFirst+"]"; // "[G]"
		var julPrefix="["+julFirst+"]"; // "[J]"
		var prefixLen=gregPrefix.length; // 3

		var calPrefix="";
		var pickerStatusText="";

		thisHere.closest(".input-block").css("width","100%");
		thisHere.css("width","55%");

		var format = thisHere.attr("data-format") || "yy-mm-dd";
		thisHere.calendarsPicker({
			dateFormat: format,
			firstDay: 1,
			yearRange: 'any',
			rangeSelect: isSpan,
			calendar: $.calendars.instance('gregorian', elTypesLocale),
			showOnFocus: false,
			onShow: function(picker, inst) {
				picker.find('tbody').append("<tr><td colspan='7' class='calendars-status'>"+
																		"<strong>"+pickerStatusText+"</strong>"+
																		"</td></tr>");
			},
			onClose: setCalPrefix,
		});

		var gredJulId = "gregJul"+thisHere.attr("id");
		var timespanId = "timespan"+thisHere.attr("id");

		thisHere.parent().children("#"+gredJulId).remove();

		var isChecked = (isSpan ? "checked" : "");
		thisHere.parent().append("<span id='"+gredJulId+"'> "+
															"<button type='button' class='editGregLink'>"+gregFirst+"</button>"+
															"<button type='button' class='editJuliLink'>"+julFirst+"</button>"+
															"<input type='checkbox' id='"+timespanId+"' "+isChecked+"> "+elTypesTimeSpan+
															"<br><strong>"+elTypesConvert+":</strong> "+
															"<a href='#' class='convGregLink'>→ ["+elTypesGregorian+"]</a> "+
															"<a href='#' class='convJuliLink'>→ ["+elTypesJulian+"]</a>"+
															"</span>");

		var thisDateEdit = thisHere.parent().find(".is-calendarsPicker");

		$("#"+gredJulId+" .editGregLink").click( function() { return editGregJuli("gregorian"); } );
		$("#"+gredJulId+" .editJuliLink").click( function() { return editGregJuli("julian"); } );

		$("#"+timespanId).change(switchTimespan);

		$("#"+gredJulId+" .convGregLink").click( function() { return convGregJuli("gregorian"); } );
		$("#"+gredJulId+" .convJuliLink").click( function() { return convGregJuli("julian"); } );

		// -----------------------------

		function setPickerToGregJuli(to) {
			var myCalendar = $.calendars.instance(to, elTypesLocale);
			thisDateEdit.calendarsPicker("option", { calendar: myCalendar } );
			pickerStatusText=(to=="gregorian" ? elTypesGregorian : elTypesJulian);
		}

		// -----------------------------

		function removeCalPrefix(thisDateEdit) {
			var curPrefix=thisDateEdit.val().substr(0,prefixLen);
			if ( (curPrefix==gregPrefix) || (curPrefix==julPrefix) ) {
				thisDateEdit.val(thisDateEdit.val().substr(prefixLen+1))
			}
		}

		// -----------------------------

		function defineCalPrefix(what) {
			calPrefix=(what=="gregorian" ? gregPrefix : julPrefix);
		}

		function setCalPrefix() {
			if ( (calPrefix) && (thisHere.val()) ) { thisHere.val(calPrefix+" "+thisHere.val()); }
		}

		// -----------------------------

		function editGregJuli(what) {
			event.preventDefault();

			setPickerToGregJuli(what);

			removeCalPrefix(thisDateEdit);
			defineCalPrefix(what);

			thisDateEdit.calendarsPicker("show");
		}

		// -----------------------------

		function switchTimespan() {
			var isSpan = $(this).is(':checked');
			thisDateEdit.calendarsPicker("option", { rangeSelect: isSpan } );

			var curValue=thisDateEdit.val();

			var curPrefix=curValue.substr(0,prefixLen+1);
			if ( (curPrefix==gregPrefix+" ") || (curPrefix==julPrefix+" ") ) {
				curValue=curValue.substr(prefixLen+1);
			}
			else { curPrefix=""; }

			if (curValue) {
				var curSep=curValue.indexOf(" - ");
				var currentlySpan=(curSep!=-1);

				if (currentlySpan!=isSpan) {
					if (isSpan) { thisDateEdit.val(curPrefix+curValue+" - "+curValue); }
					else { thisDateEdit.val(curPrefix+curValue.substr(0,curSep)); }
				}
			}
		}

		// -----------------------------

		function convGregJuli(to) {
			event.preventDefault();

			var from=(to=='gregorian' ? 'julian' : 'gregorian');

			removeCalPrefix(thisDateEdit);

			var canConvert=true;

			if (to=="julian") {
				var curdate=thisDateEdit.calendarsPicker('getDate');
				for (var i = 0; i < curdate.length; i++) {
					canConvert=( (canConvert) && (canConvToJulian(curdate[i])) );
				}
			}

			if (canConvert) {
				setPickerToGregJuli(from);
				thisDateEdit.calendarsPicker("show");
	
				var curdate=thisDateEdit.calendarsPicker('getDate');
	
				var toCalendar=$.calendars.instance(to, elTypesLocale);
				var newdate=new Array();
	
				for (var i = 0; i < curdate.length; i++) {
					newdate[i]=curdate[i];
					newdate[i] = toCalendar.fromJD(curdate[i].toJD());
				}
	
				setPickerToGregJuli(to);
				defineCalPrefix(to);
				thisDateEdit.calendarsPicker('setDate', newdate);
			}

			else {
				defineCalPrefix(from);
				setCalPrefix();
			}
		}

		// -----------------------------

		function canConvToJulian(date) {
			var result=true;

			var dateString=String(date);

			var year=parseInt(dateString.substr(0,4));
			var month=parseInt(dateString.substr(5,2));
			var day=parseInt(dateString.substr(8,2));

			if (year<1582) { result=false; }
			else if ( (year==1582) && (month<10) ) { result=false; }
			else if ( (year==1582) && (month==10) && (day<15) ) { result=false; }

			console.log(year+" | "+month+" | "+day+" = "+result);

			return result;
		}

		// -----------------------------

	});
});
