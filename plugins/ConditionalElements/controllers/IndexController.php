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

            if ($this->getRequest()->isPost()) {
                if ($form->isValid($_POST)) {
                    try{
                        $form->saveFromPost();
                        $this->_helper->flashMessenger(__('The dependency "%s" was successfully updated.', $itemType->name), 'success');
                        $this->_helper->redirector('show', null, null, array('id'=>$itemType->id));
                    } catch (Omeka_Validate_Exception $e) {
                        $this->_helper->flashMessenger($e);
                    }
                } else {
                    $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
                }
            }

            $this->view->conditional_elements = $conditionalElements;

           }
        public function addAction()
        {

          if ($this->getRequest()->isPost()) {
                  try{
                    $json=get_option('conditional_elements_dependencies');
                    if (!$json) { $json="null"; }
                    $dependencies = json_decode($json);
                    $existingdependent = $_POST['existingdependent'];
                    $newdependent =$_POST['newdependent'];
                //    $simpleVocabTerm->terms = $this->_sanitizeTerms($terms);
                    $this->_helper->flashMessenger(__('The dependent "%s" was successfully added.', $existingdependent), 'success');
                  } catch (Omeka_Validate_Exception $e) {
                      $this->_helper->flashMessenger($e);
                  }
              } else {
                  $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
              }

        }

        public function dependeeAction()
         {
         }
         public function termAction()
          {
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


        /**
         * Sanitize the terms for insertion into the database.
         *
         * @param string $terms
         * @return string
         */
        private function _sanitizeTerms($terms)
        {
            $termsArr = explode("\n", $terms);
            $termsArr = array_map('trim', $termsArr); // trim all values
            $termsArr = array_filter($termsArr); // remove empty values
            $termsArr = array_unique($termsArr); // remove duplicate values
            $terms = implode("\n", $termsArr);
            $terms = trim($terms);
            return $terms;
        }

}
