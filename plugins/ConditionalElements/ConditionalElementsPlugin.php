<?php

/**
 * ConditionalElements plugin.
 *
 * @package Omeka\Plugins\ConditionalElements
 */
class ConditionalElementsPlugin extends Omeka_Plugin_AbstractPlugin {
	/**
	 * @var array This plugin's hooks.
	 */
	protected $_hooks = array(
		'admin_head', // embed our jQuery code when adding / editing objects
		'define_acl',
	);

	    /**
	     * @var array This plugin's filters.
	     */
	protected $_filters = array('admin_navigation_main');

	function hookDefineAcl($args)
	{
	    // Restrict access to super and admin users.
	    $args['acl']->addResource('ConditionalElements_Index');
	}

	function filterAdminNavigationMain($nav)
	{
	  if(is_allowed('ConditionalElements_Index', 'index')) {
	      $nav[] = array('label' => __('Conditional Elements'), 'uri' => url('conditional-elements'));
	  }
	  return $nav;
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
			if (!$json) { $json="null"; } else { $json = $this->_removeOutdatedDependencies($json); }
			/* */

			echo "<script>var conditionalElementsDep=$json;</script>";
			// ------------------------------------------

			queue_js_file('conditionalelements');
		} # if ($module === 'default' ...
	} # public function hookAdminHead()

	/**
	 * Check JSON array of existing dependencies for non-existent dependents / dependees and filter them
	 */
	private function _removeOutdatedDependencies($json) {

		$result = $json;
		// echo "Pre JSON: $result<br>\n";

		if ($json) {

			$existing_ids = array();
			$db = get_db();
			$select = "SELECT id FROM $db->Element";
			$ids = $db->fetchAll($select);
			foreach($ids as $id) { $existing_ids[$id["id"]] = true; }

			$arr = json_decode($result);
			// echo "<pre>==== Pre Array = ".count($arr).": "; print_r($arr); echo "</pre>\n";

			$newarr = array();

			foreach($arr as $dep) {
				if ( isset($existing_ids[$dep[0]]) and isset($existing_ids[$dep[2]]) ) {
					$newarr[] = $dep;
				}
			}
			// echo "<pre>==== Post Array = ".count($newarr).": "; print_r($newarr); echo "</pre>\n";

			$result=json_encode($newarr);
		} # if ($json)

		// echo "Post JSON: $result<br>\n"; die();
		return $result;

	}

} # class
