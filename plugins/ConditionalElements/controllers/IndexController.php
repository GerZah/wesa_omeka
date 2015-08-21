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
          $dependent_id = $this->_getParam('dependent_id');
        }

        public function addAction()
        {
        }

        public function dependeeAction()
         {
         }
         public function termAction()
          {
            if ($this->getRequest()->isPost()) {
                      try{
                         $json=get_option('conditional_elements_dependencies');
                         if (!$json) { $json="null"; }
                         $dependencies[] = json_decode($json,true);
                        // $dependent = _sanitizeTerms($_POST['dependentName']); Andere Finanziers
                        // $dependee = _sanitizeTerms($_POST['dependeeName']);   Handwerksbetrieb
                        // $term = _sanitizeTerms($_POST['term']);               Bildhauer
                        //

                        $custom = array('0'=>'111', '1' => 'Bildhauer', '2' => '143');
                        $dependencies[] = $custom;
                        $json= json_encode($dependencies);
                      //  set_option('conditional_elements_dependencies', $json);
                        var_dump($json);
                        $this->_helper->flashMessenger(__('The dependent "%s" was successfully added.',$custom[2]), 'success');
                        } catch (Omeka_Validate_Exception $e) {
                          $this->_helper->flashMessenger($e);
                      }
                  } else {
                      $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
                  }
          }
          public function saveAction()
          {

          }
          public function deleteAction()
        {
          if ($this->getRequest()->isPost()) {
             try{
               $dependent_id = $this->_getParam('dependent_id');
               //$dependent_id = '59';
               $json=get_option('conditional_elements_dependencies');
               if (!$json) { $json="null"; }
               $json_obj = json_decode($json,true);
               foreach ($json_obj as $key => $value) {
                   if (in_array($dependent_id, $value)) {
                       unset($json_obj[$key]);
                   }
               }
               $json_obj = json_encode($json_obj);
               var_dump($json_obj);
                //$this->_redirectAfterDelete($record);

                $this->_helper->flashMessenger(__('The dependency is successfully deleted.'), 'success');

              } catch (Omeka_Validate_Exception $e) {
                  $this->_helper->flashMessenger($e);
              }
              } else {
              $this->_helper->flashMessenger(__('There were errors deleting the dependencies. Please try again later.'), 'error');
              }
            }
        protected function _redirectAfterDelete($record)
              {
                  $this->_helper->flashMessenger(__('The dependency is successfully deleted.'), 'success');
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

        /**
         * Return the delete confirm message for deleting a record.
         *
         * @param Omeka_Record_AbstractRecord $record
         * @return string
         */
        protected function _getDeleteConfirmMessage($record)
        {
            return 'Delete';
        }


}
