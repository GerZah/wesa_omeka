jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var gregFirst=dateSearchGregorian.substr(0,1); // "G"
  var julFirst=dateSearchJulian.substr(0,1); // "J"
  var dateFirst=dateSearchDate.substr(0,1); // "D"

  var gregPrefix="["+gregFirst+"]"; // "[G]"
  var julPrefix="["+julFirst+"]"; // "[J]"
  var datePrefix=""; // empty

  $("#dateSearchWrapper").remove();
  $("#save")
    .append("<span id='dateSearchWrapper'>"+
              "<div class='dateSearchButtons field'>"+
                "<input id='dateSearchEdit' class='dateSearchHiddenEdit'>"+
                "<button id='dateSearchDBtn' class='dateSearchBtn' data-caltype='' >"+dateFirst+"</button>"+ // unspecific
                "<button id='dateSearchGBtn' class='dateSearchBtn' data-caltype='G'>"+gregFirst+"</button>"+ // Gregorian
                "<button id='dateSearchJBtn' class='dateSearchBtn' data-caltype='J'>"+julFirst+"</button>"+ // Julian
                "<input type='checkbox' id='dateSearchTimeSpan'> "+
                "<label for='dateSearchTimeSpan'>"+dateSearchTimeSpan+"</label> "+
                "<br><strong>"+dateSearchConvert+":</strong> "+
                "<a href='#' class='convGregLink'>→ ["+dateSearchGregorian+"]</a> "+
                "<a href='#' class='convJuliLink'>→ ["+dateSearchJulian+"]</a>"+
              "</div>"+
            "</span>");

  var currentTextArea = false;
  $("textarea").focus(function(e) { currentTextArea = $(this); })

  $("#dateSearchWrapper button, #dateSearchWrapper a").click(function(e) { e.preventDefault(); });

  var curCalType = "gregorian";
  var curPrefix = "";
  var curPickerStatus = "";

  $("#dateSearchEdit").calendarsPicker({
    showOnFocus: false,
    firstDay: 1,
		yearRange: 'any',
    dateFormat: "yyyy-mm-dd",
    clearText: dateSearchCancel,
    // pickerClass: "dateSearchNoClear",
    onShow: function(picker, inst) {
      picker.find('tbody').append("<tr><td colspan='7' class='calendars-status'>"+
                                  "<strong>"+curPickerStatus+"</strong>"+
                                  "</td></tr>");
    },
    onClose: function(dates) {
      // console.log('Closed with date(s): ' + dates);
      if (currentTextArea) {
         var newDate = $("#dateSearchEdit").val();
         if (newDate) { currentTextArea.replaceSelectedText(curPrefix+newDate); }
         $("#dateSearchEdit").val("");
       }
    }
  });

  $(".dateSearchBtn").click(function() {
    if (currentTextArea) {
      var calType = $(this).data("caltype");

      var sel = currentTextArea.getSelection();
      var selText = "";
      if (sel.start != sel.end) { selText = sel.text; }

      var prefix = selText.substr(0,4).toUpperCase();

      if ( (prefix == gregPrefix+" ") || (prefix == julPrefix+" ") ) {
        calType = selText.substr(1,1);
        selText = selText.substr(4);
      }

      switch (calType) {
        default  :
        case ""  :
        case "G" : curCalType = "gregorian"; break;
        case "J" : curCalType = "julian"; break;
      }

      switch (calType) {
        default  :
        case ""  : curPrefix = ""; break;
        case "G" : curPrefix = gregPrefix+" "; break;
        case "J" : curPrefix = julPrefix+" "; break;
      }

      switch (calType) {
        default  : curPickerStatus = ""; break;
        case ""  : curPickerStatus = dateSearchDate; break;
        case "G" : curPickerStatus = dateSearchGregorian; break;
        case "J" : curPickerStatus = dateSearchJulian; break;
      }

      var isSpan = $("#dateSearchTimeSpan").is(':checked');
      if (selText.indexOf(" - ") >= 0) { isSpan=true; }

      $("#dateSearchEdit").val(selText);

      $("#dateSearchEdit").calendarsPicker("option", {
        rangeSelect: isSpan,
        calendar: $.calendars.instance(curCalType, dateSearchLocale)
      } );

      $("#dateSearchEdit").show().calendarsPicker("show").hide();
    }
    else { alert(dateSearchSelectFirst); }
  });

});
