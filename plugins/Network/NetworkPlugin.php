<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class NetworkPlugin extends Omeka_Plugin_AbstractPlugin
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
        'admin_navigation_main',
        'network_globals'
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
      $sql_exhibit = "DROP TABLE IF EXISTS `$db_exhibit->NetworkExhibit`";
      $db_exhibit->query($sql_exhibit);

      $db_record = $this->_db;
      $sql_record = "DROP TABLE IF EXISTS `$db_record->NetworkRecord`";
      $db_record->query($sql_record);

    }

    /**
     * Define the ACL.
     *
     * @param array $args Contains: `acl` (Zend_Acl).
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
     * Propagate item updates to Network records.
     *
     * @param array $args Contains: `record` (Item).
     */
    public function hookAfterSaveItem($args)
    {
        $records = $this->_db->getTable('NetworkRecord');
        $records->syncItem($args['record']);
    }

    // FILTERS
    // ------------------------------------------------------------------------


    /**
     * Add link to main public menu bar.
     *
     * @param array $tabs Tabs, <LABEL> => <URI> pairs.
     * @return array The tab array with the "Network" tab.
     */
    public function filterPublicNavigationMain($tabs)
    {
        $tabs[] = array('label' => 'Network', 'uri' => url('network'));
        return $tabs;
    }


    /**
     * Add link to main admin menu bar.
     *
     * @param array $tabs Tabs, <LABEL> => <URI> pairs.
     * @return array The tab array with the "Network" tab.
     */
    public function filterAdminNavigationMain($tabs)
    {
        $tabs[] = array('label' => 'Network', 'uri' => url('network'));
        return $tabs;
    }


    /**
     * Register properties on `Network.g`.
     *
     * @param array $globals The array of global properties.
     * @param array $args Contains: `exhibit` (NetworkExhibit).
     * @return array The modified array.
     */
    public function filterNetworkGlobals($globals, $args)
    {
        return array_merge($globals, in_globals($args['exhibit']));
    }


    /**
     * Register record presenters.
     *
     * @param array $presenters Presenters, <NAME> => <ID>.
     * @return array The array, with None and StaticBubble.
     */
    public function filterNetworkPresenters($presenters)
    {
        return array_merge($presenters, array(
            'None'              => 'None',
            'Static Bubble'     => 'StaticBubble'
        ));
    }

    /**
     * Register the exhibit layout for Nealtine.
     *
     * @return void
     * @author Eric Rochester <erochest@virginia.edu>
     **/
    public function filterExhibitLayouts($layouts)
    {
        $layouts['network'] = array(
            'name'        => __('Network'),
            'description' => __('Embed a Network exhibit.')
        );
        return $layouts;
    }
}
