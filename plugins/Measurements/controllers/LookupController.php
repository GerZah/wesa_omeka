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
    $result = array();
    $result["ok"] = "works";
    // $result = array("ok" => "works!");
    // if ($this->_hasParam('test')) {
    //   $result["test"] = $this->_getParam('test');
    // }

    $area = $unit = -1;

    if ($this->_hasParam("area")) { $area = intval($this->_getParam('test')); }
    if ($this->_hasParam("unit")) { $unit = intval($this->_getParam('unit')); }

    if ( ($area>=0) and ($unit>=0) ) {
      $result["area"] = $area;
      $result["unit"] = $unit;
    }

    $this->_helper->json($result);
  }

}
