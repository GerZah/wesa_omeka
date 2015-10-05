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
  public function indexAction() {}

    public function saveAction()
    {
      if ($this->getRequest()->isPost()){
        try{
          $itemId = intval($_POST['reassignFilesItem']);
          $files = $_POST['reassignFilesFiles'];
          if(($itemId<0) or (is_null($files))){
            $this->_helper->flashMessenger(__('Please choose an item/file to reassign.'), 'error');
          }
          else{
            $db = get_db();
            $fileNames = implode(',', $files);
            $sql = "UPDATE `$db->File`set item_id = $itemId where item_id IN ($fileNames)";
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
