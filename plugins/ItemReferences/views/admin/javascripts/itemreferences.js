jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var lightbox = lity(); // https://www.npmjs.com/package/lity

  $(".itemReferencesBtn").click(function(e) {
    e.preventDefault();

    $("#new_relation_property_id").hide().prev().hide().prev().hide();
    $("#relation_comment").parent().hide();
    $("#add-relation").hide();
    $("#add-relation").parent().append("<a href='#' id='select_item' class='green button'>Select Item</a>");

    //var hiddenId = $('#new_relation_property_id').val();

    var currentHidden = $(this).prev().attr("id"); //for id
    var currentEdit = $(currentHidden).prev().attr("id"); //for title
    $("#select_item").click(function(e) {
      e.preventDefault();
      $("#"+currentHidden).val($("#object_title").html());
//      $("#"+currentHidden).val($("#new_relation_property_id").html());

      lightbox.close();
    });

    lightbox("#item-relation-selector");
  });


  $("#item-relation-selector button").click(function(e) { e.preventDefault(); });


  $(document).on('lity:close', function(event, lightbox) {
    $("#new_relation_property_id").show().prev().show().prev().show();
    $("#relation_comment").parent().show();
    $("#add-relation").show();
    $("#select_item").remove();
  });

} );
