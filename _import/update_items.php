<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	ini_set('include_path', '.' . DIRECTORY_SEPARATOR . '..');

	// -----------------------------------------------

	// Bootstrap the Omeka application.
	require_once 'bootstrap.php';

	// Configure and initialize the application.
	$application = new Omeka_Application(APPLICATION_ENV);
	$application->initialize();
  $db = get_db(); // Database connection

	$sql = "SELECT id FROM $db->Items";
	$itemIds = $db->fetchAll($sql);

	// print_r($itemIds);

	foreach($itemIds as $itemId) {
		echo $itemId["id"]."\n";
		update_item($itemId["id"]);
	}
	# update omeka_search_texts set title="", text="" where record_type="Item"
?>
