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
        $db = $this->_db;
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->ItemNetwork` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `modified_by_user_id` int(10) unsigned NOT NULL,
          `created_by_user_id` int(10) unsigned NOT NULL,
          `is_published` tinyint(1) NOT NULL,
          `title` tinytext COLLATE utf8_unicode_ci NOT NULL,
          `slug` tinytext COLLATE utf8_unicode_ci NOT NULL,
          `text` mediumtext COLLATE utf8_unicode_ci,
          `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `inserted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`id`),
            KEY `is_published` (`is_published`),
            KEY `inserted` (`inserted`),
            KEY `updated` (`updated`),
            KEY `created_by_user_id` (`created_by_user_id`),
            KEY `modified_by_user_id` (`modified_by_user_id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);

        // Save an example network.
        $network = new ItemNetwork;
        $network->modified_by_user_id = current_user()->id;
        $network->created_by_user_id = current_user()->id;
        $network->is_published = 1;
        $network->title = 'Title';
        $network->slug = 'Slug';
        $network->text = '<p>This is sample content.</p>';
        $network->save();

        $this->_installOptions();
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        // Drop the table.
        $db = $this->_db;
        $sql = "DROP TABLE IF EXISTS `$db->ItemNetwork`";
        $db->query($sql);

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
      $args['acl']->addResource('ItemNetwork_Index');

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

      $nav[] = array('label' => 'Neatline', 'uri' => url('neatline'));
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
      $nav[] = array('label' => 'Neatline', 'uri' => url('neatline'));
      return $nav;
    }


}
