<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Territorium");

	// -----------------------------------------------

	// Bootstrap the Omeka application.
	require_once 'bootstrap.php';

	// Configure and initialize the application.
	$application = new Omeka_Application(APPLICATION_ENV);
	$application->initialize();

	$db = get_db(); // Database connection

	// Get Items Types
	$sql = "SELECT id, name from {$db->Item_Types} ORDER BY id";
	$itemTypes = $db->fetchAll($sql);
	# print_r($itemTypes);

	$importItemTypeID = null;
	foreach($itemTypes as $itemType) {
		if ($itemType["name"] == importItemType) {
			$importItemTypeID = $itemType["id"];
			break;
		}
	}

	if (!$importItemTypeID) { die("Item type '".importItemType."' not found."); }

	// -----------------------------------------------
	
	#$csv = @array_map('str_getcsv', @file('CitaviWeSa_edited.csv'));

	$csv=array();

	$file = fopen('TerritoriumWeSa.csv', 'r');
	while (($line = fgetcsv($file)) !== FALSE) { if ($line) { $csv[]=$line; } }
	fclose($file);

	# print_r($csv);

	#if (!$csv) { die("CSV file error."); }

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

			$titel = $line[$headers["Titel"]];
			# $kommentar = $line[$headers["Kommentar"]];

			// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
			$metaData = array("item_type_id" => $importItemTypeID);
			$elementTexts = array(
				'Dublin Core' => array(
  				'Title' => array( array('text' => $titel, 'html' => false) ),
				),
				//'Item Type Metadata' => array(
  			//	'Anmerkungen' => array( array('text' => $kommentar, 'html' => false) ),
				//)
			);
			print_r($elementTexts);
			insert_item($metaData, $elementTexts);

		}
	}
?>
