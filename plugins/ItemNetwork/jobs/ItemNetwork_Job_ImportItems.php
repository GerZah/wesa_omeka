<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
  */


class ItemNetwork_Job_ImportItems extends Omeka_Job_AbstractJob
{


    /**
     * Import Omeka items.
     */
    public function perform()
    {

        $_records  = $this->_db->getTable('ItemNetworkRecord');
        $_exhibits = $this->_db->getTable('ItemNetworkExhibit');
        $_items    = $this->_db->getTable('Item');

        // Load the exhibit, alias the query.
        $exhibit = $_exhibits->find($this->_options['exhibit_id']);
        $query = $this->_options['query'];

        $i = 0;
        while ($items = $_items->findBy($query, 10, $i)) {
            foreach ($items as $item) {

                // Try to find an existing record.
                $record = $_records->findBySql('exhibit_id=? && item_id=?',
                    array($exhibit->id, $item->id), true
                );

                // Otherwise, create one.
                if (!$record) {
                    $record = new ItemNetworkRecord($exhibit, $item);
                    $record->added = $item->added;
                }

                $record->save();

            }
            $i++;
        }

    }


}
