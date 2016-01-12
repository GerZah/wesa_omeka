<?php
/**
* ItemReferences plugin.
*
* @package Omeka\Plugins\ItemReferences
*/
class ItemReferencesPlugin extends Omeka_Plugin_AbstractPlugin
{
  // Define Hooks
  protected $_hooks = array(
    'initialize',
    'install',
    'uninstall',
    // 'after_save_item',
    // 'define_acl',
    'config_form',
    'config',
    'admin_head',
    'admin_items_show',
  );

  //Define Filters
  // protected $_filters = array('admin_navigation_main');

  protected $_options = array(
		'item_references_local_enable' => 0, // +#+#+# actually obsolete
    'item_references_select' => "[]",
  );

  public function hookInitialize() {
    add_translation_source(dirname(__FILE__) . '/languages');
    $db = get_db();

    // Add filters
    $filter_names = array(
        'Display',
        'ElementInput',
    );
    $referenceElementsJson=get_option('item_references_select');
    if (!$referenceElementsJson) { $referenceElementsJson="null"; }
    $referenceElements = json_decode($referenceElementsJson,true);

    foreach($referenceElements as $element_id ) {
      $element = $db->getTable('Element')->find($element_id);
      $elementSet = $db->getTable('ElementSet')->find($element->element_set_id);
        foreach ($filter_names as $filter_name) {
          add_filter(
              array($filter_name, 'Item', $elementSet->name, $element->name),
              array($this, 'filter' . $filter_name)
            );
        }
      }

    SELF::$_withGeoLoc = SELF::_withGeoLoc();
  }

  private static $_withGeoLoc;
  private static $_geoLocations = array();

  private function _withGeoLoc() {
    $db = get_db();
    $result = false;
    try { $result = $db->fetchOne("SELECT 1 FROM $db->Locations LIMIT 1"); }
    catch (Exception $e) { $result = false; }
    return $result;
  }

  /**
  * Install the plugin.
  */
  public function hookInstall() {
    SELF::_installOptions();
  }

  /**
  * Uninstall the plugin.
  */
  public function hookUninstall() {

    SELF::_uninstallOptions();
  }

  /**
  * itemreferences admin navigation filter
  */
  // public function filterAdminNavigationMain($nav)
  // {
  //
  //   if(is_allowed('ItemReferences_Index', 'index')) {
  //     $nav[] = array('label' => __('Item References'), 'uri' => url('item-references'));
  //   }
  //   return $nav;
  // }

  /**
  * Define ACL entry for reassignfiles controller.
  */
  // public function hookDefineAcl($args) {
  //   $acl = $args['acl'];
  //
  //   $indexResource = new Zend_Acl_Resource('ItemReferences_Index');
  //   $acl->add($indexResource);
  //
  // }

  /**
  * Retrieve referenced items' titles and add them to item's search text
  *
  * @param array $args
  */
  // +#+#+# saving reference titles into the search index does not work consistently like that. :-(
  // public function hookAfterSaveItem($args) {
  //   if (!$args['post']) {
  //     return;
  //   }
  //
  //   $itemId = intval($args["record"]["id"]);
  //   if ($itemId) {
  //     $item = get_record_by_id('Item', $itemId);
  //
  //     $itemReferencesSelect = get_option('item_references_select');
  //     $itemReferencesSelect = ( $itemReferencesSelect ? json_decode($itemReferencesSelect) : array() );
  //
  //     if ($itemReferencesSelect) {
  //       $elementIds = implode(",", $itemReferencesSelect);
  //       $db = get_db();
  //       $sql = "SELECT text FROM $db->ElementTexts".
  //               " WHERE record_id = $itemId".
  //               " AND element_id in ($elementIds)";
  //       $refItemIds = $db->fetchAll($sql);
  //
  //       if ($refItemIds) {
  //         $refItemTitles = array();
  //         foreach($refItemIds as $refItemId) {
  //           $refItemTitles[] = SELF::getTitleForId($refItemId["text"]);
  //         }
  //         if ($refItemTitles) {
  //           $item->addSearchText(implode(" ", $refItemTitles));
  //           $item->save();
  //         }
  //       } // if ($refItemIds)
  //     } // if ($itemReferencesSelect)
  //   } // if ($itemId)
  // }

