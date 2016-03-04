<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
  */

class ItemNetwork_RecordsController extends ItemNetwork_Controller_Rest
{


    /**
     * Set the default model, get tables.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ItemNetworkRecord');
        $this->_exhibits = $this->_helper->db->getTable('ItemNetworkExhibit');
        $this->_records  = $this->_helper->db->getTable('ItemNetworkRecord');
        parent::init();
    }


    /**
     * Get a collection of records.
     * @REST
     */
    public function listAction()
    {
        echo Zend_Json::encode($this->_records->queryRecords(
            $this->_request->getParams()
        ));
    }


    /**
     * Get an individual record.
     * @REST
     */
    public function getAction()
    {
        echo Zend_Json::encode($this->_helper->db->findById()->toArray());
    }


    /**
     * Create a record.
     * @REST
     */
    public function postAction()
    {

        // Create record.
        $record = new ItemNetworkRecord();
        $post = Zend_Json::decode($this->_request->getRawBody());
        $record->saveForm($post);

        // Respond with record data.
        echo Zend_Json::encode($record->toArray());

    }


    /**
     * Update a record.
     * @REST
     */
    public function putAction()
    {

        // Find the record.
        $record = $this->_records->find($this->_request->id);

        // Decode and save PUT body.
        $record->saveForm(Zend_Json::decode(file_get_contents(
            Zend_Registry::get('fileIn')), true
        ));

        // Respond with record data.
        echo Zend_Json::encode($record->toArray());

    }


    /**
     * Delete a record.
     * @REST
     */
    public function deleteAction()
    {

        // Find and delete the record.
        $record = $this->_records->find($this->_request->id);
        $record->delete();

        // Return an empty JSON object.
        echo Zend_Json::encode(array());

    }


}
