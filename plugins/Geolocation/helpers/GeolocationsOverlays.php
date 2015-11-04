<?php

	function GeolocationConvertOverlayJsonToForm($jsonMapOverlays = false) {
		if (!$jsonMapOverlays) { $jsonMapOverlays = get_option("geolocation_map_overlays"); }

		if (!$jsonMapOverlays) { $jsonMapOverlays = "[]"; }
		$mapOverlays = json_decode($jsonMapOverlays);

		$txtOverlays = array();
		foreach($mapOverlays as $mapOverlay) {
			$txtOverlays[] = implode(";", $mapOverlay);
		}

		$geolocationMapOverlays = implode("\n", $txtOverlays);

		return $geolocationMapOverlays;
	}

	function GeolocationConvertOverlayJsonForUse($jsonMapOverlays = false) {
		if (!$jsonMapOverlays) { $jsonMapOverlays = get_option("geolocation_map_overlays"); }

		if (!$jsonMapOverlays) { $jsonMapOverlays = "[]"; }
		$mapOverlays = json_decode($jsonMapOverlays);

		$result = array();

		$regExIdx = "^\d+$"; // decimal number of at least one digit
		$regExLatLng = "^(?:\+|-)?\d+(?:.\d+)?$"; // (+|-)1234(.1234) as latitude or longitude coordinate

		foreach($mapOverlays as $mapOverlay) {

			$idx = $identifier = $imgUrl = $latNorth = $latSouth = $lngEast = $lngWest = false;

			foreach( array_keys($mapOverlay) as $key ) { $mapOverlay[$key] = trim( $mapOverlay[$key] ); }

			if ( (isset($mapOverlay[0])) and ( preg_match( "($regExIdx)", $mapOverlay[0] ) ) ) { // 1st: numerical index
				$idx = intval($mapOverlay[0]);
			} else { break; }
			if ( (isset($mapOverlay[1])) and ($mapOverlay[1]) ) { $identifier = $mapOverlay[1]; } else { break; } // 2nd element: identifier string
			if ( (isset($mapOverlay[2])) and ($mapOverlay[2]) ) { $imgUrl = $mapOverlay[2]; } else { break; } // 3rd element: image URL string
			if ( ( isset($mapOverlay[3]) ) and ( preg_match( "($regExLatLng)", trim($mapOverlay[3]) ) ) ) { // 4th element: northern latitude
				$latNorth = trim($mapOverlay[3]);
			} else { break; }
			if ( ( isset($mapOverlay[4]) ) and ( preg_match( "($regExLatLng)", trim($mapOverlay[4]) ) ) ) { // 5th element: southern latitude
				$latSouth = trim($mapOverlay[4]);
			} else { break; }
			if ( ( isset($mapOverlay[5]) ) and ( preg_match( "($regExLatLng)", trim($mapOverlay[5]) ) ) ) { // 5th element: eastern longitude
				$lngEast = trim($mapOverlay[5]);
			} else { break; }
			if ( ( isset($mapOverlay[6]) ) and ( preg_match( "($regExLatLng)", trim($mapOverlay[6]) ) ) ) { // 7th element: western longitude
				$lngWest = trim($mapOverlay[6]);
			} else { break; }

			if ( (floatval($latNorth) <= floatval($latSouth)) or (floatval($lngWest) >= floatval($lngEast)) ) { break; }

			$result[$idx] = array( "identifier" => $identifier,
															"imgUrl" => $imgUrl,
															"latNorth" => $latNorth,
															"latSouth" => $latSouth,
															"lngEast" => $lngEast,
															"lngWest" => $lngWest,
														);

		}

		if ($result) {

			$result = array( "fulldata" => $result, "jsSelect" => array( -1 => __("Select Below") ), "jsData" => json_encode($result) );

			foreach($result["fulldata"] as $idx => $overlay) {
				$result["jsSelect"][$idx] = $overlay["identifier"];
			}

		}

		# echo("<pre>" . print_r($result,true) . "</pre>");

		return $result;
	}

	function GeolocationConvertOverlayFormToJson($geolocationMapOverlays = false) {
		$jsonMapOverlays = "[]";

		if (!$geolocationMapOverlays) {
			if ( (isset($_POST['geolocation_map_overlays'])) and ($_POST['geolocation_map_overlays']) ) {
				$geolocationMapOverlays = $_POST['geolocation_map_overlays'];
			}
		}

		if ($geolocationMapOverlays) {

			$geolocationMapOverlays = $_POST['geolocation_map_overlays'];
			$txtOverlays= explode("\n", $geolocationMapOverlays);

			$mapOverlays = array();
			foreach($txtOverlays as $txtOverlay) {
				$cookedTxtOverlay = trim($txtOverlay);
				if ($cookedTxtOverlay) { $mapOverlays[] = explode(";", $cookedTxtOverlay); }
			}

			$jsonMapOverlays = json_encode($mapOverlays);
		}

		return $jsonMapOverlays;
	}

?>
