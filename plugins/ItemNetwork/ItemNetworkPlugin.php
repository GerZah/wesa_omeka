<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class ItemNetworkPlugin extends Omeka_Plugin_AbstractPlugin
{


    protected $_hooks = array(
        'install',
        'uninstall',
        'define_acl',
        'initialize',
        'define_routes',
        'after_save_item'
    );


    protected $_filters = array(
        'public_navigation_main',
        'admin_navigation_main'
    );


    // HOOKS
    // ------------------------------------------------------------------------


    /**
     * Create exhibit and record tables.
     */
    public function hookInstall()
    {
        in_schema();
    }


    /**
     * Drop exhibit and record tables.
     */
    public function hookUninstall()
    {

      // Drop the table.
      $db_exhibit = $this->_db;
      $sql_exhibit = "DROP TABLE IF EXISTS `$db_exhibit->ItemNetworkExhibit`";
      $db_exhibit->query($sql_exhibit);

      $db_record = $this->_db;
      $sql_record = "DROP TABLE IF EXISTS `$db_record->ItemNetworkRecord`";
      $db_record->query($sql_record);

    }

    /**
     * Define the ACL.
     *
     */
    public function hookDefineAcl($args)
    {
        in_defineAcl($args['acl']);
    }


    /**
     * Add translation source.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__).'/languages');
    }


    /**
     * Register routes.
     *
     * @param array $args Contains: `router` (Zend_Config).
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(
            IN_DIR.'/routes.ini'
        ));
    }


    /**
     * Propagate item updates to ItemNetwork records.
     *
     * @param array $args Contains: `record` (Item).
     */
    public function hookAfterSaveItem($args)
    {
        $records = $this->_db->getTable('ItemNetworkRecord');
        $records->syncItem($args['record']);
    }

    // FILTERS
    // ------------------------------------------------------------------------


    /**
     * Add link to main public menu bar.
     *
     * @param array $tabs Tabs, <LABEL> => <URI> pairs.
     * @return array The tab array with the "ItemNetwork" tab.
     */
    public function filterPublicNavigationMain($tabs)
    {
        $tabs[] = array('label' => 'Item Network', 'uri' => url('itemnetwork'));
        return $tabs;
    }


    /**
     * Add link to main admin menu bar.
     *
     * @param array $tabs Tabs, <LABEL> => <URI> pairs.
     * @return array The tab array with the "ItemNetwork" tab.
     */
    public function filterAdminNavigationMain($tabs)
    {
        $tabs[] = array('label' => 'Item Network', 'uri' => url('itemnetwork'));
        return $tabs;
    }


}
