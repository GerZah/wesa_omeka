<?php
/**
* @package FindOrphans
*/

/**
* Controller for FindOrphans admin pages.
*
* @package FindOrphans
*/
class FindOrphans_IndexController extends Omeka_Controller_AbstractActionController {
  /**
  * Front admin page.
  */
  public function indexAction() {

    $db = get_db();

    // -------------

    $sql = "SELECT id,name FROM `$db->ItemTypes` ORDER BY name ASC";
    $itemTypes = $db->fetchAll($sql);

    $itemTypesSelect = array( -1 => __("Select Below") );
    foreach($itemTypes as $itemType) {
      $itemTypesSelect[$itemType["id"]] = $itemType["name"];
    }


    // -------------

    $itemTypeId = (
      isset($_GET["item_type_select"])
      ? intval($_GET["item_type_select"])
      : -1
    );


    // -------------

    $sql = "SELECT name FROM `$db->ItemTypes` WHERE id = $itemTypeId";
    $itemTypeName = $db->fetchOne($sql);


    // -------------

    $orphans = array();

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

    // -------------

    $this->view->itemTypesSelect = $itemTypesSelect;
    $this->view->itemTypeId = $itemTypeId;
    $this->view->itemTypeName = $itemTypeName;
    $this->view->orphans = $orphans;
    $this->view->targetUrl = url("find-orphans");

  }

}
