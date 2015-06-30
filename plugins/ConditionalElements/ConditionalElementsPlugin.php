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
			 // Example: Only if element 53 contains "- Anderes Land -", field 59 will become visible.
			echo <<<EOT
<script>
var dependencies=[
["53", "– Anderes Land –", "59"],
["54", "– Anderer Ort –", "60"],
["56", "– Anderer Fundort –", "70"],
["55", "Bildquelle", "62"], // Bildinhalt
["62", "– Anderer Bildinhalt –", "63"],
["55", "Bildquelle", "64"], // Bildart
["64", "– Andere Bildart –", "65"],
["55", "Textquelle", "58"],
["58", "– Andere Textquelle –", "61"],
["55", "Sachquelle", "66"],
["66", "– Andere Sachquelle –", "67"],
["55", "Immaterielle Quelle", "68"],
["68", "– Andere Immaterielle Quelle –", "69"],
["57", "– Anderer Beruf –", "71"],
["72", "– Andere Funktion –", "73"],
];
</script>

EOT;
			// ... Ultimately, this array should be filled from a JSON encoded database setting.
			// ------------------------------------------

			queue_js_file('conditionalelements');

		} # if ($module === 'default' ...

	} # public function hookAdminHead()

} # class