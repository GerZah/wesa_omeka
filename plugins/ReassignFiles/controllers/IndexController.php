<?php
/**
* @package ReassignFiles
*/

/**
* Controller for ReassignFiles admin pages.
*
* @package ReassignFiles
*/
class ReassignFiles_IndexController extends Omeka_Controller_AbstractActionController
{
  /**
  * Front admin page.
  */
  public function indexAction() {
    $this->view->files = $this->_getFileNames();
  }

  public function saveAction()
  {
    if ($this->getRequest()->isPost()){
      try{
        $itemId = intval($_POST['reassignFilesItem']);
        $files = $_POST['reassignFilesFiles'];
        foreach($files as $key => $val) { $files[$key] = intval($files[$key]); }
        if(($itemId<0) or (is_null($files))){
          $this->_helper->flashMessenger(__('Please choose an item/file to reassign.'), 'error');
        }
        else{
          $db = get_db();
          $fileNames = implode(',', $files);
          $sql = "UPDATE `$db->File`set item_id = $itemId where id IN ($fileNames)";
          $db->query($sql);
          $this->_helper->flashMessenger(__('The changes are successfully saved.'), 'success');
        }
      }
      catch(Omeka_Validate_Exception $e){
        $this->_helper->flashMessenger($e);
      }
    }
  }
  /**
  * Returns all fileNames
  */
  protected function _getFileNames()
  {
    $fileNames = array();
    $db = get_db();
    $select = "SELECT et.text AS itemName, f.original_filename AS original_filename, f.item_id AS itemId, f.id AS fileId
    FROM {$db->File} f
    JOIN {$db->ElementText} et
    ON f.item_id = et.record_id
    WHERE et.element_id = 50
    GROUP BY f.id"; # GROUP BY original_filename # no grouping by filename (which might be identical)
    $files = $db->fetchAll($select);
    foreach ($files as $file) {
      $fileNames[$file['fileId']] = $file['original_filename'].
      ' [#'.$file['itemId'].
      ' ('.$file['fileId'].') - '.
      $file['itemName'].']';
    }
    return $fileNames;
  }
}
