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
  public function measurementsAction() {
    $this->view->measurementUnits = MeasurementsPlugin::unitsForAnalytics();
  }

  /**
  * Front admin page.
  */
  public function transactionsAction() {
    $this->view->relationsSelect = get_table_options('ItemRelationsProperty');

    $itemTypesSelect = array();
    foreach (get_records('ItemType', array(), 0) as $itemType) {
      $itemTypesSelect[$itemType->id] = $itemType["name"];
    }
    asort($itemTypesSelect);
    $this->view->itemTypesSelect = array(__("Select Below")) + $itemTypesSelect;

    $this->view->transactionWeights = MeasurementsPlugin::transactionWeights();
  }

}
