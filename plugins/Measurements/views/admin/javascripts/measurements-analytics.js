jQuery(document).ready(function () {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  console.log("measurementsJsonUrl = "+measurementsJsonUrl);

  var options = {
    test: "parameter"
  };

  $.ajax({
    url: measurementsJsonUrl,
    dataType: 'json',
    data: options,
    success: function (data) {
      console.log("success");
      console.log(data);
    }
  });

});
