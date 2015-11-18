jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  // --------------------------------------------------------

  $("#rangeSearchWrapper").remove();
  $("#save")
    .append("<span id='rangeSearchWrapper'>"+
              "<div class='rangeSearchButtons field'>"+
                "<label>"+rangeSearchRangeEntry+"</label> "+
                "<button class='rangeSearchBtn'>"+"Foo"+"</button>"+
                "</div>"+
              "</span>");

  // --------------------------------------------------------

  var currentTextArea = false;
  $("textarea").focus(function(e) { currentTextArea = $(this); })

  // --------------------------------------------------------

  $(".rangeSearchButtons").click(function(e) {
    e.preventDefault();

    if (currentTextArea) {

      var sel = currentTextArea.getSelection();
      var selText = "";
      if (sel.start != sel.end) { selText = sel.text; }

      // +#+#+#

      console.log(selText);
      // alert(selText);

      currentTextArea.replaceSelectedText("FOO");

    }
    else { alert(rangeSearchSelectFirst); }
  });

  // --------------------------------------------------------

} );
