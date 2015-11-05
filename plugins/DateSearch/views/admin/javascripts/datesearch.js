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

  var currentTextArea = false;
  $("textarea").focus(function(e) { currentTextArea = $(this); })

  $("#dateSearchFooBtn").click(function(e) {
    e.preventDefault();

    if (currentTextArea) {
      // https://github.com/timdown/rangyinputs
      currentTextArea.replaceSelectedText("foo");
    }
  });

});
