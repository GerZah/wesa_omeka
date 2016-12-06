jQuery(document).ready(function () {
  var $ = jQuery;

  $("#item_type_select").change(function(e) {
    var curItemType = $(this).val();
    console.log(curItemType);

    if (curItemType!=-1) {
      var targetUrl = findOrphansTargetUrl + "&item_type_select=" + curItemType;
      window.location.href = targetUrl;
    }
  });
});
