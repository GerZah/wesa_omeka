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
      window.open("findid.php?id="+$(this).data("id"))
    }
  });

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
