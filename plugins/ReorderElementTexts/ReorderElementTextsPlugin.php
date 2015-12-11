<?php

/**
* ReorderElementTexts plugin.
*
* @package Omeka\Plugins\ConditionalElements
*/
class ReorderElementTextsPlugin extends Omeka_Plugin_AbstractPlugin {
	/**
	* @var array This plugin's hooks.
	*/
	protected $_hooks = array(
		'initialize',
		'install',
		'uninstall',
		'admin_head',
	);

  protected $_options = array(
    // 'conditional_elements_dependencies' => "[]",
  );

  /**
  * Install the plugin.
  */
  public function hookInstall() {
    SELF::_installOptions();
  }

  /**
  * Uninstall the plugin.
  */
  public function hookUninstall() {
    SELF::_uninstallOptions();
  }

   /**
     * Add the translations.
     */
  public function hookInitialize() {
    // add_translation_source(dirname(__FILE__) . '/languages');
  }

	public function hookAdminHead($args) {
		// Core hookAdminHead taken from ElementTypes plugin

		$request = Zend_Controller_Front::getInstance()->getRequest();

		$module = $request->getModuleName();
		if (is_null($module)) { $module = 'default'; }

		$controller = $request->getControllerName();
		$action = $request->getActionName();

		if ($module === 'default' &&
				$controller === 'items' &&
				in_array($action, array('add',  'edit')) ) {

			queue_js_string("
				var reorderElementTestsButton = '".__("Reorder Inputs")."';
				var reorderElementTextsUrl = '".html_escape(url('reorder-element-texts/index/reorder'))."';
			");
			queue_js_file('reorderelementtexts');
		} # if ($module === 'default' ...
	} # public function hookAdminHead()

	public function checkItemElement() {
		$elements = false;

		$returnLink = "<a href='javascript:window.history.back();'>" .
	                __("Please return to the referring page.").
	                "</a>";

	  $itemId = ( isset($_GET["item"]) ? intval($_GET["item"]) : 0 );
	  $elementId = ( isset($_GET["element"]) ? intval($_GET["element"]) : 0 );

	  if (!$itemId) { echo __("No item ID specified.") . " " . $returnLink; }
	  else if (!$elementId) { echo __("No element ID specified.") . " " . $returnLink; }

	  else {
	    $db = get_db();
	    $itemExists = $db->fetchOne("SELECT count(*) FROM $db->Items WHERE id = $itemId");
	    if (!$itemExists) { echo __("Item not found.") . " " . $returnLink; }

	    else {
	      $sql = "SELECT * FROM $db->ElementTexts".
	              " WHERE record_id = $itemId".
	              " AND element_id = $elementId";
	      $elements = $db->fetchAll($sql);
	      if (!$elements) { echo __("Specified elements not found in item.") . " " . $returnLink; }
			}
		}

		return $elements;
	}

} # class
