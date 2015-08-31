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
      if (isset($_SESSION)) {
        try{
          $json=get_option('conditional_elements_dependencies');
          if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
          $dependencies = json_decode($json,true);
          $dependentName = $_SESSION['conditional_elements_dependent'];
          $dependeeName = $_SESSION['conditional_elements_dependee'];
          $term = $_SESSION['conditional_elements_term'];
          // fetches the dependent_id and dependee_id so that we can save it in JSON.
          $dependent = $this->_getDependent($dependentName);
          $dependee = $this->_getDependee($dependeeName);

          $custom = array('0'=>$dependent, '1' => $term , '2' => $dependee);
          array_push($dependencies,$custom);
          $json= json_encode($dependencies);
          set_option('conditional_elements_dependencies', $json);
          $this->_helper->flashMessenger(__('The dependent "%s" was successfully added.',$dependentName), 'success');
        }
        catch (Omeka_Validate_Exception $e) {
          $this->_helper->flashMessenger($e);
        }
      }
    } else {
      $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
    }
  }

  /**
  * Returns dependent_id based on dependentName
  */
  protected function _getDependent($dependentName)
  {
    $db = get_db();
    $select = "SELECT id FROM $db->Element WHERE name = '$dependentName'";
    $results = $db->fetchAll($select);
    $data = array();
    foreach($results as $result) {
      $data[$result['id']] = $result['id'];
    }
    return $data[$result['id']];
  }

  /**
  * Returns dependee_id based on dependeeName
  */
  protected function _getDependee($dependeeName)
  {
    $db = get_db();
    $select = "SELECT id FROM $db->Element WHERE name = '$dependeeName'";
    $results = $db->fetchAll($select);
    $data = array();
    foreach($results as $result) {
      $data[$result['id']] = $result['id'];
    }
    return $data[$result['id']];
  }

  /**
  * Deletes the dependency based on dependent_id.
  * We cannot use dependee_id simply because more than one dependent can be attached to a dependee.
  */
  public function deleteAction()
  {
    if (isset($_SESSION)) {
      try{
        $dependent_id = $_SESSION['conditional_elements_delete_dependent'];
        $json=get_option('conditional_elements_dependencies');
        if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
        $json_obj = json_decode($json,true);
        /* * / # Githa's implementation with two array iterations
        // Deletion in JSON
        foreach ($json_obj as $key => $value) {
          if ($value[2] == $dependent_id) {
            unset($json_obj[$key]);
          }
        }
        // Encode into a new array in order to avoid JSON indexing incompatiblity issue.
        $newarr = array();
        foreach($json_obj as $dep) {
          $newarr[] = $dep;
        }
        /* */
        /* */ # Gero's implementation with one array iteration
        // Construct new array from all entries that don't match the requested delete id
        $newarr = array();
        foreach($json_obj as $value) {
          if ($value[2] != $dependent_id) {
            $newarr[] = $value;
          }
				}
        /* */
        $json=json_encode($newarr);
        set_option('conditional_elements_dependencies', $json);
        $this->_helper->flashMessenger(__('The dependent is successfully deleted.',$dependent_id), 'success');
      } catch (Omeka_Validate_Exception $e) {
        $this->_helper->flashMessenger($e);
      }
    }
    else {
      $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
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
