jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var lightbox = lity(); // https://www.npmjs.com/package/lity

  // --------------------------------------------------------

  $("#rangeSearchWrapper").remove();
  $("#save")
    .append("<span id='rangeSearchWrapper'>"+
              $("#range-search-controls").html()+
              "</span>");

  // --------------------------------------------------------

  var currentTextArea = false;
  $("textarea").focus(function(e) { currentTextArea = $(this); })

  // --------------------------------------------------------

  $(".rangeSearchButtons button").click(function(e) {
    e.preventDefault();

    if (!currentTextArea) { alert(rangeSearchSelectFirst); return; }

    var sel = currentTextArea.getSelection();
    var selText = "";
    if (sel.start != sel.end) { selText = sel.text; }

    console.log(selText);
    lightbox("#range-search-popup");
    showHideSecondTriple();
  });

  // --------------------------------------------------------

  $("#range-search-popup button").click(function(e) { e.preventDefault(); });

  // --------------------------------------------------------

  function isRange() {
    return $("#rangeSearchRange").is(":checked");
  }

  // -------------------

  function showHideSecondTriple() {
    if (isRange()) {
      $("#rangeSearchSecondTriple").slideDown("fast");
    }
    else {
      $("#rangeSearchSecondTriple").slideUp("fast");
    }
  }

  // -------------------

  $("#rangeSearchRange").change(function() {
    showHideSecondTriple();
  });

  // --------------------------------------------------------

  function checkTextfields(textFields) {
    var textField;
    for(textField of textFields) {
      if (!$(textField).val().match(/\d+/)) {
        alert(rangeSearchEnterNumber);
        $(textField).focus();
        return false;
      }
    }
    return true;
  }

  // -------------------

  $("#rangeSearchApply").click(function () {
    if (!currentTextArea) { alert(rangeSearchSelectFirst); return; }

    if ($("#rangeSearchUnits").val() == -1) {
      alert(rangeSearchSelectUnit);
      $("#rangeSearchUnits").focus();
      return;
    }

    var textFields = ["#rangeSearch1", "#rangeSearch2", "#rangeSearch3"];
    if (!checkTextfields(textFields)) { return; }

    var range = isRange();

    if (range) {
      var textFields = ["#rangeSearch4", "#rangeSearch5", "#rangeSearch6"];
      if (!checkTextfields(textFields)) { return; }
    }

    var units = $('#rangeSearchUnits option:selected').text();
    units = units.split("-");

    var result="";
    result += $("#rangeSearch1").val() + units[0] + "-" +
              $("#rangeSearch2").val() + units[1] + "-" +
              $("#rangeSearch3").val() + units[2];

    if (range) {
      result += " - " +
                $("#rangeSearch4").val() + units[0] + "-" +
                $("#rangeSearch5").val() + units[1] + "-" +
                $("#rangeSearch6").val() + units[2];
    }

    currentTextArea.replaceSelectedText(result);
    lightbox.close();
  });

  // --------------------------------------------------------

  $(document).on('lity:close', function(event, lightbox) {
  });

  // --------------------------------------------------------

} );
