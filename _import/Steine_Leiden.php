<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Sandstein-Element");
	define("importRelationTitle", "ist verbaut an Gebäude oder Gebäudeteil");
	define("titleElementText", "Title");
	define("measurementElementText", "Maße");
	define("importRelationTarget", "Rathaus Leiden");

	// -----------------------------------------------
	// Bootstrap the Omeka application.
	// -----------------------------------------------

	ini_set('include_path', '.' . DIRECTORY_SEPARATOR . '..');
	require_once 'bootstrap.php';

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
	echo "importItemTypeID = $importItemTypeID\n";

	// -----------------------------------------------
	// Find item relation to be established
	// -----------------------------------------------

	$importRelationID = false; // Sanity
	$sql = "SELECT id FROM {$db->ItemRelationsProperty} WHERE label='".importRelationTitle."'";
	$importRelationID = $db->fetchOne($sql);
	if (!$importRelationID) { die("Relationship '".importRelationTitle."' not found."); }
	echo "importRelationID = $importRelationID\n";

	// -----------------------------------------------
	// Find title element ID (usually 50)
	// -----------------------------------------------

	$titleElementID = false; // Sanity
	$sql = "SELECT id FROM {$db->Elements} WHERE name='".titleElementText."'";
	$titleElementID = $db->fetchOne($sql);
	if (!$titleElementID) { die("Element ID '".titleElementText."' not found."); }
	echo "titleElementID = $titleElementID\n";

	// -----------------------------------------------
	// Find relation target item ID
	// -----------------------------------------------

	$importRelationTargetID = false; // Sanity
	$sql = "SELECT record_id FROM {$db->ElementTexts} WHERE element_id=$titleElementID AND text='".importRelationTarget."'";
	$importRelationTargetID = $db->fetchOne($sql);
	if (!$importRelationTargetID) { die("Target Item '".importRelationTarget."' not found."); }
	echo "importRelationTargetID = $importRelationTargetID\n";

	// -----------------------------------------------
	// Load CSV file into array
	// -----------------------------------------------

	$csv=array();
	$file = fopen('Steine_Leiden.csv', 'r');
	while (($line = fgetcsv($file, 0, ";")) !== FALSE) { if ($line) { $csv[]=$line; } }
	fclose($file);
	if (!$csv) { die("CSV file error."); }
	// print_r($csv);

	// -----------------------------------------------

	$headers=array(); // Sanity state

	$firstline=true;
	foreach($csv as $line) {

		if ($firstline) {
			$headers=array_flip($line);
			print_r($headers);

			$firstline=false;
		}

		else {

			# print_r($line);

			$titel = $line[$headers["Stone"]];
			$l1 = $line[$headers["Length"]];
			$l2 = $line[$headers["Height"]];
			$l3 = $line[$headers["Depth"]];

			$regEx = "/^(\d+),(\d+)$/"; // "1,234"
			foreach(array("l1", "l2", "l3") as $var) {
				if (preg_match($regEx, $$var, $matches)) {
					$$var = $matches[1]*1000 + $matches[2];
				}
			}

			echo "$titel: $l1 mm / $l2 mm / $l3 mm\n";

			$measurements = array(
				"u" => "m-cm-mm (1-100-10)",
				"l1" => array($l1,0,0,$l1), "l2" => array($l2,0,0,$l2), "l3" => array($l3,0,0,$l3),
				"f1" => array("","","",""), "f2" => array("","","",""), "f3" => array("","","",""), "v" => array("","","",""),
			);
			$measurements["l1d"] = $measurements["l1"];
			$measurements["l2d"] = $measurements["l2"];
			$measurements["l3d"] = $measurements["l3"];
			$measurements["f1d"] = array($l1 * $l2, 0, 0, 0);
			$measurements["f2d"] = array($l1 * $l3, 0, 0, 0);
			$measurements["f3d"] = array($l2 * $l3, 0, 0, 0);
			$measurements["vd"] = array($l1 * $l2 * $l3, 0, 0, 0);

			foreach(array("f1d" => 2, "f2d" => 2, "f3d" => 2, "vd" => 3) as $comp => $exp) {
				$curr = $measurements[$comp][0];
				$fact3 = pow(10, $exp);
				$measurements[$comp][3] = $curr % $fact3;
				$measurements[$comp][2] = intval($curr - $measurements[$comp][3]) / $fact3;
				$curr = $measurements[$comp][2];
				$fact2 = pow(100, $exp);
				$measurements[$comp][2] = $curr % $fact2;
				$measurements[$comp][1] = intval($curr - $measurements[$comp][2]) / $fact2;
			}

			$measurements = json_encode($measurements);

			// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
			$metaData = array("item_type_id" => $importItemTypeID);
			$elementTexts = array(
				'Dublin Core' => array(
  				titleElementText => array( array('text' => $titel, 'html' => false) ),
				),
				'Item Type Metadata' => array(
  				measurementElementText => array( array('text' => $measurements, 'html' => false) ),
				)
			);
			print_r($elementTexts);
			$item = insert_item($metaData, $elementTexts);

			$itemId = $item["id"];
			echo "New Item: $itemId\n";

			$sql="
				INSERT INTO {$db->ItemRelationsRelations}
					(subject_item_id, property_id, object_item_id)
					values ($itemId, $importRelationID, $importRelationTargetID)
			";
			$db->query($sql);

			// update_item($itemId);
			// ... unnecessary, as we don't add comments that need to go into the search index

		}
	}

?>
