<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Quelle");
	define("importBelongsRelationTitle", "gehört zu");

	// -----------------------------------------------
	// Useful IDs to link to
	// -----------------------------------------------

	$linkTargets = array(
		"Rathaus Antwerpen" => "(ID #10402)",
		"Hameln" => "(ID #1298)",
		"Bückeburg" => "(ID #747)",
	);

	// -----------------------------------------------

	ini_set('include_path', '.' . DIRECTORY_SEPARATOR . '..');

	// -----------------------------------------------

	// Bootstrap the Omeka application.
	require_once 'bootstrap.php';

	if ( !isset($_GET["proceed"]) ) {
    echo 'Please add "?proceed" to the end of this URL to proceed.'."\n\n";
    die("Quitting ... done nothing yet.");
  }

	// -----------------------------------------------

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

	$file = fopen('ChronologieWeSa3.csv', 'r');
	while (($line = fgetcsv($file)) !== FALSE) { if ($line) { $csv[]=$line; } }
	fclose($file);

	# print_r($csv);
	if (!$csv) { die("CSV file error."); }

	// -----------------------------------------------
	// Find item relations to be established
	// -----------------------------------------------

	$relationshipTitles = array( importBelongsRelationTitle => 0);

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

	foreach($csv as $line) {

		# print_r($line);

		$titel = $line[$headers["Titel"]];

		$datum = $line[$headers["Datum"]];
		// $datum = preg_replace("/(\[(?:J|G)\])(\d{4})(\d{2})(\d{2})/", "$1 $2-$3-$4", $datum);

		$ereignis = $line[$headers["Kommentar"]];
		$linkRequest = "";

		$linkRequestPos = strpos($ereignis, "Verknüpfungswunsch: ");
		if ($linkRequestPos) {
			$linkRequest = substr($ereignis, $linkRequestPos);
			$ereignis = trim(substr($ereignis, 0, $linkRequestPos), "\n");

			foreach($linkTargets as $linkTarget => $id) {
				$linkRequest = str_replace($linkTarget, "$linkTarget $id", $linkRequest);
			}

			// echo "-> $linkRequest\n";
			// die("*$ereignis*\n*$linkRequest*");
		}

		$erRegEx = "\W?\(ID\W?#(\W?\d+)\)";
		$linkIds = array();
		if (preg_match_all("/$erRegEx/", $linkRequest, $matches)) {
			$linkIds = array_unique($matches[1]);
			// echo json_encode($linkIds)."\n";
		}
		$linkRequest = preg_replace("/$erRegEx/", "", $linkRequest);

		$fundort = trim($line[$headers["Archiv"]]);
		switch ($fundort) {
			case "BE SA" : $fundort = "BE SA (Belgien, Stadsarchief Antwerpen)"; break;
			case "NLA Bü" : $fundort = "NLA Bü (Niedersächsisches Landesarchiv, Abt. Bückeburg)"; break;
			case "StAB" : $fundort = "StAB (Staatsarchiv Bremen)"; break;
			default: die("*** '$fundort' -- exiting");
		}

		/*
		StAB (Staatsarchiv Bremen)
		NL-HaNA (Nationaal Archief Den Haag)
		SAA (Stadsarchief Amsterdam)
		ELO (Erfgoed Leiden en Omstreken)
		NLA Bü (Niedersächsisches Landesarchiv, Abt. Bückeburg)
		HCO (Historisch Centrum Overijssel)
		FA Bur (Fürstliches Archiv Burgsteinfurt)
		Landesamt für Denkmalpflege Bremen
		BE SA (Belgien, Stadsarchief Antwerpen)
		HKHB (Archiv der Handelskammer Bremen)
		*/

		$signatur = $line[$headers["Signatur/Inventarnr"]];
		$folio = $line[$headers["Folierung"]];

		$personen = $line[$headers["Personen"]];

		$quelltyp = "Textquelle";

		// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
		$metaData = array("item_type_id" => $importItemTypeID);
		$elementTexts = array(
			'Dublin Core' => array(
				'Title' => array( array('text' => $titel, 'html' => false) ),
				'Description' => array( array('text' => $ereignis, 'html' => false) ),
			),
			'Item Type Metadata' => array(
					'Datum' => array( array('text' => $datum, 'html' => false) ),
					'Fundort' => array( array('text' => $fundort, 'html' => false) ),
					'Quelltyp' => array( array('text' => $quelltyp, 'html' => false) ),
					'Signatur / Inventarnummer' => array(
																					array('text' => $signatur, 'html' => false),
																					array('text' => $folio, 'html' => false),
																				),
					'Anmerkungen' => array(
														array('text' => $personen, 'html' => false),
														array('text' => $linkRequest, 'html' => false),
													),
			)
		);
		print_r($elementTexts);
		// $itemId = 0;
		$item = insert_item($metaData, $elementTexts);
		$itemId = $item["id"];

		if (($itemId) and ($linkIds)) {
			foreach($linkIds as $linkId) {
				$linkRelation = importBelongsRelationTitle;
				$linkRelationId = $relationshipTitles[$linkRelation];
				echo "--- Relation '$linkRelation' ($linkRelationId) from $itemId towards $linkId\n";
				$sql="
					INSERT INTO {$db->ItemRelationsRelations}
						(subject_item_id, property_id, object_item_id)
						VALUES ($itemId, $linkRelationId, $linkId)
				";
				$db->query($sql);
			}
		}

	}

function shortenString($string, $length) {
	$result = $string;

	$enc = "utf8";

	if (mb_strlen($string, $enc) > $length) {
		$padding = '[…]';
		$padLength = mb_strlen($padding, $enc);
		$result = mb_substr($string, 0, $length-$padLength, $enc) . $padding;
	}

	return $result;
}

?>
