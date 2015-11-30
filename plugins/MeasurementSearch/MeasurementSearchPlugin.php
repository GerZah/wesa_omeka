<?php

# Let's assume that a measurement contains out of a triple of three
# numerical value, from 0 to 9999 -- i.e. from 0000 to 9999.
define('MEASUREMENTSEARCH_NUM_MAXLEN', 4);
# A unit name should be no longer than 10 characters
define('MEASUREMENTSEARCH_UNIT_MAXLEN', 10);

/**
* MeasurementSearch plugin.
*
* @package Omeka\Plugins\MeasurementSearch
*/
class MeasurementSearchPlugin extends Omeka_Plugin_AbstractPlugin {

  protected $_hooks = array(
		'initialize', # tap into i18n
		'install', # create additional table and batch-preprocess existing items for measurement
		'uninstall', # delete table
		'config_form', # prepare and display configuration form
		'config', # store config settings in the database
		'after_save_item', # preprocess saved item for ranges
		'after_delete_item', # delete deleted item's preprocessed ranges
		# 'admin_items_search', # add a time search field to the advanced search panel in admin
		# 'public_items_search', # add a time search field to the advanced search panel in public
		# 'admin_items_show_sidebar', # Debug output of stored numbers/ranges in item's sidebar (if activated)
		# 'items_browse_sql', # filter for a range after search page submission.
	);

  protected $_options = array(
		'measurement_search_units' => '',
		'measurement_search_search_all_fields' => 1,
		'measurement_search_limit_fields' => "[]",
		'measurement_search_search_rel_comments' => 1,
		'measurement_search_debug_output' => 0,
	);

  /**
	 * Add the translations.
	 */
	public function hookInitialize() {
		add_translation_source(dirname(__FILE__) . '/languages');
	}

  /**
	 * Install the plugin.
	 */
	public function hookInstall() {
		# Create table
		$db = get_db();

    $numLen = MEASUREMENTSEARCH_NUM_MAXLEN;
    $unitLen = MEASUREMENTSEARCH_UNIT_MAXLEN;

		$sql = "
		CREATE TABLE IF NOT EXISTS `$db->MeasurementSearchValues` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`item_id` int(10) unsigned NOT NULL REFERENCES `$db->Item`,
				`height` varchar($numLen) NOT NULL,
				`width` varchar($numLen) NOT NULL,
        `depth` varchar($numLen) NOT NULL,
				`unit` varchar($unitLen) NOT NULL,
				PRIMARY KEY (`id`),
				INDEX (unit)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$db->query($sql);

		SELF::_installOptions();

		# SELF::_batchProcessExistingItems();
	}

	/**
	 * Uninstall the plugin.
	 */
	public function hookUninstall() {
		$db = get_db();

		# Drop the table
		$sql = "DROP TABLE IF EXISTS `$db->MeasurementSearchValues`";
		$db->query($sql);

		SELF::_uninstallOptions();
	}

  /**
	 * Display the plugin configuration form.
	 */
	public static function hookConfigForm() {
		$measurementSearchUnits = SELF::_prepareUnitsFromJsonForEdit();
		# echo "<pre>$measurementSearchUnits</pre>"; die();

		$searchAllFields = (int)(boolean) get_option('measurement_search_search_all_fields');

		$db = get_db();
		$sql = "select id, name from `$db->Elements` order by name asc";
		$elements = $db->fetchAll($sql);

		$searchElements = array();
		foreach($elements as $element) { $searchElements[$element["id"]] = $element["name"]; }

		$LimitFields = get_option('measurement_search_limit_fields');
		$LimitFields = ( $LimitFields ? json_decode($LimitFields) : array() );

		$withRelComments=SELF::_withRelComments();
		$searchRelComments = (int)(boolean) get_option('measurement_search_search_rel_comments');

		$debugOutput = (int)(boolean) get_option('measurement_search_debug_output'); # comment line to remove debug output panel

		require dirname(__FILE__) . '/config_form.php';

		# SELF::_constructRegEx(); // +#+#+# DEBUG
	}

