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
      }
       public function editAction()
        {
            $dependent_id = $this->_getParam('dependent_id');
            $dependee_id = $this->_getParam('dependee_id');
            $this->view->dependee = $this->_getName($dependee_id);
            $this->view->term = $this->_getParam('term');
            $this->view->dependent = $this->_getName($dependent_id);

            // get the item type
            $itemType = $this->_helper->db->findById();

            // edit the item type
            $form = $this->_getForm($itemType);
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($_POST)) {
                    try{
                        $form->saveFromPost();
                        $this->_helper->flashMessenger(__('The item type "%s" was successfully updated.', $itemType->name), 'success');
                        $this->_helper->redirector('show', null, null, array('id'=>$itemType->id));
                    } catch (Omeka_Validate_Exception $e) {
                        $this->_helper->flashMessenger($e);
                    }
                } else {
                    $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
                }
            }

            $this->view->form = $form;
            $this->view->conditional_elements = $conditionalElements;

           }
        public function addAction()
        {

          if ($this->getRequest()->isPost()) {
                  try{
                    $dependent = $_POST['dependent'];
                    $term =$_POST['term'];
                    $dependee = $_POST['dependee'];
                    $json=get_option('conditional_elements_dependencies');
                    if (!$json) { $json="null"; }
                    $data = json_decode($json, 1);
                    $newdata = array('0'=>$dependent, '1' => $term, '2'=>$dependee);
                    $data[] = $newdata;
                    $json= json_encode($data);

                    $this->_helper->flashMessenger(__('The dependency "%s" was successfully added.', $dependee), 'success');
                    $this->_helper->redirector('show', null, null, array('id'=>$dependee_id));
                  } catch (Omeka_Validate_Exception $e) {
                      $dependee_id->delete();
                      $this->_helper->flashMessenger($e);
                  }
              } else {
                  $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
              }

          // specify view variables
      //    $this->view->form = $form;
      //    $this->view->item_type = $conditionalElements;
        }


        protected function _getName($id) {
          $db = get_db();
          $sql = "SELECT name FROM $db->Element where  id = ?";
          $params = array($id);
          return $db->fetchOne($sql, $params);

        }

        public function deleteAction()
        {
            throw new Omeka_Controller_Exception_404;
        }


        protected function _redirectAfterAdd($conditionalElements)
        {
            $this->_redirect("conditional-elements/edit/{$conditionalElements->id}");
        }

        protected function _getDeleteConfirmMessage($record)
        {
            return __('This will delete the element set and all elements assigned to '
                 . 'the element set. Items will lose all metadata that is specific '
                 . 'to this element set.');
        }
        protected function _redirectAfterDelete($record)
        {
            // Redirect back to the item show page for this file
            $this->_helper->flashMessenger(__('The file was successfully deleted.'), 'success');
            $this->_helper->redirector('show', 'items', null, array('id'=>$record->item_id));
        }

        protected function _getAddSuccessMessage($conditionalElements)
        {
            return __('The dependency "%s" was successfully added!', $conditionalElements->name);
        }

        private function _getForm($conditionalElements)
        {
            require_once APP_DIR . '/forms/ItemTypes.php';
            $form = new Omeka_Form_ConditionalElements;
            $form->setConditionalElements($conditionalElements);
            fire_plugin_hook('conditional-elements_form', array('form' => $form));
            return $form;
        }

}
