<?php

/**
* Measurements plugin.
*
* @package Omeka\Plugins\Measurements
*/
class FindOrphansPlugin extends Omeka_Plugin_AbstractPlugin {

  protected $_hooks = array(
    'initialize',
    'define_acl',
	);

  protected $_filters = array('admin_navigation_main');

  /**
  * Add navigation button
  */
  public function filterAdminNavigationMain($nav) {
    if(is_allowed('FindOrphans_Index', 'index')) {
      $nav[] = array('label' => __('Find Orphans'), 'uri' => url('find-orphans'));
    }
    return $nav;
  }

  /**
	* Add the translations.
	*/
	public function hookInitialize() {
		add_translation_source(dirname(__FILE__) . '/languages');
  }

  /*
  * Define ACL entry for FindOrphans controller.
  */
  public function hookDefineAcl($args) {
    $acl = $args['acl'];

    $indexResource = new Zend_Acl_Resource('FindOrphans_Index');
    $acl->add($indexResource);

    $acl->allow(array('super', 'admin'), 'FindOrphans_Index', 'index');
  }

}

?>
