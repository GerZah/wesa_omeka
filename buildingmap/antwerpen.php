<!DOCTYPE html>
<html lang="de-DE">
	<head>
		<meta charset="utf-8">
		<title>Rathaus Antwerpen</title>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
		<link href="../application/views/scripts/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" >

		<script src="svg-pan-zoom.min.js"></script>
		<script type="text/javascript" src="buildingmap.js"></script>
		<link href="reset.css" media="all" rel="stylesheet" type="text/css" >
		<link href="buildingmap.css" rel="stylesheet" type="text/css" >
	</head>
	<body>
		<h2>Rathaus Antwerpen</h2>
		<?php

			$polygons = array(); # Sanity
			$canvasW = "100%"; $canvasH = "450"; // could/should come from the SVG DOM div

			# ------------

			$csv = array();
			$headers = array_flip(array( "ID", "Hierarchy", "Object", "ShortName", "Link" ));
			$backgroundPolygons = 0;

			// First load the background polygons
			$file = fopen('antwerpen_html_background.csv', 'r');
			$firstLine = true;
			while (($line = fgetcsv($file)) !== FALSE) {
				if ($line) {
					if ($firstLine) { $firstLine = false; }
					else {
						array_splice( $line, 2, 0, array( false, false, false ) );
						$csv[] = $line;
					}
				}
			}
			fclose($file);
			$backgroundPolygons = count($csv);

			// Now load the regular stone polygons
			$file = fopen('BremenSchuetting.csv', 'r');
			$firstLine = true;
			while (($line = fgetcsv($file)) !== FALSE) {
				if ($line) {
					if ($firstLine) { $firstLine = false; }
					else { $csv[]=$line; }
				}
			}
			fclose($file);

			if ($csv) {

				$minX = false; $maxX = false;
				$minY = false; $maxY = false;

				$polygons = array();

				foreach($csv as $element) {
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
					$polygons[] = $polygon;
				}

				foreach(array_keys($polygons) as $idx) {
					foreach(array_keys($polygons[$idx]["coords"]) as $cidx) {
						$polygons[$idx]["coords"][$cidx]["x"] = $polygons[$idx]["coords"][$cidx]["x"] - $minX;
						$polygons[$idx]["coords"][$cidx]["y"] = $maxY - $polygons[$idx]["coords"][$cidx]["y"];
					}
				}

				$maxX = $maxX - $minX; $minX = 0;
				$maxY = $maxY - $minY; $minY = 0;

			}

			$highlights = @$_GET["highlights"];
			if (!$highlights) {
				$highlights = array();
			}
			else {
				$highlights = explode(",", $highlights);
				// echo "<pre>" . print_r($highlights,true) . "</pre>";
			}

		?>
		<div>
			<svg
				id="mySvg"
				width="<?php echo $canvasW; ?>"
				height="<?php echo $canvasH; ?>"
				viewbox="<?php echo "$minX $minY $maxX $maxY"; ?>"
			>
				<?php
					foreach($polygons as $cnt => $polygon) {
						$points = array();
						foreach($polygon["coords"] as $coord) {
							$points[] = implode(",", $coord);
						}
						$points = implode(" ", $points);
						$id = $polygon["id"];
						$shortName = $polygon["shortName"];
						$highlight = ( in_array($id, $highlights) ? "hlBlock" : "" );

						$clickable = ($cnt >= $backgroundPolygons);
						$class = ( $clickable ? "buildingBlock" : "backgroundBlock" );

						echo
							($clickable ? "<a xlink:href='#' class='buildingBlockLink'>" : "" )
							. "<polygon points='$points' class='$class $highlight' id='$id'>"
							.  "<title>$shortName</title>"
							. "</polygon>"
							. ($clickable ? "</a>" : "" )
						;

					}
				?>
			</svg>
		</div>
	</body>
</html>
