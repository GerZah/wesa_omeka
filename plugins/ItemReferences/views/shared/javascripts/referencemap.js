jQuery( document ).ready(function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  if (typeof mapsData != 'undefined') {

    var numMaps = mapsData.length;
    for (var i = 0; i < numMaps; i++) {

      mapsData[i].map = new google.maps.Map(document.getElementById(mapsData[i].mapId), {
        center: {lat: 0, lng: 0},
        zoom: 0
      });

      var numCoords = mapsData[i].coords.length;
      var latLngBounds = new google.maps.LatLngBounds();

      for (var j = 0; j < numCoords; j++) {
        var curTitle = mapsData[i].coords[j].title;
        var curLat = mapsData[i].coords[j].lat;
        var curLng = mapsData[i].coords[j].lng;
        latLngBounds.extend(new google.maps.LatLng(curLat, curLng));

        mapsData[i].coords[j].marker = new google.maps.Marker({
          title: curTitle,
          position: {lat: curLat, lng: curLng},
          map: mapsData[i].map
        });
      }
      mapsData[i].map.fitBounds(latLngBounds);
    }
  }

} );