  /**
  * Display the plugin configuration form.
  */
  public static function hookConfigForm() {
    // $localItemReferences = (int)(boolean) get_option('item_references_local_enable');

    $itemReferencesSelect = get_option('item_references_select');
    $itemReferencesSelect = ( $itemReferencesSelect ? json_decode($itemReferencesSelect) : array() );

    require dirname(__FILE__) . '/config_form.php';
  }

  /**
  * Handle the plugin configuration form.
  */
  public static function hookConfig() {
    // $localItemReferences = (int)(boolean) $_POST['item_references_local_enable'];
    // set_option('item_references_local_enable', $localItemReferences);

    $itemReferencesSelect = array();
    $postIds=false;
    if (isset($_POST["item_references_select"])) { $postIds = $_POST["item_references_select"]; }
    if (is_array($postIds)) {
			foreach($postIds as $postId) {
				$postId = intval($postId);
				if ($postId) { $itemReferencesSelect[] = $postId; }
			}
		}
		$itemReferencesSelect = json_encode($itemReferencesSelect);
    set_option('item_references_select', $itemReferencesSelect );

  }

  public function hookAdminHead() {
    $request = Zend_Controller_Front::getInstance()->getRequest();

  $module = $request->getModuleName();
    if (is_null($module)) {
        $module = 'default';
    }
    $controller = $request->getControllerName();
    $action = $request->getActionName();

  if ($module === 'default'
        && $controller === 'items'
        && in_array($action, array('add', 'edit'))) {
      queue_js_file('itemreferences');
    }
  }

  public function getTitleForId($itemId) {
    $itemId = intval($itemId);
    $result = "";
    if ($itemId) {
      $item = get_record_by_id('Item', $itemId);
      $title = metadata($item, array('Dublin Core', 'Title'), array('no_filter' => true));
      $result = ($title ? $title : $result);
    }
    return $result;
  }

  public function filterElementInput($components, $args) {
    $view = get_view();

    $itemId = intval($args['value']);
    $itemTitle = SELF::getTitleForId($itemId);

    $components['input'] = "";
    $components['input'] .= $view->formText(
                              $args['input_name_stem'] . '[text]'.'-title',
                              $itemTitle,
                              array('readonly' => 'true', 'style' => 'width: auto;'),
                              null
                            );
    $components['input'] .= $view->formHidden(
                              $args['input_name_stem'].'[text]',
                              $itemId,
                              array('readonly' => 'true', 'style' => 'width: auto;'),
                              null
                            );
    $components['input'] .= " <button class='itemReferencesBtn'>".__("Select")."</button>";
    $components['html_checkbox'] = false;
    return $components;
  }

  public function filterDisplay($text, $args) {
    $result = $text;

    $itemId = intval($text);
    if ($itemId) {
      $result = __("Reference").": ";
      $itemTitle = SELF::getTitleForId($text);
      $result .= "<a href='".url('items/show/' . $text)."'>$itemTitle</a>";

      if (!SELF::$_withGeoLoc) {
        self::$_geoLocations[$itemId] = array();
      }
      else {
        $db = get_db();
        $sql = "SELECT * FROM $db->Locations WHERE item_id = $itemId";
        $geoLoc = $db->fetchAll($sql);
        if ($geoLoc) {
          self::$_geoLocations[$itemId] = $geoLoc[0];
          $result .= " (".__("Google Maps").": ";
          $result .= "<a href='https://www.google.de/maps/@".
                      $geoLoc[0]["latitude"].",".
                      $geoLoc[0]["longitude"].",".
                      $geoLoc[0]["zoom_level"]."z' target='_blank'>";
          $result .= ( $geoLoc[0]["address"] ? $geoLoc[0]["address"] : $itemTitle );
          $result .= "</a>";
          $result .= ")";
        }
      }
    }
    return $result;
  }

  public function hookAdminItemsShow() {
    // echo "foo";

    // if ( (SELF::$_withGeoLoc) AND (self::$_geoLocations) ) {
    //   echo "<h2>".__("References Geo Locations")."</h2>\n";
    //   echo "<pre>" . print_r(self::$_geoLocations,true) . "</pre>";
    // }

  }

}
