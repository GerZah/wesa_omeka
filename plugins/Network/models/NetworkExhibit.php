<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NetworkExhibit extends Network_Row_Expandable
    implements Zend_Acl_Resource_Interface
{


    public $owner_id = 0;
    public $added;
    public $modified;
    public $published;
    public $nonpublic_items;
    public $item_query;
    public $title;
    public $graph_structure;
    public $sticky_node_selection;
    public $color_item_types;
    public $all_items;
    public $all_relations;
    public $selected_relations;
    public $all_references;
    public $selected_references;
    public $public = 0;

    /**
     * If the exhibit is being published to the public site for the first
     * time, set the `published` timestamp.
     *
     * @param array $values The POST/PUT data.
     */
    public function saveForm($values)
    {

        // Assign the values.
        $this->setArray($values);

        // If the exhibit is being set "public" for the first time, set the
        // `published` timestamp to the current date.

        if (is_null($this->published) && $this->public == 1) {
            $this->published = date(self::DATE_FORMAT);
        }

        $this->save();

    }


    /**
     * Get the number of active records in the exhibit.
     *
     * @return integer The record count.
     */
    public function getNumberOfRecords()
    {
        return $this->getTable('NetworkRecord')->count(array(
            'exhibit_id' => $this->id
        ));
    }


    /**
     * Get the routing parameters or the URL string for the exhibit.
     *
     * @param string $action The controller action.
     */
    public function getRecordUrl($action = 'show')
    {
        $urlHelper = new Omeka_View_Helper_Url;
        $params = array('action' => $action, 'id' => $this->id);
        return $urlHelper->url($params, 'networkActionId');
    }

    /**
     * Delete all records that belong to the exhibit.
     */
    public function deleteChildRecords()
    {

        // Get records table and name.
        $recordsTable = $this->getTable('NetworkRecord');
        $rName = $recordsTable->getTableName();

        // Gather record expansion tables.
        foreach (in_getRecordExpansions() as $expansion) {

            $eName = $expansion->getTableName();

            // Delete expansion rows on child records.
            $this->_db->query("DELETE $eName FROM $eName
                INNER JOIN $rName ON $eName.parent_id = $rName.id
                WHERE $rName.exhibit_id = $this->id
            ");

        }

        // Delete child records.
        $recordsTable->delete(
            $rName, array('exhibit_id=?' => $this->id)
        );

    }

  /**
     * Delete all child records when the exhibit is deleted.
     */
    protected function beforeDelete()
    {
        $this->deleteChildRecords();
    }


    /**
     * Associate the model with an ACL resource id.
     *
     * @return string The resource id.
     */
    public function getResourceId()
    {
        return 'Network_Exhibits';
    }


}
