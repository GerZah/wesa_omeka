jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var lightbox = lity(); // https://www.npmjs.com/package/lity
  var buttonSelect = "Select";

  $(".itemReferencesWrapper").remove();
  $(".itemRef").parent().append("<span class='itemReferencesWrapper'>"+
                  "<button class='itemReferencesBtn' data-caltype='' >"+buttonSelect+"</button>"+
            "</span>");

  var formerLinkText = "";

  $(".itemReferencesBtn").click(function(e) {
    e.preventDefault();

    $("#new_relation_property_id").hide().prev().hide().prev().hide();
    $("#relation_comment").parent().hide();
    formerLinkText = $("#add-relation").text();
    $("#add-relation").text("Select");

    lightbox("#item-relation-selector");
  });


  $("#item-relation-selector button").click(function(e) { e.preventDefault(); });


  $(document).on('lity:close', function(event, lightbox) {
    $("#new_relation_property_id").show().prev().show().prev().show();
    $("#relation_comment").parent().show();
    $("#add-relation").text(formerLinkText);
  });


} );
