<?php
/**
 * Measurements
 * LookupController
 */

/**
 * Lookup controller.
 */
class Measurements_LookupController extends Omeka_Controller_AbstractActionController {

  public function indexAction() {
    $result = array("ok" => "works!");

    if ($this->_hasParam('test')) {
      $result["test"] = $this->_getParam('test');
    }

    $this->_helper->json($result);
  }

}
