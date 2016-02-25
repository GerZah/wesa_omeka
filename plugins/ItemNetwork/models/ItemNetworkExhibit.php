<?php
/**
 * ItemNetwork
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The ItemNetwork record class.
 *
 * @package ItemNetwork
 */
class ItemNetworkExhibit extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $modified_by_user_id;
    public $created_by_user_id;
    public $is_published = 0;
    public $title;
    public $slug;
    public $text = null;
    public $updated;
    public $inserted;

    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Search($this);
        $this->_mixins[] = new Mixin_Timestamp($this, 'inserted', 'updated');
    }

    /**
     * Get the modified by user object.
     *
     * @return User
     */
    public function getModifiedByUser()
    {
        return $this->getTable('User')->find($this->modified_by_user_id);
    }

    /**
     * Get the created by user object.
     *
     * @return User
     */
    public function getCreatedByUser()
    {
        return $this->getTable('User')->find($this->created_by_user_id);
    }

    /**
     * Validate the form data.
     */
    protected function _validate()
    {
        if (empty($this->title)) {
            $this->addError('title', __('The page must be given a title.'));
        }

        if (255 < strlen($this->title)) {
            $this->addError('title', __('The title for your page must be 255 characters or less.'));
        }

        if (!$this->fieldIsUnique('title')) {
            $this->addError('title', __('The title is already in use by another page. Please choose another.'));
        }

        if (trim($this->slug) == '') {
            $this->addError('slug', __('The page must be given a valid slug.'));
        }

        if (preg_match('/^\/+$/', $this->slug)) {
            $this->addError('slug', __('The slug for your page must not be a forward slash.'));
        }

        if (255 < strlen($this->slug)) {
            $this->addError('slug', __('The slug for your page must be 255 characters or less.'));
        }

        if (!$this->fieldIsUnique('slug')) {
            $this->addError('slug', __('The slug is already in use by another page. Please choose another.'));
        }

        if (!is_numeric($this->order) || (!(strpos((string)$this->order, '.') === false)) || intval($this->order) < 0) {
            $this->addError('order', __('The order must be an integer greater than or equal to 0.'));
        }
    }

    /**
     * Prepare special variables before saving the form.
     */
    protected function beforeSave($args)
    {
        $this->title = trim($this->title);
        // Generate the page slug.
        $this->slug = $this->_generateSlug($this->slug);
        // If the resulting slug is empty, generate it from the page title.
        if (empty($this->slug)) {
            $this->slug = $this->_generateSlug($this->title);
        }

        $this->modified_by_user_id = current_user()->id;
    }

    protected function afterSave($args)
    {
        if (!$this->is_published) {
            $this->setSearchTextPrivate();
        }
        $this->setSearchTextTitle($this->title);
        $this->addSearchText($this->title);
        $this->addSearchText($this->text);
    }

    /**
     * Generate a slug given a seed string.
     *
     * @param string
     * @return string
     */
    private function _generateSlug($seed)
    {
        $seed = trim($seed);
        $seed = strtolower($seed);
        // Replace spaces with dashes.
        $seed = str_replace(' ', '-', $seed);
        // Remove all but alphanumeric characters, underscores, and dashes.
        return preg_replace('/[^\w\/-]/i', '', $seed);
    }

  }
