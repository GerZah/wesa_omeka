jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $("#dateSearchWrapper").remove();
  $("#save")
    .css("position", "relative")
    .append("<span id='dateSearchWrapper'>"+
              "<div class='dateSearchButtons field'>"+
                "<input type='checkbox' id='dateSearchTimeSpan'> "+
                "<label for='dateSearchTimeSpan'>Span</label> "+
                "<button id='dateSearchDBtn' class='dateSearchBtn' data-caltype='' >D</button>"+ // unspecific
                "<button id='dateSearchGBtn' class='dateSearchBtn' data-caltype='G'>G</button>"+ // Gregorian
                "<button id='dateSearchJBtn' class='dateSearchBtn' data-caltype='J'>J</button>"+ // Julian
                "<input id='dateSearchEdit' class='dateSearchHiddenEdit'>"+
                "</div>"+
              "</span>");

  var currentTextArea = false;
  $("textarea").focus(function(e) { currentTextArea = $(this); })

  $("#dateSearchWrapper button").click(function(e) { e.preventDefault(); });

  var curCalType = "gregorian";
  var curPrefix = "";

  $("#dateSearchEdit").calendarsPicker({
    showOnFocus: false,
    firstDay: 1,
		yearRange: 'any',
    dateFormat: "yyyy-mm-dd",
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

      if ( (prefix == "[G] ") || (prefix == "[J] ") ) {
        calType = selText.substr(1,1);
        selText = selText.substr(4);
      }

      switch (calType) {
        default  :
        case ""  :
        case "G" : curCalType="gregorian"; break;
        case "J" : curCalType="julian"; break;
      }

      switch (calType) {
        default  :
        case ""  : curPrefix = ""; break;
        case "G" : curPrefix ="[G] "; break;
        case "J" : curPrefix ="[J] "; break;
      }

      var isSpan = $("#dateSearchTimeSpan").is(':checked');

      $("#dateSearchEdit").val(selText);

      $("#dateSearchEdit").calendarsPicker("option", {
        rangeSelect: isSpan,
        calendar: $.calendars.instance(curCalType, dateSearchLocale)
      } );

      $("#dateSearchEdit").show().calendarsPicker("show").hide();
    }
    else { alert("Please select a target text area first."); }
  });

});
