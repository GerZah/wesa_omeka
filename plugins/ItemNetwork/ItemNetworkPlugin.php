<?php
/**
 * Item Network
 *
 * @copyright
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */


/**
 * Item Network plugin.
 */
class ItemNetworkPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array('install',
                              'uninstall',
                              'initialize',
                              'define_acl',
                              'define_routes',
                              'config_form');

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array('admin_navigation_main',
                                'public_navigation_main');


    /**
     * Install the plugin.
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
        CREATE TABLE IF NOT EXISTS `$db_item->ItemNetworkItem` (

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
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        // Drop the table.
        $db_exhibit = $this->_db;
        $sql_exhibit = "DROP TABLE IF EXISTS `$db->ItemNetworkExhibit`";
        $db_exhibit->query($sql_exhibit);

        $db_item = $this->_db;
        $sql_item = "DROP TABLE IF EXISTS `$db->ItemNetworkItem`";
        $db_item->query($sql_item);

        $this->_uninstallOptions();
    }


    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
    }

    /**
     * Define the ACL.
     *
     * @param Omeka_Acl
     */
    public function hookDefineAcl($args)
    {
      // Exhibits resource.
      if (!$args['acl']->has('ItemNetwork_Exhibits')) {
          $args['acl']->addResource('ItemNetwork_Exhibits');
      }

      // Records resource.
      if (!$args['acl']->has('ItemNetwork_Items')) {
          $args['acl']->addResource('ItemNetwork_Items');
      }

    }


    /**
     * Display the plugin config form.
     */
    public function hookConfigForm()
    {
        require dirname(__FILE__) . '/config_form.php';
    }

    /**
     * Add the Simple Pages link to the admin main navigation.
     *
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
      $nav[] = array('label' => 'Item Network', 'uri' => url('itemnetwork'));
      return $nav;
    }

    /**
     * Add the pages to the public main navigation options.
     *
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterPublicNavigationMain($nav)
    {
      $nav[] = array('label' => 'Item Network', 'uri' => url('itemnetwork'));
      return $nav;
    }
    /**
     * Register routes.
     *
     * @param array $args Contains: `router` (Zend_Config).
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(
            NL_DIR.'/routes.ini'
        ));
    }


}
