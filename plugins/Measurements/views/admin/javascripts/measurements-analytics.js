jQuery(document).ready(function () {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var lightbox = lity(); // https://www.npmjs.com/package/lity

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

  var curWeightFactor = 2.0;
  var curFromRange = -1;
  var curToRange = -1;
  var curTitleFilter = "";
  var curIds = "";
  var curBreakdownNumbers = false;

  var editTimer = null;

  $("#measurementsWeightFactor").keyup(editUpdate).blur(editEnd);
  $("#measurementsIdFilter").keyup(editUpdate).blur(editEnd);
  $("#measurementsRangeFilter").keyup(editUpdate).blur(editEnd);
  $("#measurementsTitleFilter").keyup(editUpdate).blur(editEnd);
  $("#measurementsBreakdownNumbers").change(editUpdate).blur(editEnd);

  function editUpdate() {
    if (editTimer != null) { clearTimeout(editTimer); }
    editTimer = setTimeout(editEnd, 750);
  }

  function editEnd() {
    if (editTimer != null) { clearTimeout(editTimer); }
    editTimer = null;
    curTitleFilter = $("#measurementsTitleFilter").val().trim();
    curBreakdownNumbers = $("#measurementsBreakdownNumbers").prop("checked");

    var weightRegEx = /\s*(\d+)(?:(?:,|\.)(\d+))?\s*/;
    var curVal = $("#measurementsWeightFactor").val();
    var matches = curVal.match(weightRegEx);
    if (matches == null) {
      curWeightFactor = 2.0;
    }
    else {
      var prefix = matches[1];
      var suffix = matches[2];
      curWeightFactor = parseFloat(prefix+"."+suffix);
    }

    curIds = $("#measurementsIdFilter").val();

    var rangeRegEx = /\s*(\d+)(?:[\.|,](\d+))?-(\d+)(?:[\.|,](\d+))?\s*/;

    var curVal = $("#measurementsRangeFilter").val();
    if (curVal.indexOf("-")==-1) { curVal = curVal + "-" + curVal; } // q&d
    var matches = curVal.match(rangeRegEx);
    if (matches == null) {
      curFromRange = -1;
      curToRange = -1;
    }
    else {
      curFromRange = "" + matches[1];
      if (typeof matches[2] !== 'undefined') { curFromRange += "." + matches[2]; }
      curFromRage = parseFloat(curFromRange);
      curToRange = "" + matches[3];
      if (typeof matches[4] !== 'undefined') { curToRange += "." + matches[4]; }
      curToRage = parseFloat(curToRange);
      if (curFromRage > curToRage) {
        var help = curFromRage;
        curFromRage = curToRage;
        curToRage = help;
      }
    }

    updateData();
  }

  // ---------------------------------------------------------------------------

  function updateData() { // Clear / re-fill table
    var curArea = parseInt($("#measurementsArea").val());
    curUnit = parseInt($("#measurementsUnit").val());

    var ajaxData = {
      area: curArea,
      unit: curUnit,
      page: curPage,
      fromRange: curFromRange,
      toRange: curToRange,
      title: curTitleFilter,
      ids: curIds
    };
    // console.log(ajaxData);

    if ( (curArea>=0) && (curUnit>=0) ) {
      $.ajax({
        url: measurementsJsonUrl + "lookup/",
        dataType: 'json',
        data: ajaxData,
        success: function(data) { ajaxSuccess(data) }
      });
    }
    else {
      clearTable();
      updateAreaColumns();
    }
  }

  // -------------

  function updateAreaColumns() {
    var curArea = parseInt($("#measurementsArea").val());
    $(
      ".measl1, .measl2, .measl3, .measf1, .measf2, .measf3, .measv, "+
      ".measl1c, .measl2c, .measl3c, .measf1c, .measf2c, .measf3c, .measvc, .measw"
    ).hide();
    switch (curArea) {
      case 0: {
        $(".measl1, .measl2, .measl3, .measl1c, .measl2c, .measl3c").show();
        $("th.measOrig, th.measCalc").attr("colSpan", "3");
      } break;
      case 1: {
        $(".measf1, .measf2, .measf3, .measf1c, .measf2c, .measf3c").show();
        $("th.measOrig, th.measCalc").attr("colSpan", "3");
      } break;
      case 2: {
        $(".measv, .measvc, .measw").show();
        $("th.measOrig").attr("colSpan", "1");
        $("th.measCalc").attr("colSpan", "2");
      } break;
      default: {
        $(
          ".measl1, .measl2, .measl3, .measf1, .measf2, .measf3, .measv, "+
          ".measl1c, .measl2c, .measl3c, .measf1c, .measf2c, .measf3c, .measvc, .measw"
        ).show();
        $("th.measOrig").attr("colSpan", "7");
        $("th.measCalc").attr("colSpan", "8");
      } break;
    }
  }

  // -------------

  function clearTable() {
    $("#measurementsTable td").html("&nbsp;").removeClass("hlCell");
    $("#measurementsTable tr")
    .data("row", false)
    .data("id", false)
    .removeClass("hlRow")
    .removeClass("lastHl");
    $("#curPage").empty();
    $("#numPages").empty();
    $("#addRelBtn").prop("disabled", true);
    $("#measurementPaginate a").hide();
  }

  // -------------

  function ajaxSuccess(data) { // AJAX callback
    // console.log(data);
    clearTable();
    if (data.data==null) {
      // console.log("null data");
    }
    else {
      updateAreaColumns();
      // console.log("non-null data");
      curPage = parseInt(data.page);
      numPages = parseInt(data.numPages);

      $("#curPage").append(curPage+1);
      $("#numPages").append(numPages);
      if (numPages > 1)     { $(".paginateFirstLast, .paginateOne").show(); }
      if (numPages > 10)   { $(".paginateTen").show(); }
      if (numPages > 100)  { $(".paginateHun").show(); }
      if (numPages > 1000) { $(".paginateTho").show(); }

      var numData = data.data.length;
      var numRows = $("#measurementsTable tbody tr").length;
      // console.log(numData + " vs. " + numRows);
      for(var i=0; i<numRows; i++) {
        var rowId = "measurementsRow"+i;
        var itemUrl = false;
        if (i<numData) {
          itemUrl = measurementsBaseUrl + "/items/show/" + data.data[i].itemId;
          $("#"+rowId).data("row", i);
          $("#"+rowId).data("id", data.data[i].itemId);
        }
        $("#measurementsTable #"+rowId+" .measurementsCell0").html(
          ( itemUrl
            // ? "<a href='" + itemUrl + "' target='_blank'>" + data.data[i].itemTitle + "</a>"
            ? "<a href='#' class='measRowDetails' "
              + "data-row='" + i + "'"
              + "data-url='" + itemUrl + "'"
              + ">"
              + data.data[i].itemTitle
              + "</a>"
            : "&nbsp;"
          )
        );
        if (i<numData) {
          var suffixes = [ "", "c" ];
          suffixes.forEach(function(suffix) {
            var keys = [ ["l1", "l2", "l3"], ["f1", "f2", "f3"], ["v"]];
            for(var dim=0; dim<=2; dim++) {
              var unit = (suffix == "" ? data.data[i].unit : unitsSimple[curUnit] );
              var unitSuffix = ( dim == 0 ? "" : (dim == 1 ? "²" : "³") );
              keys[dim].forEach(function(key) {
                $("#measurementsTable #"+rowId+" .meas"+key+suffix)
                .html(
                  myNumberFormat(
                    data.data[i][key+suffix],
                    data.unitsInv[unit].convs,
                    dim
                  )
                  + "<br>"
                  + "<span>" + unit + "</span>" + unitSuffix
                );
                if ( (suffix == "c") && (key == data.data[i]["hl"]) ) {
                  $("#measurementsTable #"+rowId+" .meas"+key+suffix).addClass("hlCell");
                }
              });
            }
          });
          var number = data.data[i]["n"];
          number = ( number ? number : "(1)" );
          $("#measurementsTable #"+rowId+" .measn").html(number);
          if (unitsSimple[curUnit] != "m-cm-mm") {
            $("#measurementsTable #"+rowId+" .measw").html("-");
          }
          else {
            var volume = data.data[i]["vc"];

            var baseWeight = curWeightFactor * volume / 1000000000;
            var weights = [baseWeight];

            var number = data.data[i]["n"];
            if (number>1) { weights.push(baseWeight*number); }

            var fullWeightText = "";

            weights.forEach(function(weight) {
              var unit = "t";
              if (weight<1) { weight *= 1000; unit = "kg"; }
              if (weight<1) { weight *= 1000; unit = "g"; }
              var weightText = myNumberFormat(weight) + " " + unit;
              fullWeightText += ( fullWeightText ? "<br>" : "" );
              fullWeightText += weightText;
            });

            $("#measurementsTable #"+rowId+" .measw").html(fullWeightText);
          }
        }
      }

      $(".measRowDetails").unbind().click(function(e) {
        e.preventDefault();
        $(this).closest("tr").click();
        var itemUrl = $(this).data('url');
        $(".detailsItemLink").attr("href", itemUrl);

        var rowId = $(this).data('row');
        var row = "measurementsRow"+rowId;

        var titleCell = "#"+row + " td.measurementsCell0";
        $("#detailsTitle").html( $(titleCell).text() );

        var suffixes = [ "", "c" ];
        var keys = [ "l1", "l2", "l3", "f1", "f2", "f3", "v" ];

        suffixes.forEach(function(suffix) {
          keys.forEach(function(key) {
            var id = key+suffix;
            var tableCell = "#"+row + " td.meas"+id;
            var detailCell = "#details" + id;
            var cellContent = $(tableCell).html().replace("<br>", " ");
            $(detailCell).html(cellContent).removeClass("hlCell");
            if ($(tableCell).hasClass("hlCell")) { $(detailCell).addClass("hlCell"); }
          });
        });

        var addKeys = [ "w", "n" ];
        addKeys.forEach(function(addKey) {
          var tableCell = "#"+row + " td.meas"+addKey;
          var detailCell = "#details"+addKey;
          $(detailCell).html( $(tableCell).html() );
        });

        lightbox("#measurementsAnalysisPopup");
        // lightbox.open();
      });

    }
  }

  // -------------

  $(".measurementsCancelBtn").click(function(e){ lightbox.close(); });

  // ---------------------------------------------------------------------------

  $("#measurementPaginate a").click(function(e) {
    e.preventDefault();

    var clickedId = $(this).attr('id');
    var pagStep = $(this).data('pagstep');

    // console.log("clicked " + clickedId + " / " + pagStep);

    switch (pagStep) {
      case "first": curPage = 0; updateData(); break;
      case "last": curPage = numPages; updateData(); break;
      default: var offs = parseInt(pagStep); curPage += offs; updateData();
    }
  });

  // ---------------------------------------------------------------------------

  $("#measurementsTable tr").click(function(e) {
    var row = $(this).data('row');
    var id = $(this).data('id');

    if (row!==false) {
      var rowId = "measurementsRow"+row;
      var isActive = $("#"+rowId).hasClass("hlRow");
      var isLastHl = $("#"+rowId).hasClass("lastHl");
      if (isActive) {
        $("#"+rowId).removeClass("hlRow").removeClass("lastHl");
        if (isLastHl) {
          $(".hlRow").first().addClass("lastHl");
        }
      }
      else {
        $(".lastHl").removeClass("lastHl");
        $("#"+rowId).addClass("hlRow").addClass("lastHl");
      }

      $("#addRelBtn").prop("disabled", $(".hlRow").length<=1 );

    }

  });

  // -------------

  var subjectItemId = -1;
  var objectItemIds = [];

  $("#addRelBtn").click(function(e){
    e.preventDefault();

    var subjectRowId = $(".lastHl").attr("id");
    var subjectTitleCell = "#"+subjectRowId + " td.measurementsCell0";
    $("#addRelSubjectItem").html( $(subjectTitleCell).text() );
    subjectItemId = $("#"+subjectRowId).data("id");

    objectItemIds = [];
    var objectTitles = "<ul>";
    $(".hlRow").each(function(index){
      var objectRowId = $(this).attr("id");
      if (objectRowId != subjectRowId) {
        var objectTitleCell = "#"+objectRowId + " td.measurementsCell0";
        objectTitles += "<li>" + $(objectTitleCell).text() + "</li>";
        var objectItemId = $("#"+objectRowId).data("id");
        objectItemIds.push(objectItemId);
      }
    });
    objectTitles += "</ul>";
    $("#addRelObjectItems").html(objectTitles);

    $("#measurementsRelations").val("");
    $("#relationComment").val("");
    $("#doAddRelBtn").prop("disabled", true);
    $("#addRelRegularForm").show();
    $("#addRelResult").hide();

    lightbox("#measurementsAnalysisAddRel");
  });

  // -------------

  $("#measurementsRelations").change(function(){
    var selectedRelation = $("#measurementsRelations option:selected").val();
    var cantSubmit = (selectedRelation=="");
    $("#doAddRelBtn").prop("disabled", cantSubmit);
  });

  // -------------

  $("#doAddRelBtn").click(function(e){
    var selectedRelation = $("#measurementsRelations option:selected").val();
    // console.log(selectedRelation);
    if (selectedRelation == "") {
      $("#measurementsRelations").focus();
    }
    else {
      var ajaxData = {
        subjectItemId: subjectItemId,
        objectItemIds: objectItemIds,
        selectedRelation: selectedRelation,
        relationComment: $("#relationComment").val()
      };
      // console.log(ajaxData);

      $("#doAddRelBtn").prop("disabled", true);
      $.ajax({
        url: measurementsJsonUrl + "addrel/",
        dataType: 'json',
        data: ajaxData,
        success: function(data) {
          // console.log("AJAX success:", data);
          $("#addRelRegularForm").hide();
          $("#addRelResult").show();
          $("#addRelResultSuccess, #addRelResultFail").hide();
          if (data.success) { $("#addRelResultSuccess").show(); }
          else { $("#addRelResultFail").show(); }
        }
      });

    }
  });

  // ---------------------------------------------------------------------------

  function myNumberFormat(x, convs, dim) {
    var result = x;
    if ((typeof convs === 'undefined') || (!curBreakdownNumbers)) {
      result = clipZeroSuffix( number_format(x, 3, ",", ".") );
    }
    else {
      var convsSplit = convs.split("-");
      for(var i=0; (i<=2); i++) {
        convsSplit[i] = Math.pow( parseInt(convsSplit[i]), dim+1 );
      }
      // console.log(x, convs, dim, convsSplit);
      var fullNum = Math.floor(x);
      var decimals = x-fullNum;
      decimals = clipZeroSuffix( number_format(decimals, 3, ",", ".") );
      decimals = ( decimals == "0" ? "" : "," + decimals.substring(2) );

      var third = fullNum % convsSplit[2];
      fullNum = Math.floor(fullNum / convsSplit[2]);
      var second = fullNum % convsSplit[1];
      fullNum = Math.floor(fullNum / convsSplit[1]);
      var first = fullNum;

      first = number_format(first, 0, ",", ".");
      second = number_format(second, 0, ",", ".");
      third = number_format(third, 0, ",", ".");

      // console.log(first,second,third,decimals);
      result = first + "-" + second + "-" + third + decimals;
    }
    // result = x + " | " + result;
    return result;
  }

  function clipZeroSuffix(result) {
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

  // ---------------------------------------------------------------------------

});
