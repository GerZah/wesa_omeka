<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Quelle");

	// -----------------------------------------------

	ini_set('include_path', '.' . DIRECTORY_SEPARATOR . '..');

	// -----------------------------------------------

	// Bootstrap the Omeka application.
	require_once 'bootstrap.php';

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

	$file = fopen('ChronologieWeSa.csv', 'r');
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

			$datum = $line[$headers["Datum"]];
			$datum = preg_replace("/(\[(?:J|G)\])(\d{4})(\d{2})(\d{2})/", "$1 $2-$3-$4", $datum);

			$ereignis = $line[$headers["Ereignis"]];

			$fundort = $line[$headers["Fundort"]];
			switch ($fundort) {
				case "ELO" : $fundort = "ELO (Erfgoed Leiden en Omstreken)"; break;
				case "StAB" : $fundort = "StAB (Staatsarchiv Bremen)"; break;
			}

			/*
			StAB (Staatsarchiv Bremen)
			NL-HaNA [Nationaal Archief Den Haag)
			SAA (Stadsarchief Amsterdam)
			ELO (Erfgoed Leiden en Omstreken)
			NLA-Bückeburg (Niedersächsisches Landesarchiv, Abteilung Bückeburg)
			HCO (Historisch Centrum Overijssel)
			FA Bur (Fürstliches Archiv Burgsteinfurt)
			Landesamt für Denkmalpflege Bremen
			*/

			$signatur = $line[$headers["Inventarnr/Signatur"]];
			$folio = $line[$headers["Folio"]];

			$personen = $line[$headers["Personen"]];
			$transkription = $line[$headers["Transkription"]];

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
															array('text' => $transkription, 'html' => false),
														),
				)
			);
			print_r($elementTexts);
			insert_item($metaData, $elementTexts);

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
