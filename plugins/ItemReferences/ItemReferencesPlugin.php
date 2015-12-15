<?php
/**
* ItemReferences plugin.
*
* @package Omeka\Plugins\ItemReferences
*/
class ItemReferencesPlugin extends Omeka_Plugin_AbstractPlugin
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
		'item_references_local_enable' => 0,
  );
  public function hookInitialize()
  {
    add_translation_source(dirname(__FILE__) . '/languages');
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new ItemReferences_Controller_Plugin_SelectFilter);
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

    if(is_allowed('ItemReferences_Index', 'index')) {
      $nav[] = array('label' => __('Item References'), 'uri' => url('item-references'));
    }
    return $nav;
  }


  /*
  * Define ACL entry for reassignfiles controller.
  */
  public function hookDefineAcl($args)
  {
    $acl = $args['acl'];

    $indexResource = new Zend_Acl_Resource('ItemReferences_Index');
    $acl->add($indexResource);

  }


  /**
  * Display the Item References list on the item form.
  */
  public function hookAdminItemsFormItemTypes()
  {
    $localItemReferences = (int)(boolean) get_option('item_references_local_enable');
    if ($localItemReferences) {
      echo '<h3>' . __('Item References') . '</h3>';
      $itemId = metadata('item', 'id');
      echo common('itemreferenceslist', array( "ItemId" => $itemId ), 'index');
      add_filter(array('Item Reference',$itemId),
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
    set_option('item_references_elements', json_encode($elements));

  }

  /**
  * Display the plugin configuration form.
  */
  public static function hookConfigForm() {
    $localItemReferences = (int)(boolean) get_option('item_references_local_enable');

    require dirname(__FILE__) . '/config_form.php';
  }

  /**
  * Handle the plugin configuration form.
  */
  public static function hookConfig() {
    $localItemReferences = (int)(boolean) $_POST['item_references_local_enable'];
    set_option('item_references_local_enable', $localItemReferences);
    }


}
