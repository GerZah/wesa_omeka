jQuery(document).ready(function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $("a#revealPdfTextLink").click(function(e) {
    e.preventDefault();
    $("a#revealPdfTextLink").hide();
    $("div#unrevealedPdfText").show();
  });

  $("a#unrevealPdfTextLink").click(function(e) {
    e.preventDefault();
    $("a#revealPdfTextLink").show();
    $("div#unrevealedPdfText").hide();
  });

 });
