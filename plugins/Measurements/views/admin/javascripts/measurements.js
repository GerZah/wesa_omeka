jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var lightbox = lity(); // https://www.npmjs.com/package/lity
  var lightbox2 = lity();

  // ---------------------------------------------------------------------------

  var currentVisible;
  var currentInvisible;

  var curTripleUnit;
  var curSingleUnit1;
  var curSingleUnit2;
  var curSingleUnit3;
  var curConv1;
  var curConv2;
  var curConv3;

  var editFields = [
    ["l1", "measurementLength1", measurementsI18n["lengthVerb"] + " 1", 1],
    ["l2", "measurementLength2", measurementsI18n["lengthVerb"] + " 2", 1],
    ["l3", "measurementLength3", measurementsI18n["lengthVerb"] + " 3", 1],
    ["f", "measurementFace", measurementsI18n["surfaceVerb"], 2],
    ["v", "measurementVolume", measurementsI18n["volumeVerb"], 3],
  ];

  var indices = [ "", "", "²", "³" ];

  // ---------------------------------------------------------------------------

  $(".measurementsBtn").unbind("click").click(function(e) {
    e.preventDefault();

    currentVisible = $(this).prev().prev().attr("id"); // for visible text
    currentInvisible = $(this).prev().attr("id"); // for invisible text

    // Clear values
    for(var i=0; i<editFields.length; i++) {
      $("#"+editFields[i][1]).val("").removeData("values");
    }
    $("#measurementUnits").val(-1).change();

    var json = $("#"+currentInvisible).val().trim();
    if (json == "") { json=null; }
    var sourceData = null;
    try { sourceData = JSON.parse(json); } catch (err) { }

    if (sourceData !== null) {
      // Populate with (possibly empty) editable values
      for(var i=0; i<editFields.length; i++) {
        var x = sourceData[editFields[i][0]];
        $("#"+editFields[i][1]).val(x[0]).data("values", x);
      }

      var unitId = parseInt(sourceData["u"]["ui"]);
      if (unitId<0) {
        unitVerb = "";
      }
      else {
        var unitVerb = sourceData["u"]["v"];
        for(var i=0; (i<measurementsUnits.length); i++) {
          if (measurementsUnits[i]["verb"] == unitVerb) {
            unitId=i;
            break;
          }
        }
      }
      $("#measurementUnits").val(unitId).change();
    }


    lightbox("#measurementsPopup");
  } );

  $(".measurementsClearBtn").unbind("click").click(function(e) {
    e.preventDefault();
    currentVisible = $(this).prev().prev().prev().attr("id"); // for visible text
    currentInvisible = $(this).prev().prev().attr("id"); // for invisible text
    clearValues();
  } );

  function clearValues() {
    $("#"+currentVisible).val("");
    $("#"+currentInvisible).val("");
  }

  // ---------------------------------------------------------------------------

  $("#measurementsPopup a.button").unbind("click").click(function(e) {
    e.preventDefault();
  });

  // $("#measurementsCancel").click(function(e) { } ); // via  data-lity-close

  $("#measurementsClear").click(function(e) {
    clearValues();
    lightbox.close();
  } );

  $("#measurementsApply").click(function(e) {
    var targetData = new Object();

    var units = { };
    units["ui"] = curTripleUnit;
    var verb = "";
    if (curTripleUnit!=-1) {
      verb = measurementsUnits[curTripleUnit]["verb"];
    }
    units["v"] = verb;
    targetData["u"] = units;

    var nonZero = (curTripleUnit>=0);
    for (var i = 0; i<editFields.length; i++) {
      var currentEditId = editFields[i][1];
      var values = $("#"+currentEditId).data("values");
      if (typeof values === "undefined") { values = [ null, "", "", "" ]; }
      values[0] = $("#"+currentEditId).val();
      if (values[0]!="") { values[0] = parseInt(values[0]); }
      targetData[editFields[i][0]] = values;
      if (!nonZero) {
        for(var j=0; j<4; j++) {
          var v = ( values[j]=="" ? 0 : parseInt(values[j]) );
          nonZero = (nonZero || (v!=0));
          if (nonZero) { break; }
        }
      }
    }

    var invisible = (nonZero ? JSON.stringify(targetData) : "");
    $("#"+currentInvisible).val(invisible);

    var visible = (invisible == "" ? "" : verbatimTargetData(targetData) );
    $("#"+currentVisible).val(visible);

    lightbox.close();
  } );

  function verbatimTargetData(targetData) {
    result = "";
    result = measurementsI18n["unitVerb"] + ": " +  targetData["u"]["v"] + "\n\n";

    for(var i=0; i<editFields.length; i++) {
      var values = targetData[editFields[i][0]];
      for(var j=0; j<4; j++) {
        values[j] = parseInt(values[j]);
        if (isNaN(values[j])) { values[j]=0; }
      }
      result += editFields[i][2] + " = " + values[0] + " " + curSingleUnit3;
      result += indices[editFields[i][3]];

      result += " (";
      var valueText = new Array();
      for(j=1; j<=3; j++) {
        valueText.push(
          values[j] + " " +
          measurementsUnits[curTripleUnit]["units"][j-1] +
          indices[editFields[i][3]]
        );
      }
      result += valueText.join(" / ");
      result += ")\n";
    }

    return result;
  }

  // ---------------------------------------------------------------------------

  $("#measurementUnits").change(function(e){
    curTripleUnit = $(this).val();

    curSingleUnit1 = curSingleUnit2 = curSingleUnit3 = "";
    curConv1 = curConv2 = curConv3 = 0;

    if (curTripleUnit>=0) {
      curSingleUnit1 = measurementsUnits[curTripleUnit]["units"][0];
      curSingleUnit2 = measurementsUnits[curTripleUnit]["units"][1];
      curSingleUnit3 = measurementsUnits[curTripleUnit]["units"][2];
      curConv1 = measurementsUnits[curTripleUnit]["convs"][0];
      curConv2 = measurementsUnits[curTripleUnit]["convs"][1];
      curConv3 = measurementsUnits[curTripleUnit]["convs"][2];
    }
    updateUnitSpans();

    // perform necessary automatic calculations to normalization values
    for(var i=0; i<editFields.length; i++) { recalcTripleToSingle(editFields[i][1]); }

    // +#+#+# calculate derived values
  } );

  function updateUnitSpans() {
    $(".measurementsLenghtUnit1").empty().append(curSingleUnit1);
    $(".measurementsLenghtUnit2").empty().append(curSingleUnit2);
    $(".measurementsLenghtUnit3").empty().append(curSingleUnit3);
    if (curSingleUnit1) {
      $(".measurementsFaceUnit").append(indices[2]);
      $(".measurementsVolumeUnit").append(indices[3]);
    }
  }

  // ---------------------------------------------------------------------------

  var currentEditId;

  $(".measurementsTextField").click(function(e) {
    if (curTripleUnit<0) {
      alert(measurementsI18n["selectTriple"]);
      currentEditId = null;
    }
    else {
      currentEditId = this.id;

      var currentTitle = $("#"+currentEditId).data("title");
      $("#measurementsTripleEditTitle").empty().append(currentTitle);

      var values = $("#"+currentEditId).data("values");
      if (typeof values === "undefined") { values = [ null, "", "", "" ]; }
      for(var i=1; (i<=3); i++) { $("#measurementValue"+i).val(values[i]); }

      var currentExp = $("#"+currentEditId).data("exp");
      $(".measurementValue").removeClass("measurementsFaceUnit measurementsVolumeUnit");
      switch (currentExp) {
        case 2: $(".measurementValue").addClass("measurementsFaceUnit"); break;
        case 3: $(".measurementValue").addClass("measurementsVolumeUnit"); break;
      }
      updateUnitSpans();

      lightbox2("#measurementsPopup2");
    }
  } );

  // ---------------------------------------------------------------------------

  $("#measurementsPopup2 a.button").unbind("click").click(function(e) {
    e.preventDefault();
  });

  // $("#measurementsValuesCancel").click(function(e) { } ); // via  data-lity-close

  $("#measurementsValuesClear").click(function(e) {
    $("#"+currentEditId).val("");
    $("#"+currentEditId).removeData("values");
    lightbox2.close();
  } );

  $("#measurementsValuesApply").click(function(e) {
    var parseError = false;
    var values = new Array();
    for(var i=1; (i<=3); i++) {
      values[i] = $("#measurementValue"+i).val();
      if (values[i].match(/^\d*$/)==null) {
        alert(measurementsI18n["enterNumerical"]);
        $("#measurementValue"+i).focus();
        parseError = true;
        break;
      }
    }
    if (!parseError) {
      for(var i=1; (i<=3); i++) {
        values[i] = (values[i]=="" ? 0 : values[i])
        values[i] = parseInt(values[i]);
      }
      $("#"+currentEditId).data("values", values);
      recalcTripleToSingle(currentEditId);
      lightbox2.close();
    }
  } );

  function recalcTripleToSingle(editId) {
    var values = $("#"+editId).data("values");
    var exp = parseInt($("#"+editId).data("exp"));
    if (typeof values !== "undefined") {
      var newval = (values[1] * Math.pow(curConv2,exp) + values[2]) * Math.pow(curConv3,exp) + values[3];
      $("#"+editId).val(newval);
    }
  }

  // ---------------------------------------------------------------------------

} );
