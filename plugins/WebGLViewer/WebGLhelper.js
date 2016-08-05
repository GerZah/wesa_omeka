jQuery('.webGlFrame').on("load", function(event) {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $('.webGlFrame').contents().find( "div#edit" ).css( "width", "0px" );
  $('.webGlFrame').contents().find( "div#view" ).css( "margin-right", "0px" );

  var that = document.getElementById(event.currentTarget.id).contentWindow;
  that.resize(); // http://stackoverflow.com/a/22148030

  $(document).on('keypress', panner);
  $(that).on('keypress', panner);

  function panner(e) {
    // console.log('Handler for .keypress() called. - ' + e.charCode);
    switch (e.charCode) {
      case 97: aKey(0.1); break; // a
      case 115: sKey(0.1); break; // s
      case 100: dKey(0.1); break; // d
      case 119: wKey(0.1); break; // w
      case 65: aKey(0.5); break; // A
      case 83: sKey(0.5);; break; // S
      case 68: dKey(0.5); break; // D
      case 87: wKey(0.5); break; // W
    }
  }

  $(".wasdLink").click(function(e){ e.preventDefault(); });
  $("#wasdA").click(function(){ aKey(0.5); });
  $("#wasdS").click(function(){ sKey(0.5); });
  $("#wasdD").click(function(){ dKey(0.5); });
  $("#wasdW").click(function(){ wKey(0.5); });

  function aKey(step) { that.cameraTargetX -= step; }
  function sKey(step) { that.cameraTargetY += step; }
  function dKey(step) { that.cameraTargetX += step; }
  function wKey(step) { that.cameraTargetY -= step; }

} );
