jQuery('#webGlFrame').on("load", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $('#webGlFrame').contents().find( "div#edit" ).css( "width", "0px" );

  $('#webGlFrame').contents().find( "div#view" ).css( "margin-right", "0px" );
  document.getElementById('webGlFrame').contentWindow.resize(); // http://stackoverflow.com/a/22148030
} );
