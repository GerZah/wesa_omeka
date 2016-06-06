jQuery(document).ready(function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var first = true;

  showHideShowAllRelations();
  $("#all_relations").change(showHideShowAllRelations);

  function showHideShowAllRelations() {
    var showAllRelations = $("#all_relations").is(":checked");
    var obj = $("#selected_relations-label").parent();

    if (showAllRelations) { if (first) { obj.hide(); } else { obj.slideUp(); } }
    else { if (first) { obj.show(); } else { obj.slideDown(); } }
    first = false;
  }

});
