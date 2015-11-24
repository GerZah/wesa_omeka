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

  $("#range-search-apply").click(function () {
    if (currentTextArea) {
      currentTextArea.replaceSelectedText("FOO");
    }
  });

  // --------------------------------------------------------

  $(document).on('lity:close', function(event, lightbox) {
  });

  // --------------------------------------------------------

} );
