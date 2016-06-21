<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Define a three-tiered ACL:
 *
 *  - Supers and Admins can do everything.
 *
 *  - Contributors can add/update/delete their own exhibits and records, but
 *  not exhibits or records that belong to other users.
 *
 *  - Researchers can't access any Network content.
 *
 * @param Zend_Acl $acl
 */
function in_defineAcl($acl)
{


    // Exhibits resource.
    if (!$acl->has('Network_Exhibits')) {
        $acl->addResource('Network_Exhibits');
    }

    // Records resource.
    if (!$acl->has('Network_Records')) {
        $acl->addResource('Network_Records');
    }


    // Anonymous:
    // ------------------------------------------------------------------------

    // Anyone can view items.
    $acl->allow(null, 'Items', array('get'));

    // Anyone can view exhibits.
    $acl->allow(null, 'Network_Exhibits', array(
        'index',
        'show',
        'browse',
        'get'
    ));

    // Anyone can view records.
    $acl->allow(null, 'Network_Records', array(
        'index',
        'list',
        'get'
    ));


    // Contributor:
    // ------------------------------------------------------------------------

    // Contributors can add and delete-confirm exhibits.
    $acl->allow('contributor', 'Network_Exhibits', array(
        'add',
        'delete-confirm'
    ));

    // Contributors can edit their own exhibits.
    $acl->allow('contributor', 'Network_Exhibits', array(
        'showNotPublic',
        'editSelf',
        'editorSelf',
        'putSelf',
        'importSelf',
        'deleteSelf'
    ));
    $acl->allow('contributor', 'Network_Exhibits', array(
        'edit',
        'editor',
        'put',
        'import',
        'delete'
    ), new Omeka_Acl_Assert_Ownership);

    // Contributors can create their own records.
    $acl->allow('contributor', 'Network_Records', 'post');

    // Contributors can edit/elete their own records.
    $acl->allow('contributor', 'Network_Records', array(
        'putSelf',
        'deleteSelf'
    ));
    $acl->allow('contributor', 'Network_Records', array(
        'put',
        'delete'
    ), new Network_Acl_Assert_RecordOwnership);


    // Super and Admin:
    // ------------------------------------------------------------------------

    // Supers and admins can do everything.
    $acl->allow(array('super', 'admin'), 'Network_Exhibits');
    $acl->allow(array('super', 'admin'), 'Network_Records');


}
