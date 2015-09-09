<?php

/**
* DateSearch plugin.
*
* @package Omeka\Plugins\DateSearch
*/
class RangeSearchPlugin extends Omeka_Plugin_AbstractPlugin {

	/**
	* @var array This plugin's hooks.
	*/
	protected $_hooks = array(
		'initialize', # tap into i18n
		'install', # create additional table and batch-preprocess existing items for ranges
		'uninstall', # delete table
		'config_form', # 
		'config', # store config settings in the database
		'after_save_item', # preprocess saved item for ranges
		'after_delete_item', # delete deleted item's preprocessed ranges
		'admin_items_search', # add a time search field to the advanced search panel in admin
		'items_browse_sql', # filter for a date after search page submission.
	);

	protected $_options = array(
		'range_search_units' => '',
	);

	/**
	 * Add the translations.
	 */
	public function hookInitialize() {
		# add_translation_source(dirname(__FILE__) . '/languages');
	}

	/**
	 * Install the plugin.
	 */
	public function hookInstall() {
		# Create table
		$db = get_db();

		# Let's assume that a "numval" = number value is at the most "12345678-1234-1234" == 18 chars long
		# And let's assume that any unit name is at the most 20 chars long ("Reichsmark" would be 10)

		$sql = "
		CREATE TABLE IF NOT EXISTS `$db->RangeSearchValues` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`item_id` int(10) unsigned NOT NULL REFERENCES `$db->Item`,
				`numval` varchar(18) NOT NULL,
				`unit` varchar(20) NOT NULL,
				PRIMARY KEY (`id`),
				INDEX (unit)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$db->query($sql);

		SELF::_installOptions();

		SELF::_batchProcessExistingItems();
	}

	/**
	 * Uninstall the plugin.
	 */
	public function hookUninstall() {
		$db = get_db();

		# Drop the table
		$sql = "DROP TABLE IF EXISTS `$db->RangeSearchValues`";
		$db->query($sql);

		SELF::_uninstallOptions();
	}

	/**
	 * Display the plugin configuration form.
	 */
	public static function hookConfigForm() {
		$rangeSearchUnits = SELF::_decodeUnitsFromOption(get_option('range_search_units'));
		require dirname(__FILE__) . '/config_form.php';
	}

	/**
	 * Handle the plugin configuration form.
	 */
	public static function hookConfig() {
		$oldRangeSearchUnits = get_option('range_search_units');
		$newRangeSearchUnits = SELF::_encodeUnitsFromTextArea($_POST['range_search_units']);
		set_option('range_search_units', $newRangeSearchUnits );
		if ($oldRangeSearchUnits != $newRangeSearchUnits) { SELF::_batchProcessExistingItems(); }
	}

	/**
	 * Decode JSON array from DB option to be displayable in textarea on config page
	 */
	private function _decodeUnitsFromOption($option) {
		$lines = ($option ? json_decode($option) : array() );
		return implode("\n", $lines);
	}

	/**
	 * Encode content of textarea on config page to be stored as a JSON array in DB option
	 */                                
	private function _encodeUnitsFromTextArea($textArea) {
		$textArea = str_replace(chr(10), chr(13), $textArea);
		$textArea = str_replace(chr(13).chr(13), chr(13), $textArea);

		$lines = explode(chr(13), $textArea);
		$nonEmptyLines = array();
		foreach($lines as $line) {
			$line = trim($line);
			$line = substr($line, 0, 20);
			if ($line) { $nonEmptyLines[]=$line; }
		}

		return json_encode($nonEmptyLines);
	}

	/**
	 * Preprocess ALL existing items  which could be rather EVIL in huge installations
	 */
	private function _batchProcessExistingItems() {
		$db = get_db();
		$sql= "select id from `$db->Items`";
		$items = $db->fetchAll($sql);
		foreach($items as $item) { SELF::preProcessItem($item["id"]); }
	}

	/**
	 * Preprocess yyyy / yyyy-mm / yyyy-mm-dd dates after saving an item add/edit form.
	 *
	 * @param array $args
	 */
	public function hookAfterSaveItem($args) {
			if ( (!$args['post']) and (!$args['insert']) ) {
					return;
			}

			$item_id = intval($args["record"]["id"]);
			if ($item_id) { SELF::preProcessItem($item_id); }

			# die("After Save Item");

	} # hookAfterSaveItem()

	/**
	 * Delete pre-processed dates after an item has been deleted
	 *
	 * @param array $args
	 */
	public function hookAfterDeleteItem($args) {
			$db = get_db();

			$item_id = intval($args["record"]["id"]);

			if ($item_id) {
				$sql = "delete from `$db->RangeSearchValues` where item_id=$item_id";
				$db->query($sql);
			}

			# echo "<pre>After Delete Item - ID: $item_id\nSQL: $sql\n"; print_r($args); die("</pre>");
	} # hookAfterDeleteItem()

