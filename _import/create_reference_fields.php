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

	// --------------- Find Metadata element set ID

	$itemTypeMetaData = $db->fetchOne("SELECT id FROM $db->ElementSets".
																		" WHERE name='Item Type Metadata'");

	// --------------- Display all orphaned metadata fields

  $sql = "SELECT id, name".
          " FROM $db->Elements".
          " WHERE element_set_id = $itemTypeMetaData".
          " AND id NOT IN (".
          "   SELECT element_id".
          "   FROM $db->ItemTypesElements".
          " )".
          " ORDER BY name ASC";
  // echo "$sql\n";

  $elements = $db->fetchAll($sql);
	echo "Orphaned metadata fields (not references in item types):\n";
  print_r($elements);

	// --------------- Display all orphaned text fields

	$sql = "SELECT id, text".
          " FROM $db->ElementTexts".
          " WHERE element_id NOT IN (".
          "   SELECT id".
          "   FROM $db->Elements".
          " )";
  // echo "$sql\n";

  $elements = $db->fetchAll($sql);
	echo "Orphaned text fields (without corresponding elements):\n";
  print_r($elements);

	//  --------------- abort if command line switch is not given

	if ( !isset($_GET["proceed"]) ) {
    echo "\n".'Please add "?proceed" to the end of this URL to proceed.'."\n\n";
    die("Quitting ... done nothing yet.");
  }

	//  --------------- Delete all orphaned metadata fields

	$sql = "DELETE".
          " FROM $db->Elements".
          " WHERE element_set_id = $itemTypeMetaData".
          " AND id NOT IN (".
          "   SELECT element_id".
          "   FROM $db->ItemTypesElements".
          " )";
	$db->query($sql);

	$sql = "DELETE".
          " FROM $db->ElementTexts".
          " WHERE element_id NOT IN (".
          "   SELECT id".
          "   FROM $db->Elements".
          " )";
	$db->query($sql);

	//  --------------- Insert additional metadata fields

	$rows = array("Referenzen", "Orts-Referenzen", "Weg-Referenzen");

	foreach($rows as $weight => $row) {

		$sql = "INSERT INTO $db->Elements".
					" (element_set_id, name, description, comment) ".
					" VALUES ($itemTypeMetaData, '$row', '', '')";
		// echo "$sql\n";
		$result = $db->query($sql);

		$sql = "SELECT id FROM $db->Elements".
					" WHERE element_set_id = $itemTypeMetaData".
					" AND name = '$row'";
		// echo "$sql\n";
		$id = $db->fetchOne($sql);
		echo "ID: $id\n";
		if ($id) {
			// echo "hier\n";

			$order = 50 + $weight;

			$sql = "INSERT INTO `$db->ItemTypesElements`".
						" (`item_type_id`, `element_id`, `order`)".
						" (SELECT id, $id, $order FROM $db->ItemTypes)";
			echo "$sql\n";
			$db->query($sql);

		}


	}



?>
