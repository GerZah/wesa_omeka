<?php
/**
 * Measurements
 * LookupController
 */

/**
 * Lookup controller.
 */
class Measurements_LookupController extends Omeka_Controller_AbstractActionController {

  public function indexAction() {
    $result = array();
    $result["data"] = null; # Sanity

    $area = $unit = $page = $fromId = $toId = $fromRange = $toRange = -1;
    $title = "";
    $measurements = array();

    if ($this->_hasParam("area")) { $area = intval($this->_getParam('area')); }
    if ($this->_hasParam("unit")) { $unit = intval($this->_getParam('unit')); }
    if ($this->_hasParam("page")) { $page = intval($this->_getParam('page')); }
    if ($this->_hasParam("fromId")) { $fromId = intval($this->_getParam('fromId')); }
    if ($this->_hasParam("toId")) { $toId = intval($this->_getParam('toId')); }
    if ($this->_hasParam("fromRange")) { $fromRange = doubleval($this->_getParam('fromRange')); }
    if ($this->_hasParam("toRange")) { $toRange = doubleval($this->_getParam('toRange')); }
    if ($this->_hasParam("title")) { $title = $this->_getParam('title'); }

    $units = MeasurementsPlugin::getSaniUnits();
    // print_r($units);
    if (isset($units[$unit])) {
      $unitsInv = array();
      foreach(array_keys($units) as $unitIdx) {
        $unitIndex = implode("-", $units[$unitIdx]["units"]);
        $unitsInv[$unitIndex] = array(
          "idx" => $unitIdx,
          "mmConv" => doubleval($units[$unitIdx]["mmconv"]),
          "convs" => implode("-", $units[$unitIdx]["convs"])
        );
      }
      // print_r($unitsInv);

      $curUnit = $units[$unit];
      $targetUnit = implode("-",$curUnit["units"]);
      $mmconv = $curUnit["mmconv"];
      settype($mmconv, "double");
      $result["targetUnit"] = $targetUnit;
      $result["mmConv"] = $mmconv;

      $area = max(-1, min(2, $area)); // -1..2

      if ($area>=0) {
        $result["area"] = $area;
        $result["unit"] = $unit;

        $db = get_db();

        $where = array();
        // $where[] = "1";
        $where[] = "it.item_type_id = " .
                    $db->fetchOne("
                      SELECT id FROM `$db->ItemType`
                      WHERE name = 'Sandstein-Element'
                    ");

        if ( ($fromId>0) and ($toId>0) and ($fromId<=$toId) ) {
          $where[] = "item_id >= $fromId AND item_id<=$toId";
        }

        $titleAnd = "1";
        if ($title) {
          $titleClause = "1"; # Sanity

          $titleClauses = array();
          $titles = explode(",", $title); // Multiple search terms, seperated by comma
          foreach(array_keys($titles) as $idx) {
            $oneTitle = MeasurementsPlugin::mres(trim($titles[$idx]));
            if ($oneTitle) { $titleClauses[] = "text LIKE '%$oneTitle%'";}
          }
          if ($titleClauses) {
            $titleClause = "(" . implode(" OR ", $titleClauses) . ")";
          }

          $idAnd = "";
          if ( ($fromId>0) and ($toId>0) and ($fromId<=$toId) ) {
            $idAnd = "AND record_id >= $fromId AND record_id<=$toId";
          }

          $titleElement = $db->fetchOne("
                            SELECT id FROM `$db->Elements`
                            WHERE name = 'Title'
                          ");

          $qu = "
            SELECT record_id
            FROM `$db->ElementTexts`
            WHERE element_id=$titleElement AND $titleClause
            $idAnd
          ";
          $ids = $db->fetchAll($qu);
          if (!$ids) {
            $titleAnd = "0";
          }
          else {
            $recordIds = array();
            foreach($ids as $id) {
              $recordIds[] = $id["record_id"];
            }
            $recordIds = implode(", ", $recordIds);
            $titleAnd = "item_id in ($recordIds)";
            // echo "<pre>$titleAnd\n" . print_r($recordIds,true) . "</pre>"; die();
          }

        }
        $where[] = $titleAnd;

        $where = implode(" AND ", $where);

        $qu = "
          SELECT item_id as itemId, l1d as l1, l2d as l2, l3d as l3, f1d as f1, f2d as f2, f3d as f3, vd as v, unit, number as n
          FROM `$db->MeasurementsValues` mv
          LEFT JOIN `$db->Items` it ON mv.item_id = it.id
          WHERE $where
        ";
        $singleMeasurements = $db->fetchAll($qu); // Eeeevil! :-(

        foreach($singleMeasurements as $singleMeasurement) {
          SELF::_sortDimensions($singleMeasurement);
          switch ($area) {
            case 0: { // Dimension
              SELF::_addMeasurement($measurements, $singleMeasurement, "l1");
              SELF::_addMeasurement($measurements, $singleMeasurement, "l2");
              SELF::_addMeasurement($measurements, $singleMeasurement, "l3");
            } break;
            case 1: { // Face
              SELF::_addMeasurement($measurements, $singleMeasurement, "f1");
              SELF::_addMeasurement($measurements, $singleMeasurement, "f2");
              SELF::_addMeasurement($measurements, $singleMeasurement, "f3");
            } break;
            case 2: { // Volume
              SELF::_addMeasurement($measurements, $singleMeasurement, "v");
            } break;
          }
        }

        // First convert only the "virtual" ["x"] value for all measurements
        // (to reduce calculation complexity)
        foreach(array_keys($measurements) as $idx) {
          SELF::_convertMeasurement($measurements[$idx], $targetUnit, $unitsInv, $area);
        }

        // Sort decending by the converted ["xc] value
        usort($measurements, function($x,$y) {
          if ($x["xc"] == $y["xc"]) {
            if ($x["l1"] == $y["l1"]) {
              if ($x["l2"] == $y["l2"]) {
                if ($x["l2"] == $y["l2"]) {
                  return 0;
                }
                return ($x["l3"] > $y["l3"] ? -1 : 1);
              }
              return ($x["l2"] > $y["l2"] ? -1 : 1);
            }
            return ($x["l1"] > $y["l1"] ? -1 : 1);
          }
          return ($x["xc"] > $y["xc"] ? -1 : 1);
        });

      }

    }

    // now limit the sorted array based on the entered range
    if ( ($fromRange>0) and ($toRange>0) and ($fromRange<=$toRange) ) {
      foreach(array_keys($measurements) as $idx) {
        if ( ($measurements[$idx]["xc"]<$fromRange) or ($measurements[$idx]["xc"]>$toRange) ) {
          unset($measurements[$idx]);
        }
      }
    }

    // Calculate full number of pages and calculate current page (if not within range)
    $numPages = floor((sizeOf($measurements)-1) / MEASUREMENT_TABLE_LEN) + 1;
    $page = max(0, min($numPages-1, $page));

    // crop current page's entries into significantly smaller array
    $from = $page * MEASUREMENT_TABLE_LEN;
    $slice = array_slice($measurements, $from , MEASUREMENT_TABLE_LEN);

    // Remove the now obsolete ["x"] / ["xc"] values from the remaining measurements
    foreach(array_keys($slice) as $idx) {
      unset($slice[$idx]["x"]);
      unset($slice[$idx]["xc"]);
    }

    // Now convert all values -- but only for the remaining slice
    foreach(array_keys($slice) as $idx) {
      SELF::_convertMeasurement($slice[$idx], $targetUnit, $unitsInv, $area, true);
      // $slice[$idx]["convs"] = $unitsInv[$slice[$idx]["unit"]]["convs"];
    }

    // collect page's item's IDs to retrieve their titles
    $itemIds = array();
    foreach($slice as $measurement) {
      $itemId = $measurement["itemId"];
      $itemIds[$itemId] = $itemId; // automatically eliminating duplicates
    }

    // retrieve item titles
    $itemTitles = array();
    foreach($itemIds as $itemId) {
      $item = get_record_by_id('Item', $itemId);
      $itemTitle = metadata($item, array('Dublin Core', 'Title'));
      $itemTitles[$itemId] = $itemTitle;
    }

    // put item titles back into page's entries
    foreach(array_keys($slice) as $idx) {
      $itemId = $slice[$idx]["itemId"];
      $slice[$idx]["itemTitle"] = $itemTitles[$itemId];
    }

    $result["data"] = $slice;

    $result["pageLen"] = MEASUREMENT_TABLE_LEN;
    $result["numPages"] = $numPages;
    $result["page"] = $page;
    $result["from"] = $from;

    $result["unitsInv"] = $unitsInv;
    // $result["requestUri"] = $_SERVER["REQUEST_URI"];

    $this->_helper->json($result);
  }

  // ---------------------------------------------------------------------------

  private function _addMeasurement(&$measurements, &$singleMeasurement, $x) {
    if ($singleMeasurement[$x]) {
      $measurement = $singleMeasurement;
      $measurement["x"] = $singleMeasurement[$x];
      $measurement["hl"] = $x;
      $measurements[] = $measurement;
    }
  }

  // ---------------------------------------------------------------------------

  private function _convertMeasurement(&$measurement, $targetUnit, &$unitsInv, $area,  $allValues=false) {
    // Sanity values
    foreach(array("l1", "l2", "l3", "f1", "f2", "f3", "v", "x") as $x) {
      if (isset($measurement[$x])) {
        $measurement[$x."c"] = $measurement[$x];
      }
    }

    $sourceUnit = $measurement["unit"]; // This measurement's source unit
    if ( ($sourceUnit != $targetUnit) and isset($unitsInv[$sourceUnit]) ) {
      $factor = $unitsInv[$sourceUnit]["mmConv"] / $unitsInv[$targetUnit]["mmConv"];
      $factor = array( $factor, pow($factor, 2), pow($factor, 3) );
      // echo print_r($factor,true) . "\n";

      if (isset($measurement["x"])) { // Always convert ["x"] -- if (still) present
        $measurement["xc"] = $measurement["x"] * $factor[$area]; // don't round xc for better sortability
      }

      if ($allValues) {
        foreach(array("l1", "l2", "l3") as $x) { // lengthes
          $measurement[$x."c"] = round($measurement[$x] * $factor[0], 3);
        }
        foreach(array("f1", "f2", "f3") as $x) { // faces
          $measurement[$x."c"] = round($measurement[$x] * $factor[1], 3);
        }
        $measurement["vc"] = round($measurement["v"] * $factor[2], 3); // volume
      }
    }
  }

  // ---------------------------------------------------------------------------

  private function _sortDimensions(&$measurement) {
    SELF::_swapDimension($measurement, "l1", "l2");
    SELF::_swapDimension($measurement, "l2", "l3");
    SELF::_swapDimension($measurement, "l1", "l2");

    SELF::_swapDimension($measurement, "f1", "f2");
    SELF::_swapDimension($measurement, "f2", "f3");
    SELF::_swapDimension($measurement, "f1", "f2");
  }

  private function _swapDimension(&$measurement, $a, $b) {
    if ($measurement[$a] < $measurement[$b]) {
      SELF::_swap($measurement[$a], $measurement[$b]);
    }
  }

  private function _swap(&$x,&$y) {
    $tmp=$x;
    $x=$y;
    $y=$tmp;
  }

  // ---------------------------------------------------------------------------

}