	/**
	 * Pre-process one item's textual data and store timespans in DateSearchDates table
	 */
	private function preProcessItem($item_id) {
		$db = get_db();

		if ($item_id) {
			$sql = "delete from `$db->RangeSearchValues` where item_id=$item_id";
			$db->query($sql);

			$text = $db->fetchOne("select text from `$db->SearchTexts` where record_type='Item' and record_id=$item_id");

			if ($text !== false) {

				# Check if we could add relation comments in case Item Relations is installed and has been patched
				# to feature relation comments.
				$withRelComments=false;
				$sql = "show columns from `$db->ItemRelationsRelations` where field='relation_comment'";
				try { $withRelComments = ($db->fetchOne($sql) !== false); }
				catch (Exception $e) { $withRelComments=false; }

				if ($withRelComments) {
					$sql = "select relation_comment from `$db->ItemRelationsRelations` where subject_item_id=$item_id";
					$comments = $db->fetchAll($sql);
					foreach($comments as $comment) { $text .= " ".$comment["relation_comment"]; }
				}

				/*
				$cookedDates = SELF::_processDateText($text);
				# echo "<pre>"; print_r($cookedDates); die("</pre>");

				if ($cookedDates) {

					$values = array();
					foreach($cookedDates as $cookedDate) {
						SELF::_swapIfNecessary($cookedDate[0], $cookedDate[1]);
						$values[]='('.$item_id.',"'.$cookedDate[0].'","'.$cookedDate[1].'")';
					}
					$values = implode(", ", $values);

					$sql = "insert into `$db->DateSearchDates` (item_id, fromdate, todate) values $values";
					$db->query($sql);
					# die($sql);

				} # if ($cookedDates)
				*/
			} # if ($text)
		} # if ($item_id)
	} #  preProcessItem()

	/**
	 * Display the time search form on the admin advanced search page
	 */
	public function hookAdminItemsSearch() {
		echo common('range-search-advanced-search', null);
	}

	/**
	 * Filter for an date after search page submission.
	 *
	 * @param array $args
	 */
	public function hookItemsBrowseSql($args) {
		/*
		$select = $args['select'];
		$params = $args['params'];

		$regEx = SELF::_constructRegEx();
		$date = $regEx["date"];
		$dateTimespan = $regEx["dateTimespan"];

		if (	(isset($params['date_search_term'])) and
					(preg_match( "($dateTimespan)", $params['date_search_term'])) ) {

			$singleCount = preg_match_all ( "($date)", $params['date_search_term'], $singleSplit );
			$timespan = array();
			$timespan[] = $singleSplit[0][0];
			$timespan[] = $singleSplit[0][ ($singleCount==2 ? 1 : 0 ) ];
			$timespan = SELF::_expandTimespan($timespan);

			$searchFromDate = $timespan[0];
			$searchToDate = $timespan[1];

			$db = get_db();
			$select
					->join(
							array('date_search_dates' => $db->DateSearchDates),
							"date_search_dates.item_id = items.id",
							array()
					)
					->where("'$searchFromDate'<=date_search_dates.todate and '$searchToDate'>=date_search_dates.fromdate");
					# die("<pre>$searchFromDate / $searchToDate --- $select</pre>");

		}
		*/
	}

	# ------------------------------------------------------------------------------------------------------

	/**
	 * Create the necessary regEx expressions to deal with yyyy / yyyy-mm / yyyy-mm-dd / yyyy-mm-dd - yyyy-mm-dd
	 */
	private function _constructRegEx() {

		$result = array();

		/*
		# Construct RegEx
		$year = "\d{4}";
		$month = $day = "\d{1,2}";
		$monthDay = "$month(?:-$day)?";
		$date = "$year(?:-$monthDay)?\b";
		$separator = "\s*-\s*";
		$dateTimespan = "$date(?:$separator$date)?";

		$julGregPrefix = "\[([J,G])\] ";
		$julGregDateTimeSpan = $julGregPrefix.$dateTimespan;

		$result=array(
								"year" => $year,
								"month" => $month,
								"day" => $day,
								"monthDay" => $monthDay,
								"date" => $date,
								"separator" => $separator,
								"dateTimespan" => $dateTimespan,
								"julGregPrefix" => $julGregPrefix,
								"julGregDateTimeSpan" => $julGregDateTimeSpan,
							);
		*/

		return $result;

	}

	# ------------------------------------------------------------------------------------------------------

} # class
