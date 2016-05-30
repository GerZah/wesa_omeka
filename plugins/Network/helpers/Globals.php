<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Construct exhibit globals array.
 *
 * @param NetworkExhibit $exhibit The exhibit.
 * @return array The array of globals.
 */
function in_globals($exhibit)
{

    // Get style defaults from `styles.ini`.
    $styles = new Zend_Config_Ini(IN_DIR.'/styles.ini');

    return array('network' => array(

        // EXHIBIT
        // --------------------------------------------------------------------

        'exhibit'           => $exhibit->toArray(),

        // API ENDPOINTS
        // --------------------------------------------------------------------

        'record_api'        => public_url('network/records'),
        'exhibit_api'       => public_url('network/exhibits/'.$exhibit->id),
        'item_search_api'   => public_url('items/browse'),
        'item_body_api'     => public_url('network/items'),

        // STRINGS
        // --------------------------------------------------------------------

        'strings'           => in_getStrings(IN_DIR.'/strings.json'),

        // OPENLAYERS
        // --------------------------------------------------------------------

        'openlayers_theme'  => in_getOpenLayersThemeDir()

    ));

}
