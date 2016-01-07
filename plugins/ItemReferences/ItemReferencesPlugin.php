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
    'define_acl',
    'config_form',
    'config',
    'admin_head',
  );

  //Define Filters
  protected $_filters = array('admin_navigation_main');


  protected $_options = array(
		'item_references_local_enable' => 0, // +#+#+# actually obsolete
    'item_references_select' => "[]",
  );
  public function hookInitialize()
  {
    add_translation_source(dirname(__FILE__) . '/languages');
    $db = get_db();

    // Add filters
    $filter_names = array(
        'Display',
        'ElementInput',
    );
    $referenceElementsJson=get_option('item_references_select');
    if (!$referenceElementsJson) { $referenceElementsJson="null"; }
    $referenceElements = json_decode($referenceElementsJson,true);

    foreach($referenceElements as $element_id ) {
      $element = $db->getTable('Element')->find($element_id);
      $elementSet = $db->getTable('ElementSet')->find($element->element_set_id);
        foreach ($filter_names as $filter_name) {
        add_filter(
            array($filter_name, 'Item', $elementSet->name, $element->name),
            array($this, 'filter' . $filter_name)
        );
        }
}
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

  public function hookAfterSaveItem($args)
  {
    if (!$args['post']) {
      return;
    }

    $record = $args['record'];
    $post = $args['post'];

  }

  /**
  * Display the plugin configuration form.
  */
  public static function hookConfigForm() {
    // $localItemReferences = (int)(boolean) get_option('item_references_local_enable');

    $itemReferencesSelect = get_option('item_references_select');
    $itemReferencesSelect = ( $itemReferencesSelect ? json_decode($itemReferencesSelect) : array() );

    require dirname(__FILE__) . '/config_form.php';

    }

  /**
  * Handle the plugin configuration form.
  */
  public static function hookConfig() {
    // $localItemReferences = (int)(boolean) $_POST['item_references_local_enable'];
    // set_option('item_references_local_enable', $localItemReferences);

    $itemReferencesSelect = array();
    $postIds=false;
    if (isset($_POST["item_references_select"])) { $postIds = $_POST["item_references_select"]; }
    if (is_array($postIds)) {
			foreach($postIds as $postId) {
				$postId = intval($postId);
				if ($postId) { $itemReferencesSelect[] = $postId; }
			}
		}
		$itemReferencesSelect = json_encode($itemReferencesSelect);
    set_option('item_references_select', $itemReferencesSelect );

    }

    public function hookAdminHead() {
        queue_js_file('itemreferences');
  		// $request = Zend_Controller_Front::getInstance()->getRequest();
    	// 	if ($module === 'default'
  		// 		&& $controller === 'items'
  		// 		&& in_array($action, array('add', 'edit'))) {
      //
      //   require dirname(__FILE__) . '/ItemReferencesUI.php';

  		//}
  	}

    public function filterElementInput($components, $args)
    {
      $view = get_view();
          //hidden field formHidden
      $components['input'] = $view->formHidden( $args['input_name_stem'].'[text]'.'id', $args['value'] , array('style' => 'width: 250px;'),null);

//readonly
      $components['input'] .= $view->formText( $args['input_name_stem'] . '[text]'.'title',  $args['value'], array('readonly' => 'true','class' => 'itemRef','style' => 'width: 250px;'),null);


      $components['input'] .= "<button class='itemReferencesBtn'>".__("Select")."</button>";
      $components['html_checkbox'] = false;
      return $components;
    }

    public function filterDisplay($text, $args)
    {
         return $text."(modified)";
    }
}
