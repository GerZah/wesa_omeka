jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var lightbox = lity(); // https://www.npmjs.com/package/lity
  var buttonSelect = "Select";

  $(".itemReferencesWrapper").remove();
  $(".itemRef").parent().append("<span class='itemReferencesWrapper'>"+
                  "<button class='itemReferencesBtn' data-caltype='' >"+buttonSelect+"</button>"+
            "</span>");



  $(".itemReferencesBtn").click(function(e) {
    e.preventDefault();
    lightbox("#item-relation-selector");
  });


  $("#item-relation-selector button").click(function(e) { e.preventDefault(); });


  $(document).on('lity:close', function(event, lightbox) {
  });


} );
