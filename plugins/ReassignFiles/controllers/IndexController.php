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
    $this->view->files = reassignFiles_getFileNames(); // from helpers/ReassignFilesFunctions.php
  }

  public function saveAction()
  {
    if ($this->getRequest()->isPost()){
      try{
        $itemId = intval($_POST['reassignFilesItem']);
        $files = $_POST['reassignFilesFiles'];
        foreach($files as $key => $val) { $files[$key] = intval($val); }
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

}
