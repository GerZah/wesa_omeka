<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Sandstein-Element");
	define("importTargetRelationTitle", "ist verbaut an Gebäude oder Gebäudeteil");
	define("importBelongsRelationTitle", "gehört zu");
	define("titleElementText", "Title");
	define("measurementElementText", "Maße");

	$importItemTypes = array(
		0 => array( "name" => "Gebäude / Bauwerk" ),
		1 => array( "name" => "Gebäudeteil" ),
		2 => array( "name" => "Sandstein-Baugruppe" ),
		3 => array( "name" => "Sandstein-Bauteil" ),
		4 => array( "name" => "Sandstein-Element" ),
	);

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
	// Find target item type IDs
	// -----------------------------------------------

	$importItemTypesVerb = array();
	$importItemTypesInv = array();

	foreach($importItemTypes as $id => $importType) {
		$name = $importType["name"];
		$importItemTypesVerb[] = $name;
		$importItemTypesInv[$name] = $id;
	}
	$importItemTypesClause = "('" . implode("', '", $importItemTypesVerb) . "')";

	$sql = "SELECT id, name FROM {$db->ItemTypes} WHERE name IN $importItemTypesClause";
	$importItemTypeIDs = $db->fetchAll($sql);

	foreach($importItemTypeIDs as $importItemType) {
		$importItemTypes[ $importItemTypesInv[ $importItemType["name"] ] ]["id"] = $importItemType["id"];
	}
	echo "importItemTypes: " . print_r($importItemTypes,true) . "\n";

	// -----------------------------------------------
	// Find item relations to be established
	// -----------------------------------------------

	$relationshipTitles = array( importTargetRelationTitle => 0, importBelongsRelationTitle => 0);

	foreach(array_keys($relationshipTitles) as $title) {
		$sqlTitle = addcslashes($title, '%_');
		$sql = "SELECT id FROM {$db->ItemRelationsProperty} WHERE label='".$sqlTitle."'";
		$titleId	 = $db->fetchOne($sql);
		if ($titleId) { $relationshipTitles[$title] = $titleId;  }
	}

	print_r($relationshipTitles);

	// -----------------------------------------------
	// Find title element ID (usually 50)
	// -----------------------------------------------

	$titleElementID = false; // Sanity
	$sql = "SELECT id FROM {$db->Elements} WHERE name='".titleElementText."'";
	$titleElementID = $db->fetchOne($sql);
	if (!$titleElementID) { die("Element ID '".titleElementText."' not found."); }
	echo titleElementText . " / titleElementID = $titleElementID\n";

	// -----------------------------------------------
	// Load CSV file into array
	// -----------------------------------------------

	$csv=array();
	$file = fopen('amsterdam_front_8_skaliert_edited_01.csv', 'r');
	while (($line = fgetcsv($file, 0, ",")) !== FALSE) { if ($line) { $csv[]=$line; } }
	fclose($file);
	if (!$csv) { die("CSV file error."); }

	$csv = array_slice($csv, 0, 100); // +#+#+# DEBUG

	$headers=array_flip($csv[0]); // Store Headers array for later use ...
	unset($csv[0]); // ... but remove them from the array
	print_r($headers);

	usort($csv, function ($a, $b) {
		global $headers;

		$a_ = substr_count( $a[ $headers["ShortName"] ], "_" );
		$b_ = substr_count( $b[ $headers["ShortName"] ], "_" );
    if ($a_ == $b_) {
      return 0;
    }
    return ($a_ < $b_) ? -1 : 1;
	});

	// print_r($csv);

	/* */
	$alreadyCreated = array();

	foreach($csv as $line) {

		$shortName = $line[ $headers["ShortName"] ];
		$alreadyCreated[ $shortName ] = true;

		$link = $line[ $headers["Link"] ];
		if ( ($link) and (!$alreadyCreated[ $link ]) ) {
			die("*** Unknown reference: $link\n");
		}

		$importType = substr_count($shortName, "_");

		echo
			$importType . " = " . $importItemTypes[$importType]["name"] .
			": ".
			implode(", ", array_slice($line, $headers["Object"], 8)).
			"\n"
		;
	}
	/* */

	die();

	// -----------------------------------------------

	foreach($csv as $line) {
		# print_r($line);

		# +#+#+#+# From here down: old code for Michael's blocks --
		# which is absolutely unusable here.

		# Concept:

		# 0. Don't import "RhAMS", but calculate its item ID -- which is #1183 --
		#			and store it as the initial value in the name/id array
		# ... but then import all others.

		# 1. Construct title from ShortName, Longname, and Name
		# 2. Determine the object item type from
		#			$importType = substr_count($shortName, "_");
		#			($importItemTypes[$importType]["name"])
		#			== $importItemTypes[$importType]["id"]
		# 3. If import item type == 4 == "Sandstein-Element":
		# 		Construct measurements vom Length, Height, and Depths
		# 4. _NOW_ import the item and store
		# 5. Determine the freshly imported ItemID -- and store it together with the ShortName in the name/ID array
		# 6. Figure out the linked item ID from the stored item IDs array (starting with "RhAMS" / 1183)
		# 7. That ID should be found in the name/ID array
		# 8. Create that link -- together with importTargetRelationTitle / $relationshipTitles[importTargetRelationTitle]

		/* * /
		$titel = $line[$headers["Stein-Titel"]];
		$l1 = $line[$headers["Dimension 1"]];
		$l2 = $line[$headers["Dimension 2"]];
		$l3 = $line[$headers["Dimension 3"]];
		$unit = $line[$headers["Einheit"]];
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
					$matches[1] * 2 * 12
					+ $matches[2] * 12
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
			"u" => "$unit (1-2-12)",
			"l1" => $l1, "l2" => $l2, "l3" => $l3,
			"f1" => array("","","",""), "f2" => array("","","",""), "f3" => array("","","",""), "v" => array("","","",""),
			"n" => $num,
		);

		$l1 = $measurements["l1"][0];
		$l2 = $measurements["l2"][0];
		$l3 = $measurements["l3"][0];

		$measurements["l1d"] = $measurements["l1"];
		$measurements["l2d"] = $measurements["l2"];
		$measurements["l3d"] = $measurements["l3"];
		$measurements["f1d"] = array($l1 * $l2, 0, 0, 0);
		$measurements["f2d"] = array($l1 * $l3, 0, 0, 0);
		$measurements["f3d"] = array($l2 * $l3, 0, 0, 0);
		$measurements["vd"] = array($l1 * $l2 * $l3, 0, 0, 0);

		foreach(array("f1d" => 2, "f2d" => 2, "f3d" => 2, "vd" => 3) as $comp => $exp) {
			$fact3 = pow(2, $exp);
			$fact2 = pow(12, $exp);
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

		$anmerkungen = trim($line[$headers["Anmerkung"]]);
		$charge = trim($line[$headers["Charge/ Position"]]);

		// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
		$metaData = array("item_type_id" => $importItemTypeID); // +#+#+#+#
		$elementTexts = array(
			'Dublin Core' => array(
				titleElementText => array( array('text' => $titel, 'html' => false) ),
			),
			'Item Type Metadata' => array(
				measurementElementText => array( array('text' => $measurements, 'html' => false) ),
				"Anmerkungen" => array( array('text' => $anmerkungen, 'html' => false) ),
				"Charge / Position" => array( array('text' => $charge, 'html' => false) ),
				"Sandstein-Produkt" => array( array('text' => "Halbfertigprodukt", 'html' => false) ),
				"Halbfertigprodukt" => array( array('text' => "Blockstein", 'html' => false) ),
			)
		);
		print_r($elementTexts);

		// $itemId = 0; // Default value for debug
		$item = insert_item($metaData, $elementTexts);
		$itemId = $item["id"];
		echo "New Item: $itemId\n";

		$values = array();

		$zielObjekte = @$line[$headers["Zielobjekt"]]; // Zielobjekte (target objects) and ...
		$zielObjekte = ( $zielObjekte ? explode(";", $zielObjekte) : array() );

		foreach($zielObjekte as $zielObjekt) {
			echo "Ziel: $zielObjekt\n";
			$zielId = $relationTargets[$zielObjekt];
			$relationshipId = $relationshipTitles[importTargetRelationTitle];
			if (($zielId) and ($relationshipId) ){ $values[] = "$itemId,$relationshipId,$zielId"; }
		}

		$transaktionen = @$line[$headers["Transaktion"]]; // ... and Transaktionen (transactions)
		$transaktionen = ( $transaktionen ? explode(";", $zielObjekte) : array() );

		foreach($transaktionen as $transaktion) {
			echo "Transaktion: $transaktion\n";
			$transaktionId = $relationTargets[$transaktion];
			$relationshipId = $relationshipTitles[importBelongsRelationTitle];
			if (($transaktionId) and ($relationshipId) ){ $values[] = "$itemId,$relationshipId,$transaktionId"; }
		}

		if ($values) {
			$valuesVerb = "(" . implode("),(", $values) . ")";
			echo "$valuesVerb\n";

			$sql="
				INSERT INTO {$db->ItemRelationsRelations}
					(subject_item_id, property_id, object_item_id)
					VALUES $valuesVerb
			";
			$db->query($sql);

			// update_item($itemId);
			// ... unnecessary, as we don't add comments that need to go into the search index

		}

		/* */

	}

?>
