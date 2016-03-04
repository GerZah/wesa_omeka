<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
  */


/**
 * Define a three-tiered ACL:
 *
 *  - Supers and Admins can do everything.
 *
 *  - Contributors can add/update/delete their own exhibits and records, but
 *  not exhibits or records that belong to other users.
 *
 *  - Researchers can't access any ItemNetwork content.
 *
 * @param Zend_Acl $acl
 */
function in_defineAcl($acl)
{


    // Exhibits resource.
    if (!$acl->has('ItemNetwork_Exhibit')) {
        $acl->addResource('ItemNetwork_Exhibit');
    }

    // Records resource.
    if (!$acl->has('ItemNetwork_Record')) {
        $acl->addResource('ItemNetwork_Record');
    }


    // Anonymous:
    // ------------------------------------------------------------------------

    // Anyone can view items.
    $acl->allow(null, 'Items', array('get'));

    // Anyone can view exhibits.
    $acl->allow(null, 'ItemNetwork_Exhibit', array(
        'index',
        'show',
        'browse',
        'get'
    ));

    // Anyone can view records.
    $acl->allow(null, 'ItemNetwork_Record', array(
        'index',
        'list',
        'get'
    ));


    // Contributor:
    // ------------------------------------------------------------------------

    // Contributors can add and delete-confirm exhibits.
    $acl->allow('contributor', 'ItemNetwork_Exhibit', array(
        'add',
        'delete-confirm'
    ));

    // Contributors can edit their own exhibits.
    $acl->allow('contributor', 'ItemNetwork_Exhibit', array(
        'showNotPublic',
        'editSelf',
        'editorSelf',
        'putSelf',
        'importSelf',
        'deleteSelf'
    ));
    $acl->allow('contributor', 'ItemNetwork_Exhibit', array(
        'edit',
        'editor',
        'put',
        'import',
        'delete'
    ), new Omeka_Acl_Assert_Ownership);

    // Contributors can create their own records.
    $acl->allow('contributor', 'ItemNetwork_Record', 'post');

    // Contributors can edit/elete their own records.
    $acl->allow('contributor', 'ItemNetwork_Record', array(
        'putSelf',
        'deleteSelf'
    ));
    $acl->allow('contributor', 'ItemNetwork_Record', array(
        'put',
        'delete'
    ), new ItemNetwork_Acl_Assert_RecordOwnership);


    // Super and Admin:
    // ------------------------------------------------------------------------

    // Supers and admins can do everything.
    $acl->allow(array('super', 'admin'), 'ItemNetwork_Exhibit');
    $acl->allow(array('super', 'admin'), 'ItemNetwork_Record');


}
