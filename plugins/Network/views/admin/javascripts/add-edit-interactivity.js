jQuery(document).ready(function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  // --------

  var firstRelations = { val: true };
  showHideShowAllRelations();
  $("#all_relations").change(showHideShowAllRelations);

  function showHideShowAllRelations() {
    var showAllRelations = $("#all_relations").is(":checked");
    var listParent = $("#selected_relations-label").parent();
    var buttonParent = $("#unselect_relations").parent();
    showHideBlock(showAllRelations, listParent, buttonParent, firstRelations);
  }
  $("#unselect_relations").click(function() { $("#selected_relations option:selected").prop("selected", false); });

  // --------

  var firstReferences = { val: true };
  showHideShowAllReferences();
  $("#all_references").change(showHideShowAllReferences);

  function showHideShowAllReferences() {
    var showAllReferences = $("#all_references").is(":checked");
    var listParent = $("#selected_references-label").parent();
    var buttonParent = $("#unselect_references").parent();
    showHideBlock(showAllReferences, listParent, buttonParent, firstReferences);
  }
  $("#unselect_references").click(function() { $("#selected_references option:selected").prop("selected", false); });

  // --------

  function showHideBlock(showHide, list, button, first) {
    if (showHide) {
      if (first.val) { list.hide(); button.hide(); }
      else { list.slideUp(); button.slideUp("fast"); }
    }
    else {
      if (first.val) { list.show(); button.show(); }
      else { list.slideDown(); button.slideDown("slow"); }
    }
    first.val = false;
  }

  // --------

});
