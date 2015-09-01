<?php
/**
* ConditionalElements
* @copyright Copyright 2010-2014 Roy Rosenzweig Center for History and New Media
* @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
*/

/**
* The Configuration controller.
*
* @package Omeka\Plugins\ConditionalElements
*/
class ConditionalElements_IndexController extends Omeka_Controller_AbstractActionController
{

  /**
  * Configuration form.
  */
  public function indexAction() {
    $this->_helper->db->setDefaultModelName('ConditionalElements');
    $json=get_option('conditional_elements_dependencies');
    if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
  }

  /**
  * Check JSON array of existing dependencies for non-existent dependents / dependees and filter them
  */
  private function _removeOutdatedDependencies($json) {
    $result = $json;
    // echo "Pre JSON: $result<br>\n";
    if ($json) {
      $existing_ids = array();
      $db = get_db();
      $select = "SELECT id FROM $db->Element";
      $ids = $db->fetchAll($select);
      foreach($ids as $id){ $existing_ids[$id["id"]] = true; }
      $arr = json_decode($result);
      if ($arr) {
        // echo "<pre>==== Pre Array = ".count($arr).": "; print_r($arr); echo "</pre>\n";
        $newarr = array();
        foreach($arr as $dep) {
          if ( isset($existing_ids[$dep[0]]) and isset($existing_ids[$dep[2]]) ) {
            $newarr[] = $dep;
          }
        }
        // echo "<pre>==== Post Array = ".count($newarr).": "; print_r($newarr); echo "</pre>\n";
        $result=json_encode($newarr);
      }# if ($json)
    }
    // echo "Post JSON: $result<br>\n"; die();
    return $result;
  }

  /**
  * Saves the new dependency.
  */
  public function saveAction()
  {
    if ($this->getRequest()->isPost()) {
      try{
        $json=get_option('conditional_elements_dependencies');
        if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
        $dependencies = json_decode($json,true);
        $dependent = $_POST['dependent'];
        $dependee = $_POST['dependee'];
        $term = $_POST['term'];

        $custom = array('0' => $dependee, '1' => $term , '2' => $dependent);
        $dependencies[]=$custom;
        $json= json_encode($dependencies);
        set_option('conditional_elements_dependencies', $json);
      	if ( ($dependent) and ($dependee) and ($term) )
        {
        $this->_helper->flashMessenger(__('The dependent is successfully added.'), 'success');
        }
        else {
        $this->_helper->flashMessenger(__('There were errors in creating the dependency.'), 'error');
        }
        }
      catch (Omeka_Validate_Exception $e) {
        $this->_helper->flashMessenger($e);
      }
    }
  }

  /**
  * Deletes the dependency based on dependent_id.
  * We cannot use dependee_id simply because more than one dependent can be attached to a dependee.
  */
  public function deleteAction()
  {
    try{
      $dependent_id = $_GET['dependent_id'];
      $json=get_option('conditional_elements_dependencies');
      if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
      $json_obj = json_decode($json,true);
      // Construct new array from all entries that don't match the requested delete id
      $newarr = array();
      foreach($json_obj as $value) {
        if ($value[2] != $dependent_id) {
          $newarr[] = $value;
        }
			}
      $json=json_encode($newarr);
      set_option('conditional_elements_dependencies', $json);
      if ($dependent_id)
      {
      $this->_helper->flashMessenger(__('The dependent is successfully deleted.'), 'success');
      }
      else {
      $this->_helper->flashMessenger(__('There were errors in deleting the dependency.'), 'error');
      }
    } catch (Omeka_Validate_Exception $e) {
      $this->_helper->flashMessenger($e);
    }
  }

  /**
  * Empty actions that are used only for navigation between pages.
  * These are mandatory to avoid Zend controller Dispatch error.
  */
  public function addAction()
  {

  }
  public function dependeeAction()
  {

  }
  public function termAction()
  {

  }
  public function confirmAction()
  {

  }
}
