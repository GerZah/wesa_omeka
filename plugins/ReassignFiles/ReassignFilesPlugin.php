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
        'after_save_item',
        'admin_items_form_files',
        'define_acl',
    );

    //Define Filters
    protected $_filters = array('admin_navigation_main');

    public function hookInitialize()
    {

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
      $db = $this->_db;
      // reassign the selected files from other items to the current item
        if (isset($post['reassignFiles-files'])) {
            $itemId= intval($post['reassignFiles-files']);
            $db = $this->_db;
            $sql = "UPDATE `$db->File`set item_id = $itemId";
            $db->query($sql);
      }
}
}
