<?php

# Let's assume that a measurement contains out of a triple of three
# numerical value, from 0 to 9999 -- i.e. from 0000 to 9999.
define('MEASUREMENTSEARCH_NUM_MAXLEN', 4);
# A unit name should be no longer than 10 characters
define('MEASUREMENTSEARCH_UNIT_MAXLEN', 10);

/**
* MeasurementSearch plugin.
*
* @package Omeka\Plugins\MeasurementSearch
*/
class MeasurementSearchPlugin extends Omeka_Plugin_AbstractPlugin {

  protected $_hooks = array(
		'initialize', # tap into i18n
		'install', # create additional table and batch-preprocess existing items for measurement
		'uninstall', # delete table
		# 'config_form', # prepare and display configuration form
		# 'config', # store config settings in the database
		# 'after_save_item', # preprocess saved item for ranges
		# 'after_delete_item', # delete deleted item's preprocessed ranges
		# 'admin_items_search', # add a time search field to the advanced search panel in admin
		# 'public_items_search', # add a time search field to the advanced search panel in public
		# 'admin_items_show_sidebar', # Debug output of stored numbers/ranges in item's sidebar (if activated)
		# 'items_browse_sql', # filter for a range after search page submission.
	);

  /**
	 * Add the translations.
	 */
	public function hookInitialize() {
		add_translation_source(dirname(__FILE__) . '/languages');
	}

  /**
	 * Install the plugin.
	 */
	public function hookInstall() {
		# Create table
		$db = get_db();

    $numLen = MEASUREMENTSEARCH_NUM_MAXLEN;
    $unitLen = MEASUREMENTSEARCH_UNIT_MAXLEN;

		$sql = "
		CREATE TABLE IF NOT EXISTS `$db->MeasurementSearchValues` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`item_id` int(10) unsigned NOT NULL REFERENCES `$db->Item`,
				`height` varchar($numLen) NOT NULL,
				`width` varchar($numLen) NOT NULL,
        `depth` varchar($numLen) NOT NULL,
				`unit` varchar($unitLen) NOT NULL,
				PRIMARY KEY (`id`),
				INDEX (unit)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$db->query($sql);

		SELF::_installOptions();

		# SELF::_batchProcessExistingItems();
	}

	/**
	 * Uninstall the plugin.
	 */
	public function hookUninstall() {
		$db = get_db();

		# Drop the table
		$sql = "DROP TABLE IF EXISTS `$db->MeasurementSearchValues`";
		$db->query($sql);

		SELF::_uninstallOptions();
	}


} # class
