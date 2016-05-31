<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class Network_Form_Exhibit extends Omeka_Form
{


    private $exhibit;


    /**
     * Construct the exhibit add/edit form.
     */
    public function init()
    {
        parent::init();
        $this->_registerElements();
    }


    /**
     * Bind an exhibit record to the form.
     *
     * @param NetworkExhibit $exhibit The exhibit record.
     */
    public function setExhibit(NetworkExhibit $exhibit)
    {
        $this->exhibit = $exhibit;
    }


    /**
     * Define the form elements.
     */
    private function _registerElements()
    {

        // Title:
        $this->addElement('text', 'title', array(
            'label'         => __('Title'),
            'description'   => __('A top-level heading for the exhibit, displayed in the page header in the public view for the exhibit.'),
            'value'         => $this->exhibit->title,
            'required'      => true,
            'size'          => 40,
            'validators'    => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => __('Enter a title.')
                        )
                    )
                )
            )
        ));

        // Public:
        $this->addElement('checkbox', 'public', array(
            'label'         => __('Public'),
            'description'   => __('By default, exhibits are visible only to site administrators. Check here to publish the exhibit to the public site.'),
            'value'         => $this->exhibit->public
        ));
        //select item relations

        $itemRelationValues = array();
        $itemRelationValues = get_table_options('ItemRelationsProperty');
        unset($itemRelationValues[""]); // remove "Select below"

        $this->addElement('multiselect', 'selected_relations', array(
            'label' => __('Item Relations'),
            'description' => __("By default, all the item relations are selected. Please select the required item relations."),
            'value'  => explode(",", $this->exhibit->selected_relations),
            'multiOptions' => $itemRelationValues,
            'size' => 20,
              'style' => 'width: 500px;'
          ));

        // Submit:
        $this->addElement('submit', 'submit', array(
            'label' => __('Save Exhibit')
        ));

        $this->addDisplayGroup(array(
            'title',
            'public',
            'selected_relations'
        ), 'fields');

        $this->addDisplayGroup(array(
            'submit'
        ), 'submit_button');

    }


}
