<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	ini_set('max_execution_time', 300); // 300 seconds == 5 minutes

	define("measurementsElement", "Maße");

	// -----------------------------------------------
	// Bootstrap the Omeka application.
	// -----------------------------------------------

	ini_set('include_path', '.' . DIRECTORY_SEPARATOR . '..');
	require_once 'bootstrap.php';

	if ( !isset($_GET["proceed"]) ) {
    echo 'Please add "?proceed" to the end of this URL to proceed.'."\n\n";
    die("Quitting ... done nothing yet.");
  }
	// Configure and initialize the application.
	$application = new Omeka_Application(APPLICATION_ENV);
	$application->initialize();
	$db = get_db(); // Database connection

  // -----------------------------------------------

	$measurementsElement = $db->fetchOne(
		"SELECT id FROM `$db->Elements` WHERE `name`='" . measurementsElement . "'"
	);
	if (!$measurementsElement) {
		die("*** Measurements Element '" . measurementsElement . "' not found. (exiting)\n");
	}

	else {
		echo "--- Measurements Element ID of '" . measurementsElement . "' is $measurementsElement.\n";

		$unitFilter = '"u":"m-cm-mm (1-100-10)"';
		$sql = "
			SELECT *
			FROM `$db->ElementTexts`
			WHERE element_id = $measurementsElement
			AND text LIKE '%$unitFilter%'
		";
		$measurements = $db->fetchAll($sql);

		if (!$measurements) {
			die("*** No possibly relevant measurement text elements found (exiting)\n");
		}

		else {
			echo "--- Found " . count($measurements) . " possibly relevant measurement elements.\n";

			// echo print_r($measurements,true) . "\n"; die();

			$updateCounter = 0;

			foreach($measurements as $measurement) {

				// echo print_r($measurement,true) . "\n"; die();

				$id = $measurement["id"];
				$recordId = $measurement["record_id"];

				$measurementDataJson = $measurement["text"];
				$measurementData = json_decode($measurementDataJson, true);

				// echo print_r($measurementData,true) . "\n"; die();

				$isChanged = false;

				foreach(
					array(
						"l1" => 1, "l2" => 1, "l3" => 1,
						"f1" => 2, "f2" => 2, "f3" => 2, "v" => 3,
						"l1d" => 1, "l2d" => 1, "l3d" => 1,
						"f1d" => 2, "f2d" => 2, "f3d" => 2, "vd" => 3,
					) as $comp => $exp
				) {
					$fact3 = pow(100, $exp);
					$fact2 = pow(10, $exp);
					$fact23 = $fact2*$fact3;
					if ($measurementData[$comp][0] !== "") {

						$trial = calcTrial($measurementData, $comp, $fact2, $fact23);
						if ($trial != $measurementData[$comp][0]) {

							echo "$comp before: " . json_encode($measurementData[$comp]) . " - ".$measurementData[$comp][0]." vs. $trial\n";
							$curr = $measurementData[$comp][0];
							$measurementData[$comp][1] = intval($curr / $fact23);
							$curr = $curr - $measurementData[$comp][1]*$fact23;
							$measurementData[$comp][2] = intval($curr / $fact2);
							$curr = $curr - $measurementData[$comp][2]*$fact2;
							$measurementData[$comp][3] = $curr;

							$trial = calcTrial($measurementData, $comp, $fact2, $fact23);
							echo "$comp after : " . json_encode($measurementData[$comp]) . " - ".$measurementData[$comp][0]." vs. $trial\n";

							$isChanged = true;
						}
					}
				}

				if ($isChanged) {
					echo "-- Updating ID $id for record_id $recordId\n";
					$updateCounter++;

					$measurementDataJson = json_encode($measurementData);
					$sql = "
						UPDATE `$db->ElementTexts`
						SET text = '$measurementDataJson'
						WHERE id = $id
					";
					$db->query($sql);
					// die();
				}

				// echo print_r($measurementData,true) . "\n"; die();
			}

			echo "--- Updated $updateCounter relevant measurement elements (exiting).\n";


		}

	}

	function calcTrial($measurementData, $comp, $fact2, $fact23) {
		$trial =
			$measurementData[$comp][3]
			+ $measurementData[$comp][2]*$fact2
			+ $measurementData[$comp][1]*$fact23
		;
		return $trial;
	}

?>
