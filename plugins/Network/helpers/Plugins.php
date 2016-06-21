<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Gather exhibit expansion tables.
 *
 * @return array An array of `Network_Table_Expansion`.
 */
function in_getExhibitExpansions()
{
    return apply_filters('network_exhibit_expansions', array());
}


/**
 * Gather record expansion tables.
 *
 * @return array An array of `Network_Table_Expansion`.
 */
function in_getRecordExpansions()
{
    return apply_filters('network_record_expansions', array());
}


/**
 * Gather exhibit tabs via the `network_exhibit_tabs` filter.
 *
 * @param NetworkExhibit $exhibit The exhibit.
 * @return array An array of widget name => ids.
 */
function in_getExhibitTabs($exhibit)
{
    return apply_filters('network_exhibit_tabs', array(), array(
        'exhibit' => $exhibit
    ));
}


/**
 * Gather global properties exposed via the `network_globals` filter.
 *
 * @param NetworkExhibit $exhibit The exhibit.
 * @return array The modified array of key => values.
 */
function in_getGlobals($exhibit)
{
    return apply_filters('network_globals', array(), array(
        'exhibit' => $exhibit
    ));
}
