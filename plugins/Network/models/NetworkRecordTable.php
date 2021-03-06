<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NetworkRecordTable extends Network_Table_Expandable
{


    /**
     * Gather expansion tables.
     *
     * @return array The tables.
     */
    public function getExpansionTables()
    {
        return in_getRecordExpansions();
    }


    /**
     * Get a starting selection object for record queries.
     *
     * @return Omeka_Db_Select The modified select.
     */
    public function getSelect()
    {

        $select = parent::getSelect();

        // Select `coverage` as plaintext.
        //$select->columns(array('coverage' => in_getGeometry('coverage')));

        // Order by date created.
        $select->order('added DESC');

        return $select;

    }


    /**
     * Update record item references when an item is changed.
     *
     * @param Item $item The item record.
     */
    public function syncItem($item)
    {
        $records = $this->findBySql('item_id=?', array($item->id));
        foreach ($records as $record) { $record->save(); }
    }


    // RECORDS API
    // ------------------------------------------------------------------------


    /**
     * Construct records array for exhibit and editor.
     *
     * @param array $params Associative array of filter parameters.
     * @return array The collection of records.
     */
    public function queryRecords($params=array())
    {

        $this->params = $params;
        $this->select = $this->getSelect();
        $this->result = array();

        $this->beforeQuery();
        $this->result['records'] = $this->select->query()->fetchAll();
        $this->afterQuery();

        return $this->result;

    }


    /**
     * Prepare the select.
     */
    public function beforeQuery()
    {
        $this->filterExhibit();
        $this->filterHasDate();
        $this->filterPlugins();
        $this->filterTags();
    }


    /**
     * Process the result.
     */
    public function afterQuery()
    {
        $this->countRecords();
    }


    // FILTERS
    // ------------------------------------------------------------------------


    /**
     * Filter by exhibit.
     */
    protected function filterExhibit()
    {
        if (isset($this->params['exhibit_id'])) {

            $this->select->where(
                "exhibit_id = ?", $this->params['exhibit_id']
            );

        }
    }

    /**
     * Filter by tags query.
     */
    protected function filterTags()
    {
        if (isset($this->params['tags'])) {

            foreach ($this->params['tags'] as $tag) {
                $this->select->where(
                    "MATCH (tags) AGAINST (? IN BOOLEAN MODE)",
                    $tag
                );
            }

        }
    }


    /**
     * Filter by keyword query.
     */
    protected function filterQuery()
    {
        if (isset($this->params['query'])) {

            $this->select->where(
                "MATCH (item_title, title, body) AGAINST (?)",
                $this->params['query']
            );

        }
    }


    /**
     * Match records with dates.
     */
    protected function filterHasDate()
    {
        if (isset($this->params['hasDate'])) {

            $this->select->where(
                'start_date IS NOT NULL OR end_date IS NOT NULL'
            );

        }
    }


    /**
     * Pass the select to plugins for modification.
     */
    protected function filterPlugins()
    {
        $this->select = apply_filters('network_query_records',
            $this->select, array('params' => $this->params)
        );
    }


    // POST-PROCESSING
    // ------------------------------------------------------------------------


    /**
     * Count the total, un-paginated size of the result set.
     */
    protected function countRecords()
    {

        // Strip off limit and columns.
        $this->select->reset(Zend_Db_Select::LIMIT_COUNT);
        $this->select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $this->select->reset(Zend_Db_Select::COLUMNS);

        // Count the total result size.
        $this->result['numFound'] = $this->select->columns('COUNT(*)')->
            query()->fetchColumn();

    }


}
