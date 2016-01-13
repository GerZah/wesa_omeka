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
    'public_head',
    // 'public_items_show',
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
            array($this, "filter$filter_name")
        );
      }
    }

    SELF::$_withGeoLoc = SELF::_withGeoLoc();
  }

  private static $_withGeoLoc;
  private static $_geoLocations = array();

  private function _withGeoLoc() {
    $db = get_db();
    return $db->fetchOne("SELECT active FROM $db->Plugins WHERE name='GeoLocation' LIMIT 1");
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
    if (is_null($module)) { $module = 'default'; }
    $controller = $request->getControllerName();
    $action = $request->getActionName();

    if ($module === 'default' && $controller === 'items' && in_array($action, array('add', 'edit'))) {
      queue_js_file('itemreferences');
    }

    if ($module === 'default' && $controller === 'items' && $action === 'show') {
      queue_js_file('referencemap');
    }

  }

  public function hookPublicHead() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $module = $request->getModuleName();
    if (is_null($module)) { $module = 'default'; }
    $controller = $request->getControllerName();
    $action = $request->getActionName();

    if ($module === 'default' && $controller === 'items' && $action === 'show') {
      queue_js_file('referencemap');
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
    $components['input'] .= "<button class='itemReferencesClearBtn'>".__("Clear")."</button>";
    $components['html_checkbox'] = false;
    return $components;
  }

  public function filterDisplay($text, $args) {
    $result = $text;

    $itemId = intval($text);
    if ($itemId) {
      $referenceUrl = url('items/show/' . $text);
      $result = __("Reference").": ";
      $itemTitle = SELF::getTitleForId($text);
      $result .= "<a href='$referenceUrl'>$itemTitle</a>";

      $element_id = $args["element_text"]->element_id;

      if (!isset(self::$_geoLocations[$element_id])) {
        self::$_geoLocations[$element_id] = array();
      }
      if (!isset(self::$_geoLocations[$element_id][$itemId])) {
        self::$_geoLocations[$element_id][$itemId] = array();
      }

      if (SELF::$_withGeoLoc) {
        $db = get_db();
        $sql = "SELECT * FROM $db->Locations WHERE item_id = $itemId";
        $geoLoc = $db->fetchAll($sql);
        if ($geoLoc) {
          $geoLoc[0]["url"] = $referenceUrl;
          $geoLoc[0]["geo_title"] = $itemTitle;
          $geoLoc[0]["geo_title"] .= ( $geoLoc[0]["address"] ? " - " . $geoLoc[0]["address"] : "" );
          self::$_geoLocations[$element_id][$itemId] = $geoLoc[0];
          /* * /
          $lat = $geoLoc[0]["latitude"];
          $lng = $geoLoc[0]["longitude"];
          $zoom = $geoLoc[0]["zoom_level"];
          $title = $geoLoc[0]["geo_title"];
          $result .= "<br>(".__("Geolocation").": ";
          $result .= "<a href='https://www.google.de/maps".
                      "/place/$lat+$lng".
                      "/@$lat,$lng,$zoom"."z' target='_blank'>";
          $result .= $title;
          $result .= "</a>";
          $result .= ")";
          /* */
        }
      }
    }
    return $result;
  }

  public function hookAdminItemsShow() {
    // echo "foo";

    if ( (SELF::$_withGeoLoc) AND (self::$_geoLocations) ) {

      echo "<h2>".__("Geolocations of References Items")."</h2>\n";

      $mapsData = array();

      foreach(self::$_geoLocations as $elementId => $geoLocation) {
        if ($geoLocation) {
          $db = get_db();
          $sql = "SELECT name FROM $db->Elements WHERE id = $elementId";
          $elementName = $db->fetchOne($sql);
          echo "<h4>$elementName</h4>\n";

          $data = array(
            "mapId" => "map".$elementId,
            "coords" => array(),
          );

          foreach($geoLocation as $pin) {
            if ($pin) {
              $data["coords"][] = array(
                "title" => $pin["geo_title"],
                "lat" => $pin["latitude"],
                "lng" => $pin["longitude"],
                "url" => $pin["url"],
              );
            }
          }

          echo "<div id='".$data["mapId"]."' style='height:350px; width:100%;'></div>\n";

          $mapsData[] = $data;

        }

      }

      // echo "<pre>" . print_r($mapsData,true) . "</pre>";
      // echo "<pre>" . json_encode($mapsdata) . "</pre>";

      echo "<script>var mapsData=".json_encode($mapsData)."</script>\n";

    }

  }

}
