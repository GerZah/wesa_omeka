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
    ["l1",  "measurementLength1", measurementsI18n["lengthVerb"] + " 1",  1],
    ["l2",  "measurementLength2", measurementsI18n["lengthVerb"] + " 2",  1],
    ["l3",  "measurementLength3", measurementsI18n["lengthVerb"] + " 3",  1],
    ["f1",  "measurementFace1",   measurementsI18n["faceVerb"]   + " 1",  2],
    ["f2",  "measurementFace2",   measurementsI18n["faceVerb"]   + " 2",  2],
    ["f3",  "measurementFace3",   measurementsI18n["faceVerb"]   + " 3",  2],
    ["v",   "measurementVolume",  measurementsI18n["volumeVerb"],         3],
  ];

  var allEditFields = editFields.concat([
    ["l1d", "measurementLength1Derived", measurementsI18n["lengthVerb"] + " 1",  1],
    ["l2d", "measurementLength2Derived", measurementsI18n["lengthVerb"] + " 2",  1],
    ["l3d", "measurementLength3Derived", measurementsI18n["lengthVerb"] + " 3",  1],
    ["f1d", "measurementFace1Derived",   measurementsI18n["faceVerb"]   + " 1",  2],
    ["f2d", "measurementFace2Derived",   measurementsI18n["faceVerb"]   + " 2",  2],
    ["f3d", "measurementFace3Derived",   measurementsI18n["faceVerb"]   + " 3",  2],
    ["vd",  "measurementVolumeDerived",  measurementsI18n["volumeVerb"],         3],
  ]);

  var indices = [ "", "", "²", "³" ];

  // ---------------------------------------------------------------------------

  $(document).on('lity:ready', function(e, lb) {
    var currentLb = $(lb).find(".lity-content").find(">:first-child").attr("id");
    switch (currentLb) {
      case "measurementsPopup": $("#measurementUnits").focus(); break;
      case "measurementsPopup2": $("#measurementValue1").focus(); break;
    }
    //
  });

  // ---------------------------------------------------------------------------

  $(".measurementsField").unbind("click").click(function(e) {
    $(this).next().next().click();
  });

  $(".measurementsBtn").unbind("click").click(function(e) {
    e.preventDefault();

    currentVisible = $(this).prev().prev().attr("id"); // for visible text
    currentInvisible = $(this).prev().attr("id"); // for invisible text

    clearAllEdits();

    var json = $("#"+currentInvisible).val().trim();
    if (json == "") { json=null; }
    var sourceData = null;
    try { sourceData = JSON.parse(json); } catch (err) { }

    var unitId = -1;
    if (sourceData !== null) {
      // Populate with (possibly empty) editable values
      for(var i=0; i<editFields.length; i++) {
        var x = sourceData[editFields[i][0]];
        $("#"+editFields[i][1]).val(x[0]).data("values", x);
      }

      var unitVerb = sourceData["u"];
      for(var i=0; (i<measurementsUnits.length); i++) {
        if (measurementsUnits[i]["verb"] == unitVerb) {
          unitId=i;
          break;
        }
      }
      $("#measurementUnits").val(unitId);
      $("#measurementNumber").val(sourceData["n"]);
    }

    $("#measurementUnits").val(unitId).change(); // incl. update derived

    lightbox("#measurementsPopup");
  } );

  $(".measurementsClearBtn").unbind("click").click(function(e) {
    e.preventDefault();
    currentVisible = $(this).prev().prev().prev().attr("id"); // for visible text
    currentInvisible = $(this).prev().prev().attr("id"); // for invisible text
    clearValues();
  } );

  function clearValues() {
    clearAllEdits();
    $("#"+currentVisible).val("");
    $("#"+currentInvisible).val("");
  }

  function clearAllEdits() {
    for(var i=0; i<allEditFields.length; i++) {
      $("#"+allEditFields[i][1]).val("").removeData("values");
    }
    $("#measurementUnits").val(-1);
    $("#measurementNumber").val("");
  }

  // ---------------------------------------------------------------------------

  $("#measurementsPopup a.button").unbind("click").click(function(e) {
    e.preventDefault();
  });

  // $("#measurementsCancel").click(function(e) { } ); // via  data-lity-close

  $("#measurementsClear").click(function(e) { // unbind("click").
    clearValues();
    lightbox.close();
  } );

  $("#measurementsApply").click(function(e) { // unbind("click").
    var targetData = new Object();
    if ( (curTripleUnit==null) || (curTripleUnit<0) ) {
      $("#measurementUnits").focus();
      alert(measurementsI18n["selectTriple"]);
      return;
    }

    targetData["n"] = $("#measurementNumber").val();
    if ( (targetData["n"]!="") && (targetData["n"].match(/^\d*$/)==null) ) {
      $("#measurementNumber").focus();
      alert(measurementsI18n["enterNumber"]);
      return;
    }

    var verb = "";
    if ( (curTripleUnit !== null) && (curTripleUnit>=0) ) {
      verb = measurementsUnits[curTripleUnit]["verb"];
    }
    targetData["u"] = verb;

    var nonZero = (curTripleUnit>=0);
    for (var i = 0; i<allEditFields.length; i++) {
      var currentField = allEditFields[i];
      var currentEditId = currentField[1];
      var values = $("#"+currentEditId).data("values");
      if (typeof values === "undefined") { values = [ null, "", "", "" ]; }
      values[0] = $("#"+currentEditId).val();
      if (values[0]!="") { values[0] = parseFloat(values[0]); }
      targetData[currentField[0]] = values;
      if (!nonZero) {
        for(var j=0; j<4; j++) {
          var v = ( values[j]=="" ? 0 : parseFloat(values[j]) );
          nonZero = (nonZero || (v!=0));
          if (nonZero) { break; }
        }
      }
    }

    // console.log(targetData);

    var invisible = (nonZero ? JSON.stringify(targetData) : "");
    $("#"+currentInvisible).val(invisible);

    var visible = (invisible == "" ? "" : verbatimTargetData(targetData) );
    $("#"+currentVisible).val(visible);

    lightbox.close();
  } );

  function verbatimTargetData(targetData) {
    var result = "";
    var cache = [];

    for(var i=0; i<allEditFields.length; i++) {
      var currentField = allEditFields[i];
      var key = currentField[0];
      switch (key) {
        case "l1":
            result += measurementsI18n["enteredData"] + ":\n\n";
          break;
        case "l1d":
            result += "\n"+measurementsI18n["derivedData"] + ":\n\n";
          break;
      }
      var values = targetData[key];
      var allZero = true;
      for(var j=0; j<4; j++) {
        values[j] = parseFloat(values[j]);
        if (isNaN(values[j])) { values[j]=0; }
        allZero &= !values[j];
      }

      var cacheHit = false;
      if (key.slice(-1) != "d") {
        cache[key] = JSON.stringify(values);
      }
      else if (typeof cache[key.slice(0, -1)] !== "undefined") {
        var cached = cache[key.slice(0, -1)];
        cacheHit = (JSON.stringify(values) == cached);
      }

      if ((!allZero) && (!cacheHit)) {
        result += currentField[2] + " = " + myNumberFormat(values[0]) + " " + curSingleUnit3;
        result += indices[currentField[3]];
        result += " (";
        var valueText = new Array();
        for(j=1; j<=3; j++) {
          valueText.push(
            myNumberFormat(values[j]) + " " +
            measurementsUnits[curTripleUnit]["units"][j-1] +
            indices[currentField[3]]
          );
        }
        result += valueText.join(" / ");
        result += ")\n";
      }
    }

    if (targetData["n"]!="") {
      result += "\n" + measurementsI18n["numberVerb"] + ": " +targetData["n"] + "\n";
    }

    return result;
  }

  // ---------------------------------------------------------------------------

  $("#measurementUnits").unbind("change").change(function(e){
    curTripleUnit = $(this).val();

    curSingleUnit1 = curSingleUnit2 = curSingleUnit3 = "";
    curConv1 = curConv2 = curConv3 = 0;

    if ( (curTripleUnit !== null) && (curTripleUnit>=0) ) {
      curSingleUnit1 = measurementsUnits[curTripleUnit]["units"][0];
      curSingleUnit2 = measurementsUnits[curTripleUnit]["units"][1];
      curSingleUnit3 = measurementsUnits[curTripleUnit]["units"][2];
      curConv1 = measurementsUnits[curTripleUnit]["convs"][0];
      curConv2 = measurementsUnits[curTripleUnit]["convs"][1];
      curConv3 = measurementsUnits[curTripleUnit]["convs"][2];
    }
    updateUnitSpans();
    for(var i=0; i<editFields.length; i++) { recalcTripleToSingle(editFields[i][1]); }
    recalcDerivedValues();
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

  $(".measurementsTextField").unbind("click").click(function(e) {
    if ( (curTripleUnit === null) || (curTripleUnit<0) ) {
      alert(measurementsI18n["selectTriple"]);
      $("#measurementUnits").focus();
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
      $("#measurementValue1").focus();
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
    recalcDerivedValues();
    lightbox2.close();
  } );

  $("#measurementsValuesApply").click(function(e) {
    var parseError = false;
    var values = new Array();
    for(var i=1; (i<=3); i++) {
      values[i] = $("#measurementValue"+i).val().replace(",", ".");
      if (values[i].match(/^\d*(?:.\d+)?$/)==null) {
        alert(measurementsI18n["enterNumerical"]);
        $("#measurementValue"+i).focus();
        parseError = true;
        break;
      }
    }
    if (!parseError) {
      for(var i=1; (i<=3); i++) {
        values[i] = (values[i]=="" ? 0 : values[i]);
        values[i] = parseFloat(values[i]);
      }
      $("#"+currentEditId).data("values", values);
      recalcTripleToSingle(currentEditId);
      recalcDerivedValues();
      lightbox2.close();
    }
  } );

  function recalcTripleToSingle(editId) {
    var values = $("#"+editId).data("values");
    var exp = parseFloat($("#"+editId).data("exp"));
    if (typeof values !== "undefined") {
      var newval = (values[1] * Math.pow(curConv2,exp) + values[2]) * Math.pow(curConv3,exp) + values[3];
      $("#"+editId).val(newval);
    }
  }

  // ---------------------------------------------------------------------------

  function recalcDerivedValues() {
    // console.log("recalcDerivedValues");

    var origData = [];

    for(var i=0; i<editFields.length; i++) {
      var curVal = parseFloat($("#"+editFields[i][1]).val());
      if (isNaN(curVal)) { curVal=0; }
      origData[editFields[i][0]] = curVal;
      $("#"+editFields[i][1]+"Derived").val( curVal )
        .removeClass("measurementsCalculated measurementsDeriveError");
      $("#"+editFields[i][1]+"Derived").data("values", $("#"+editFields[i][1]).data("values"));
    }

    var derivedData = [];

    if (origData["l1"] && origData["l2"] && origData["l3"]) {
      derivedData["v"] = origData["l1"] * origData["l2"] * origData["l3"];
      derivedData["f1"] = origData["l1"] * origData["l2"];
      derivedData["f2"] = origData["l1"] * origData["l3"];
      derivedData["f3"] = origData["l2"] * origData["l3"];
    }

    if (origData["l1"] && origData["l2"] && !origData["l3"]) {
      derivedData["f1"] = origData["l1"] * origData["l2"];
    }

    if (origData["l1"] && !origData["l2"] && origData["l3"]) {
      derivedData["f1"] = origData["l1"] * origData["l3"];
    }

    if (origData["!l1"] && origData["l2"] && origData["l3"]) {
      derivedData["f1"] = origData["l2"] * origData["l3"];
    }

    if (origData["f1"] && origData["f2"] && origData["f3"]) {
      derivedData["l1"] = Math.sqrt( origData["f1"] * origData["f2"] / origData["f3"] );
      derivedData["l2"] = Math.sqrt( origData["f1"] * origData["f3"] / origData["f2"] );
      derivedData["l3"] = Math.sqrt( origData["f2"] * origData["f3"] / origData["f1"] );
      derivedData["v"] = Math.sqrt( origData["f1"] * origData["f2"] * origData["f3"] );
      // derivedData["v"] = derivedData["l1"] * derivedData["l2"] * derivedData["l3"]; // alternatively
    }

    // console.log(origData);
    // console.log(derivedData);

    for(var i=0; i<editFields.length; i++) {
      var key = editFields[i][0];
      if (typeof derivedData[key] !== "undefined") {
        derivedData[key] = Math.round(derivedData[key] * 1000) / 1000;
        var field = "#"+editFields[i][1]+"Derived";
        $(field).val(derivedData[key]).addClass("measurementsCalculated");
        if (origData[key] && origData[key]!=derivedData[key]) { $(field).addClass("measurementsDeriveError"); }
        $(field).data("values", reUnitValue(i));
      }
    }
  }

  function reUnitValue(field) {
    var exp = editFields[field][3] ;
    var conv2 = Math.pow(curConv2, exp);
    var conv3 = Math.pow(curConv3, exp);

    var oldValue = parseFloat($("#"+editFields[field][1]).val());
    var newValue = parseFloat($("#"+editFields[field][1]+"Derived").val());
    var value = ( isNaN(newValue) ? oldValue : newValue );
    value = parseFloat( isNaN(value) ? 0 : value );

    var result = [];
    result[0] = value;

    result[3] = value;
    result[2] = Math.floor(result[3] / conv3);
    result[3] = result[3] % conv3;
    result[1] = Math.floor(result[2] / conv2);
    result[2] = result[2] % conv2;

    // console.log("exp: "+exp+" - "+conv2+"/"+conv3+" = "+result);
    return result;
  }

  // ---------------------------------------------------------------------------

  function myNumberFormat(x) {
    return number_format(x, 3, ",", ".").replace(/0+$/,'').replace(/,+$/,'');
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

} );
