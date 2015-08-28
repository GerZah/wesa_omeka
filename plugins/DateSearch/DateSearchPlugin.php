<?php

/**
* DateSearch plugin.
*
* @package Omeka\Plugins\DateSearch
*/
class DateSearchPlugin extends Omeka_Plugin_AbstractPlugin {

	/**
	* @var array This plugin's hooks.
	*/
	protected $_hooks = array(
		'install', # create additional table and batch-preprocess existing items for dates / timespans
		'uninstall', # delete table
		'after_save_item', # preprocess saved item for dates / timespans
		'after_delete_item', # delete deleted item's preprocessed dates / timespans
		'admin_items_search', # add a time search field to the advanced search panel in admin
		'items_browse_sql', # filter for a date after search page submission.
	);

	/**
	 * Install the plugin.
	 */
	public function hookInstall() {
		# Create table
		$db = $this->_db;

		# Dates are always "YYYY-MM-DD", i.e. 10 characters long

		$sql = "
		CREATE TABLE IF NOT EXISTS `$db->DateSearchDates` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`item_id` int(10) unsigned NOT NULL REFERENCES `$db->Item`,
				`fromdate` varchar(23) NOT NULL,
				`todate` varchar(23) NOT NULL,
				PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$db->query($sql);

		$this->_installOptions();
	}

	/**
	 * Uninstall the plugin.
	 */
	public function hookUninstall() {
		$db = $this->_db;

		# Drop the table
		$sql = "DROP TABLE IF EXISTS `$db->DateSearchDates`";
		$db->query($sql);

		$this->_uninstallOptions();
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

			$db = $this->_db;
			$item_id = intval($args["record"]["id"]);

			if ($item_id) {
				$sql = "delete from `$db->DateSearchDates` where item_id=$item_id";
				$db->query($sql);

				$text = $db->fetchOne("select text from `$db->SearchTexts` where record_type='Item' and record_id=$item_id");

				if ($text) {
					$cookedDates = $this->_processDateText($text);
					# echo "<pre>"; print_r($cookedDates); die("</pre>");

					if ($cookedDates) {

						$values = array();
						foreach($cookedDates as $cookedDate) {
							$this->_swapIfNecessary($cookedDate[0], $cookedDate[1]);
							$values[]='('.$item_id.',"'.$cookedDate[0].'","'.$cookedDate[1].'")';
						}
						$values = implode(", ", $values);

						$sql = "insert into `$db->DateSearchDates` (item_id, fromdate, todate) values $values";
						$db->query($sql);
						# die($sql);

					}

				}

				# $sql = "insert into `$db->DateSearchDates` (item_id, fromdate, todate) ".
				# 					"values ($item_id,'1546-01-01','1546-08-17'), ($item_id,'1546-12-01','1546-12-03')";
				# $db->query($sql);
				# die("<pre>$sql</pre>");
			}

			# die("After Save Item");

	} # hookAfterSaveItem()

	/**
	 * Delete pre-processed dates after an item has been deleted
	 *
	 * @param array $args
	 */
	public function hookAfterDeleteItem($args) {
			$db = $this->_db;

			$item_id = intval($args["record"]["id"]);

			if ($item_id) {
				$sql = "delete from `$db->DateSearchDates` where item_id=$item_id";
				$db->query($sql);
			}

			# echo "<pre>After Delete Item - ID: $item_id\nSQL: $sql\n"; print_r($args); die("</pre>");
	} # hookAfterDeleteItem()

	/**
	 * Display the time search form on the admin advanced search page
	 */
	public function hookAdminItemsSearch() {
		echo common('date-search-advanced-search', array(
			/*'formSelectProperties' => get_table_options('DateSearchProperty')*/)
		);
	}

	/**
	 * Filter for an date after search page submission.
	 *
	 * @param array $args
	 */
	public function hookItemsBrowseSql($args) {
		$select = $args['select'];
		$params = $args['params'];

		if (	(isset($params['date_search_term'])) and
					($this->_checkDate($params['date_search_term'])) ) {

			# $searchFromDate = "1546-08-17";
			# $searchToDate = "1546-08-17";

			$timespan = $this->_expandTimespan($params['date_search_term']);
			$searchFromDate = $timespan[0];
			$searchToDate = $timespan[1];

			$db = $this->_db;
			$select
					->join(
							array('date_search_dates' => $db->DateSearchDates),
							"date_search_dates.item_id = items.id",
							array()
					)
					->where("'$searchFromDate'<=date_search_dates.todate and '$searchToDate'>=date_search_dates.fromdate");
					# die("$searchFromDate / $searchToDate --- $select");

		}

	}

	# ------------------------------------------------------------------------------------------------------

