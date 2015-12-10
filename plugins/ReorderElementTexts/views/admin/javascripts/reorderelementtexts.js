jQuery(document).bind("omeka:elementformload", function() {

	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  // alert("foo");

  // Concept:

  // Select div.field .inputs .input-block
  // and count them: https://api.jquery.com/size/
  // Then add button, etc. etc.

  $(".field .inputs .input-block").css("border", "thin dotted black");

} );
