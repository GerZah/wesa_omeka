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
  public function indexAction() {
    $this->_helper->db->setDefaultModelName('ConditionalElements');
    $json=get_option('conditional_elements_dependencies');
    if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
  }
  public function addAction()
  {

  }

  public function dependeeAction()
  {
  }
  public function termAction()
  {

  }
  public function saveAction()
  {
    // $form = new Application_Form_Add();
    // $form->submit->setLabel('Add');
    // $this->view->form = $form;
    if ($this->getRequest()->isPost()) {
      // $formData = $this->getRequest()->getPost();
      // if ($form->isValid($formData)) {
      //         //  $name = $form->getValue('dependentName');
      //  $email = $form->getValue('email');
      //  $phone = $form->getValue('phone');

      try{
        $json=get_option('conditional_elements_dependencies');
        if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
        $dependencies[] = json_decode($json,true);
        $dependent = $_POST['dependent']; // Andere Finanziers 143
        $dependee = $_POST['dependee'];   //Handwerksbetrieb 111
        $term = $_POST['term'];               //Bildhauer

        $custom = array('0'=>$dependent, '1' => $term , '2' => $dependee);
      //  $dependencies[] = $custom;
        $dependencies = array_merge($dependencies, $custom);
        $json= json_encode($dependencies);
        set_option('conditional_elements_dependencies', $json);
        $this->_helper->flashMessenger(__('The dependent "%s" was successfully added.',$custom[2]), 'success');
      //                      }
      } catch (Omeka_Validate_Exception $e) {
        $this->_helper->flashMessenger($e);
      }
    } else {
      $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
    }
  }
  public function confirmAction()
  {

  }
  public function deleteAction()
  {
    if ($this->getRequest()->isPost()) {
      try{
        $json=get_option('conditional_elements_dependencies');
        if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
        $json_obj[] = json_decode($json,true);
        foreach ($json_obj as $key => $value) {
          if ($value[2] == $dependent_id) {
            unset($json_obj[$key]);
          }
        }
        $newarr = array();
        foreach($json_obj as $dep) {
            $newarr[] = $dep;
            }
        $json=json_encode($newarr);
        set_option('conditional_elements_dependencies', $json);
        $this->_helper->flashMessenger(__('The dependent is successfully deleted.'), 'success');

      } catch (Omeka_Validate_Exception $e) {
        $this->_helper->flashMessenger($e);
      }
    } else {
      $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
    }
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
      // echo "<pre>==== Pre Array = ".count($arr).": "; print_r($arr); echo "</pre>\n";

      $newarr = array();

      foreach($arr as $dep) {
        if ( isset($existing_ids[$dep[0]]) and isset($existing_ids[$dep[2]]) ) {
          $newarr[] = $dep;
        }
      }
      // echo "<pre>==== Post Array = ".count($newarr).": "; print_r($newarr); echo "</pre>\n";

      $result=json_encode($newarr);
    } # if ($json)

    // echo "Post JSON: $result<br>\n"; die();
    return $result;

  }


}
