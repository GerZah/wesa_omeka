jQuery(document).ready(function () {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  // console.log("measurementsJsonUrl = "+measurementsJsonUrl);

  var curPage = -1;
  var numPages = 0;
  updateData(); // Init

  $("#measurementsArea").change(function(e) { // Change Area Callback
    updateData();
  });

  $("#measurementsUnit").change(function(e) { // Change Unit Callback
    updateData();
  });

  function updateData() { // Clear / re-fill table
    var curArea = parseInt($("#measurementsArea").val());
    var curUnit = parseInt($("#measurementsUnit").val());
    // console.log("updateData – area = " + curArea + " / unit = " + curUnit);

    if ( (curArea>=0) && (curUnit>=0) ) {
      $.ajax({
        url: measurementsJsonUrl,
        dataType: 'json',
        data: { area: curArea, unit: curUnit, page: curPage },
        success: function(data) { ajaxSuccess(data) }
      });
    }
    else {
      clearTable();
    }
  }

  function clearTable() {
    $("#measurementsTable td").empty().append("&nbsp;");
    $("#curPage").empty();
    $("#numPages").empty();
  }

  function ajaxSuccess(data) { // AJAX callback
    // console.log(data);
    clearTable();
    if (data.data==null) {
      // console.log("null data");
    }
    else {
      // console.log("non-null data");
      curPage = parseInt(data.page);
      numPages = parseInt(data.numPages);
      $("#curPage").append(curPage+1);
      $("#numPages").append(numPages);
      var numData = data.data.length;
      var numRows = $("#measurementsTable tbody tr").length;
      // console.log(numData + " vs. " + numRows);
      for(i=0; i<numRows; i++) {
        var rowId = "measurementsRow"+i;
        var itemTitle = "&nbsp;";
        if (i<numData) { itemTitle = "foo"+data.data[i].itemTitle; }
        $("#measurementsTable #"+rowId+" .measurementsCell0").empty().append(itemTitle);
      }
    }
  }

  $("#measurementPaginate a").click(function(e) { // .unbind("click")
    e.preventDefault();

    var clickedId = $(this).attr('id');
    var pagStep = $(this).data('pagstep');

    // console.log("clicked " + clickedId + " / " + pagStep);

    switch (pagStep) {
      case "p2": curPage = numPages; updateData(); break;
      case "p1": curPage += 1; updateData(); break;
      case "m1": curPage -= 1; updateData(); break;
      case "m2": curPage = 0; updateData(); break;
    }
  });

});
