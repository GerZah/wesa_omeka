<?php

/**
 * @version $Id$
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright Center for History and New Media, 2010
 * @package SimpleContactForm
 */

/**
 * SimpleContactForm plugin class
 *
 * @copyright Center for History and New Media, 2010
 * @package SimpleContactForm
 */
// Define Constants.
define('SIMPLE_CONTACT_FORM_PAGE_PATH', 'contact/');
define('ALLOWED_FIELDNAME', "/^[a-z|0-9|\_|\-|\.]*$/i");

class SimpleContactFormPlugin extends Omeka_Plugin_AbstractPlugin
{
    // Define Hooks
    protected $_hooks = array(
        'install',
        'uninstall',
        'initialize',
        'define_routes',
        'config_form',
        'config'
    );

    //Add filters
    protected $_filters = array(
        'public_navigation_main'
    );

   public function hookInstall()
    {
        add_translation_source(dirname(__FILE__).'/languages');

        // Define Constants.
        define('SIMPLE_CONTACT_FORM_CONTACT_PAGE_TITLE', __('Contact Us'));
        define('SIMPLE_CONTACT_FORM_CONTACT_PAGE_INSTRUCTIONS', __('Please send us your comments and suggestions.'));
        define('SIMPLE_CONTACT_FORM_THANKYOU_PAGE_TITLE', __('Thank You For Your Feedback'));
        define('SIMPLE_CONTACT_FORM_THANKYOU_PAGE_MESSAGE', __('We appreciate your comments and suggestions.'));
        define('SIMPLE_CONTACT_FORM_ADMIN_NOTIFICATION_EMAIL_SUBJECT', __('A User Has Contacted You'));
        define('SIMPLE_CONTACT_FORM_ADMIN_NOTIFICATION_EMAIL_MESSAGE_HEADER', __('A user has sent you the following message:'));
        define('SIMPLE_CONTACT_FORM_USER_NOTIFICATION_EMAIL_SUBJECT', __('Thank You'));
        define('SIMPLE_CONTACT_FORM_USER_NOTIFICATION_EMAIL_MESSAGE_HEADER', __('Thank you for sending us the following message:'));
        define('SIMPLE_CONTACT_FORM_ADD_TO_MAIN_NAVIGATION', 1);
        define('SIMPLE_CONTACT_FORM_ADDITIONAL_FIELDS', '');

        set_option('simple_contact_form_reply_from_email', get_option('administrator_email'));
        set_option('simple_contact_form_forward_to_email', get_option('administrator_email'));
        set_option('simple_contact_form_admin_notification_email_subject', SIMPLE_CONTACT_FORM_ADMIN_NOTIFICATION_EMAIL_SUBJECT);
        set_option('simple_contact_form_admin_notification_email_message_header', SIMPLE_CONTACT_FORM_ADMIN_NOTIFICATION_EMAIL_MESSAGE_HEADER);
        set_option('simple_contact_form_user_notification_email_subject', SIMPLE_CONTACT_FORM_USER_NOTIFICATION_EMAIL_SUBJECT);
        set_option('simple_contact_form_user_notification_email_message_header', SIMPLE_CONTACT_FORM_USER_NOTIFICATION_EMAIL_MESSAGE_HEADER);
        set_option('simple_contact_form_contact_page_title', SIMPLE_CONTACT_FORM_CONTACT_PAGE_TITLE);
        set_option('simple_contact_form_contact_page_instructions', SIMPLE_CONTACT_FORM_CONTACT_PAGE_INSTRUCTIONS);
        set_option('simple_contact_form_thankyou_page_title', SIMPLE_CONTACT_FORM_THANKYOU_PAGE_TITLE);
        set_option('simple_contact_form_thankyou_page_message', SIMPLE_CONTACT_FORM_THANKYOU_PAGE_MESSAGE);
        set_option('simple_contact_form_add_to_main_navigation', SIMPLE_CONTACT_FORM_ADD_TO_MAIN_NAVIGATION);
        set_option('simple_contact_form_additional_fields', SIMPLE_CONTACT_FORM_ADDITIONAL_FIELDS);
    }

    public function hookUninstall()
    {
        delete_option('simple_contact_form_reply_from_email');
        delete_option('simple_contact_form_forward_to_email');
        delete_option('simple_contact_form_admin_notification_email_subject');
        delete_option('simple_contact_form_admin_notification_email_message_header');
        delete_option('simple_contact_form_user_notification_email_subject');
        delete_option('simple_contact_form_user_notification_email_message_header');
        delete_option('simple_contact_form_contact_page_title');
        delete_option('simple_contact_form_contact_page_instructions');
        delete_option('simple_contact_form_thankyou_page_title');
        delete_option('simple_contact_form_add_to_main_navigation');
        delete_option('simple_contact_form_additional_fields');
    }

