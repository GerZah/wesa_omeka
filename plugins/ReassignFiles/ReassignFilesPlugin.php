<?php

/**
 * @version $Id$
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Center for History and New Media, 2010
 * @package Reassign Files
 */

/**
 * Reassign Files plugin class
 *
 * @copyright Center for History and New Media, 2010
 * @package Reassign Files
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
    protected $_filters = array(
        'admin_navigation_main',
    );
    public function hookInitialize()
    {

    }
    /**
     * reassignfiles admin navigation filter
     */
    public function filterAdminNavigationMain($nav)
    {

        $nav[] = array(
            'label'   => __('Reassign Files'),
            'uri'     => url(
                    array(
                        'module'=>'reassignfiles',
                        'controller'=>'index',
                        'action'=>'index',
                        ), 'default'
                    ),
            'resource' => 'ReassignFiles_Index'
        );

        return $nav;
    }

    /*
     * Define ACL entry for reassignfiles controller.
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('ReassignFiles_Index');
    }

    /**
     * Display the reassignfiles files list on the  itemf form.
     * This simply adds a heading to the output
     */
    public function hookAdminItemsFormFiles()
    {
        echo '<h3>' . __('Add Existing Files') . '</h3>';
        reassignfiles_list();
    }

    public function hookAfterSaveItem($args)
    {
        $item = $args['record'];
        $post = $args['post'];

        if (!($post && isset($post['reassignfiles-files']))) {
            return;
        }

        $fileNames = $post['reassignfiles-files'];
        if ($fileNames) {
            if (!reassignfiles_can_access_files_dir()) {
                throw new reassignfiles_Exception(__('The reassignfiles files directory must be both readable and writable.'));
            }
            $filePaths = array();
            foreach($fileNames as $fileName) {
                $filePaths[] = reassignfiles_validate_file($fileName);
            }

            $files = array();
            try {
                $files = insert_files_for_item($item, 'Filesystem', $filePaths, array('file_ingest_options'=> array('ignore_invalid_files'=>false)));
            } catch (Omeka_File_Ingest_InvalidException $e) {
                release_object($files);
                $item->addError('reassignfiles', $e->getMessage());
                return;
            } catch (Exception $e) {
                release_object($files);
                throw $e;
            }
            release_object($files);

            // delete the files
            foreach($filePaths as $filePath) {
                try {
                    unlink($filePath);
                } catch (Exception $e) {
                    throw $e;
                }
            }
        }
    }
}