	private function _swapIfNecessary(&$x,&$y) {
		# as in http://stackoverflow.com/a/26549027
		if ($x > $y) {
			$tmp=$x;
			$x=$y;
			$y=$tmp;
		}
	}

	# ------------------------------------------------------------------------------------------------------

	private function _processDateText($text) {
		$regEx = $this->_constructRegEx();
		$dateTimespan = $regEx["dateTimespan"];
		$date = $regEx["date"];

		$allCount = preg_match_all( "($dateTimespan)", $text, $allMatches);
		# echo "<pre>Count: $allCount\n"; print_r($allMatches); die("</pre>");

		$cookedDates = array();
		foreach($allMatches[0] as $singleMatch) {
			$singleCount = preg_match_all ( "($date)", $singleMatch, $singleSplit );
			$timespan = array();
			$timespan[] = $singleSplit[0][0];
			$timespan[] = $singleSplit[0][ ($singleCount==2 ? 1 : 0 ) ];
			$timespan = $this->_expandTimespan($timespan);
			$cookedDates[] = $timespan;
		}
		#echo "<pre>"; print_r($cookedDates); die("</pre>");

		return $cookedDates;
	}

	# ------------------------------------------------------------------------------------------------------

	/**
	 * Create the necessary regEx expressions to deal with yyyy / yyyy-mm / yyyy-mm-dd / yyyy-mm-dd - yyyy-mm-dd
	 */
	private function _constructRegEx() {

		# Construct RegEx
		$year = "\d{4}";
		$month = $day = "\d{1,2}";
		$monthDay = "$month(?:-$day)?";
		$date = "$year(?:-$monthDay)?\b";
		$separator = "\s*-\s*";
		$dateTimespan = "$date(?:$separator$date)?";

		$result=array(
								"year" => $year,
								"month" => $month,
								"day" => $day,
								"monthDay" => $monthDay,
								"date" => $date,
								"separator" => $separator,
								"dateTimespan" => $dateTimespan,
							);

		return $result;

	}

	# ------------------------------------------------------------------------------------------------------

	/**
	 * Check whether a string is either yyyy, yyyy-mm, or yyyy-mm-dd -- or yyyy-mm-dd - yyyy-mm-dd
	 *
	 * @param string $chkDate - string to be checked
	 * @param boolean $timespan=false - check for date only or a full timespan
	 * @return int from preg_match 1 / 0 / false
	 */
	private function _checkDate($chkDate, $timespan=false) {
		$regEx = $this -> _constructRegEx();
		$chkTerm = $regEx[($timespan ? "dateTimespan" : "date")];
		return preg_match( "($chkTerm)", $chkDate);
	}

	# ------------------------------------------------------------------------------------------------------

	/**
	 * Transform a (valid) date yyyy-mm-dd into a timespan -- down to yyyy-01-01 to yyyy-12-31
	 *
	 * @param string $timespan as in single date or timespan
	 * @result array [0] => left edge, [1] => right edge
	 */
	private function _expandTimespan($timespan) {
		$result = $timespan;
	
		if (!is_array($result)) { $result = array($result, $result); }
	
		$result[0] = $this->_updateDate($result[0], -1); # -1 == left edge, xxxx-01-01
		$result[1] = $this->_updateDate($result[1], +1); # +1 == right edge, xxxx-12-31
		
		return $result;
	}

	# ------------------------------------------------------------------------------------------------------

	/**
	 * Take a valid yyyy / yyyy-m / yyyy-mm / yyyy-m-d / yyyy-mm-d / yyyy-mm-dd
	 * and transform it towards a left edge of possibly yyyy-01-01 or yyyy-12-31
	 * or at least add leading zeros, as in yyyy-0m-0d
	 *
	 * @param string $date to be updated
	 * @param int edge -- -1 -> left edge (-01-01) / +1 -> right edge (-12-31)
	 * @result string $date -- transformed towards edge and with leading zeros
	 */
	protected function _updateDate($date, $edge) {
		$result=$date;

		$regEx = $this -> _constructRegEx();

		$year = $regEx["year"];
		$month =$regEx["month"];
		$day = $regEx["day"];
	
		$yearOnly = "^$year$";
		$yearMonth = "^$year-$month$";
		$yearMonthDay = "^$year-$month-$day$";
	
		if ( preg_match( "($yearOnly)", $result ) ) { $result = $result."-".( $edge<0 ? "1" : "12" ); }
		if ( preg_match( "($yearMonth)", $result ) ) { $result = $result."-".( $edge<0 ? "1" : "31" ); }
	
		if ( preg_match( "($yearMonthDay)", $result ) ) {
			$oneDigit = "\b(\d)\b";
			$result = preg_replace("($oneDigit)", '0${0}', $result);
		}
	
		return $result;
	}

	# ------------------------------------------------------------------------------------------------------

} # class
