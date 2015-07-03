<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Literatur");

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
	
	$csv = array_map('str_getcsv', file('Test-Export.csv'));

	$headers=array(); // Sanity state

	$firstline=true;
	foreach($csv as $line) {

		if ($firstline) {
			$headers=array_flip($line);
			print_r($headers);

			$firstline=false;
		}

		else {

			// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
			$metaData = array("item_type_id" => $importItemTypeID);
			$elementTexts = array(
				'Dublin Core' => array(
  				// 'Title' => array( array('text' => $line[$headers["Titel"]], 'html' => false), array('text' => "foo", 'html' => false), ),
  				'Title' => array( array('text' => $line[$headers["Titel"]], 'html' => false) ),
  				'Description' => array( array('text' => $line[$headers["Untertitel"]], 'html' => false) ),
  				'Creator' => array( array('text' => $line[$headers["Autor, Herausgeber oder Institution"]], 'html' => false) ),
  				'Type' => array( array('text' => $line[$headers["Dokumententyp"]], 'html' => false) ),
  				'Date' => array( array('text' => $line[$headers["Jahr ermittelt"]], 'html' => false) ),
  				'Publisher' => array( array('text' => $line[$headers["Verlag"]], 'html' => false) ),
  				'Identifier' => array( array('text' => $line[$headers["ISBN"]], 'html' => false) ),
				)
			);
			print_r($elementTexts);
			insert_item($metaData, $elementTexts);

		}
	}

?>
