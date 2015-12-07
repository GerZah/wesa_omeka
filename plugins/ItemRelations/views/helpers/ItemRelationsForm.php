<?php
/**
 * Helper to display the Item Relations Form.
 */
class ItemRelations_View_Helper_ItemRelationsForm extends Zend_View_Helper_Abstract
{
    /**
     * Returns the form code to add item relations.
     *
     * @param Item $item
     * @return string Html string.
     */
    public function itemRelationsForm($item)
    {
        $view = $this->view;
        $db = get_db();

        // Prepare the list of subjects to avoid sql queries in the view.
        $subjectRelations = ItemRelationsPlugin::prepareSubjectRelations($item);
        $sql = "SELECT id FROM {$db->ItemRelationsProperty} WHERE label = ?";
        foreach ($subjectRelations as &$subjectRelation) {
            // Get the default value to be selected in dropdown.
            $subjectRelation['subject_id'] = $db->fetchOne($sql, array($subjectRelation['relation_text']));
        }

        // Prepare list of used item types for the select form.
        $itemTypesList = array(
            '-1' => '- ' . __('All') . ' -',
        );
        $itemTypesList += $this->_getUsedItemTypes();

        $html = $view->partial('common/item-relations-form.php', array(
            'item' => $item,
            'provideRelationComments' => get_option('item_relations_provide_relation_comments'),
            'formSelectProperties' => get_table_options('ItemRelationsProperty'),
            'subjectRelations' => $subjectRelations,
            'objectRelations' => ItemRelationsPlugin::prepareObjectRelations($item),
            'itemTypesList' => $itemTypesList,
        ));

        $html .= '<link href="' . css_src('lity.min', 'javascripts/lity') . '" rel="stylesheet">';
        $html .= '<link href="' . css_src('item-relations') . '" rel="stylesheet">';
        $html .= js_tag('lity.min', $dir = 'javascripts/lity');
        $html .= '<script type="text/javascript">var url = ' . json_encode(url('item-relations/lookup/')) . '</script>';
        $html .= js_tag('item-relations');

        return $html;
    }

    /**
     * Get the list of used item types for select form.
     *
     * @return array
     */
    protected function _getUsedItemTypes()
    {
        $db = get_db();

        $itemTypesTable = $db->getTable('ItemType');
        $itemTypesAlias = $itemTypesTable->getTableAlias();

        $select = $itemTypesTable->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(array(), array($itemTypesAlias . '.id', $itemTypesAlias . '.name'))
            ->joinInner(array('items' => $db->Item), "items.item_type_id = $itemTypesAlias.id", array())
            ->group($itemTypesAlias . '.id')
            ->order($itemTypesAlias . '.name ASC');

        $permissions = new Omeka_Db_Select_PublicPermissions('Items');
        $permissions->apply($select, 'items');

        $itemTypes = $db->fetchPairs($select);

        return $itemTypes;
    }
}
