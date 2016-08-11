jQuery(document).ready(function () {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  $(".videoEmbedLink").click(function(e){
    e.preventDefault();

    var videoId = $(this).data("video");
    var playFrom = $(this).data("from");
    var playTo = $(this).data("to");
    // console.log(videoId,playFrom,playTo);

    var videoObj = document.getElementById(videoId);
    videoObj.addEventListener('timeupdate', timeUpdateCallback);
    videoObj.currentTime = playFrom;
    videoObj.play();

    function timeUpdateCallback() {
      if(this.currentTime > playTo){
        this.pause();
        this.removeEventListener('timeupdate', timeUpdateCallback);
      }
    }

  });

});
