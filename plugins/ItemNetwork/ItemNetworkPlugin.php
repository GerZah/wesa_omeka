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

          // Create the table.
          $db_exhibit = $this->_db;
          $sql_exhibit = "
          CREATE TABLE IF NOT EXISTS `$db_exhibit->ItemNetworkExhibit` (

                  id                      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  owner_id                INT(10) UNSIGNED NOT NULL,
                  added                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  modified                TIMESTAMP NULL,
                  published               TIMESTAMP NULL,
                  item_query              TEXT NULL,
                  title                   TEXT NULL,
                  slug                    VARCHAR(100) NOT NULL,
                  public                  TINYINT(1) NOT NULL,
                  PRIMARY KEY             (id)

            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
          $db_exhibit->query($sql_exhibit);

          $db_item = $this->_db;
          $sql_item = "
          CREATE TABLE IF NOT EXISTS `$db_item->ItemNetworkRecord` (

                id                      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                owner_id                INT(10) UNSIGNED NOT NULL,
                item_id                 INT(10) UNSIGNED NULL,
                exhibit_id              INT(10) UNSIGNED NULL,
                added                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                modified                TIMESTAMP NULL,
                slug                    VARCHAR(100) NULL,
                title                   MEDIUMTEXT NULL,
                item_title              MEDIUMTEXT NULL,
                body                    MEDIUMTEXT NULL,
                coverage                GEOMETRY NOT NULL,
                tags                    TEXT NULL,
                widgets                 TEXT NULL,
                presenter               VARCHAR(100) NULL,
                start_date              VARCHAR(100) NULL,
                end_date                VARCHAR(100) NULL,
                after_date              VARCHAR(100) NULL,
                before_date             VARCHAR(100) NULL,
                PRIMARY KEY             (id)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
          $db_item->query($sql_item);

          $this->_installOptions();

    }


    /**
     * Drop exhibit and record tables.
     */
    public function hookUninstall()
    {
      // Drop the table.
      $db_exhibit = $this->_db;
      $sql_exhibit = "DROP TABLE IF EXISTS `$db->ItemNetworkExhibit`";
      $db_exhibit->query($sql_exhibit);

      $db_item = $this->_db;
      $sql_item = "DROP TABLE IF EXISTS `$db->ItemNetworkRecord`";
      $db_item->query($sql_item);

      $this->_uninstallOptions();
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
