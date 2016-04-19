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
    $this->view->measurementUnits = MeasurementsPlugin::unitsForAnalytics();
  }

}
