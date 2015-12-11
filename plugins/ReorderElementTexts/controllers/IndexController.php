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
class ReorderElementTexts_IndexController extends Omeka_Controller_AbstractActionController {

  public function reorderAction() {
    queue_js_file('reorderelementtexts_drag');
    queue_css_file('reorderelementtexts_drag');
  }

  public function updateAction() { }

}
