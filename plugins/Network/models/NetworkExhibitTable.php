<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NetworkExhibitTable extends Network_Table_Expandable
{


    /**
     * Gather expansion tables.
     *
     * @return array The tables.
     */
    public function getExpansionTables()
    {
        return in_getExhibitExpansions();
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
            'Network_Exhibits'
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
     * Find exhibit by Id.
     *
     * @param string $slug The slug.
     * @return Omeka_record The exhibit.
     */
    public function findByExhibitId($id)
    {
        return $this->findBySql('id=?', array($id), true);
    }

    /**
     * Return the columns to be used for creating an HTML select of Networks.
     *
     * @return array
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function _getColumnPairs()
    {
        return array(
            'network_exhibits.id',
            'network_exhibits.title'
        );
    }

}
