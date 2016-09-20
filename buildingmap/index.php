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
			$canvasW = 1000; $canvasH = 320; // could/should come from the SVG DOM div

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
				// $cancelCnt = 100;

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

				foreach(array_keys($polygons) as $idx) {
					foreach(array_keys($polygons[$idx]["coords"]) as $cidx) {
						$polygons[$idx]["coords"][$cidx]["x"] = $polygons[$idx]["coords"][$cidx]["x"] - $minX;
						$polygons[$idx]["coords"][$cidx]["y"] = $maxY + $minY - $polygons[$idx]["coords"][$cidx]["y"];
					}
				}

				$maxX = $maxX - $minX; $minX = 0;
				$maxY = $maxY - $minY; $minY = 0;

				// ---------------------

				// echo "<p>"
				// . "minX: $minX / maxX: $maxX <br>"
				// . "minY: $minY / maxY: $maxY"
				// . "</p>";
			}
		?>
		<div>
			<!-- width="<?php echo $canvasW; ?>"
			height="<?php echo $canvasH; ?>" -->
			<svg
				id="mySvg"
				viewbox="<?php echo "$minX $minY $maxX $maxY"; ?>"
			>
				<?php
					foreach($polygons as $polygon) {
						$points = array();
						foreach($polygon["coords"] as $coord) {
							$points[] = implode(",", $coord);
						}
						$points = implode(" ", $points);
						$id = $polygon["id"];
						$shortName = $polygon["shortName"];
						echo
							"<polygon "
							. "points='$points' class='buildingBlock' "
							. "data-id='$id'"
							. ">"
							."<title>$shortName</title>"
							."</polygon>"
						;
					}
				?>
			</svg>
		</div>
	</body>
</html>
