jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var lightbox = lity(); // https://www.npmjs.com/package/lity
  var lightbox2 = lity();

  var currentVivisble;
  var currentInvisible;

  $(".measurementsBtn").unbind("click").click(function(e) {
    e.preventDefault();

    currentVivisble = $(this).prev().prev().attr("id"); // for visible text
    currentInvisible = $(this).prev().attr("id"); // for invisible text

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

  $(".measurementsTextField").click(function(e) {
    var currentEditId = this.id;
    console.log(currentEditId);
    lightbox2("#measurementsPopup2");
  } );

  // ---------------------------------------------------------------------------

} );
