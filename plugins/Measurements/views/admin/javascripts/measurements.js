jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var lightbox = lity(); // https://www.npmjs.com/package/lity
  var lightbox2 = lity();

  // ---------------------------------------------------------------------------

  var currentVivisble;
  var currentInvisible;

  var curTripleUnit;
  var curSingleUnit1;
  var curSingleUnit2;
  var curSingleUnit3;
  var curConv1;
  var curConv2;
  var curConv3;

  // ---------------------------------------------------------------------------

  $(".measurementsBtn").unbind("click").click(function(e) {
    e.preventDefault();

    currentVivisble = $(this).prev().prev().attr("id"); // for visible text
    currentInvisible = $(this).prev().attr("id"); // for invisible text

    // +#+#+# Clear values -- or better: populate with (possibly empty) editable values
    $("#measurementUnits").val(-1).change();
    for(var i=1; i<=3; i++) {
      $("#measurementLength"+i).val("").removeData("values");
    }
    $("#measurementFace").val("").removeData("values");
    $("#measurementVolume").val("").removeData("values");

    lightbox("#measurementsPopup");
  } );

  $(".measurementsClearBtn").unbind("click").click(function(e) {
    e.preventDefault();

    currentVivisble = $(this).prev().prev().prev().attr("id"); // for visible text
    currentInvisible = $(this).prev().prev().attr("id"); // for invisible text

    clearValues();
  } );

  function clearValues() {
    $("#"+currentVivisble).val("");
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
    $("#"+currentVivisble).val("foo");
    $("#"+currentInvisible).val("bar");
    lightbox.close();
  } );

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

    // +#+#+# perform necessary automatic calculations to normalization / target values
    for(var i=1; i<=3; i++) { recalcTripleToSingle("measurementLength"+i); }
    recalcTripleToSingle("measurementFace");
    recalcTripleToSingle("measurementVolume");
  } );

  function updateUnitSpans() {
    $(".measurementsLenghtUnit1").empty().append(curSingleUnit1);
    $(".measurementsLenghtUnit2").empty().append(curSingleUnit2);
    $(".measurementsLenghtUnit3").empty().append(curSingleUnit3);
    if (curSingleUnit1) {
      $(".measurementsFaceUnit").append("²");
      $(".measurementsVolumeUnit").append("³");
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
