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

			// ------------------------------------------
			// An array of dependencies:
			// Each dependency is represented by a "dependent", a "term", and a "dependee".
			// ... meaning: If and only if the "dependent"'s value equals the "term", the "dependee" will be visible.
			 // Example: Only if element 54 contains "- Anderer -", field 60 will become visible.
			echo <<<EOT
<script>
	var dependencies=[
		["54", "- Anderer -", "60"],
	];
</script>

EOT;
			// ... Ultimately, this array should be filled from a JSON encoded database setting.
			// ------------------------------------------

			queue_js_file('conditionalelements');

		} # if ($module === 'default' ...

	} # public function hookAdminHead()

} # class