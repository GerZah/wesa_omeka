var lightbox = lity(); // https://www.npmjs.com/package/lity

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

      lightbox("#range-search-popup");
      console.log(selText);

    }
    else { alert(rangeSearchSelectFirst); }
  });

  // --------------------------------------------------------

  $(document).on('lity:close', function(event, lightbox) {
    if (currentTextArea) {
      currentTextArea.replaceSelectedText("FOO");
    }
  });

  // --------------------------------------------------------

} );