    /**
     * Add translation source.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__).'/languages');
    }
    /**
     * Adds 2 routes for the form and the thank you page.
     **/
    function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $router->addRoute(
            'simple_contact_form_form',
            new Zend_Controller_Router_Route(
                SIMPLE_CONTACT_FORM_PAGE_PATH,
                array('module'       => 'simple-contact-form')
            )
        );

        $router->addRoute(
            'simple_contact_form_thankyou',
            new Zend_Controller_Router_Route(
                SIMPLE_CONTACT_FORM_PAGE_PATH.'thankyou',
                array(
                    'module'       => 'simple-contact-form',
                    'controller'   => 'index',
                    'action'       => 'thankyou',
                )
            )
        );
    }

    public function hookConfigForm()
    {
        add_translation_source(dirname(__FILE__).'/languages');
        include 'config_form.php';
    }

    public function hookConfig($args)
    {
        $post = $args['post'];
        set_option('simple_contact_form_reply_from_email', $post['reply_from_email']);
        set_option('simple_contact_form_forward_to_email', $post['forward_to_email']);
        set_option('simple_contact_form_admin_notification_email_subject', $post['admin_notification_email_subject']);
        set_option('simple_contact_form_admin_notification_email_message_header', $post['admin_notification_email_message_header']);
        set_option('simple_contact_form_user_notification_email_subject', $post['user_notification_email_subject']);
        set_option('simple_contact_form_user_notification_email_message_header', $post['user_notification_email_message_header']);
        set_option('simple_contact_form_contact_page_title', $post['contact_page_title']);
        set_option('simple_contact_form_contact_page_instructions',$post['contact_page_instructions']);
        set_option('simple_contact_form_thankyou_page_title', $post['thankyou_page_title']);
        set_option('simple_contact_form_thankyou_page_message', $post['thankyou_page_message']);
        set_option('simple_contact_form_add_to_main_navigation', $post['add_to_main_navigation']);
        set_option('simple_contact_form_additional_fields', $post['additional_fields']);
    }

    public function filterPublicNavigationMain($nav)
    {
        $contact_title = get_option('simple_contact_form_contact_page_title');
        $contact_add_to_navigation = get_option('simple_contact_form_add_to_main_navigation');
        if ($contact_add_to_navigation) {
            //$nav[$contact_title] = uri(array(), 'simple_contact_form_form');
                $nav[] = array(
                    'label'   => $contact_title,
                    'uri'     => url(array(),'simple_contact_form_form'),
                    'visible' => true
                );
        }
        return $nav;
    }

    public function prepareAdditionalFields() {

      $additional_fields = get_option('simple_contact_form_additional_fields');
      $lines = explode("\n", $additional_fields);

      $result = array();

      foreach($lines as $line) {
        $params = explode(";", $line);
        foreach($params as $key => $val) { $params[$key] = trim($val); }

        $fieldName = (isset($params[0]) ? $params[0] : false);
        $match = preg_match(ALLOWED_FIELDNAME, $fieldName);
        $fieldName = ( $match ? $fieldName : false );

        if ($fieldName) {
          $fieldLabel = (isset($params[1]) ? $params[1] : false);

          if ($fieldLabel) {
            $multiLine = ((isset($params[2])) and ($params[2] == "multi"));
            $dropDown = ((isset($params[2])) and ($params[2] == "dropdown") and (isset($params[3])));
            $dropDowns = array();

            if ($dropDown) {
              $dropDowns = array( -1 => __("Select Below") );
              for($i=3; $i<count($params); $i++) {
                $dropDowns[$params[$i]] = $params[$i];
                unset($params[$i]);
              }
            }

            $fieldType = "generic";
            if ($multiLine) { $fieldType = "multi"; }
            if ($dropDown) { $fieldType = "dropdown"; }

            $fieldValue = ( isset($_POST[$fieldName]) ? $_POST[$fieldName] : "" );

            $result[] = array(
              "fieldName" => $fieldName,
              "fieldLabel" => $fieldLabel,
              "fieldType" => $fieldType,
              "dropDowns" => $dropDowns,
              "fieldValue" => $fieldValue,
            );

          }
        }
      }

      return $result;

    } // function prepareAdditionalFields()

}