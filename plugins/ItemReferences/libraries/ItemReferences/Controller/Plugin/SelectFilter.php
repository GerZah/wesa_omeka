<?php
/**
 * Filter selected form elements to a select menu containing custom terms.
 *
 * @package Omeka\Plugins\ ItemReferences
 */
class ItemReferences_Controller_Plugin_SelectFilter extends Zend_Controller_Plugin_Abstract
{
    /**
     * All routes that render an item element form, including those requested
     * via AJAX.
     *
     * @var array
     */
    protected $_defaultRoutes = array(
        array('module' => 'default', 'controller' => 'items',
              'actions' => array('add', 'edit', 'change-type')),
        array('module' => 'default', 'controller' => 'elements',
              'actions' => array('element-form')),
    );


    protected $_itemReferences;

    /**
     * Set the filters pre-dispatch only on configured routes.
     *
     * @param Zend_Controller_Request_Abstract
     */
    public function preDispatch($request)
    {
        $db = get_db();

        // Some routes don't have a default module, which resolves to NULL.
        $currentModule = is_null($request->getModuleName()) ? 'default' : $request->getModuleName();
        $currentController = $request->getControllerName();
        $currentAction = $request->getActionName();

        // Allow plugins to register routes that contain form inputs rendered by
        // Omeka_View_Helper_ElementForm::_displayFormInput().
        $routes = apply_filters('item_references_routes', $this->_defaultRoutes);

        // Apply filters to defined routes.
        foreach ($routes as $route) {

            // Check registered routed against the current route.
            if ($route['module'] != $currentModule
             || $route['controller'] != $currentController
             || !in_array($currentAction, $route['actions']))
            {
                continue;
            }

            // Add the filters if the current route is registered. Cache the
            // vocab terms for use by the filter callbacks.
            $select = $db->getTable('SimpleVocabTerm')->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('element_id', 'terms'));
            $this->_itemReferences = $db->fetchPairs($select);
            foreach ($this->_itemReferences as $element_id => $terms) {
                $element = $db->getTable('Element')->find($element_id);
                $elementSet = $db->getTable('ElementSet')->find($element->element_set_id);
                add_filter(array('ElementInput', 'Item', $elementSet->name, $element->name),
                           array($this, 'filterElementInput'));
            }
            // Once the filter is applied for one route there is no need to
            // continue looping the routes.
            break;
        }
    }

    /**
     * Filter the element input.
     *
     * @param array $components
     * @param array $args
     * @return array
     */
    public function filterElementInput($components, $args)
    {
        echo common('itemreferenceslist', null, 'index');
        $terms = explode("\n", $this->_itemReferences[$args['element']->id]);
        $selectTerms = array('' => 'Select Below') + array_combine($terms, $terms);
        $components['input'] = get_view()->formText( $args['input_name_stem'] . '[text]',  $args['value'], array('style' => 'width: 300px;'),null);
        $components['html_checkbox'] = false;
        return $components;
    }
}
