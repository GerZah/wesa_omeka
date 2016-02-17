jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $(".measurementsBtn").unbind("click").click(function(e) {
    e.preventDefault();
  } );

  $(".measurementsClearBtn").click(function(e) {
    e.preventDefault();

    var currentVivisble = $(this).prev().prev().prev().attr("id"); // for visible text
    var currentInvisible = $(this).prev().prev().attr("id"); // for invisible text

    $("#"+currentVivisble).val("");
    $("#"+currentInvisible).val("");
  } );

} );
