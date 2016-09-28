$(document).ready(function() {
  // console.log("document ready");

  $(".buildingBlockLink").click(function(e){
    event.preventDefault();
    var verb = $(this).data("id") + ": " + $(this).data("name");
    console.log("buildingBlockLink "+verb);
    // alert(verb);
    // window.open("findid.php?id="+$(this).data("id"))
  });

  $('a.buildingBlockLink[title]').qtip({
    position: {
      my: 'top left',
      at: 'bottomleft',
      target: 'mouse',
      adjust: { mouse: false },
    },
    show: 'click',
    hide: 'click'
  });

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
