<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class ItemNetwork_ExhibitsController extends ItemNetwork_Controller_Rest
{


    /**
     * Set the default model, get tables.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ItemNetworkExhibit');
        $this->_exhibits = $this->_helper->db->getTable('ItemNetworkExhibit');
    }


    // REST API:
    // ------------------------------------------------------------------------


    /**
     * Fetch exhibit via GET.
     * @REST
     */
    public function getAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        echo Zend_Json::encode($this->_helper->db->findById()->toArray());
    }


    /**
     * Update exhibit via PUT.
     * @REST
     */
    public function putAction()
    {

        $this->_helper->viewRenderer->setNoRender(true);

        // Update the exhibit.
        $exhibit = $this->_helper->db->findById();
        $exhibit->saveForm(Zend_Json::decode(file_get_contents(
            Zend_Registry::get('fileIn')), true
        ));

        // Propagate CSS.
        $exhibit->pushStyles();

        // Respond with exhibit data.
        echo Zend_Json::encode($exhibit->toArray());

    }


    // Admin CRUD actions:
    // ------------------------------------------------------------------------


    /**
     * Browse exhibits.
     */
    public function browseAction()
    {

        // By default, sort by added date.
        if (!$this->_getParam('sort_field')) {
            $this->_setParam('sort_field', 'added');
            $this->_setParam('sort_dir', 'd');
        }

        parent::browseAction();

    }


    /**
     * Create a new exhibit.
     */
    public function addAction()
    {

        $exhibit = new ItemNetworkExhibit;
        $form = $this->_getExhibitForm($exhibit);

        // Process form submission.
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $exhibit->saveForm($form->getValues());
            $this->_helper->redirector('browse');
        }

        // Push form to view.
        $this->view->form = $form;


    }


    /**
     * Edit exhibit settings.
     */
    public function editAction()
    {

        $exhibit = $this->_helper->db->findById();
        $form = $this->_getExhibitForm($exhibit);

        // Process form submission.
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            $exhibit->saveForm($form->getValues());
            $this->_helper->redirector('browse');
        }

        // Push exhibit and form to view.
        $this->view->network_exhibit = $exhibit;
        $this->view->form = $form;


    }


    /**
     * Import items from Omeka.
     */
    public function importAction()
    {

        $exhibit = $this->_helper->db->findById();

        if ($this->_request->isPost()) {

            // Save the query.
            $post = $this->_request->getPost();
            $exhibit->item_query = serialize($post);
            $exhibit->save();

            // Import items.
            Zend_Registry::get('job_dispatcher')->sendLongRunning(
                'ItemNetwork_Job_ImportItems', array(
                    'exhibit_id'    => $exhibit->id,
                    'query'         => $post
                )
            );

            // Flash success.
            $this->_helper->flashMessenger(
                $this->_getImportStartedMessage(), 'success'
            );

            // Redirect to browse.
            $this->_helper->redirector('browse');

        }

        // Populate query.
        $query = unserialize($exhibit->item_query);
        $_REQUEST = $query; $_GET = $query;

    }


    // Public views:
    // ------------------------------------------------------------------------


    /**
     * Show exhibit.
     */
    public function showAction()
    {

        $this->_helper->viewRenderer->setNoRender(true);

        // Try to find an exhibit with the requested slug.
        $exhibit = $this->_exhibits->findBySlug($this->_request->slug);
        if (!$exhibit) throw new Omeka_Controller_Exception_404;

        // Assign exhibit to view.
        $this->view->network_exhibit = $exhibit;
      }


    /**
     * Show fullscreen exhibit.
     */
    public function fullscreenAction()
    {

        // Try to find an exhibit with the requested slug.
        $exhibit = $this->_exhibits->findBySlug($this->_request->slug);
        if (!$exhibit) throw new Omeka_Controller_Exception_404;

        // Assign exhibit to view.
        $this->view->network_exhibit = $exhibit;


    }


    // Helpers:
    // ------------------------------------------------------------------------


    /**
     * Return the pagination page length.
     *
     * Currently, $pluralName is ignored.
     */
    protected function _getBrowseRecordsPerPage($pluralName=null)
    {
        if (is_admin_theme()) return (int) get_option('per_page_admin');
        else return (int) get_option('per_page_public');
    }


    /**
     * Set the delete success message.
     *
     * @param NeatlineExhibit $exhibit
     */
    protected function _getDeleteSuccessMessage($exhibit)
    {
        return __('The exhibit "%s" was successfully deleted!',
            $exhibit->title
        );
    }


    /**
     * Set the delete confirm message.
     *
     * @param NeatlineExhibit $exhibit
     */
    protected function _getDeleteConfirmMessage($exhibit)
    {
        return __('This will delete "%s" and its associated metadata.',
            $exhibit->title
        );
    }


    /**
     * Set the import started message.
     */
    protected function _getImportStartedMessage()
    {
        return __('The item import was successfully started!');
    }


}
