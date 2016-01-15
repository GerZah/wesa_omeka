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
      var polyLineCoordinates = [ ];
      var itemReferencesShowLines = mapsData[i].line;

      for (var j = 0; j < numCoords; j++) {
        var curTitle = mapsData[i].coords[j].title;
        var curLat = mapsData[i].coords[j].lat;
        var curLng = mapsData[i].coords[j].lng;
        var curUrl = mapsData[i].coords[j].url;
        latLngBounds.extend(new google.maps.LatLng(curLat, curLng));

        mapsData[i].coords[j].marker = new google.maps.Marker({
          // https://groups.google.com/d/msg/google-maps-api/2k3T5hwI5Ck/RRso0D2jB1oJ
          // http://www.lass.it/Web/viewer.aspx?id=4
          // yellow, green, ltblue, blue, red, purple, pink, orange
          // *.png *-dot.png *-pushpin.png
          icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
          title: curTitle,
          position: {lat: curLat, lng: curLng},
          map: mapsData[i].map,
          linkUrl: curUrl
        });

        google.maps.event.addListener(mapsData[i].coords[j].marker, 'click', function() {
          window.location.href = this.linkUrl;
        });

        if (itemReferencesShowLines) {
          polyLineCoordinates.push( { lat: curLat, lng: curLng } );
        }
      }
      mapsData[i].map.fitBounds(latLngBounds);

      if (itemReferencesShowLines) {
        var polyLine = new google.maps.Polyline({
            path: polyLineCoordinates,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
          });
        polyLine.setMap(mapsData[i].map);
      }
    }

    $(".refMapOvlSel").change( function() {
      var overlayIdx = this.value;
      var mapArr = $(this).data("map-arr");
      var map = mapsData[mapArr].map;
      mapSelOverlay(overlayIdx, map)
    } );
    $(".refMapOvlSel").change();

    function mapSelOverlay(overlayIdx, map) {
      if (typeof map.mapOverlay != 'undefined') { map.mapOverlay.setMap(null); }
      if ( (overlayIdx>=0) && (typeof mapOverlays[overlayIdx] != 'undefined') ) {
        var overlayData = mapOverlays[overlayIdx];
        var imageBounds = {
          north: parseFloat(overlayData["latNorth"]),
          south: parseFloat(overlayData["latSouth"]),
          west:  parseFloat(overlayData["lngWest"]),
          east:  parseFloat(overlayData["lngEast"])
        };
        map.mapOverlay = new google.maps.GroundOverlay( overlayData["imgUrl"], imageBounds);
        map.mapOverlay.setMap(map);
      }
    }

  }

} );
