<?php

define('MEASUREMENT_UNIT_LEN', 200);
define('MEASUREMENT_TABLE_LEN', 20);

/**
* Measurements plugin.
*
* @package Omeka\Plugins\Measurements
*/
class MeasurementsPlugin extends Omeka_Plugin_AbstractPlugin {

  protected $_hooks = array(
    'initialize',
    'install',
    'uninstall',
    'upgrade',
    'config_form', # prepare and display configuration form
    'config', # store config settings in the database
    'admin_head',
    'after_save_item',
    'after_delete_item',
    'admin_items_search',
    'items_browse_sql',
    'public_items_search',
    'admin_items_show_sidebar',
	);

  # ----------------------------------------------------------------------------

  protected $_filters = array('admin_navigation_main');

  public function filterAdminNavigationMain($nav) {
    $nav[] = array('label' => __('Measurements Analysis'), 'uri' => url('measurements'));
    return $nav;
  }

  # ----------------------------------------------------------------------------

  protected $_options = array(
    'measurements_units' => '[]',
    'measurements_select' => '[]',
    'measurements_debug_output' => false
	);

  // One potential unit -- e.g. "[Group] abc-def-ghi (1-10-10)" or "abc-def-ghi (1-10-10)"
  // protected static $_saniUnitRegex = "^\W*(?:\[(\S+)\]\W+)?(\S+)-(\S+)-(\S+)\W+\(1-(\d+)-(\d+)\)\W*$";
  // New version: "[G] a-b-c (1-2-3) [12.34]" or "[G] a-b-c (1-2-3) [12,34]"
  protected static $_saniUnitRegex = "^\W*(?:\[(\S+)\]\W+)?(\S+)-(\S+)-(\S+)\W+\(1-(\d+)-(\d+)\)(?:\W+\[(\d+)(?:(?:\.|,)(\d+))?])?\W*$";

  protected static $_indices = array( "", "", "²", "³" );
  protected static $_editFields; // see _initEditFields()

  # ----------------------------------------------------------------------------

  private static $_saniUnits;
  private static $_measurementsElements;
  private static $_debugOutput;
  private static $_itemRelationsActive;

  # ----------------------------------------------------------------------------

  /**
	* Add the translations.
	*/
	public function hookInitialize() {
		add_translation_source(dirname(__FILE__) . '/languages');
    SELF::_initStatics();

    // Add filters
    $filter_names = array(
        'Display',
        'ElementInput',
    );

    $measurementElements = SELF::$_measurementsElements;

    $db = get_db();
    foreach($measurementElements as $element_id ) {
      $element = $db->getTable('Element')->find($element_id);
      $elementSet = $db->getTable('ElementSet')->find($element->element_set_id);
      foreach ($filter_names as $filter_name) {
        add_filter(
            array($filter_name, 'Item', $elementSet->name, $element->name),
            array($this, "filter$filter_name")
        );
      }
    }

    // Add ID filter to all (!) elements -- except those that are already filtered
    $idFilterInfix = "";
    if ($measurementElements) {
      $measurementElementsVerb = implode(",", $measurementElements);
      $idFilterInfix = "WHERE el.id NOT IN ($measurementElementsVerb)";
    }
    $sql = "
      SELECT es.name AS el_set, el.name AS el_name
      FROM `$db->Elements` el
      LEFT JOIN `$db->ElementSets` es ON el.element_set_id = es.id
      $idFilterInfix
    ";
    $elements = $db->fetchAll($sql);
    foreach($elements as $element) {
        add_filter(
            array("Display", 'Item', $element["el_set"], $element["el_name"]),
            array($this, "filterDisplayID")
        );
    }

	}

  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  private function _linkURL(&$text, $allMatches, $phpFileName) {
    foreach($allMatches as $match) {
      $url = public_url("/buildingmap/$phpFileName.php?highlights=".$match[0]);
      $text = str_replace(
        $match[0],
        "<a href='$url' target='_blank'>[" . $match[0] . "]</a>",
        $text
      );
    }
  }

