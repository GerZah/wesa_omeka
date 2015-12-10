<?php
/**
* ObjectReferences plugin.
*
* @package Omeka\Plugins\ObjectReferences
*/
class ObjectReferencesPlugin extends Omeka_Plugin_AbstractPlugin
{
  // Define Hooks
  protected $_hooks = array(
    'initialize',
    'install',
    'uninstall',
    'after_save_item',
    'admin_items_form_item_types',
    'define_acl',
    'config_form',
    'config',
  );

  //Define Filters
  protected $_filters = array('admin_navigation_main');

  protected $_options = array(
		'object_references_local_enable' => 0,
  );
  public function hookInitialize()
  {
    add_translation_source(dirname(__FILE__) . '/languages');
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new ObjectReferences_Controller_Plugin_SelectFilter);
  }

  /**
  * Install the plugin.
  */
  public function hookInstall() {
    SELF::_installOptions();
  }

  /**
  * Uninstall the plugin.
  */
  public function hookUninstall() {

    SELF::_uninstallOptions();
  }
  /**
  * reassignfiles admin navigation filter
  */
  public function filterAdminNavigationMain($nav)
  {

    if(is_allowed('ObjectReferences_Index', 'index')) {
      $nav[] = array('label' => __('Object References'), 'uri' => url('object-references'));
    }
    return $nav;
  }


  /*
  * Define ACL entry for reassignfiles controller.
  */
  public function hookDefineAcl($args)
  {
    $acl = $args['acl'];

    $indexResource = new Zend_Acl_Resource('ObjectReferences_Index');
    $acl->add($indexResource);

  }


  /**
  * Display the Object References list on the item form.
  */
  public function hookAdminItemsFormItemTypes()
  {
    $localObjectReferences = (int)(boolean) get_option('object_references_local_enable');
    if ($localObjectReferences) {
      echo '<h3>' . __('Object References') . '</h3>';
      $itemId = metadata('item', 'id');
      echo common('objectreferenceslist', array( "ItemId" => $itemId ), 'index');
      add_filter(array('Object Reference',$itemId),
                 array($this, 'FilterElementInput'));
    }
  }

  public function hookAfterSaveItem($args)
  {
    if (!$args['post']) {
      return;
    }

    $record = $args['record'];
    $post = $args['post'];
    $elements = $post['referenceElement'];
    set_option('object_references_elements', json_encode($elements));

  }

  /**
  * Display the plugin configuration form.
  */
  public static function hookConfigForm() {
    $localObjectReferences = (int)(boolean) get_option('object_references_local_enable');

    require dirname(__FILE__) . '/config_form.php';
  }

  /**
  * Handle the plugin configuration form.
  */
  public static function hookConfig() {
    $localObjectReferences = (int)(boolean) $_POST['object_references_local_enable'];
    set_option('object_references_local_enable', $localObjectReferences);
    }


}
