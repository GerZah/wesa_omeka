<?php

/**
 * @package     omeka
 * @subpackage  Item Network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class ItemNetwork_ItemsController extends ItemNetwork_Controller_Rest
{


    /**
     * Set the controller model.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Item');
        parent::init();
    }


    /**
     * Get the compiled metadata for an individual Omeka item.
     * @REST
     */
    public function getAction()
    {

        // Load the Omeka item.
        $item = $this->_helper->db->findById();
        $record = null;

        // If a record is specified, load it.
        if (!is_null($this->_request->record)) {
            $rTable = $this->_helper->db->getTable('ItemNetworkItem');
            $record = $rTable->find($this->_request->record);
        }

        // Output the item metadata
        echo nl_getItemMarkup($item, $record);



    }
    function nl_getItemMarkup($item, $record=null)
    {

        // Set the item on the view.
        set_current_record('item', $item);

        if (!is_null($record)) {

            // Get exhibit slug and tags.
            $slug = $record->getExhibit()->slug;
            $tags = nl_explode($record->tags);

            // First, try to render an exhibit-specific `item-[tag].php` template.

            foreach ($tags as $tag) { try {
                return get_view()->render(
                    'exhibits/themes/'.$slug.'/item-'.$tag.'.php'
                );
            } catch (Exception $e) {}}

            // Next, try to render an exhibit-specific `item.php` template.

            try {
                return get_view()->render(
                    'exhibits/themes/'.$slug.'/item.php'
                );
            } catch (Exception $e) {}

        }

        // Default to the global `item.php` template, which is included in the
        // core plugin and can also be overridden in the public theme:

        return get_view()->render('exhibits/item.php');

    }


}
