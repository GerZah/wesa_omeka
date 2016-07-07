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
            'description'   => __('A top-level heading for the exhibit, displayed in the '.
                                  'page header in the public view for the exhibit.'),
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
            'description'   => __('By default, exhibits are visible only to site administrators. '.
                                  'Check here to publish the exhibit to the public site.'),
            'value'         => $this->exhibit->public
        ));

        // Graph Structure:
        $this->addElement('select', 'graph_structure', array(
            'label'         => __('Graph Structure'),
            'description'   => __('The network graph supports multiple graph structuring heuristics: '.
                                  'While "Grid" is the default, "Spread" provides an always different, '.
                                  'more organic presentation, which might require significantly more '.
                                  'browser resources to initially balance the graph.'),
            'multiOptions'  => array( 0 => __("Grid"), 1 => __("Spread" )),
            'value'         => $this->exhibit->graph_structure
        ));

        // Sticky node selection:
        $this->addElement('checkbox', 'sticky_node_selection', array(
            'label'         => __('Sticky Item Selection'),
            'description'   => __('By default, when clicking an item, this item and all its neighors are '.
                                  'being highlighted, while all the other items are being greyed out. '.
                                  'If you check this box, formerly highlighted items will remain highlighed '.
                                  'until clicking the background. This may help exploring higher-level '.
                                  'neighborhood contexts by clicking from item to item.'),
            'value'         => $this->exhibit->sticky_node_selection
        ));

        // Color item types:
        $this->addElement('checkbox', 'color_item_types', array(
            'label'         => __('Color Item Types'),
            'description'   => __('By default, items from different item types will be displayed '.
                                  'in different colors. Uncheck this box if you want all of them '.
                                  'to remain black/grey.'),
            'value'         => $this->exhibit->color_item_types
        ));

        // All Items:
        $this->addElement('checkbox', 'all_items', array(
            'label'         => __('All Items'),
            'description'   => __('By default, the network will display only those items that have '.
                                  'at least one connection to one other item. Check this box to '.
                                  'force displaying all imported items.'),
            'value'         => $this->exhibit->all_items
        ));

        // All Relations
        $this->addElement('checkbox', 'all_relations', array(
            'label'         => __('All Item Relations'),
            'description'   => __('By default, all relations will be displayed. '.
                                  'Uncheck this box to limit the displayed relations.'),
            'value'         => $this->exhibit->all_relations
        ));

        //select item relations
        $itemRelationValues = array();
        $itemRelationValues = get_table_options('ItemRelationsProperty');
        unset($itemRelationValues[""]); // remove "Select below"

        $this->addElement('multiselect', 'selected_relations', array(
            'label' => __('Item Relations'),
            'description' => __('Please select all relations that you would like to display. '.
                                'If you do not want to display any relations, deselect all.'),
            'value'  => explode(",", $this->exhibit->selected_relations),
            'multiOptions' => $itemRelationValues,
            'size' => 10
        ));
        $this->addElement('button', 'unselect_relations', array(
            'label' => __('Unselect'),
            'class' => "red button"
        ));

        // select item references
        if (NetworkPlugin::itemReferencesActive()) {
          // All References
          $this->addElement('checkbox', 'all_references', array(
              'label'         => __('All Item References'),
              'description'   => __('By default, all reference elements will be displayed. '.
                                    'Uncheck this box to limit the displayed element types.'),
              'value'         => $this->exhibit->all_references
          ));

          // Selected References:

          // vgl. ItemReferences:_retrieveReferenceElements()
          $referenceElementsJson=get_option('item_references_select');
          if (!$referenceElementsJson) { $referenceElementsJson="[]"; }
          $referenceElements = json_decode($referenceElementsJson,true);
          $referenceElementTitles = NetworkPlugin::referenceElementTitles($referenceElements);

          $this->addElement('multiselect', 'selected_references', array(
              'label'         => __('Item References'),
              'description' => __('Please select all reference elements that you would like to display. '.
                                  'If you do not want to display any reference elements, deselect all.'),
              'multiOptions'  => $referenceElementTitles,
              'value'         => explode(",", $this->exhibit->selected_references),
              'size' => 10
          ));
          $this->addElement('button', 'unselect_references', array(
              'label' => __('Unselect'),
              'class' => "red button"
          ));
        }

        // Submit:
        $this->addElement('submit', 'submit', array(
            'label' => __('Save Exhibit')
        ));

        $this->addDisplayGroup(array(
            'title',
            'graph_structure',
            'sticky_node_selection',
            'color_item_types',
            'public',
            'all_items',
            'all_relations',
            'selected_relations',
            'unselect_relations',
            'all_references',
            'selected_references',
            'unselect_references',
        ), 'fields');

        $this->addDisplayGroup(array(
            'submit'
        ), 'submit_button');

    }


}
