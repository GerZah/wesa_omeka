jQuery(document).ready(function () {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  // ---------------------------------------------------------------------------

  $("#applyBtn").click(updateUrl);
  $("#st, #rel, #tr").change(updateUrl);
  $("#idfilter, #titlefilter, #weightfactor").keyup(checkEnterKey);

  function checkEnterKey(e) { if (e.keyCode == 13) { updateUrl(); } }

  function updateUrl() {
    var curUrl = window.location.href.split('?')[0]; // http://stackoverflow.com/a/28662284

    var st = $("#st").val();
    var rel = $("#rel").val();
    var tr = $("#tr").val();
    var page = $("#page").val();
    page = ( page == null ? "" : page );
    var idfilter = $("#idfilter").val().trim();
    var titlefilter = $("#titlefilter").val().trim();
    var weightfactor = $("#weightfactor").val().trim();

    var newUrl = curUrl
      + "?st=" + st
      + "&rel=" + rel
      + "&tr=" + tr
      + "&page=" + page
      + "&idfilter=" + idfilter
      + "&titlefilter=" + titlefilter
      + "&weightfactor=" + weightfactor
    ;

    window.location = newUrl;
  }

  // ---------------------------------------------------------------------------

  var allShown = false;

  $("a.transactionShowHideAllRows").click(function(e) {
    e.preventDefault();
    allShown = !allShown;
    if (allShown) {
      $(".itemsHiddenUpFront").show();
    }
    else {
      $(".itemsHiddenUpFront").hide();
    }
  });

  $("a.transactionShowHideRows").click(function(e){
    e.preventDefault();
    var clickedId = $(this).data('item');
    $("tbody.tr"+clickedId).toggle();
  });

  // ---------------------------------------------------------------------------

});