  /**
  * content filter, checking for "RhAMS" IDs via RegEx and linking over to the SVG building map
  */
  public function filterDisplayID($text, $args) {
    $regEx = "ID1608011\d+_\d+"; // RhAMS building blocks
    if (preg_match_all("/$regEx/", $text, $allMatches)) { SELF::_linkUrl($text, $allMatches, "rhams"); }

    $regEx = "THL_FAS_ID\d+_\d+"; // Leiden Facade building blocks
    if (preg_match_all("/$regEx/", $text, $allMatches)) { SELF::_linkUrl($text, $allMatches, "leidenfassade"); }

    $regEx = "THL_TR_ID\d+_\d+"; // Leiden Stairs building blocks
    if (preg_match_all("/$regEx/", $text, $allMatches)) { SELF::_linkUrl($text, $allMatches, "leidentreppe"); }

    $regEx = "SchB_ID\d+_\d+"; // Bremen Schüttinbg building blocks
    if (preg_match_all("/$regEx/", $text, $allMatches)) { SELF::_linkUrl($text, $allMatches, "bremenschuetting"); }

    $regEx = "THAnt_ID\d+_\d+"; // RhAnt building blocks
    if (preg_match_all("/$regEx/", $text, $allMatches)) { SELF::_linkUrl($text, $allMatches, "antwerpen"); }

    $regEx = "GewB_ID\d+_\d+"; // Bremen Gewerbehaus building blocks
    if (preg_match_all("/$regEx/", $text, $allMatches)) { SELF::_linkUrl($text, $allMatches, "bremengewerbehaus"); }

    $regEx = "ID161213152328_\d+"; // Batavia Gate building blocks
    if (preg_match_all("/$regEx/", $text, $allMatches)) { SELF::_linkUrl($text, $allMatches, "batavia"); }

    return $text;
  }

  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  protected function _initEditFields() {
    SELF::$_editFields = array(
      array("l1",  __("Dimension") . " 1", 1),
      array("l2",  __("Dimension") . " 2", 1),
      array("l3",  __("Dimension") . " 3", 1),
      array("f1",  __("Face")   . " 1", 2),
      array("f2",  __("Face")   . " 2", 2),
      array("f3",  __("Face")   . " 3", 2),
      array("v",   __("Volume"), 3),
      array("l1d", __("Dimension") . " 1", 1),
      array("l2d", __("Dimension") . " 2", 1),
      array("l3d", __("Dimension") . " 3", 1),
      array("f1d", __("Face")   . " 1", 2),
      array("f2d", __("Face")   . " 2", 2),
      array("f3d", __("Face")   . " 3", 2),
      array("vd",  __("Volume"), 3),
    );
  }

  public function itemRelationsActive() { return SELF::$_itemRelationsActive; }

  /**
	* Initialize static variables (units, selected elements, etc.) so they can be used anywhere
	*/
  protected function _initStatics() {
    SELF::_initEditFields();
    SELF::$_saniUnits = SELF::_prepareSaniUnits( SELF::_getRawUnitsFromConfig() );
    SELF::$_measurementsElements = SELF::_retrieveMeasurementElements();
    SELF::$_debugOutput = (int)(boolean) get_option('measurements_debug_output');

    $db = get_db();
    $qu = "SELECT active FROM `$db->Plugins` WHERE name='ItemRelations'";
    SELF::$_itemRelationsActive = !!$db->fetchOne($qu);
  }

  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  /**
	* Read units from config -- i.e. array of text lines that was entered in config box
	*/
  protected function _getRawUnitsFromConfig() {
    $json = get_option("measurements_units");
    $json = ( $json ? $json : "[]" );
    return json_decode($json);
  }

  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  /**
	* Sanitize raw units and leave only those that were entered correctly, together with all detail data
	*/
  protected function _prepareSaniUnits($units) {
    // $units[] = "  abc-def-ghi (1-10-10)  "; // DEBUG Test Data
    // $units[] = "  m-cm-mm (1-100-10)  "; // DEBUG Test Data

    $saniUnits = array();

    $cnt = 0;

    foreach($units as $unit) {
      if (preg_match("/".SELF::$_saniUnitRegex."/", $unit, $matches)) {
        $group = $matches[1];
        // $group = ($group ? $group : __("[n/a]"));
        $saniUnit = array(
          "units" => array( $matches[2], $matches[3], $matches[4] ),
          "convs" => array( 1, intval($matches[5]), intval($matches[6]) ),
        );
        $saniUnit["verb"] = $saniUnit["units"][0]."-".$saniUnit["units"][1]."-".$saniUnit["units"][2]." ".
                            "(1-".$saniUnit["convs"][1]."-".$saniUnit["convs"][2].")";
        $saniUnit["mmconv"] = (
          $matches[4] == "mm"
          ? "1"
          : ( isset($matches[7])
            ? $matches[7] . (isset($matches[8]) ? ".".$matches[8] : ""  )
            : false
            )
        );
        // echo "<pre>" . print_r($matches,true) . "</pre>";
        // echo "<pre>" . print_r($saniUnit,true) . "</pre>";
        if (!is_array(@$saniUnits[$group])) { $saniUnits[$group] = array(); }
        $saniUnits[$group][$cnt++] = $saniUnit;
      }
    }

    ksort($saniUnits);
    if (@$saniUnits[""]) {
      $emptyKey = $saniUnits[""];
      unset($saniUnits[""]);
      $saniUnits[__("[n/a]")] = $emptyKey;
    }

    // echo "<pre>" . print_r($saniUnits,true) . "</pre>";
    // die();

    return $saniUnits;
  }

  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  /**
  * Retrieve the element IDs that are supposed to store measurements from JSON configuration variable
  */
  protected function _retrieveMeasurementElements() {
    $measurementElementsJson=get_option('measurements_select');
    if (!$measurementElementsJson) { $measurementElementsJson="null"; }
    $measurementElements = json_decode($measurementElementsJson,true);
    return $measurementElements;
  }

  # ----------------------------------------------------------------------------

  /**
	* Install the plugin.
	*/
	public function hookInstall() {
		SELF::_installOptions();
    SELF::_initEditFields(); // will fail to deliver i18n strings, which we won't need here

    $fields = array();
    foreach(SELF::$_editFields as $editField) {
      $key = $editField[0];
      $fields[] = "`$key` DOUBLE DEFAULT NULL,";
    }
    $fields = implode("\n", $fields);

    $db = get_db();
    $sql = "
      CREATE TABLE IF NOT EXISTS `$db->MeasurementsValues` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `item_id` int(10) unsigned NOT NULL REFERENCES `$db->Item`,
          $fields
          `unit` varchar(".MEASUREMENT_UNIT_LEN.") NOT NULL,
          `number` int(10) unsigned DEFAULT NULL,
          PRIMARY KEY (`id`),
          INDEX (unit)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ";
    $db->query($sql);

	}

