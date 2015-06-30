<?php

/**
 * @file
 * Conditional Elements plugin main file.
 */

/**
 * Conditional Elements plugin main class.
 */
class ConditionalElementsPlugin extends Omeka_Plugin_AbstractPlugin {

	protected $_hooks = array(
		'admin_head', // embed our jQuery code when adding / editing objects
	);

	public function hookAdminHead($args) {
		// Core hookAdminHead taken from ElementTypes plugin

		$request = Zend_Controller_Front::getInstance()->getRequest();

		$module = $request->getModuleName();
		if (is_null($module)) { $module = 'default'; }

		$controller = $request->getControllerName();
		$action = $request->getActionName();

		if ($module === 'default' &&
				$controller === 'items' &&
				in_array($action, array('add',  'edit'))) {

			// -----------------------
			// Before including the conditionalelements.js, THIS is the place to output
			// a <script>...</script> tag that will create and populate a dependencies
			// JavaScript array. That way, the code in there will attach all the
			// necessary onChange events and also initialize the page view accordingly.
			// -----------------------

			queue_js_file('conditionalelements');

		} # if ($module === 'default' ...

	} # public function hookAdminHead()

} # class