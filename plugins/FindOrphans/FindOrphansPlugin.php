<?php

/**
* Measurements plugin.
*
* @package Omeka\Plugins\Measurements
*/
class FindOrphansPlugin extends Omeka_Plugin_AbstractPlugin {

  protected $_hooks = array(
    'initialize',
    // 'install',
    // 'uninstall',
    'config_form', # prepare and display configuration form
    'config', # store config settings in the database
	);

  /**
	* Add the translations.
	*/
	public function hookInitialize() {
		add_translation_source(dirname(__FILE__) . '/languages');
  }

  /**
	* Display the plugin configuration form.
	*/
	public static function hookConfigForm() {

    $db = get_db();
    $sql = "SELECT id,name FROM `$db->ItemTypes` ORDER BY name ASC";
    $itemTypes = $db->fetchAll($sql);

    $itemTypesSelect = array( -1 => __("Select Below") );
    foreach($itemTypes as $itemType) {
      $itemTypesSelect[$itemType["id"]] = $itemType["name"];
    }

    $targetUrl = url("plugins/config?name=FindOrphans");

    $itemTypeId = (
      isset($_GET["item_type_select"])
      ? intval($_GET["item_type_select"])
      : -1
    );

    $orphans = array();

    $sql = "SELECT name FROM `$db->ItemTypes` WHERE id = $itemTypeId";
    $itemTypeName = $db->fetchOne($sql);
    if ($itemTypeName) {

      $sql="
        SELECT DISTINCT(it.id) FROM `$db->Items` it
        LEFT JOIN `$db->ItemRelationsRelations` ir
        ON it.id = ir.subject_item_id OR it.id = ir.object_item_id
        WHERE it.item_type_id = $itemTypeId
        AND ir.id IS NULL
      ";

      $orphansIds = $db->fetchAll($sql);

      foreach($orphansIds as $orphan) {
        $orphanId = $orphan["id"];
        $orphanUrl = url('items/show/' . $orphanId);
        $orphanItem = get_record_by_id('Item', $orphanId);
        $orphanTitle = "#$orphanId - " . metadata($orphanItem, array('Dublin Core', 'Title'));
        $orphans[$orphanId] = array( "url" => $orphanUrl, "title" => $orphanTitle);
      }

    }

    $view = get_view();
    echo $view->partial(
      'plugins/find-orphans-config-form.php',
      array(
        "itemTypesSelect" => $itemTypesSelect,
        "targetUrl" => $targetUrl,
        "itemTypeId" => $itemTypeId,
        "itemTypeName" => $itemTypeName,
        "orphans" => $orphans
      )
    );

  }

  /**
	* Handle the plugin configuration form.
	*/
	public static function hookConfig() {
  }


}

?>
