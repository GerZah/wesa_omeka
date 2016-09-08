jQuery(document).ready(function () {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  // ---------------------------------------------------------------------------

  // $("#applyBtn").click(updateUrl);
  $("#st").change(updateUrl);
  $("#rel").change(updateUrl);
  $("#tr").change(updateUrl);

  function updateUrl() {
    var curUrl = window.location.href.split('?')[0]; // http://stackoverflow.com/a/28662284

    var st = $("#st").val();
    var rel = $("#rel").val();
    var tr = $("#tr").val();
    var page = $("#page").val();

    var newUrl = curUrl
      + "?st=" + st
      + "&rel=" + rel
      + "&tr=" + tr
      + "&page=" + page
    ;

    window.location = newUrl;
  }

  // ---------------------------------------------------------------------------

});
