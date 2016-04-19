jQuery(document).ready(function () {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  console.log("measurementsJsonUrl = "+measurementsJsonUrl);

  // // ##### AJAX test code -- start
  // var options = {
  //   test: "parameter"
  // };
  //
  // $.ajax({
  //   url: measurementsJsonUrl,
  //   dataType: 'json',
  //   data: options,
  //   success: function (data) {
  //     console.log("success");
  //     console.log(data);
  //   }
  // });
  // // ##### AJAX test code -- end

  updateData(); // Init

  $("#measurementsArea").change(function(e) { // Change Area Callback
    // var listSelect = $(this).val();
    // console.log("Area change: " + listSelect);
    updateData();
  });

  $("#measurementsUnit").change(function(e) { // Change Unit Callback
    // var listSelect = $(this).val();
    // console.log("Unit change: " + listSelect);
    updateData();
  });

  function updateData() { // Clear / re-fill table
    $("#measurementsTable td").empty().append("&nbsp;");
    var curArea = $("#measurementsUnit").val();
    var curUnit = $("#measurementsArea").val();
    console.log("updateData – area = " + curArea + " / unit = " + curUnit);

    if ( (curArea>=0) && (curUnit>=0) ) {
      $.ajax({
        url: measurementsJsonUrl,
        dataType: 'json',
        data: { area: curArea, unit: curUnit },
        success: function(data) { ajaxSuccess(data) }
      });
    }
  }

  function ajaxSuccess(data) { // AJAX callback
    console.log("success");
    console.log(data);
  }

  $("#measurementPaginate a").click(function(e) { // .unbind("click")
    e.preventDefault();

    var clickedId = $(this).attr('id');
    var pagStep = $(this).data('pagstep');

    console.log("clicked " + clickedId + " / " + pagStep);
  });

});
