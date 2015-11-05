jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $("#dateSearchWrapper").remove();
  $("#save")
    .css("position", "relative")
    .append("<span id='dateSearchWrapper'>"+
              "<div class='dateSearchButtons field'>"+
                // "<button id='dateSearchFooBtn'>foo</button>"+
                "<button id='dateSearchDBtn'>D</button>"+
                "</div>"+
              "</span>");

  var currentTextArea = false;
  $("textarea").focus(function(e) { currentTextArea = $(this); })

  /*
  $("#dateSearchFooBtn").click(function(e) {
    e.preventDefault();

    if (currentTextArea) {
      // https://github.com/timdown/rangyinputs
      currentTextArea.replaceSelectedText("foo");
    }
  });
  */

  $("#dateSearchDBtn")
  .calendarsPicker(
    { showOnFocus: false,
      firstDay: 1,
			yearRange: 'any',
      rangeSelect: false, // true
      // dateFormat: "dd.mm.yy",
      calendar: $.calendars.instance('gregorian', elTypesLocale),
      onClose: function(dates) {
        console.log('Closed with date(s): ' + dates);
        if (currentTextArea) {
          var newDates = String(dates);
          if (newDates) { currentTextArea.replaceSelectedText(newDates); }
        }
      }
    }
  )
  .click(function(e) {
    e.preventDefault();
    if (currentTextArea) {
      $(this).val(''); // +#+#+# this can't be right - calendar reference required to reset
      var sel = currentTextArea.getSelection();
      console.log(sel.text);
      var selText = null;
      if (sel.start != sel.end) { selText = sel.text; }
      $(this).calendarsPicker({ defaultDate: selText }); // +#+#+# this doesn't work
      $(this).calendarsPicker("show");
    }
    else { alert("First select target text area."); }
  });

});
