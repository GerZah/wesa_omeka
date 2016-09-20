<html>
	<head>
		<title>Building Map</title>
		<link href="../application/views/scripts/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" >
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script type="text/javascript" src="buildingmap.js"></script>
		<link href="./buildingmap.css" rel="stylesheet" type="text/css" >
	</head>
	<body>
		<?php

			$polygons = array(); # Sanity
			$canvasW = 1400; $canvasH = 1400; // could/should come from the SVG DOM div

			# ------------

			$csv=array();

			$file = fopen('RhAMS.csv', 'r');
			while (($line = fgetcsv($file)) !== FALSE) { if ($line) { $csv[]=$line; } }
			fclose($file);

			if ($csv) {

				$headers=array_flip(array_shift($csv));

				// echo "<pre>" . print_r($headers,true) . "</pre>";
				// echo "<pre>" . print_r($csv,true) . "</pre>";

				$minX = false; $maxX = false;
				$minY = false; $maxY = false;

				$polygons = array();
				$cancelCnt = -1;

				foreach($csv as $element) {

					if (!$cancelCnt--) { break; }

					$polygon = array(
						"id" => $element[ $headers["ID"] ],
						"shortName" => $element[ $headers["ShortName"] ],
						"coords" => array(),
					);

					$idx = 5;
					while (isset($element[$idx])) {
						$x = $element[$idx++];
						$y = $element[$idx++];
						$polygon["coords"][] = array("x" => $x, "y" => $y);

						if ($minX === false) { $minX = $x ;} else { if ($x < $minX) { $minX = $x; } }
						if ($maxX === false) { $maxX = $x ;} else { if ($x > $maxX) { $maxX = $x; } }

						if ($minY === false) { $minY = $y ;} else { if ($y < $minY) { $minY = $y; } }
						if ($maxY === false) { $maxY = $y ;} else { if ($y > $maxY) { $maxY = $y; } }

					}
					// echo json_encode($polygon)."<br>";
					// echo "<pre>" . count($polygon["coords"]) . " - " . print_r($polygon,true) . "</pre>";
					// echo $polygon["shortName"] . ": " . $polygon["id"] . " / " . count($polygon["coords"]) . "<br>";
					$polygons[] = $polygon;
				}

				// ---------------------

				$diffX = $maxX - $minX;
				$diffY = $maxY - $minY;
				$aspect = $diffX / $diffY;

				$paintW = $canvasW; $paintH = $canvasH;
				if ($aspect > 1) { $paintH /= $aspect; } else { $paintW *= $aspect; }

				// ---------------------

				$maxXN = $paintW-1; $maxYN = $paintH-1;
				$maxXT = $maxX - $minX; $maxYT = $maxY - $minY;
				$factX = $maxXN / $maxXT; $factY = $maxYN / $maxYT;

				foreach(array_keys($polygons) as $pIdx) {
					$normCoords = array();
					foreach(array_keys($polygon["coords"]) as $idx) {
						$normCoords[$idx] = array(
							"x" => ($polygons[$pIdx]["coords"][$idx]["x"]-$minX) * $factX,
							"y" => ($polygons[$pIdx]["coords"][$idx]["y"]-$minY) * $factY,
						);
					}
					$polygons[$pIdx]["normCoords"] = $normCoords;
					// echo "<pre>" . json_encode($polygon["coords"]) . "</pre>";
					// echo "<pre>" . json_encode($polygon["normCoords"]) . "</pre>";
				}

				// ---------------------

				echo "<p>"
				. "minX: $minX / maxX: $maxX <br>"
				. "minY: $minY / maxY: $maxY"
				. "</p>";

				echo "<p>"
				. "diffX: $diffX / diffY: $diffY = aspect: $aspect : 1"
				. "</p>";

				echo "<p>"
				. "canvasW: $canvasW / canvasH: $canvasH<br>"
				. "paintW: $paintW / paintH: $paintH<br>"
				. "</p>";

				echo "<p>"
				. "maxXN: $maxXN / maxYN: $maxYN<br>"
				. "maxXT: $maxXT / maxYN: $maxYT<br>"
				. "factX: $factX / factY: $factY><br>"
				. "</p>";

			}
		?>
		<div>
			<svg id="mySvg" width="<?php echo $canvasW; ?>" height="<?php echo $canvasH; ?>" viewbox="0 0 50 50">
		  	<!-- <circle class="blueYellow" cx="50" cy="50" r="40" /> -->
				<?php
					foreach($polygons as $polygon) {
						$points = array();
						foreach($polygon["normCoords"] as $normCoord) {
							$points[] = $normCoord["x"] . "," . ($paintH - $normCoord["y"]);
						}
						$points = implode(" ", $points);
						echo "<polygon points='$points' class='buildingBlock' />";
					}
				?>
			</svg>
		</div>
	</body>
</html>