  /**
	 * Handle the plugin configuration form.
	 */
	public static function hookConfig() {
		// Unit configuration
		$measurementSearchUnits = SELF::_encodeUnitsFromTextArea($_POST['measurement_search_units']);
		set_option('measurement_search_units', $measurementSearchUnits );

		// Search All Fields switch
		$searchAllFields = (int)(boolean) $_POST['measurement_search_search_all_fields'];
		set_option('measurement_search_search_all_fields', $searchAllFields);

		// Limit Fields list (in case "Search All Fields" is false
		$limitFields = array();
		$postIds=false;
		if (isset($_POST["measurement_search_limit_fields"])) { $postIds = $_POST["measurement_search_limit_fields"]; }
		if (is_array($postIds)) {
			foreach($postIds as $postId) {
				$postId = intval($postId);
				if ($postId) { $limitFields[] = $postId; }
			}
		}
		sort($limitFields);
		$limitFields = json_encode($limitFields);
		set_option('measurement_search_limit_fields', $limitFields);

		// Search Relationship Comments switch
		$searchRelComments = (int)(boolean) $_POST['measurement_search_search_rel_comments'];
		set_option('measurement_search_search_rel_comments', $searchRelComments);

		// Debug Output switch -- if present
		$debugOutput = 0; // Sanity
		if (isset($_POST['measurement_search_debug_output'])) {
			$debugOutput = (int)(boolean) $_POST['measurement_search_debug_output'];
		}
		set_option('measurement_search_debug_output', $debugOutput);

		$reprocess = (int)(boolean) $_POST['measurement_search_trigger_reindex'];
		if ($reprocess) { SELF::_batchProcessExistingItems(); }
		# echo "<pre>"; print_r($_POST); echo "</pre>"; die();
	}

  /**
	 * Fetch JSON array from DB option as a PHP array
	 */
	private function _fetchUnitArray() {
		$json = get_option('measurement_search_units');
		$json = ( $json ? $json : "[]" );
		return json_decode($json);
	}

	/**
	 * Transform unit array to be edited in textarea on config page
	 */
	private function _prepareUnitsFromJsonForEdit() {
		$arr = SELF::_fetchUnitArray();
		return ( $arr ? implode("\n", $arr) : "" );
	}

  /**
  * Transform plausible entries from units array for use in RegEx
  */
  private function _decodeUnitsForRegEx() {
    $result = array();

    $arr = SELF::_fetchUnitArray();
    if ($arr) {
      foreach($arr as $unit) {
        if ( $sanUnit = preg_quote(trim($unit)) ) { $result[] = $sanUnit; }
      }
    }

    return $result;
  }

	/**
	 * Encode content of textarea on config page to be stored as a JSON array in DB option
	 */
	private function _encodeUnitsFromTextArea($textArea) {
		$textArea = str_replace(chr(10), chr(13), $textArea);
		$textArea = str_replace(chr(13).chr(13), chr(13), $textArea);
		$textArea = stripslashes($textArea);

		$lines = explode(chr(13), $textArea);
		$nonEmptyLines = array();
		foreach($lines as $line) {
			$line = trim($line);
			$line = substr($line, 0, 20);
			if ($line) { $nonEmptyLines[]=$line; }
		}

		return json_encode($nonEmptyLines);
	}

	/**
	 * Preprocess ALL existing items which could be rather EVIL in huge installations
	 */
	private function _batchProcessExistingItems() {
		$db = get_db();
		$sql= "select id from `$db->Items`";
		$items = $db->fetchAll($sql);
		foreach($items as $item) { SELF::_preProcessItem($item["id"]); }
	}

	/**
	 * Preprocess numbers after saving an item add/edit form.
	 *
	 * @param array $args
	 */
	public function hookAfterSaveItem($args) {
			if ( (!$args['post']) and (!$args['insert']) ) {
					return;
			}

			$item_id = intval($args["record"]["id"]);
			if ($item_id) { SELF::_preProcessItem($item_id); }

			# die("After Save Item");

	} # hookAfterSaveItem()

	/**
	 * Delete pre-processed numbers after an item has been deleted
	 *
	 * @param array $args
	 */
	public function hookAfterDeleteItem($args) {
			$db = get_db();

			$item_id = intval($args["record"]["id"]);

			if ($item_id) {
				$sql = "delete from `$db->MeasurementSearchValues` where item_id=$item_id";
				$db->query($sql);
			}

			# echo "<pre>After Delete Item - ID: $item_id\nSQL: $sql\n"; print_r($args); die("</pre>");
	} # hookAfterDeleteItem()

	/**
	 * Determine if Item Relations is installed, and if it's patched to feature relationship comments
	 */
	private function _withRelComments() {
		$db = get_db();

		$withRelComments=false;
		$sql = "show columns from `$db->ItemRelationsRelations` where field='relation_comment'";
		try { $withRelComments = ($db->fetchOne($sql) !== false); }
		catch (Exception $e) { $withRelComments=false; }

		return $withRelComments;
	}

	/**
	 * Get an item's relationship comment text
	 */
	private function _relationshipCommentText($item_id) {
		$db = get_db();
		$text = "";

		# Check if we could add relation comments in case Item Relations is installed and has been patched
		# to feature relation comments.
		$withRelComments=SELF::_withRelComments();

		if ($withRelComments) {
			$sql = "select relation_comment from `$db->ItemRelationsRelations` where subject_item_id=$item_id";
			$comments = $db->fetchAll($sql);
			if ($comments) {
				foreach($comments as $comment) { $text .= " ".$comment["relation_comment"]; }
			}
		}

		return $text;
	}

