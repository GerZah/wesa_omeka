<?php


/**
* ConditionalElements plugin.
*
* @package Omeka\Plugins\ReassignFiles
*/
class ReassignFilesPlugin extends Omeka_Plugin_AbstractPlugin
{
  // Define Hooks
  protected $_hooks = array(
    'initialize',
    'install',
    'uninstall',
    'after_save_item',
    'admin_items_form_files',
    'define_acl',
    'config_form',
    'config',
  );

  //Define Filters
  protected $_filters = array('admin_navigation_main');

  protected $_options = array(
    'reassign_files_orphaned_items_prefixes' => 0,
  );
  public function hookInitialize()
  {

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

    if(is_allowed('ReassignFiles_Index', 'index')) {
      $nav[] = array('label' => __('Reassign Files'), 'uri' => url('reassign-files'));
    }
    return $nav;
  }

  /*
  * Define ACL entry for reassignfiles controller.
  */
  public function hookDefineAcl($args)
  {
    $args['acl']->addResource('ReassignFiles_Index');
  }

  /**
  * Display the reassignfiles list on the  item form.
  * This simply adds a heading to the output
  */
  public function hookAdminItemsFormFiles()
  {
    echo '<h3>' . __('Add Existing Files') . '</h3>';
    echo common('reassignfileslist', array(), 'index');
  }

  public function hookAfterSaveItem($args)
  {
    if (!$args['post']) {
      return;
    }

    $record = $args['record'];
    $post = $args['post'];
    #echo "<pre>"; print_r($_POST); die("</pre>");

    $db = $this->_db;
    // reassign the selected files from other items to the current item
    if (isset($post['reassignFilesFiles'])) {
      $itemId = 10; // for testing
      $files = $_POST['reassignFilesFiles'];
      $fileNames = implode(',', $files);
      $db = $this->_db;
      $sql = "UPDATE `$db->File`set item_id = $itemId where item_id IN ($fileNames)";
      $db->query($sql);
    }
  }

  /**
   * Display the plugin configuration form.
   */
  public static function hookConfigForm() {
    $useReassignFilesPrexifes = (int)(boolean) get_option('reassign_files_orphaned_items_prefixes');

    require dirname(__FILE__) . '/config_form.php';
  }

  /**
   * Handle the plugin configuration form.
   */
  public static function hookConfig() {

    $prevUseReassignFilesPrexifes = (int)(boolean) get_option('reassign_files_orphaned_items_prefixes');
    $newUseReassignFilesPrexifes = (int)(boolean) $_POST['reassign_files_orphaned_items_prefixes'];
    set_option('reassign_files_orphaned_items_prefixes', $newUseReassignFilesPrexifes);
  }

}