  # ----------------------------------------------------------------------------

	/**
	* Uninstall the plugin.
	*/
	public function hookUninstall() {
		SELF::_uninstallOptions();

    $db = get_db();
		$db->query("DROP TABLE IF EXISTS `$db->MeasurementsValues`");
  }

  # ----------------------------------------------------------------------------

	/**
	* Upgrade the plugin.
	*/
  public function hookUpgrade($args) {
    if (version_compare($args['old_version'], '0.2', '<')) {
      $db = get_db();
      $qu="
        ALTER TABLE `$db->MeasurementsValues`
          MODIFY l1  DOUBLE NOT NULL,
          MODIFY l2  DOUBLE NOT NULL,
          MODIFY l3  DOUBLE NOT NULL,
          MODIFY f1  DOUBLE NOT NULL,
          MODIFY f2  DOUBLE NOT NULL,
          MODIFY f3  DOUBLE NOT NULL,
          MODIFY v   DOUBLE NOT NULL,
          MODIFY l1d DOUBLE NOT NULL,
          MODIFY l2d DOUBLE NOT NULL,
          MODIFY l3d DOUBLE NOT NULL,
          MODIFY f1d DOUBLE NOT NULL,
          MODIFY f2d DOUBLE NOT NULL,
          MODIFY f3d DOUBLE NOT NULL,
          MODIFY vd  DOUBLE NOT NULL
      ";
      $db->query($qu);
    }
    if (version_compare($args['old_version'], '0.3', '<')) {
      $db = get_db();
      $qu="ALTER TABLE `$db->MeasurementsValues` ADD `number` INT UNSIGNED DEFAULT NULL";
      $db->query($qu);
    }
  }


  # ----------------------------------------------------------------------------

  /**
	* Display the plugin configuration form.
	*/
	public static function hookConfigForm() {
    $measurementUnits = SELF::_getRawUnitsFromConfig();
    $measurementUnits = implode("\n", $measurementUnits);
    $saniUnits = SELF::$_saniUnits;

    $sqlDb = get_db();
    $select = "
      SELECT es.name AS element_set_name, e.id AS element_id,
      e.name AS element_name, it.name AS item_type_name
      FROM {$sqlDb->ElementSet} es
      JOIN {$sqlDb->Element} e ON es.id = e.element_set_id
      LEFT JOIN {$sqlDb->ItemTypesElements} ite ON e.id = ite.element_id
      LEFT JOIN {$sqlDb->ItemType} it ON ite.item_type_id = it.id
      WHERE es.id = 3
      ORDER BY it.name, e.name
    ";
    $records = $sqlDb->fetchAll($select);
    $elements = array();
    foreach ($records as $record) {
      $optGroup = $record['item_type_name']
                ? __('Item Type') . ': ' . $record['item_type_name']
                : $record['element_set_name'];
      $value = $record['element_name'];
      $elements[$optGroup][$record['element_id']] = $value;
    }

    $measurementElements = SELF::$_measurementsElements;
    $debugOutput = SELF::$_debugOutput; # comment line to remove debug output panel

		require dirname(__FILE__) . '/config_form.php';
	}

  # ----------------------------------------------------------------------------

  /**
	* Handle the plugin configuration form.
	*/
	public static function hookConfig() {
    $measurementUnits = @$_POST["measurements_units"];
    $measurementUnits = str_replace("\r", "\n", $measurementUnits);
    $measurementUnits = str_replace("\n\n", "\n", $measurementUnits);
    $measurementUnits = explode("\n", $measurementUnits);
    set_option("measurements_units", json_encode($measurementUnits));

    $measurementsSelect = array();
    $postIds=false;
    $postIds = @$_POST["measurements_elements"];
    if (is_array($postIds)) {
      foreach($postIds as $postId) {
        $postId = intval($postId);
        if ($postId) { $measurementsSelect[] = $postId; }
      }
    }
    $measurementsSelect = array_unique($measurementsSelect);
    $measurementsSelect = json_encode($measurementsSelect);
    set_option('measurements_select', $measurementsSelect);

    SELF::$_debugOutput = (int)(boolean) @$_POST['measurements_debug_output'];
    set_option('measurements_debug_output', SELF::$_debugOutput);

    $reprocess = (int)(boolean) $_POST['measurements_trigger_reindex'];
    if ($reprocess) {
      SELF::_initStatics();
      SELF::_batchProcessExistingItems();
    }
  }

  # ----------------------------------------------------------------------------

  /**
  * Add measurements JavaScript code to editor
  */
  public function hookAdminHead() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $module = $request->getModuleName();
    if (is_null($module)) { $module = 'default'; }
    $controller = $request->getControllerName();
    $action = $request->getActionName();

