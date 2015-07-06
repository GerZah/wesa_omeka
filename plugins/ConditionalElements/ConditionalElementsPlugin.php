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
			// Each dependency is represented by a "dependee", a "term", and a "dependent".
			// ... meaning: If and only if the "dependee"'s value equals the "term", the "dependent" will be visible.
			// Example: Only if element 53 contains "- Anderes Land -", field 59 will become visible.

			// Create sample settings and store them in a setting
			/* * /
			$conditionalElementsDep=array(
					array("53", "– Anderes Land –", "59"),
					array("54", "– Anderer Ort –", "60"),
					array("56", "– Anderer Fundort –", "70"),
					array("55", "Bildquelle", "62"), // Bildinhalt
					array("62", "– Anderer Bildinhalt –", "63"),
					array("55", "Bildquelle", "64"), // Bildart
					array("64", "– Andere Bildart –", "65"),
					array("55", "Textquelle", "58"),
					array("58", "– Andere Textquelle –", "61"),
					array("55", "Sachquelle", "66"),
					array("66", "– Andere Sachquelle –", "67"),
					array("55", "Immaterielle Quelle", "68"),
					array("68", "– Andere Immaterielle Quelle –", "69"),
					array("57", "– Anderer Beruf –", "71"),
					array("72", "– Andere Funktion –", "73"),
				);

			$json=json_encode($conditionalElementsDep);
      set_option('conditional_elements_dependencies', $json);
			/* */

			// Retrieve dependencies from Database
			/* */
			$json=get_option('conditional_elements_dependencies');
			if (!$json) { $json="null"; }
			/* */

			echo "<script>var conditionalElementsDep=$json;</script>";
			// ------------------------------------------

			queue_js_file('conditionalelements');

		} # if ($module === 'default' ...

	} # public function hookAdminHead()

} # class