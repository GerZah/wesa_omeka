<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class NetworkRecord extends Network_Row_Expandable
    implements Zend_Acl_Resource_Interface
{


    public $owner_id = 0;
    public $item_id;
    public $exhibit_id;
    public $added;
    public $modified;
    public $title;
    public $item_title;
    public $body;
    public $start_date;
    public $end_date;
    public $after_date;
    public $before_date;



        /**
        * Set exhibit and item references.
         *
         * @param NetworkExhibit $exhibit The exhibit record.
         * @param Item $item The item record.
         */
        public function __construct($exhibit=null, $item=null)
        {

            parent::__construct();

            // Set exhibit and item foreign keys.
            if (!is_null($exhibit)) $this->exhibit_id = $exhibit->id;
            if (!is_null($item)) $this->item_id = $item->id;


        }

    /**
     * Get the parent exhibit record.
     *
     * @return NetworkExhibit The parent exhibit.
     */
    public function getExhibit()
    {
        return get_record_by_id('NetworkExhibit', $this->exhibit_id);
    }


    /**
     * Get the parent item record.
     *
     * @return Item The parent item.
     */
    public function getItem()
    {
        return get_record_by_id('Item', $this->item_id);
    }


    /**
     * Save data from a POST or PUT request.
     *
     * @param array $values The POST/PUT values.
     */
    public function saveForm($values)
    {

        // Cache the original tags string.
        $oldTags = in_explode($this->tags);

        // Mass-assign the form.
        $this->setArray($values);

    }


    /**
     * Compile the Omeka item reference, if one exists.
     */
    public function compileItem()
    {

        // Get parent item.
        $item = $this->getItem();
        if (!$item) return;

        // Compile the item title:
        $this->item_title = metadata($item, array('Dublin Core', 'Title'));

    }


    /**
     * Before saving, compile the coverage and item reference.
     */
    public function save()
    {
        $this->compileItem();
        parent::save();
    }


    /**
     * Associate the model with an ACL resource id.
     *
     * @return string The resource id.
     */
    public function getResourceId()
    {
        return 'Network_Records';
    }


}
