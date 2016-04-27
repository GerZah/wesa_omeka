jQuery(document).ready(function () {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  // console.log("measurementsJsonUrl = "+measurementsJsonUrl);

  var curPage = -1;
  var numPages = 0;
  var curUnit = -1;
  updateData(); // Init

  // ---------------------------------------------------------------------------

  $("#measurementsArea").change(function(e) { // Change Area Callback
    updateData();
  });

  // -------------

  $("#measurementsUnit").change(function(e) { // Change Unit Callback
    updateData();
  });

  // ---------------------------------------------------------------------------

  var curFrom = -1;
  var curTo = -1;
  var curTitleFilter = "";

  var editTimer = null;

  $("#measurementsIdFilter").keyup(editUpdate).blur(editUpdate);
  $("#measurementsTitleFilter").keyup(editUpdate).blur(editUpdate);

  function editUpdate() {
    if (editTimer != null) {
      clearTimeout(editTimer);
      editTimer = null;
    }
    editTimer = setTimeout(editEnd, 1000);
  }

  function editEnd() {
    editTimer = null;
    curTitleFilter = $("#measurementsTitleFilter").val().trim();

    var rangeRegEx = /\s*(\d+)-(\d+)\s*/;

    var curVal = $("#measurementsIdFilter").val();
    var result = curVal.match(rangeRegEx);
    if (result == null) {
      curFrom = -1;
      curTo = -1;
    }
    else {
      curFrom = parseInt(result[1]);
      curTo = parseInt(result[2]);
      if (curFrom>curTo) {
        var help = curFrom;
        curFrom = curTo;
        curTo = help;
      }
    }

    updateData();
  }

  // ---------------------------------------------------------------------------

  function updateData() { // Clear / re-fill table
    var curArea = parseInt($("#measurementsArea").val());
    curUnit = parseInt($("#measurementsUnit").val());

    if ( (curArea>=0) && (curUnit>=0) ) {
      $.ajax({
        url: measurementsJsonUrl,
        dataType: 'json',
        data: {
          area: curArea,
          unit: curUnit,
          page: curPage,
          from: curFrom,
          to: curTo,
          title: curTitleFilter
        },
        success: function(data) { ajaxSuccess(data) }
      });
    }
    else {
      clearTable();
    }
  }

  // -------------

  function clearTable() {
    $("#measurementsTable td").empty().append("&nbsp;").removeClass("hlCell");
    $("#curPage").empty();
    $("#numPages").empty();
  }

  // -------------

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
        var itemUrl = false;
        if (i<numData) {
          itemUrl = measurementsBaseUrl + "/items/show/" + data.data[i].itemId;
        }
        $("#measurementsTable #"+rowId+" .measurementsCell0").empty().append(
          ( itemUrl
            ? "<a href='" + itemUrl + "' target='_blank'>" + data.data[i].itemTitle + "</a>"
            : "&nbsp;"
          )
        );
        if (i<numData) {
          var suffixes = [ "", "c" ];
          suffixes.forEach(function(suffix) {
            var keys = [ ["l1", "l2", "l3"], ["f1", "f2", "f3"], ["v"]];
            for(dim=0; dim<=2; dim++) {
              var unit = (suffix == "" ? data.data[i].unit : unitsSimple[curUnit] );
              var unitSuffix = ( dim == 0 ? "" : (dim == 1 ? "²" : "³") );
              keys[dim].forEach(function(key) {
                $("#measurementsTable #"+rowId+" .meas"+key+suffix)
                .empty()
                .append(
                  myNumberFormat(data.data[i][key+suffix])
                  + "<br>"
                  + "<span>" + unit + "</span>" + unitSuffix
                );
                if ( (suffix == "c") && (key == data.data[i]["hl"]) ) {
                  $("#measurementsTable #"+rowId+" .meas"+key+suffix).addClass("hlCell");
                }
              });
            }
          });
        }
      }
    }
  }

  // ---------------------------------------------------------------------------

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

  // ---------------------------------------------------------------------------

  function myNumberFormat(x) {
    var result = number_format(x, 3, ",", ".");
    var len = result.length;
    while (result.substring(len-1) == "0") {
      result = result.substring(0, len-1);
      len -= 1;
    }
    if (result.substring(len-1) == ",") { result = result.substring(0, len-1); }
    return result;
  }

  /* http://phpjs.org/functions/number_format/ */
  function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '')
      .replace(/[^0-9+\-Ee.]/g, '')
    var n = !isFinite(+number) ? 0 : +number,
      prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
      sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
      dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
      s = '',
      toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec)
        return '' + (Math.round(n * k) / k)
          .toFixed(prec)
      }
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
      .split('.')
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
    }
    if ((s[1] || '')
      .length < prec) {
      s[1] = s[1] || ''
      s[1] += new Array(prec - s[1].length + 1)
        .join('0')
    }
    return s.join(dec)
  }

});
