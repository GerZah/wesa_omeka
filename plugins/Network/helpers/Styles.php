<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Explode a comma-delimited string. Trim and strip whitespace.
 *
 * @param string $list A comma-delimited list.
 * @return array The array of strings.
 */
function in_explode($list)
{
    return explode(',', trim(str_replace(' ', '', $list)));
}


/**
 * Get array of shared style columns.
 *
 * @return array An array of column names.
 */
function in_getStyles()
{
    return array(

        // DATES
        'start_date',
        'end_date',
        'after_date',
        'before_date'

    );
}
