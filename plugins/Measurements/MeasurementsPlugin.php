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

    // One potential unit -- e.g. "abc-def-ghi (1-10-10)"
    $saniUnitRegex = "^\W*([a-zA-Z]+)-([a-zA-Z]+)-([a-zA-Z]+)\W+\(1-(\d+)-(\d+)\)\W*$";

    foreach($units as $unit) {
      if (preg_match("/$saniUnitRegex/", $unit, $matches)) {
        $saniUnit = array(
          "units" => array( $matches[1], $matches[2], $matches[3] ),
          "convs" => array( 1, intval($matches[4]), intval($matches[5]) ),
        );
        $saniUnit["verb"] = $saniUnit["units"][0]."-".$saniUnit["units"][1]."-".$saniUnit["units"][2]." ".
                            "(1-".$saniUnit["convs"][1]."-".$saniUnit["convs"][2].")";
        // echo "<pre>" . print_r($matches,true) . "</pre>";
        // echo "<pre>" . print_r($saniUnit,true) . "</pre>";
        $saniUnits[] = $saniUnit;
      }
    }

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
  * Filter to modify measurements fields in item editor -- to show the popup, etc.
  */
  public function filterElementInput($components, $args) {
    $view = get_view();
    $components['html_checkbox'] = false;
    return $components;
  }

  # ----------------------------------------------------------------------------

  /**
  * Filter to modify measurements fields during rendering -- to display calculated values, etc.
  */
  public function filterDisplay($text, $args) {
    $result = $text." foo";
    return $result;
  }

  # ----------------------------------------------------------------------------

}

?>
