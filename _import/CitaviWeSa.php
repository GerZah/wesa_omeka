<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Literatur / Online-Referenz");

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

	$file = fopen('CitaviWeSa_4.csv', 'r');
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

			$autor = $line[$headers["Autor, Herausgeber oder Institution"]];
			$titel = $line[$headers["Titel"]];
			$untertitel = $line[$headers["Untertitel"]];
			$jahr = $line[$headers["Jahr ermittelt"]];
			$url = trim($line[$headers["URL"]]);

			$litTyp = ( $url ? "Online-Referenz" : "Gedruckte Ausgabe" );

			$kurztitel = shortenString($autor, 20)." / " . $jahr . " (" . shortenString("$titel - $untertitel", 30) . ")";

			// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
			$metaData = array("item_type_id" => $importItemTypeID);
			$elementTexts = array(
				'Dublin Core' => array(
  				# 'Creator' => array( array('text' => $autor, 'html' => false) ),
  				'Title' => array( array('text' => $kurztitel, 'html' => false),
														array('text' => $titel, 'html' => false),
														array('text' => $untertitel, 'html' => false) ),
				),
				'Item Type Metadata' => array(
  				'Autor, Herausgeber oder Institution' => array( array('text' => $autor, 'html' => false) ),
  				'Jahr ermittelt' => array( array('text' => $jahr, 'html' => false) ),
  				'Dokumententyp' => array( array('text' => $line[$headers["Dokumententyp"]], 'html' => false) ),
  				'Ort' => array( array('text' => $line[$headers["Ort"]], 'html' => false) ),
  				'Zeitschrift/Zeitung' => array( array('text' => $line[$headers["Zeitschrift/Zeitung"]], 'html' => false) ),
  				'Band' => array( array('text' => $line[$headers["Band"]], 'html' => false) ),
  				'Nummer' => array( array('text' => $line[$headers["Nummer"]], 'html' => false) ),
  				'Seiten von-bis' => array( array('text' => $line[$headers["Seiten von–bis"]], 'html' => false) ),
					'Literaturtyp' => array( array('text' => $litTyp, 'html' => false) ),
					'Online-Referenz' => array( array('text' => $url, 'html' => false) ),
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