	/**
	 * Pre-process one item's textual data and store measurements in MeasurementSearchValues table
	 */
  private function _preProcessItem($item_id) {
    $db = get_db();

    if ($item_id) {
      $sql = "delete from `$db->MeasurementSearchValues` where item_id=$item_id";
      $db->query($sql);

      $text = false;

      $searchAllFields = (int)(boolean) get_option('measurement_search_search_all_fields');

      if ($searchAllFields) {
        $text = $db->fetchOne("select text from `$db->SearchTexts` where record_type='Item' and record_id=$item_id");
        $text = ( $text ? $text : "" );

        $text .= SELF::_relationshipCommentText($item_id);
        $text = ( $text ? $text : false );
      } # if ($searchAllFields)

      else { # !$searchAllFields

        $limitFields = get_option('measurement_search_limit_fields');
        $limitFields = ( $limitFields ? json_decode($limitFields) : array() );

        $elementIds=array();
        if (is_array($limitFields)) {
          foreach($limitFields as $limitField) {
            $limitField = intval($limitField);
            if ($limitField) { $elementIds[] = $limitField; }
          }
          sort($elementIds);
        }

        if ($elementIds) {
          $elementIds = "(" . implode(",", $elementIds) . ")";

          $elementTexts = $db -> fetchAll("select text from `$db->ElementTexts`".
                                          " where record_id=$item_id".
                                          " and element_id in $elementIds");
          if ($elementTexts) {
            $text = "";
            foreach($elementTexts as $elementText) { $text .= " " . $elementText["text"]; }
          } # if ($elementTexts)
        } # if ($elementIds)

        $searchRelComments = (int)(boolean) get_option('measurement_search_search_rel_comments');

        if ($searchRelComments) {
          $text = ( $text ? $text : "" );
          $text .= SELF::_relationshipCommentText($item_id);
          $text = ( $text ? $text : false );
        }

      }  # !$searchAllFields

      if ($text !== false) {

        //  $cookedRanges = SELF::_processRangeText($text);
        //  # echo "<pre>"; print_r($cookedRanges); die("</pre>");
        //
        //  if ($cookedRanges) {
        //
        //    $values = array();
        //    foreach($cookedRanges as $cookedRange) {
        //      SELF::_swapIfNecessary($cookedRange[0], $cookedRange[1]);
        //      $values[]='('.$item_id.',"'.$cookedRange[0].'","'.$cookedRange[1].'","'.$cookedRange[2].'")';
        //    }
        //    $values = implode(", ", $values);
        //
        //    $sql = "insert into `$db->RangeSearchValues` (item_id, fromnum, tonum, unit) values $values";
        //    $db->query($sql);
        //    # die($sql);
        //
        //  } # if ($cookedDates)

        $cookedMeasurements = SELF::_processMeasurementText($text);
        # +#+#+#
        // echo "<pre>" . print_r($cookedMeasurements,true) . "</pre>";
        // die();

      } # if ($text)
    } # if ($item_id)
  } #  _preProcessItem()

  /**
	 * Main regex processing to extract measurements, to be able to store them later
	 */
	private function _processMeasurementText($text) {
		$regEx = SELF::_constructRegEx();
		# echo "<pre>$text\n" . print_r($regEx,true) . "</pre>";
		foreach($regEx as $key => $val) { $$key = $val; }

		$allCount = preg_match_all( "($fullRegEx)i", $text, $allMatches);
		# echo "<pre>Count: $allCount\n" . print_r($allMatches,true) . "</pre>";

		$cookedMeasuerements = array();
		foreach(array_keys($allMatches[0]) as $singleIdx) {

      # echo "<pre>" . print_r($allMatches[0][$singleIdx],true) . "</pre>";

      $cookedMeasuerement = array();
      for($i=1 ; $i<=4 ; $i++) {
        $cookedMeasuerement[] = $allMatches[$i][$singleIdx];
      }
      $cookedMeasuerements[] = $cookedMeasuerement;

		}
		# echo "<pre>" . print_r($cookedMeasuerements,true) . "</pre>"; die();

		return $cookedMeasuerements;
	}

  /**
	 * Create the necessary regEx expressions to deal with 123x456x789 measurements
	 */
	private function _constructRegEx() {

    $oneNumber = "\d{1,".MEASUREMENTSEARCH_NUM_MAXLEN."}";
    $mainTriple = "($oneNumber)x($oneNumber)x($oneNumber)";

    $fullRegEx = $mainTriple;

    $DBunits = SELF::_decodeUnitsForRegEx();
    if ($DBunits) { $fullRegEx .= "\s(" . implode("|", $DBunits) . ")"; }

    $result = array(
      "oneNumber" => $oneNumber,
      "mainTriple" => $mainTriple,
      "fullRegEx" => $fullRegEx,
    );

    // echo "<pre>\n\n\n\n\n</pre>";
    // echo "<pre>" . print_r($result, true) . "</pre>";

    return $result;
  }

} # class
