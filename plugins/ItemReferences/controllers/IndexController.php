<?php
/**
* Controller for ItemReferences admin pages.
*
* @package ItemReferences
*/
class ItemReferences_IndexController extends Omeka_Controller_AbstractActionController
{
  /**
  * Front admin page.
  */
  public function indexAction() {
    $this->_helper->db->setDefaultModelName('ItemReferences');
    #if (!$json) { $json="[]"; } else { $json = $this->_removeOutdatedDependencies($json); }
  }
  /**
  * Check JSON array of existing dependencies for non-existent dependents / dependees and filter them
  */
  // private function _removeOutdatedDependencies($json) {
  //   $result = $json;
  //   // echo "Pre JSON: $result<br>\n";
  //   if ($json) {
  //     $existing_ids = array();
  //     $db = get_db();
  //     $select = "SELECT id FROM $db->Element";
  //     $ids = $db->fetchAll($select);
  //     foreach($ids as $id){ $existing_ids[$id["id"]] = true; }
  //     $arr = json_decode($result);
  //     if ($arr) {
  //       // echo "<pre>==== Pre Array = ".count($arr).": "; print_r($arr); echo "</pre>\n";
  //       $newarr = array();
  //       foreach($arr as $dep) {
  //         if ( isset($existing_ids[$dep[0]]) and isset($existing_ids[$dep[2]]) ) {
  //           $newarr[] = $dep;
  //         }
  //       }
  //       // echo "<pre>==== Post Array = ".count($newarr).": "; print_r($newarr); echo "</pre>\n";
  //       $result=json_encode($newarr);
  //     }# if ($json)
  //   }
  //   // echo "Post JSON: $result<br>\n"; die();
  //   return $result;
  // }

}
