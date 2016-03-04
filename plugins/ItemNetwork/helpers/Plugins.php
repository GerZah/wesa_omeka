<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Gather exhibit expansion tables.
 *
 * @return array An array of `Neatline_Table_Expansion`.
 */
function in_getExhibitExpansions()
{
    return apply_filters('itemnetwork_exhibit_expansions', array());
}


/**
 * Gather record expansion tables.
 *
 * @return array An array of `Neatline_Table_Expansion`.
 */
function in_getRecordExpansions()
{
    return apply_filters('itemnetwork_record_expansions', array());
}
