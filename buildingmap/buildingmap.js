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
    // console.log("isDragging: " + isDragging);
    event.preventDefault();
    var verb = $(this).data("id"); // + ": " + $(this).data("name");
    if (!isDragging) {
      // console.log("buildingBlockLink " + verb);
      // alert(verb);
      $(this).find("polygon").addClass("hlBlock");
      window.open("findid.php?id="+$(this).data("id"))
    }
  });

  $(window).resize(function(){
    zoomBuilding.resize();
    zoomBuilding.fit();
    zoomBuilding.center();
  })

  setTimeout(function() {
    $(".hlBlock").each(function( index ) {
      // console.log( index + ": " + $( this ).text() + " / " + $( this, "polygon" ).attr("id") );
      // http://stackoverflow.com/a/39788577/5394093
      var poly = document.getElementById($( this, "polygon" ).attr("id"));
      var bbox = poly.getBBox();
      var center = {
        x: bbox.x + bbox.width/2,
        y: bbox.y + bbox.height/2
      };
      // console.log(bbox,center);
      zoomBuilding.pan({x:0,y:0});
      var realZoom= zoomBuilding.getSizes().realZoom;
      zoomBuilding.pan({
        x: -(center.x*realZoom)+(zoomBuilding.getSizes().width/2),
        y: -(center.y*realZoom)+(zoomBuilding.getSizes().height/2)
      });
      zoomBuilding.zoom(5);
    });
  }, 1000);

  // setTimeout(function() {
  //   zoomBuilding.zoom(1);
  //   zoomBuilding.pan({x:-100, y:-100});
  // }, 1000);
  //
  // setTimeout(function() {
  //   zoomBuilding.zoom(2);
  //   zoomBuilding.pan({x:-100, y:-100});
  // }, 2000);
  //
  // setTimeout(function() {
  //   zoomBuilding.zoom(3);
  //   zoomBuilding.pan({x:-100, y:-100});
  // }, 3000);

  // --------------

  // $('a.buildingBlockLink[title]').qtip({
  //   position: {
  //     my: 'top left',
  //     at: 'bottomleft',
  //     target: 'mouse',
  //     adjust: { mouse: false },
  //   },
  //   show: 'click',
  //   hide: 'click'
  // });

  // $('a.buildingBlockLink[title]').qtip({
  //   show: 'click',
  //   hide: 'click'
  // });

  // $('.buildingBlock').qtip({
  //   content: {
  //       text: 'Support for SVG with no extra configuration! Awesome.'
  //   },
  //   position: {
  //       my: 'top left',
  //       at: 'bottom right'
  //   }
  // });

});
