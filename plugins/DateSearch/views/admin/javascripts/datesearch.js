jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $("#dateSearchWrapper").remove();
  $("#save")
    .css("position", "relative")
    .append("<span id='dateSearchWrapper'>"+
              "<div class='dateSearchButtons field'>"+
                "<button id='dateSearchFooBtn'>foo</button>"+
                "</div>"+
              "</span>");

  $("#dateSearchFooBtn").click(function(e) {
    e.preventDefault();

    // ### http://stackoverflow.com/a/15977052/5394093
    var element = "Elements-50-0-text";
    var caretPos = document.getElementById("Elements-50-0-text").selectionStart;
    var textAreaTxt = jQuery("#Elements-50-0-text").val();
    var txtToAdd = "foo";
    jQuery("#Elements-50-0-text").val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
  });

});