    if ($module === 'default' && $controller === 'items' && in_array($action, array('add', 'edit'))) {
      $units = SELF::_getTripleUnits();
      $tripleSelect = $units["tripleSelect"];
      $ungroupedSaniUnits = $units["ungroupedSaniUnits"];
      require dirname(__FILE__) . '/measurements-form.php';
    }

  }

  protected function _getTripleUnits() {
    $tripleSelect = array( -1 => __("Select Below") );
    $leastSelect = array( -1 => __("Select Below") );
    $leastSimple = array();
    $saniUnits = SELF::$_saniUnits;
    $ungroupedSaniUnits = array();
    foreach(SELF::$_saniUnits as $groupName => $saniUnitsGroup) {
      $tripleSelect[$groupName] = array();
      $leastSelect[$groupName] = array();
      foreach($saniUnitsGroup as $idx => $saniUnit) {
        $tripleSelect[$groupName][$idx] = $saniUnit["verb"];
        $leastSelect[$groupName][$idx] = implode("-", $saniUnit["units"]) .
          ( $saniUnit["units"][2] == "mm" ? "" : " (1 ".$saniUnit["units"][2]." = ".$saniUnit["mmconv"]." mm)" );
        $leastSimple[$idx] = implode("-", $saniUnit["units"]) ; // $saniUnit["units"][2];
        $ungroupedSaniUnits[$idx] = $saniUnit;
      }
    }
    ksort($ungroupedSaniUnits);
    ksort($leastSimple);
    return array(
      "tripleSelect" => $tripleSelect,
      "leastSelect" => $leastSelect,
      "leastSimple" => $leastSimple,
      "ungroupedSaniUnits" => $ungroupedSaniUnits
    );
  }

  public function getSaniUnits() {
    $units = SELF::_getTripleUnits();
    return $units["ungroupedSaniUnits"];
  }

  # ----------------------------------------------------------------------------

  /**
  * Filter to modify measurements fields in item editor -- to show the popup, etc.
  */
  public function filterElementInput($components, $args) {
    $view = get_view();
    $invisibleContent = $args['value']; // invisible text -- regular element's content, here JSON data
    $visibleContent = "*$invisibleContent*"; // visible text -- transformed, readable version of $invisibleContent JSON
    $visibleContent = SELF::_verbatimSourceData($invisibleContent);
    $components['input'] = "";
    $components['input'] .= $view->formTextarea(
                              $args['input_name_stem'] . '[text]'.'-editdisplay',
                              $visibleContent,
                              array('readonly' => 'true', 'rows' => 10, 'class' => 'measurementsField'),
                              null
                            );
    $components['input'] .= $view->formHidden(
                              $args['input_name_stem'].'[text]',
                              $invisibleContent,
                              array('readonly' => 'true', 'style' => 'width: auto;'),
                              null
                            );
    $components['input'] .= " <button class='measurementsBtn'>".__("Edit")."</button>";
    $components['input'] .= "<button class='measurementsClearBtn'>".__("Clear")."</button>";
    $components['html_checkbox'] = false;
    return $components;
  }

  protected function myNumberFormat($x) {
    return rtrim ( rtrim( number_format($x, 3, ",", "."), "0" ), ",");
    // $result = number_format($x, 3, ",", ".");
    // $result = preg_replace("/0+$/", "", $result);
    // $result = preg_replace("/,+$/", "", $result);
    // return $result;
  }

  protected function _verbatimSourceData($json, $br="") {
    $sourceData = json_decode(html_entity_decode($json));
    $result = "";

    if ($sourceData) {
      $tripleUnit =  $sourceData->u;

      $singleUnits = array( "", "", "", "" );
      if (preg_match("/".SELF::$_saniUnitRegex."/", $tripleUnit, $matches)) {
        $singleUnits = array( "", $matches[2], $matches[3], $matches[4] );
      }

      $cache = array();

      foreach(array_keys(SELF::$_editFields) as $i) {
        $currentField = SELF::$_editFields[$i];
        $editField = $currentField[0];
        switch ($editField) {
          case 'l1':
              $result .= __("Entered Data").": \n\n";
            break;
          case 'l1d':
              $result .= "\n".__("Derived Data").": \n\n";
            break;
        }
        $values = $sourceData->$editField;
        $values = ( is_array($values) ? $values : array() );

        $allZero = true;
        foreach(array_keys($values) as $idx) {
          $values[$idx] = floatval($values[$idx]);
          $allZero &= !$values[$idx];
        }

        $cacheHit = false;
        if (substr($editField,-1) != "d") {
          $cache[$editField] = $values;
        }
        else if ( isset($cache[substr($editField,0,2)]) ) {
          $cached = $cache[substr($editField,0,-1)];
          $cacheHit = !array_diff_assoc($values,$cached);
        }

        if ( (!$allZero) and (!$cacheHit) ) {
          $result .= $currentField[1] . " = ";
          $result .= SELF::myNumberFormat($values[0]) . " " . $singleUnits[3] . SELF::$_indices[$currentField[2]];

          $result .= " (";
          $valueText = array();
          for($j=1; $j<=3; $j++) {
            $valueText[] = SELF::myNumberFormat($values[$j]) . " " . $singleUnits[$j] . SELF::$_indices[$currentField[2]];
          }
          $result .= implode(" / ", $valueText);
          $result .= ")\n";
        }
      }

      $number = @$sourceData->n;
      if ($number) {
        $result .= "\n" . __("Number") . ": " . $number . "\n";
      }

    }

    if ($br) { $result = str_replace("\n", $br, $result); }

    return $result;
  }

  # ----------------------------------------------------------------------------

  /**
  * Filter to modify measurements fields during rendering -- to display calculated values, etc.
  */
  public function filterDisplay($text, $args) {
    $result = SELF::_verbatimSourceData($text, "<br>");
    return $result;
  }

  # ----------------------------------------------------------------------------

  /**
	* Delete preprocessed measurements after an item has been deleted
	*/
	public function hookAfterDeleteItem($args) {
		$itemId = intval($args["record"]["id"]);
		if ($itemId) {
      $db = get_db();
			$db->query("DELETE FROM `$db->MeasurementsValues` WHERE item_id=$itemId");
		}
	}

  # ----------------------------------------------------------------------------

  /**
  * Preprocess measurements after saving an item add/edit form.
  */
	public function hookAfterSaveItem($args) {
			$itemId = intval(@$args["record"]["id"]);
			if ($itemId) {
        SELF::_preProcessItem($itemId);
      }
	}

  # ----------------------------------------------------------------------------

  /**
	* Preprocess ALL existing items which could be rather EVIL in huge installations
	*/
	private function _batchProcessExistingItems() {
		$db = get_db();
		$sql= "select id from `$db->Items`";
		$items = $db->fetchAll($sql);
		foreach($items as $item) { SELF::_preProcessItem($item["id"]); }
	}

  # ----------------------------------------------------------------------------

  /**
	* Pre-process one item's textual data and measurements in search index table
	*/
	private function _preProcessItem($itemId) {
    $db = get_db();
    $db->query("DELETE FROM `$db->MeasurementsValues` WHERE item_id=$itemId");

    if (!SELF::$_measurementsElements) return;

    $measurementElements = implode(",", SELF::$_measurementsElements);

    $sql = "SELECT text FROM $db->ElementTexts".
            " WHERE record_id=$itemId AND element_id IN ($measurementElements)";
    $elements = $db->fetchAll($sql);

    if ($elements) {

      $dataTuples = array();
      foreach($elements as $element) {
        $json = $element["text"];
        $data = @json_decode($json,true);
        $dataTuple = array();
        if ($data) {
          $dataTuple[] = $itemId;
          preg_match("/".SELF::$_saniUnitRegex."/", $data["u"], $matches);
          // full "ab-cd-de" triple unit to avoid confusion
          $dataTuple["u"] = $db->quote($matches[2]."-".$matches[3]."-".$matches[4]);
          $dataTuple["n"] = ( $data["n"]=="" ? "null" : $db->quote($data["n"]) );
          foreach(SELF::$_editFields AS $editField) {
            $dataTuple[$editField[0]] = floatval($data[$editField[0]][0]); // round()
          }
        }
        if ($dataTuple) { $dataTuples[] = implode(",",$dataTuple); }
      }

      if ($dataTuples) {

        $dataSet = array( "item_id", "unit", "number" );
        foreach(SELF::$_editFields AS $editField) {
          $dataSet[] = $editField[0];
        }
        $dataSet = implode(",", $dataSet);

        $sql = "INSERT INTO `$db->MeasurementsValues` ($dataSet) values ".
                "(" . implode("), (", $dataTuples) . ")";
        $db->query($sql);

      }
    }
  }

  # ----------------------------------------------------------------------------

  /**
	* Display the measurement search form on the admin advanced search page in admin
	*/
	public function hookAdminItemsSearch() { SELF::_itemsSearch();  }

	/**
	* Display the measurement search form on the admin advanced search page in public
	*/
	public function hookPublicItemsSearch() { SELF::_itemsSearch();  }

  /**
	* Display the measurement search form on the admin advanced search page
	*/
  protected function _itemsSearch() {
    $units = SELF::_getTripleUnits();
    $tripleSelect = $units["tripleSelect"];
    unset($tripleSelect[-1]); // remove "Select below"
    foreach(array_keys($tripleSelect) as $tripleGroupIdx) {
      foreach(array_keys($tripleSelect[$tripleGroupIdx]) as $idx) {
        $singleUnit = $tripleSelect[$tripleGroupIdx][$idx];
        preg_match("/".SELF::$_saniUnitRegex."/", $singleUnit, $matches);
        unset($tripleSelect[$tripleGroupIdx][$idx]);
        $tripleSelect[$tripleGroupIdx][$idx."-1"] = $matches[4] . " (" . $singleUnit  .")";
        $tripleSelect[$tripleGroupIdx][$idx."-2"] = $matches[4] . SELF::$_indices[2];
        $tripleSelect[$tripleGroupIdx][$idx."-3"] = $matches[4] . SELF::$_indices[3];
      }
    }
    $tripleSelect = array("#" => __("Number")) + $tripleSelect;
    // echo "<pre>" . print_r($tripleSelect,true) . "</pre>";
    echo common('measurements-advanced-search', array("tripleSelect" => $tripleSelect ));
  }

  # ----------------------------------------------------------------------------

  /**
	* Filter for a number after search page submission.
	*/
	public function hookItemsBrowseSql($args) {
		$select = $args['select'];
		$params = $args['params'];

    $measurementsTerm = @$params["measurements_term"];

    $regExOneOrTwoNum = "^\W*(\d+)(:?\W*-\W*(\d+))?\W*$";

    if ($measurementsTerm) {
      preg_match("/$regExOneOrTwoNum/", $measurementsTerm, $matches);
      if ($matches) {
        $db = get_db();

        $from = $matches[1];
        $to = @$matches[3]; // could be undefined and thus 0

        $units = array();

        $tripleUnits = SELF::_getTripleUnits();
        $ungroupedSaniUnits = $tripleUnits["ungroupedSaniUnits"];

        $measurementsUnits = @$params["measurements_units"];
        $searchForNumber = (!is_array($measurementsUnits));
        if (is_array($measurementsUnits)) {
          foreach($measurementsUnits as $measurementsUnit) {
            if ($measurementsUnit=="#") { $searchForNumber = true; }
            preg_match("/$regExOneOrTwoNum/", $measurementsUnit, $matches);
            if ($matches) {
              $unit = $matches[1];
              $exp = @$matches[3];
              if (!$exp) { $exp=1; }
              $tripleUnit = @$ungroupedSaniUnits[$unit];
              if ($tripleUnit) {
                $units[] = array(
                  "u" => $unit,
                  "e" => $exp,
                  "t" => implode("-", $tripleUnit["units"]),
                );
              }
            }
          }
        }

        // echo "<pre>" . print_r($units,true) . "</pre>"; die();

  			$select
  					->join(
  							array('measurements_values' => $db->MeasurementsValues),
  							"measurements_values.item_id = items.id",
  							array()
  					);

        $conditions = array();

        if ($searchForNumber) {
          if (!$to) {
            $conditions[] = "(number=$from)";
          }
          else {
            $conditions[] = "(number BETWEEN $from AND $to)";
          }
        }

        if (!is_array($measurementsUnits)) {
          if (!$units) { $units[] = array("e" => 0); }
        }

        foreach($units as $unit) {
          $condition = "";

          $exp = $unit["e"];
          if ($exp) {
            $tripleUnit = $db->quote($unit["t"]);
            $condition .= "(measurements_values.unit=$tripleUnit AND (";
          }

          $fieldConditions = array();
          foreach(SELF::$_editFields as $editField) {
            if ( ($exp == 0) or ($editField[2] == $exp) ) {
              if (!$to) {
                $fieldConditions[] = $editField[0] . "=$from";
              }
              else {
                $fieldConditions[] = "(".
                                      $editField[0] . " BETWEEN $from AND $to".
                                      ")";
              }
            }
          }
          $condition .= implode(" OR ", $fieldConditions);
          if ($exp) { $condition .= ") )"; }
          $conditions[] = $condition;
        }
        $select->where(implode(" OR ", $conditions));

        // echo "<pre>$from-$to\n" . print_r($units,true) . "</pre>";
        // echo "<pre>\n" . print_r($conditions,true) . "</pre>";
        // echo "<pre>" . print_r($select,true) . "</pre>";
        // die();
      }
    }
  }

  # ----------------------------------------------------------------------------

  /**
  * Debug output of stored measurements in item's sidebar (if activated)
  */
  public function hookAdminItemsShowSidebar($args) {
		if (SELF::$_debugOutput) {
			$itemID = $args['item']['id'];
			if ($itemID) {
				echo "<div class='panel'><h4>".__("Measurements Debug Output")."</h4>\n";
				$db = get_db();
				$sql = "select * from `$db->MeasurementsValues` where item_id=$itemID";
				$measurements = $db->fetchAll($sql);
				if ($measurements) {
					echo "<ul>\n";
					foreach($measurements as $measurement) {
            $foundFirstDerived = false;
            $data = array();
            foreach(SELF::$_editFields AS $editField) {
              $key = $editField[0];
              $break = "";
              if (!$foundFirstDerived) {
                if (substr($key,-1) == "d") {
                  $foundFirstDerived = true;
                  $break="<br>";
                }
              }
              $data[$key] = $break . "$key=" . $measurement[$key];
            }
            echo "<li><strong>".$measurement["unit"]."</strong>:<br>".
                  implode(", ", $data).
                  "</li>";
					}
					echo "</ul>\n";
				}
				echo "</div>\n";
			}
		}
	}

  # ----------------------------------------------------------------------------

  public function unitsForAnalytics() {
    $tripleUnits = SELF::_getTripleUnits();
    $result = array(
      "select" => $tripleUnits["leastSelect"],
      "simple" => $tripleUnits["leastSimple"]
    );
    return $result;
  }

  # ----------------------------------------------------------------------------

  protected function _getVsDefaultTableProperty($getField, $dbField, $dbTable, $dbVerbField, $verbVal, $def) {
    $result = $def; # Sanity
    $db = get_db();
    $table = $db->$dbTable;
    if (isset($_GET[$getField])) {
      $get = intval($_GET[$getField]);
      $qu = "SELECT COUNT(*) FROM `$table` WHERE $dbField=$get";
      if ($db->fetchOne($qu)) { $result = $get; }
    }
    else {
      $qu = "SELECT $dbField FROM `$table` WHERE $dbVerbField='$verbVal'";
      $result = $db->fetchOne($qu);
    }
    return $result;
  }

  # ----------------------------------------------------------------------------

  public function transactionWeights() {

    if (!SELF::$_itemRelationsActive) {
      return array(
        "cnt" => 0,
        "maxPage" => 0,
        "page" => 0,
        "itemDetails" => false,
        "sandstoneElementItemType" => 0,
        "belongsToRelation" => 0,
        "transactionItemType" => 0,
        "idfilter" => "",
        "titlefilter" => "",
        "weightfactor" => ""
      );
    }

    $db = get_db();

    // -----------------
    // $debugMaxCount = 12; # +#+#+# How many simulated items should we show?
    // ----------------- ... should come out plugin configuration and/or _GET parameters
    $transactionsPerPage = 10; # how many transactions to be display in pagintion
    // -----------------

    // ----------------- determine the weight factor to be reckoned with
    $weightfactor = trim(@$_GET["weightfactor"]);
    if (preg_match("/(\d+)(?:(?:,|.)(\d+))?/", $weightfactor, $match)) {
      $weightString = $match[1] . ( isset($match[2]) ? "." . $match[2] : ""  );
      $weightFactorFloat = floatval($weightString);
    }
    else {
      $weightfactor = "2,0";
      $weightFactorFloat = 2;
    }
    // -----------------

    // ----------------- find out which item type should be used with weights -- usually "Sandstein-Element" == 21
    $sandstoneElementItemType = SELF::_getVsDefaultTableProperty(
      "st", "id", "ItemType", "name", "Sandstein-Element", -1
    );

    // ----------------- find out which relation should be used -- usualls "gehört zu" == 195
    $belongsToRelation = SELF::_getVsDefaultTableProperty(
      "rel", "id", "ItemRelationsProperty", "label", "gehört zu", -1
    );

    // ----------------- find out which item type should be references -- usually "Transaktion" == 24
    $transactionItemType = SELF::_getVsDefaultTableProperty(
      "tr", "id", "ItemType", "name", "Transaktion", -1
    );

    // ----------------- apply ID filter?
    $idFilterArr = array();
    $idFilter = trim(@$_GET["idfilter"]); // so what did we get?
    if ($idFilter) {
      if (preg_match_all("/\d+(?:-\d+)?/", $idFilter, $allMatches)) { // split it at commas
        foreach($allMatches[0] as $singleMatch) {
          preg_match("/(\d+)(?:-(\d+))?/", $singleMatch, $match); // split the single ones into 1-2 decimals each
          $from = $match[1];
          $to = ( isset($match[2]) ? $match[2] : $from );
          // echo "<pre>$singleMatch: $from - $to</pre>";
          if ($from>$to) { $help = $from; $from = $to; $to = $help; }
          $idFilterArr[] = array($from, $to); // one normalized entry
        }
      }
    }
    $idFilter = ( $idFilterArr ? $idFilter : "" ); // did what we got make sense? then OK, otherwise blank it

    // ----------------- create the infix to be actually filtering for IDs
    $idFilterInfix = "";
    if ($idFilterArr) {
      $fromTo = array();
      foreach($idFilterArr as $area) {
        $fromTo[] = "(it.id BETWEEN " . $area[0] . " AND " . $area[1] . ")";
      }
      $idFilterInfix = "AND (" . implode(" OR " , $fromTo) .  ")";
      // echo "<pre>$idFilterInfix</pre>"; die();
    }

    // ----------------- create the infix to be filter for item titles
    $titleFilterInfix = "";
    $title = trim(@$_GET["titlefilter"]); // so what did we get?
    if ($title) {
      $titleClauses = array();
      $titles = explode(",", $title); // Multiple search terms, seperated by comma
      foreach(array_keys($titles) as $idx) {
        $oneTitle = MeasurementsPlugin::mres(trim($titles[$idx]));
        if ($oneTitle) { $titleClauses[] = "text LIKE '%%$oneTitle%%'";}
      }
      if ($titleClauses) {
        $titleClause = "(" . implode(" OR ", $titleClauses) . ")";
        $titleElementId = $db->fetchOne("SELECT id FROM `$db->Elements` WHERE name = 'Title'");
        $nameIdFilterInfix = str_replace("it.id", "record_id", $idFilterInfix);
        $titleFilterInfix = "
          AND it.id in (
            SELECT record_id
            FROM `$db->ElementTexts`
            WHERE element_id = $titleElementId
            $nameIdFilterInfix
            AND $titleClause
          )
        ";
      }
      // die("<pre>$titleFilterInfix</pre>");
    }

    // How to find transactions which stone blocks relating to them?
    $sqlStub = "
      SELECT %s
      FROM `$db->Items` it
      LEFT JOIN `$db->ItemRelationsRelations` rel ON rel.object_item_id = it.id
      LEFT JOIN `$db->Items` obit ON rel.subject_item_id = obit.id
      WHERE obit.item_type_id = $sandstoneElementItemType
      AND rel.property_id = $belongsToRelation
      AND it.item_type_id = $transactionItemType
      $idFilterInfix
      $titleFilterInfix
      %s
      %s
    ";

    // How many do we have of those? Altogether?
    $countSql = sprintf($sqlStub, "COUNT( DISTINCT(it.id) ) AS cnt", "", "");
    // die("<pre>$countSql</pre>");
    $count = $db->fetchOne($countSql);
    if (isset($debugMaxCount)) { $count = $debugMaxCount; }
    $maxPage = floor(($count-1) / 10);

    // Sanity Values
    $page = -1;
    $items = array();
    $itemDetails = array();
    $invUnits = array();

    if ($maxPage >= 0) { // Do we at least have one page?

      $page = intval(@$_GET["page"]); // Which page are we asked to display?
      $page = ( $page<0 ? 0 : $page ); // Push negative values to at least first page == 0
      $page = ( $page>$maxPage ? $maxPage : $page ); // Does that page even exist?

      $from = $page * $transactionsPerPage;
      if (isset($debugMaxCount)) { $from = 0; }

      $itemSql = sprintf(
        $sqlStub,
        "DISTINCT(it.id)",
        "ORDER BY it.modified DESC",
        "LIMIT $transactionsPerPage OFFSET $from"
      );
      // die("<pre>$itemSql</pre>");
      $items = $db->fetchAll($itemSql);

      $tripleUnits = SELF::_getTripleUnits();
      $invUnits = array();
      foreach($tripleUnits["ungroupedSaniUnits"] as $unit) {
        $invUnits[ $unit["verb"] ] = $unit["mmconv"] * $unit["mmconv"] * $unit["mmconv"];
      }
      // echo "<pre>" . print_r($invUnits,true) . "</pre>"; die();
      // Should be something like:
      // Array
      // (
      //     [m-cm-mm (1-100-10)] => 1
      //     [AmstE-F-D (1-2-12)] => 13124.983471812
      //     [BremE-F-D (1-2-12)] => 14019.312673828
      // )

      foreach($items as $item) {
        $itemId = $item["id"];
        $fullItem = get_record_by_id('Item', $itemId);
        $itemData = array();

        $itemData["itemId"] = $itemId;
        $itemData["itemTitle"] = metadata($fullItem, array('Dublin Core', 'Title'));

        $stoneIdQuery = "
          SELECT DISTINCT(subject_item_id) AS itemId
          FROM `$db->ItemRelationsRelations`
          WHERE object_item_id = $itemId
          AND property_id = $belongsToRelation
        ";
        $stones = $db->fetchAll($stoneIdQuery);
        // $itemData["stones"] = $stones;

        $fullVolume = 0;

        $itemData["stoneData"] = array();
        foreach($stones as $stone) {
          $stoneId = $stone["itemId"];
          $stoneItem = get_record_by_id('Item', $stoneId);
          $stoneTitle = metadata($stoneItem, array('Dublin Core', 'Title'));

          $stoneDetails = array();
          $stoneDetails["t"] = $stoneTitle;

          $measurementElements = implode(",", SELF::$_measurementsElements);
          $sql = "
            SELECT text
            FROM `$db->ElementTexts`
            WHERE record_id = $stoneId
            AND element_id IN ($measurementElements)
            LIMIT 1
          ";
          $stoneMeasurementsJson = $db->fetchOne($sql);
          $stoneMeasurements = json_decode($stoneMeasurementsJson);

          $stoneDetails["u"] = $stoneMeasurements->u;

          $stoneDetails["n"] = intval(@$stoneMeasurements->n);
          if ($stoneDetails["n"]<1) { $stoneDetails["n"] = 1; }

          $stoneDetails["v"] = $stoneMeasurements->vd[0];

          if ( isset( $invUnits[$stoneDetails["u"]] ) ) {
            $mmConv = $invUnits[$stoneDetails["u"]];
            $stoneDetails["vc"] = $stoneDetails["v"] * $mmConv;

            $fullVolume += $stoneDetails["n"] * $stoneDetails["vc"];
            $stoneDetails["w"] = $stoneDetails["vc"] / (1000*1000*1000) * $weightFactorFloat;
            $stoneDetails["wn"] = $stoneDetails["w"] * $stoneDetails["n"];
          }

          $itemData["stoneData"][$stoneId] = $stoneDetails;
        }

        $itemData["fullV"] = $fullVolume;
        $itemData["fullW"] = $fullVolume / (1000*1000*1000) * $weightFactorFloat;
        $itemDetails[$itemId] = $itemData;
      }

    }

    if (isset($debugMaxCount)) {
      if (count($itemDetails)==0) {
        $count = 0;
        $page = -1;
        $maxPage = -1;
      }
      else {
        if ($page<$maxPage) {
          $simPage = 0;
          $firstItem = array_pop($itemDetails);
          while (count($itemDetails) < $transactionsPerPage) {
            array_push($itemDetails, $firstItem);
          }
        }
      }
    }

    return array(
      "cnt" => $count,
      "maxPage" => $maxPage,
      "page" => $page,
      // "itemIds" => $items,
      "itemDetails" => $itemDetails,
      // "invUnits" => $invUnits,
      "sandstoneElementItemType" => $sandstoneElementItemType,
      "belongsToRelation" => $belongsToRelation,
      "transactionItemType" => $transactionItemType,
      "idfilter" => $idFilter,
      "titlefilter" => $title,
      "weightfactor" => $weightfactor
    );

  }

  # ----------------------------------------------------------------------------

  public function mres($value) {
    // http://stackoverflow.com/a/1162502
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
    return str_replace($search, $replace, $value);
  }

  # ----------------------------------------------------------------------------
}

?>
