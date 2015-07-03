<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("numDummyContent", 500);
	define("minTitelLength", 8);
	define("maxTitelLength", 32);

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
	$maxItemTypes = count($itemTypes)-1;

	print_r($itemTypes);

	// -----------------------------------------------

	for($i=0; $i < numDummyContent; $i++) {

		$randType = $itemTypes[rand(0, $maxItemTypes)]["id"];
		$randTitle = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),
										0, rand(minTitelLength, maxTitelLength)
									);
		echo "$randType / $randTitle\n";

		$metaData = array("item_type_id" => $randType);
		$elementTexts = array(
			'Dublin Core' => array(
 				'Title' => array( array('text' => $randTitle, 'html' => false) ),
			)
		);
		insert_item($metaData, $elementTexts);



	}

?>
