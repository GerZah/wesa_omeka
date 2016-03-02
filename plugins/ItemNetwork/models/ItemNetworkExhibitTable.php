<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class ItemNetworkExhibitTable extends ItemNetwork_Table_Expandable
{


    /**
     * Gather expansion tables.
     *
     * @return array The tables.
     */
    public function getExpansionTables()
    {
        return nl_getExhibitExpansions();
    }


    /**
     * Add public/private permissions filtering to base select.
     *
     * @return Omeka_Db_Select The filtered select.
     */
    public function getSelect()
    {

        $select = parent::getSelect();

        // Create the permissions manager.
        $permissions = new Omeka_Db_Select_PublicPermissions(
            'ItemNetwork_Exhibits'
        );

        // Filter out private exhibits for public users.
        $permissions->apply($select, $this->getTableAlias());

        return $select;

    }


    /**
     * Find exhibit by slug.
     *
     * @param string $slug The slug.
     * @return Omeka_record The exhibit.
     */
    public function findBySlug($slug)
    {
        return $this->findBySql('slug=?', array($slug), true);
    }

    /**
     * Return the columns to be used for creating an HTML select of ItemNetworks.
     *
     * @return array
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function _getColumnPairs()
    {
        return array(
            'itemnetwork_exhibits.id',
            'itemnetwork_exhibits.title'
        );
    }

}
