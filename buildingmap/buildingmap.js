$(document).ready(function() {
  // console.log("document ready");

  var zoomBuilding = svgPanZoom('#mySvg', {
    zoomEnabled: true,
    controlIconsEnabled: true,
    dblClickZoomEnabled: false,
    fit: true,
    center: true,
  });

  var isDragging = false; // http://stackoverflow.com/a/4139860

  $(".buildingBlockLink")
  .mousedown(function(){ isDragging=false; })
  .mousemove(function(){ isDragging=true; })
  .click(function(e){
    event.preventDefault();
    var verb = $(this).find("polygon").attr("id");
    if (!isDragging) {
      $(this).find("polygon").addClass("hlBlock");
      window.open("findid.php?id="+verb);
    }
  });

  $(window).resize(function(){
    zoomBuilding.resize();
    zoomBuilding.fit();
    zoomBuilding.center();
  })

  setTimeout(function() {
    // inspired by http://stackoverflow.com/a/39788577/5394093

    var minX = false;
    var maxX = false;
    var minY = false;
    var maxY = false;

    $(".hlBlock").each(function( index ) {
      var poly = document.getElementById( $(this).attr("id") );
      var bbox = poly.getBBox();
      minX = ( minX == false ? bbox.x : Math.min(minX, bbox.x) );
      minY = ( minY == false ? bbox.y : Math.min(minY, bbox.y) );
      maxX = ( maxX == false ? bbox.x+bbox.width : Math.max(maxX, bbox.x+bbox.width) );
      maxY = ( maxY == false ? bbox.y+bbox.height : Math.max(maxY, bbox.y+bbox.height) );
    });

    if (minX != false) { // all others, too
      var xDiff = maxX-minX;
      var yDiff = maxY-minY;
      var centerX = minX + xDiff/2;
      var centerY = minY + yDiff/2;

      zoomBuilding.pan({x:0,y:0});
      var sizes = zoomBuilding.getSizes();
      zoomBuilding.pan({
        x: -(centerX*sizes.realZoom)+(sizes.width/2),
        y: -(centerY*sizes.realZoom)+(sizes.height/2)
      });

      var viewBox = sizes.viewBox
        , newScale = Math.min(viewBox.width/xDiff, viewBox.height/yDiff);
      zoomBuilding.zoom(newScale);
    }
  }, 1000);

});
