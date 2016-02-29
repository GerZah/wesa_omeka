<?php

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
		'config_form', # prepare and display configuration form
		'config', # store config settings in the database
    'admin_head',
		// 'after_save_item',
		// 'after_delete_item',
		// 'admin_items_search',
		// 'public_items_search',
		// 'admin_items_show_sidebar',
		// 'items_browse_sql',
	);

  # ----------------------------------------------------------------------------

  protected $_options = array(
    'measurements_units' => '[]',
    'measurements_select' => '[]'
	);

  // One potential unit -- e.g. "[Group] abc-def-ghi (1-10-10)" or "abc-def-ghi (1-10-10)"
  protected static $_saniUnitRegex = "^\W*(?:\[(\S+)\]\W+)?(\S+)-(\S+)-(\S+)\W+\(1-(\d+)-(\d+)\)\W*$";

  # ----------------------------------------------------------------------------

  private static $_saniUnits;
  private static $_measurementsElements;

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

	}

  # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  /**
	* Initialize static variables (units, selected elements, etc.) so they can be used anywhere
	*/
  protected function _initStatics() {
    SELF::$_saniUnits = SELF::_prepareSaniUnits( SELF::_getRawUnitsFromConfig() );
    SELF::$_measurementsElements = SELF::_retrieveMeasurementElements();
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
        // echo "<pre>" . print_r($matches,true) . "</pre>";
        // echo "<pre>" . print_r($saniUnit,true) . "</pre>";
        if (!is_array(@$saniUnits[$group])) { $saniUnits[$group] = array(); }
        $saniUnits[$group][$cnt++] = $saniUnit;
      }
    }

    ksort($saniUnits);
    if ($saniUnits[""]) {
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
	}

  # ----------------------------------------------------------------------------

	/**
	* Uninstall the plugin.
	*/
	public function hookUninstall() {
		SELF::_uninstallOptions();
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
                ? __('Item Type') . ': ' . __($record['item_type_name'])
                : __($record['element_set_name']);
      $value = __($record['element_name']);
      $elements[$optGroup][$record['element_id']] = $value;
    }

    $measurementElements = SELF::$_measurementsElements;

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
      $tripleSelect = array( -1 => __("Select Below") );
      $saniUnits = SELF::$_saniUnits;
      $ungroupedSaniUnits = array();
      foreach(SELF::$_saniUnits as $groupName => $saniUnitsGroup) {
        $tripleSelect[$groupName] = array();
        foreach($saniUnitsGroup as $idx => $saniUnit) {
          $tripleSelect[$groupName][$idx] = $saniUnit["verb"];
          $ungroupedSaniUnits[$idx] = $saniUnit;
        }
      }
      ksort($ungroupedSaniUnits);
      require dirname(__FILE__) . '/measurements-form.php';
    }

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

  protected function _verbatimSourceData($json, $br="") {
    $sourceData = json_decode(html_entity_decode($json));
    $result = "";

    if ($sourceData) {
      $tripleUnit =  $sourceData->u;

      $singleUnits = array( "", "", "", "" );
      if (preg_match("/".SELF::$_saniUnitRegex."/", $tripleUnit, $matches)) {
        $singleUnits = array( "", $matches[1], $matches[2], $matches[3] );
      }

      $editFields = array(
        array("l1", __("Length") . " 1", 1),
        array("l2", __("Length") . " 2", 1),
        array("l3", __("Length") . " 3", 1),
        array("f1", __("Face")   . " 1", 2),
        array("f2", __("Face")   . " 2", 2),
        array("f3", __("Face")   . " 3", 2),
        array("v", __("Volume"), 3),
        array("l1d", __("Length") . " 1", 1),
        array("l2d", __("Length") . " 2", 1),
        array("l3d", __("Length") . " 3", 1),
        array("f1d", __("Face")   . " 1", 2),
        array("f2d", __("Face")   . " 2", 2),
        array("f3d", __("Face")   . " 3", 2),
        array("vd", __("Volume"), 3),
      );
      $indices = array( "", "", "²", "³" );
      $cache = array();

      foreach(array_keys($editFields) as $i) {
        $currentField = $editFields[$i];
        $key = $currentField[0];
        switch ($key) {
          case 'l1':
              $result .= __("Entered Data").": \n\n";
            break;
          case 'l1d':
              $result .= "\n".__("Derived Data").": \n\n";
            break;
        }
        $editField = $key;
        $values = $sourceData->$editField;

        $allZero = true;
        foreach(array_keys($values) as $idx) {
          $values[$idx] = intval($values[$idx]);
          $allZero &= !$values[$idx];
        }

        $cacheHit = false;
        if (substr($key,-1) != "d") {
          $cache[$key] = $values;
        }
        else if ( isset($cache[substr($key,0,2)]) ) {
          $cached = $cache[substr($key,0,-1)];
          $cacheHit = !array_diff_assoc($values,$cached);
        }

        if ( (!$allZero) and (!$cacheHit) ) {
          $result .= $currentField[1] . " = ";
          $result .= $values[0] . " " . $singleUnits[3] . $indices[$currentField[2]];

          $result .= " (";
          $valueText = array();
          for($j=1; $j<=3; $j++) {
            $valueText[] = $values[$j] . " " . $singleUnits[$j] . $indices[$currentField[2]];
          }
          $result .= implode(" / ", $valueText);
          $result .= ")\n";
        }
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

}

?>
