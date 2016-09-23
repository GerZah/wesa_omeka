<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Sandstein-Element");
	define("titleElementText", "Title"); # usually 50
	define("measurementsElementText", "Maße"); # should be 167

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
	// Find target item type ID
	// -----------------------------------------------

	$importItemTypeID = false; // Sanity
	$sql = "SELECT id FROM {$db->ItemTypes} WHERE name='" . importItemType . "'";
	$importItemTypeID = $db->fetchOne($sql);
	if (!$importItemTypeID) { die("Item type '".importItemType."' not found."); }
	echo importItemType . " / importItemTypeID = $importItemTypeID\n";

	// -----------------------------------------------
	// Find title element ID (usually 50)
	// -----------------------------------------------

	$titleElementID = false; // Sanity
	$sql = "SELECT id FROM {$db->Elements} WHERE name='".titleElementText."'";
	$titleElementID = $db->fetchOne($sql);
	if (!$titleElementID) { die("Element ID '".titleElementText."' not found."); }
	echo titleElementText . " / titleElementID = $titleElementID\n";

	// -----------------------------------------------
	// Find measurement element ID (should be 167)
	// -----------------------------------------------

	$measurementsElementID = false; // Sanity
	$sql = "SELECT id FROM {$db->Elements} WHERE name='".measurementsElementText."'";
	$measurementsElementID = $db->fetchOne($sql);
	if (!$measurementsElementID) { die("Element ID '".measurementsElementText."' not found."); }
	echo measurementsElementText . " / measurementsElementID = $measurementsElementID\n";

	// -----------------------------------------------
	// Load CSV file into array
	// -----------------------------------------------

	$csv=array();
	$file = fopen('Tab_Amsterdam_1.csv', 'r');
	while (($line = fgetcsv($file, 0, ",")) !== FALSE) { if ($line) { $csv[]=$line; } }
	fclose($file);
	if (!$csv) { die("CSV file error."); }
	// print_r($csv);

	// -----------------------------------------------
	// Re-import - update title and values
	// -----------------------------------------------

	$headers=array_flip(array_shift($csv));
	print_r($headers);

	foreach($csv as $line) {

		$titel = $line[$headers["Stein-Titel"]];
		$firstLetter = substr($titel, 0,1);
		$newTitel = $titel;
		if ($firstLetter=="O") { $newTitel = "OR-".substr($titel,1); }
		if ($firstLetter=="W") { $newTitel = "WR-".substr($titel,1); }

		$l1 = $line[$headers["Dimension 1"]]; $l1 = ( $l1 ? $l1 : "0-0-0");
		$l2 = $line[$headers["Dimension 2"]]; $l2 = ( $l2 ? $l2 : "0-0-0");
		$l3 = $line[$headers["Dimension 3"]]; $l3 = ( $l3 ? $l3 : "0-0-0");
		// $unit = $line[$headers["Einheit"]];
		$unit = "AmsDezE-F-D";
		$num = $line[$headers["Anzahl"]];

		$regEx = "/^(\d+),(\d+)$/"; // "1,234"
		$regEx = "/^(\d+)-(\d+)-(\d+)(?:,(\d+))?$/"; // "1-2-3,4"
		foreach(array("l1", "l2", "l3") as $var) {
			if (preg_match($regEx, $$var, $matches)) {
				echo $$var . " = ";
				if (@$matches[4]) {
					$matches[3] = $matches[3] . "." . $matches[4];
				}
				foreach(array_keys($matches) as $i) { $matches[$i] = floatval($matches[$i]); }
				$$var =
					$matches[1] * 2 * 10
					+ $matches[2] * 10
					+ $matches[3]
				;
				$$var = floatval($$var);
				$$var = array($$var, $matches[1], $matches[2], $matches[3]);
				echo json_encode($$var) . "\n";
			}
			else { $matches = array(0,0,0,0); }
		}

		// "l1":[65,0,5,5],"l2":[19.5,0,1,7.5],"l3":[17,0,1,5]

		$measurements = array(
			"u" => "$unit (1-2-10)",
			"l1" => $l1, "l2" => $l2, "l3" => $l3,
			"f1" => array("","","",""), "f2" => array("","","",""), "f3" => array("","","",""), "v" => array("","","",""),
			"n" => $num,
		);

		$l1 = $measurements["l1"][0]; $l1 = ($l1 ? $l1 : "0");
		$l2 = $measurements["l2"][0]; $l2 = ($l2 ? $l2 : "0");
		$l3 = $measurements["l3"][0]; $l3 = ($l3 ? $l3 : "0");

		$measurements["l1d"] = $measurements["l1"];
		$measurements["l2d"] = $measurements["l2"];
		$measurements["l3d"] = $measurements["l3"];
		$measurements["f1d"] = array($l1 * $l2, 0, 0, 0);
		$measurements["f2d"] = array($l1 * $l3, 0, 0, 0);
		$measurements["f3d"] = array($l2 * $l3, 0, 0, 0);
		$measurements["vd"] = array($l1 * $l2 * $l3, 0, 0, 0);

		foreach(array("f1d" => 2, "f2d" => 2, "f3d" => 2, "vd" => 3) as $comp => $exp) {
			$fact3 = pow(2, $exp);
			$fact2 = pow(10, $exp);
			$fact23 = $fact2*$fact3;
			$curr = $measurements[$comp][0];
			$measurements[$comp][1] = intval($curr / $fact23);
			$curr = $curr - $measurements[$comp][1]*$fact23;
			$measurements[$comp][2] = intval($curr / $fact2);
			$curr = $curr - $measurements[$comp][2]*$fact2;
			$measurements[$comp][3] = $curr;
		}

		$measurements = json_encode($measurements);

		// "l1d":[65,0,5,5],"l2d":[19.5,0,1,7.5],"l3d":[17,0,1,5]
		// "f1d":[1267.5,2,0,115.5],"f2d":[1105,1,3,97],"f3d":[331.5,0,2,43.5]
		// "vd":[21547.5,1,4,811.5]}

		echo "\n$titel -> $newTitel\n$measurements\n";

		// $item = insert_item($metaData, $elementTexts);
		// $itemId = $item["id"];
		// echo "New Item: $itemId\n";

		$sql = "
			SELECT id, record_id
			FROM `$db->ElementTexts`
			WHERE element_id = $titleElementID
			AND text = '$titel'
		";
		$found = $db->fetchAll($sql);

		if ($found) {
			echo json_encode($found)."\n";
			$id = $found[0]["id"];
			$recordId = $found[0]["record_id"];

			$updateSql = "
				UPDATE `$db->ElementTexts`
				SET text =  '$newTitel'
				WHERE id = $id
			";
			$db->query($updateSql);

			$updateSql = "
				UPDATE `$db->ElementTexts`
				SET text =  '$measurements'
				WHERE record_id = $recordId
				AND element_id = $measurementsElementID
			";
			$db->query($updateSql);

			update_item($recordId);

		}

		echo "\n";

	}

?>
