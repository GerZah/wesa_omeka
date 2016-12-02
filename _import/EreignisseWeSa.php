<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Ereignis");
	define("importReferencedInLiterature", "wird referenziert in Literatur");
	define("importPartOfEvent", "ist beteiligt an Ereignis");

	// -----------------------------------------------

	ini_set('include_path', '.' . DIRECTORY_SEPARATOR . '..');

	// -----------------------------------------------

	// Bootstrap the Omeka application.
	require_once 'bootstrap.php';

	if ( !isset($_GET["proceed"]) ) {
    echo 'Please add "?proceed" to the end of this URL to proceed.'."\n\n";
    die("Quitting ... done nothing yet.");
  }

	// Configure and initialize the application.
	$application = new Omeka_Application(APPLICATION_ENV);
	$application->initialize();

	$db = get_db(); // Database connection
	$importItemTypeID = false; // Sanity
	$sql = "SELECT id from {$db->Item_Types} where name='" . importItemType . "'";
	$importItemTypeID = $db->fetchOne($sql);
	if (!$importItemTypeID) { die("Item type '".importItemType."' not found."); }

	// -----------------------------------------------

	#$csv = @array_map('str_getcsv', @file('CitaviWeSa_edited.csv'));

	$csv=array();

	$file = fopen('EreignisseWeSa.csv', 'r');
	while (($line = fgetcsv($file)) !== FALSE) { if ($line) { $csv[]=$line; } }
	fclose($file);

	// print_r($csv);

	if (!$csv) { die("CSV file error."); }

	// -----------------------------------------------
	// Find item relations to be established
	// -----------------------------------------------

	$relationshipTitles = array(
		importReferencedInLiterature => 0,
		importPartOfEvent => 0
	);

	foreach(array_keys($relationshipTitles) as $title) {
		$sqlTitle = addcslashes($title, '%_');
		$sql = "SELECT id FROM {$db->ItemRelationsProperty} WHERE label='".$sqlTitle."'";
		$titleId	 = $db->fetchOne($sql);
		if ($titleId) { $relationshipTitles[$title] = $titleId;  }
	}

	print_r($relationshipTitles);

	// -----------------------------------------------

	$headers=array_flip($csv[0]); // Store Headers array for later use ...
	unset($csv[0]); // ... but remove them from the array
	print_r($headers);

	foreach($csv as $line) {

		foreach(array_keys($headers) as $header) {
			$simpleHeader = str_replace(" ", "", strtolower($header));
			$$simpleHeader = $line[$headers[$header]];
			echo $simpleHeader . " - " . $$simpleHeader . "\n";
		}

		$linkRequest = "";
		$linkRequestPos = strpos($kommentar, "Verknüpfungswunsch: ");
		if ($linkRequestPos>=0) {
			$linkRequest = substr($kommentar, $linkRequestPos);
			$kommentar = trim(substr($kommentar, 0, $linkRequestPos), "\n");
			echo "-> $linkRequest\n";
		}

		$erRegEx = "\W?\(ID\W?#(\W?\d+)\)";
		$linkIds = array();
		if (preg_match_all("/$erRegEx/", $linkRequest, $matches)) {
			$linkIds = array_unique($matches[1]);
			echo json_encode($linkIds)."\n";
		}
		$linkRequest = preg_replace("/$erRegEx/", "", $linkRequest);

		// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
		$metaData = array("item_type_id" => $importItemTypeID);
		$elementTexts = array(
			'Dublin Core' => array(
  			'Title' => array( array('text' => $titel, 'html' => false) ),
				'Description' => array( array('text' => $kommentar, 'html' => false) ),
			),
			'Item Type Metadata' => array(
				'Datum' => array( array('text' => $datum, 'html' => false) ),
  			'Anmerkungen' => array(
					array('text' => $personen, 'html' => false),
					array('text' => $linkRequest, 'html' => false),
				),
			)
		);
		print_r($elementTexts);
		$item = insert_item($metaData, $elementTexts);
		$itemId = $item["id"];

		if (($itemId) and ($linkIds)) {
			foreach($linkIds as $linkId) {
				$linkRelation = importPartOfEvent;
				$linkRelationId = $relationshipTitles[$linkRelation];
				echo "--- Relation '$linkRelation' ($linkRelationId) from $linkId towards $itemId\n";
				$sql="
					INSERT INTO {$db->ItemRelationsRelations}
						(subject_item_id, property_id, object_item_id)
						VALUES ($linkId, $linkRelationId, $itemId)
				";
				$db->query($sql);
			}
		}

		if (($itemId) and ($literaturid)) {
			$linkRelation = importReferencedInLiterature;
			$linkRelationId = $relationshipTitles[$linkRelation];
			echo "--- Relation '$linkRelation' ($linkRelationId) from $itemId towards $literaturid\n";
			$sql="
				INSERT INTO {$db->ItemRelationsRelations}
					(subject_item_id, property_id, object_item_id, relation_comment)
					VALUES ($itemId, $linkRelationId, $literaturid, '$literatur')
			";
			$db->query($sql);

		}

	}
?>
