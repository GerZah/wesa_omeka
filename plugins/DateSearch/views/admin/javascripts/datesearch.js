jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $("#dateSearchWrapper").remove();
  $("#save")
    .css("position", "relative")
    .append("<span id='dateSearchWrapper'>"+
              "<div class='dateSearchButtons field'>"+
                "<button id='dateSearchDBtn'>D</button>"+
                "<input id='dateSearchEdit' class='dateSearchHiddenEdit'>"+
                "</div>"+
              "</span>");

  var currentTextArea = false;
  $("textarea").focus(function(e) { currentTextArea = $(this); })

  $("#dateSearchEdit").calendarsPicker({
    showOnFocus: false,
    firstDay: 1,
		yearRange: 'any',
    // rangeSelect: false, // true
    // dateFormat: "yyyy-mm-dd",
    // calendar: $.calendars.instance('gregorian', elTypesLocale),
    onClose: function(dates) {
      // console.log('Closed with date(s): ' + dates);
      if (currentTextArea) {
         var newDates = $("#dateSearchEdit").val();
         if (newDates) { currentTextArea.replaceSelectedText(newDates); }
         $("#dateSearchEdit").val("");
       }
    }
  });

  $("#dateSearchDBtn").click(function(e) {
    e.preventDefault();
    if (currentTextArea) {
      var sel = currentTextArea.getSelection();
      // console.log(sel.text);
      var selText = null;
      if (sel.start != sel.end) { selText = sel.text; }
      $("#dateSearchEdit").val(selText);
      $("#dateSearchEdit").calendarsPicker("option", {
        calendar: $.calendars.instance("gregorian", elTypesLocale),
        dateFormat: "yyyy-mm-dd",
        rangeSelect: false
      } );
      $("#dateSearchEdit").calendarsPicker("show");
    }
    else { alert("First select target text area."); }
  });

});
