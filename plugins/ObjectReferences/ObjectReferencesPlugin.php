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
    'admin_items_form_files',
    'define_acl',
  );

  //Define Filters
  protected $_filters = array('admin_navigation_main','element_input');

  protected $_options = array(
    'reassign_files_delete_orphaned_items' => 1,
		'reassign_files_local_reassign' => 0,
  );
  public function hookInitialize()
  {
    add_translation_source(dirname(__FILE__) . '/languages');
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

  /**
   * Filter the element input.
   *
   * @param array $components
   * @param array $args
   * @return array
   */
  public function filterElementInput($components, $args)
  {
      // Use the cached vocab terms instead of
      $terms = explode("\n", $this->_simpleVocabTerms[$args['element']->id]);
      $selectTerms = array('' => 'Select Below') + array_combine($terms, $terms);
      $components['input'] = get_view()->formSelect(
          $args['input_name_stem'] . '[text]',
          $args['value'],
          array('style' => 'width: 300px;'),
          $selectTerms
      );
      $components['html_checkbox'] = false;
      return $components;
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
  * Display the reassignfiles list on the  item form.
  * This simply adds a heading to the output
  */
  public function hookAdminItemsFormFiles()
  {
    $localReassign = (int)(boolean) get_option('reassign_files_local_reassign');
    if ($localReassign) {
      echo '<h3>' . __('Add Files from Other Items') . '</h3>';
      $itemId = metadata('item', 'id');
      $fileNames = reassignFiles_getFileNames($itemId); // from helpers/ReassignFilesFunctions.php
      echo common('reassignfileslist', array( "fileNames" => $fileNames ), 'index');
    }
  }

  public function hookAfterSaveItem($args)
  {
    if (!$args['post']) {
      return;
    }

    $record = $args['record'];
    $post = $args['post'];
    #echo "<pre>"; print_r($_POST); die("</pre>");

    // reassign the selected files from other items to the current item
    if (isset($post['reassignFilesFiles']) and (isset($post['itemId']))) {
      $itemID = ( $post['itemId'] ? $post['itemId'] : $args["record"]["id"] );
      $errMsg = reassignFiles_reassignFiles($itemID, $post['reassignFilesFiles']);
      # if ($errMsg) { $this->_helper->flashMessenger( $errMsg, 'error' ); }
      // ... turns out, we don't actually have a $this->_helper object here :-(
    }
  }

}
