<?php
	// -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------

	define("importItemType", "Akteur");

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

	if ( !isset($_GET["proceed"]) ) {
    echo 'Please add "?proceed" to the end of this URL to proceed.'."\n\n";
    die("Quitting ... done nothing yet.");
  }

	// -----------------------------------------------

	$csv=array();

	$file = fopen('ProsopographieWeSa5.csv', 'r');
	while (($line = fgetcsv($file)) !== FALSE) { if ($line) { $csv[]=$line; } }
	fclose($file);

	print_r($csv);

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

			$name = $line[$headers["Name"]];
			$andereSchreibweisen = $line[$headers["andere Schreibweisen"]];
			$geburtszeitpunkt = @$line[$headers["Geburtszeitpunkt"]];
			$sterbezeitpunkt = @$line[$headers["Sterbezeitpunkt"]];
			$kommentar = $line[$headers["Kommentar"]];
			$kommentar2 = @$line[$headers["Kommentar 2"]];

			// Process main name plus alternative spellings
			$namen = array( trim($name) );
			if ($andereSchreibweisen) {
				$altNamen = explode(";", $andereSchreibweisen);
				foreach($altNamen as $altName) { $namen[] = trim($altName); }
			}
			$title = array();
			foreach($namen as $name) {
				$title[] = array('text' => $name, 'html' => false);
			}

			// process metadata

			$metadata = array();

			// process date of birth / date of death and create a lifespan
			$lebenszeit = "";
			if ( ($geburtszeitpunkt) or ($sterbezeitpunkt) ) {
				if (!$sterbezeitpunkt) { $lebenszeit = "[G] $geburtszeitpunkt -"; }
				elseif (!$geburtszeitpunkt) { $lebenszeit = "- [G] $sterbezeitpunkt"; }
				else { $lebenszeit = "[G] $geburtszeitpunkt - $sterbezeitpunkt"; }
			}
			if ($lebenszeit) { $metadata['Lebenszeit'] = array( array('text' => $lebenszeit, 'html' => false) ); }

			// process comment
			$anmerkungen = "";
			if ($kommentar) {
				$year = "\d{4}";
				$anmerkungen = preg_replace( "/($year)/", "[G] $1", $kommentar );
				$anmerkungen = str_replace("[G][G]", "[G]", $anmerkungen);
			}
			if ($anmerkungen) {
				$anmerkungenData = array();
				$anmerkungenData[] = array('text' => $anmerkungen, 'html' => false);
				if ($kommentar2) {
					$anmerkungenData[] = array('text' => $kommentar2, 'html' => false);
				}
				$metadata['Anmerkungen'] = $anmerkungenData;
			}

			// http://omeka.readthedocs.org/en/eb3/Reference/libraries/globals/insert_item.html
			$metaData = array("item_type_id" => $importItemTypeID);
			$elementTexts = array( 'Dublin Core' => array( 'Title' => $title ) );
			if ($metadata) { $elementTexts['Item Type Metadata'] = $metadata; }
			print_r($elementTexts);
			insert_item($metaData, $elementTexts);

		}
	}
?>
