<?php

/**
* @package     omeka
* @subpackage  network
* @copyright   2014 Rector and Board of Visitors, University of Virginia
* @license     http://www.apache.org/licenses/LICENSE-2.0.html
*/

class Network_ExhibitsController extends Network_Controller_Rest
{


  /**
  * Set the default model, get tables.
  */
  public function init()
  {
    $this->_helper->db->setDefaultModelName('NetworkExhibit');
    $this->_exhibits = $this->_helper->db->getTable('NetworkExhibit');
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

  $exhibit = new NetworkExhibit;
  $exhibit->all_relations = true;
  $exhibit->all_references = true;
  $exhibit->sticky_node_selection = false;
  $exhibit->color_item_types = true;
  $form = $this->_getExhibitForm($exhibit);

  // Process form submission.
  if ($this->_request->isPost() && $form->isValid($_POST)) {
    $exhibit->saveForm($form->getValues());
    $this->_helper->redirector('browse');
  }

  // Push form to view.
  $this->view->form = $form;
  queue_js_file('add-edit-interactivity');
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
  queue_js_file('add-edit-interactivity');
}

/**
* View Items in Exhibit.
*/
public function viewAction()
{

  $exhibit = $this->_helper->db->findById();

  // Process form submission.
  if ($this->_request->isPost() && $form->isValid($_POST)) {
    $exhibit->saveForm($form->getValues());
    $this->_helper->redirector('browse');
  }

  // Push exhibit and form to view.
  $this->view->network_exhibit = $exhibit;

}

/**
* Remove Items in Exhibit.
*/
public function removeAction()
{


}
/**
* Readd Items in Exhibit.
*/
public function undoAction()
{

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
    'Network_Job_ImportItems', array(
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


/**
* Confirm exhibit.
*/
public function confirmAction()
{

  // get POST values, fetchAll from items table and display

  // echo "<pre>" . print_r($_POST,true) . "</pre>"; //die();
}


// Public views:
// ------------------------------------------------------------------------


/**
* Show exhibit.
*/
public function showAction()
{

  $this->_helper->viewRenderer->setNoRender(true);

  // Try to find an exhibit with the requested id.
  $exhibit = $this->_exhibits->findByExhibitId($this->_request->id);
  if (!$exhibit) throw new Omeka_Controller_Exception_404;

  // Assign exhibit to view.
  $this->view->network_exhibit = $exhibit;
  $this->view->exhibit_id = $exhibit->id;

  // Try to render exhibit-specific template.
  try { $this->render("themes/$exhibit->id/show"); }
  catch (Exception $e) { $this->render('show'); }


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
* Construct the details form.
*
* @param NetworkExhibit $exhibit
*/
protected function _getExhibitForm($exhibit)
{
  return new Network_Form_Exhibit(array('exhibit' => $exhibit));
}

/**
* Set the delete success message.
*
* @param NetworkExhibit $exhibit
*/
protected function _getDeleteSuccessMessage($exhibit)
{
  return __('The network "%s" was successfully deleted!',
  $exhibit->title
);
}


/**
* Set the delete confirm message.
*
* @param NetworkExhibit $exhibit
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
