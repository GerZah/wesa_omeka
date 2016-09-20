$(document).ready(function() {
  // console.log("document ready");

  $(".buildingBlock").click(function(){
    // console.log($(this).data("id"));
    window.open("findid.php?id="+$(this).data("id"))
  });
});
