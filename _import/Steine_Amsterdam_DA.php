<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	ini_set('max_execution_time', 300); // 300 seconds == 5 minutes

	// -----------------------------------------------

	define("importItemType", "Sandstein-Element");
	define("importTargetRelationTitle", "ist verbaut an Gebäude oder Gebäudeteil");
	define("importBelongsRelationTitle", "gehört zu");
	define("titleElementText", "Title");
	define("measurementElementText", "Maße");

	$importItemTypes = array(
		1 => array( "name" => "Sandstein-Element" ),
		2 => array( "name" => "Sandstein-Bauteil" ),
		3 => array( "name" => "Sandstein-Baugruppe" ),
		4 => array( "name" => "Gebäudeteil" ),
		5 => array( "name" => "Gebäudesegment" ),
		6 => array( "name" => "Gebäude / Bauwerk" ),
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
	$file = fopen('amsterdam_front_9_skaliert_edited_02a.csv', 'r');
	if (!$file) { die("File error."); }
	while (($line = fgetcsv($file, 0, ",")) !== FALSE) { if ($line) { $csv[]=$line; } }
	fclose($file);
	if (!$csv) { die("CSV file error."); }

	$headers=array_flip($csv[0]); // Store Headers array for later use ...
	unset($csv[0]); // ... but remove them from the array
	print_r($headers);

	usort($csv, function ($a, $b) {
		global $headers;

		// Sort by Hierarchy, but in descending order
		$a_ = -$a[ $headers["Hierarchy"] ];
		$b_ = -$b[ $headers["Hierarchy"] ];
    if ($a_ == $b_) {
      return 0;
    }
    return ($a_ < $b_) ? -1 : 1;
	});

	// $csv = array_slice($csv, 0, 100); # +#+#+# DEBUG
	// print_r($csv);

	$alreadyCreated = array();

	/* * / // alreadyCreated simulation (check if sorted sequence will work)
	foreach($csv as $line) {

		$shortName = $line[ $headers["ShortName"] ];
		$alreadyCreated[ $shortName ] = true;

		$importType = $line[ $headers["Hierarchy"] ];
		$importItemType = $importItemTypes[$importType];

		echo
			$importType . " = " . $importItemType["name"] .
			": ".
			implode(", ", array_slice($line, $headers["Object"], 8)).
			"\n"
		;

		$link = $line[ $headers["Link"] ];
		if ( ($link) and (!@$alreadyCreated[ $link ]) ) {
			echo "*** Unknown reference: $link. (Exiting.)\n"; die();
		}

	}
	print_r($alreadyCreated);
	die();
	/* */

	// -----------------------------------------------

	foreach($csv as $line) {
		// print_r($line);

		# Concept:

		# 0. Don't import "RhAMS", but calculate its item ID -- which is #1183 --
		#			and store it as the initial value in the name/id array
		# ... but then import all others.

		# 1. Construct title from ShortName, Longname, and Name
		# 2. Determine the object item type $importType from (NEW) "Hierarchy" column
		#			($importItemTypes[$importType]["name"])
		#			== $importItemTypes[$importType]["id"]
		# 3. If import item type == 4 == "Sandstein-Element":
		# 		Construct measurements vom Length, Height, and Depths
		# 4. _NOW_ import the item and store
		# 5. Determine the freshly imported ItemID -- and store it together with the ShortName in the name/ID array
		# 6. Figure out the linked item ID from the stored item IDs array (starting with "RhAMS" / 1183)
		# 7. That ID should be found in the name/ID array
		# 8. Create that link -- together with importTargetRelationTitle / $relationshipTitles[importTargetRelationTitle]

		$shortName = $line[$headers["ShortName"]];

		if ($shortName == "RhAMS") {
			# Step 0: Don't import "RhAMS", but calculate its item ID -- which is #1183
			$sql = "
				SELECT record_id FROM `$db->ElementTexts`
				WHERE text='Amsterdam, Rathaus' AND element_id = $titleElementID
			";
			$rhAmsId = $db->fetchOne($sql);
			if ($rhAmsId) { $alreadyCreated[$shortName] = $rhAmsId; }
			else { die("*** '$shortName' not found in DB. (Exiting.)"); }
			// echo "$sql\n";
			// echo $rhAmsId;
			// die();
		}

		else {

			# Step 1: Construct title (array) from ShortName, LongName, and Name
			$titles = array(
				array('text' => $shortName, 'html' => false),
				array('text' => $line[$headers["Longname"]], 'html' => false),
				array('text' => $line[$headers["Name"]], 'html' => false),
			);
			// print_r($titles);

			# Step 2: Determine object item type
			$importType = $line[ $headers["Hierarchy"] ];
			$importItemType = $importItemTypes[$importType];
			// print_r($importItemType);

			$anmerkungen = $line[ $headers["ID"] ];

			$itemTypeMetaData = array(
				"Anmerkungen" => array( array('text' => $anmerkungen, 'html' => false) )
			);

			echo "------- $anmerkungen - ".$importItemType["name"].": $shortName\n";

			# Step 3: "Sandstein-Element"? Gather dimensions and calculate derived values
			$unit = $num = null;
			$l1 = $l2 = $l3 = 0;

			$hasMeasurements = (
				($line[$headers["Length"]] != "0,00") and
				($line[$headers["Height"]] != "0,00") and
				($line[$headers["Depth"]] != "0,00")
			);

			if ($hasMeasurements) {
				$unit = "m-cm-mm"; // fixed for all entries
				$num = 1; // fixed -- one of each

				$l1 = floatval(str_replace(",", ".", $line[$headers["Length"]]));
				$l2 = floatval(str_replace(",", ".", $line[$headers["Height"]]));
				$l3 = floatval(str_replace(",", ".", $line[$headers["Depth"]]));

				foreach(array("l1", "l2", "l3") as $var) {
					$tupel = array(0,0,0,0);
					if ($$var>=1) {
						$tupel[1] = intval(floor($$var)); // m
						$$var = $$var - $tupel[1];
					}
					$$var = $$var * 100; // cm
					if ($$var>=1) {
						$tupel[2] = intval(floor($$var));
						$$var = $$var - $tupel[2];
					}
					$$var = $$var * 10; // mm
					if ($$var>=1) {
						$tupel[3] = intval(floor($$var));
						$$var = $$var - $tupel[3];
					}
					$tupel[0] = $tupel[1]*1000 + $tupel[2]*10 + $tupel[3];
					$$var = $tupel;
					// echo $var . " - " . print_r($$var,true) . "\n";
				}

				$measurements = array(
					"u" => "$unit (1-100-10)",
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
					$fact3 = pow(100, $exp);
					$fact2 = pow(10, $exp);
					$fact23 = $fact2*$fact3;
					$curr = $measurements[$comp][0];
					$measurements[$comp][1] = intval($curr / $fact23);
					$curr = $curr - $measurements[$comp][1]*$fact23;
					$measurements[$comp][2] = intval($curr / $fact2);
					$curr = $curr - $measurements[$comp][2]*$fact2;
					$measurements[$comp][3] = $curr;
				}

				// print_r($measurements);
				$measurements = json_encode($measurements);
				echo $measurements."\n";

				$itemTypeMetaData += array(
					measurementElementText => array( array('text' => $measurements, 'html' => false) ),
					// "Sandstein-Produkt" => array( array('text' => "Halbfertigprodukt", 'html' => false) ),
					// "Halbfertigprodukt" => array( array('text' => "Blockstein", 'html' => false) ),
				);

			}

			$dublinCore = array(
				titleElementText => $titles
			);

			// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
			$metaData = array("item_type_id" => $importItemType["id"]);

			$elementTexts = array(
				'Dublin Core' => $dublinCore,
				'Item Type Metadata' => $itemTypeMetaData,
			);

			// print_r($metaData);
			// print_r($elementTexts);

			# Step 4: Import item
			// $itemId = 0; // Default value for debug
			$item = insert_item($metaData, $elementTexts);

			# Step 5: Determine freshly imported itemId
			$itemId = $item["id"];
			echo "New Item: $itemId\n";

			$alreadyCreated[$shortName] = $itemId;

			$link = $line[$headers["Link"]];
			if ($link) {
				$linkId = $alreadyCreated[$link];
				if (!$linkId) {
					echo "*** Unknown reference: $link. (Exiting.)\n"; die();
				}
				else {

					$linkRelation = (
						$importItemType["name"] == "Sandstein-Element"
						? importTargetRelationTitle
						: importBelongsRelationTitle
					);
					$linkRelationId = $relationshipTitles[$linkRelation];
					echo "Relation '$linkRelation' ($linkRelationId) from $itemId towards $linkId\n";

					$sql="
						INSERT INTO {$db->ItemRelationsRelations}
							(subject_item_id, property_id, object_item_id)
							VALUES ($itemId, $linkRelationId, $linkId)
					";
					$db->query($sql);

				}
			}
		}
	}

	print_r($alreadyCreated);

?>
