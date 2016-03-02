<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


if (!defined('NL_DIR')) define('NL_DIR', dirname(__FILE__));

// Plugin:
require_once NL_DIR.'/ItemNetworkPlugin.php';

// Models:
require_once NL_DIR.'/models/abstract/ItemNetwork_Row_Abstract.php';
require_once NL_DIR.'/models/abstract/ItemNetwork_Row_Expandable.php';
require_once NL_DIR.'/models/abstract/ItemNetwork_Row_Expansion.php';
require_once NL_DIR.'/models/abstract/ItemNetwork_Table_Expandable.php';
require_once NL_DIR.'/models/abstract/ItemNetwork_Table_Expansion.php';


// Helper classes:
require_once NL_DIR.'/jobs/ItemNetwork_Job_ImportItems.php';
require_once NL_DIR.'/controllers/abstract/ItemNetwork_Controller_Rest.php';
require_once NL_DIR.'/assertions/ItemNetwork_Acl_Assert_RecordOwnership.php';
require_once NL_DIR.'/forms/ItemNetwork_Form_Exhibit.php';

// Helper functions:
require_once NL_DIR.'/helpers/Acl.php';
require_once NL_DIR.'/helpers/Plugins.php';
require_once NL_DIR.'/helpers/Views.php';


// Set the PUT source.
Zend_Registry::set('fileIn', 'php://input');


// Run the plugin.
$itemnetwork = new ItemNetworkPlugin();
$itemnetwork->setUp();
