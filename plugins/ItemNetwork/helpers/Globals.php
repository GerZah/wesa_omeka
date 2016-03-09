<?php

/**
 * @package     omeka
 * @subpackage  itemnetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Construct exhibit globals array.
 *
 * @param ItemNetworkExhibit $exhibit The exhibit.
 * @return array The array of globals.
 */
function in_globals($exhibit)
{

    // Get style defaults from `styles.ini`.
    $styles = new Zend_Config_Ini(IN_DIR.'/styles.ini');

    return array('itemnetwork' => array(

        // EXHIBIT
        // --------------------------------------------------------------------

        'exhibit'           => $exhibit->toArray(),

        // API ENDPOINTS
        // --------------------------------------------------------------------

        'record_api'        => public_url('itemnetwork/records'),
        'exhibit_api'       => public_url('itemnetwork/exhibits/'.$exhibit->id),
        'item_search_api'   => public_url('items/browse'),
        'item_body_api'     => public_url('itemnetwork/items'),

        // CONSTANTS
        // --------------------------------------------------------------------

        'wms_mime'          => get_plugin_ini('ItemNetwork', 'wms_mime'),
        'per_page'          => (int) get_plugin_ini('ItemNetwork', 'per_page'),
        'styles'            => $styles->toArray(),


        // STRINGS
        // --------------------------------------------------------------------

        'strings'           => nl_getStrings(IN_DIR.'/strings.json'),


    ));

}
