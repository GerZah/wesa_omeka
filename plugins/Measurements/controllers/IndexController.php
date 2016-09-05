<?php
/**
* @package Measurements
*/

/**
* Controller for Measurements admin pages.
*
* @package Measurements
*/
class Measurements_IndexController extends Omeka_Controller_AbstractActionController
{

  /**
  * Front admin page.
  */
  public function indexAction() {
  }

  /**
  * Measurement Table -- with all the action
  */
  public function tableAction() {
    $this->view->measurementUnits = MeasurementsPlugin::unitsForAnalytics();
  }

}
