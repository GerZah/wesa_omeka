<?php
/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */
  queue_js_file('cytoscape.min');
  // queue_js_file('jquery-2.0.3.min'); // not necessary, works with Omeka's jQuery (currently 1.12.0)
  queue_js_file('network');
  queue_css_file('network');
  queue_js_string('var cytoBaseUrl = ' . json_encode(CURRENT_BASE_URL) . ';');
  queue_js_file('jquery.qtip.min');
  queue_css_file('jquery.qtip.min');
  queue_js_file('cytoscape-qtip');
?>

<?php echo head(array(
  'title' => in_getExhibitField('title'),
  'bodyclass' => 'network show'
)); ?>
<!-- Exhibit title: -->
<h1><?php echo in_getExhibitField('title'); ?></h1>
<hr>
<div id="cy"></div>
  <?php
    $db = get_db();

    // ------------------------------------ Fetch network exhibit data

    // ----- Get display configuration switches

    $selectSwitches = "
      SELECT all_items, all_relations
      FROM `$db->NetworkExhibit`
      WHERE id = $exhibit_id
    ";
    $switches = $db->fetchAll($selectSwitches);
    $allItems = intval(!!$switches[0]["all_items"]); // Display all items, as opposed to limiting to participating ones
    $allRelations = intval(!!$switches[0]["all_relations"]); // Display all relations (if selected or by default)

    // ----- Get required relation IDs (if set)

    $relations = "";

    if (!$allRelations) {
      $selectRelations = "
        SELECT selected_relations
        FROM `$db->NetworkExhibit`
        WHERE id = $exhibit_id
      ";
      $relations = $db->fetchOne($selectRelations);
    }

    // ----- Get actual relations

    // relation filter infix
    $relationInfix = ($relations
      ? "AND property_id IN ($relations)"
      : "AND ($allRelations)"
    );

    // Item subquery -- actually fetches all items that were imported into this exhibt
    $selectItemIds = "
      SELECT item_id
      FROM `$db->NetworkRecord`
      WHERE exhibit_id = $exhibit_id
    ";

    // full query including item subquery / relation infix
    $selectEdges = "
      SELECT subject_item_id, object_item_id, property_id
      FROM `$db->ItemRelationsRelations`
      WHERE subject_item_id IN ($selectItemIds)
      AND object_item_id IN ($selectItemIds)
      $relationInfix
      ORDER BY subject_item_id
    ";
    $edges = $db->fetchAll($selectEdges);

    // ----- Collect relevant property IDs

    $propertyIds = array();
    foreach($edges as $edge) {
      $propertyId = $edge["property_id"];
      $propertyIds[$propertyId] = $propertyId;
    }
    $propertyIdsVerb = ( $propertyIds ? implode(",", $propertyIds) : "-1" );

    // ----- Retrieve relevant property texts

    $selectPropertyLabels = "
      SELECT id, label
      FROM `$db->ItemRelationsProperty`
      WHERE id IN ($propertyIdsVerb)
    ";
    $propertyLabelSets = $db->fetchAll($selectPropertyLabels);

    $propertyLabels = array();
    foreach($propertyLabelSets as $propertyLabelSet) {
      $propertyLabels[$propertyLabelSet["id"]] = $propertyLabelSet["label"];
    }

    // ----- Generate item list

    $items = array();
    $itemIds = array();

    if ($allItems) { // Force all? Get all item ids
      $allItems = $db->fetchAll($selectItemIds);
      foreach($allItems as $oneItem) {
        $itemId = $oneItem["item_id"];
        $items[$itemId] = $oneItem;
        $itemIds[$itemId] = $itemId;
      }
    }
    else { // Limit item list so it contains only those that are actually related
      foreach($edges as $edge) { // might add items multiple times, but won't create duplicates
        $idxs = array($edge["subject_item_id"], $edge["object_item_id"]);
        foreach($idxs as $idx) {
          $items[$idx] = array( "item_id" => $idx );
          $itemIds[$idx] = $idx;
        }
      }
    }

    // ----- Fetch items' titles

    if ($items) {
      $itemIds = implode(",", array_keys($itemIds));
      $selectItemTitles = "
        SELECT item_id, item_title
        FROM `$db->NetworkRecord`
        WHERE exhibit_id = $exhibit_id
        AND item_id IN ($itemIds)
      ";
      $itemTitles = $db->fetchAll($selectItemTitles);
      foreach($itemTitles as $itemTitle) {
        $items[$itemTitle["item_id"]]["item_title"] = $itemTitle["item_title"];
      }
    }

    // ------------------------------------ Create arrays for JSON transfer

    $nodeData = array();
    foreach($items as $item) {
      $nodeData[] = array (
        "data" => array(
          "id" => $item["item_id"],
          "name" => @$item["item_title"]
        )
      );
    }

    $edgeData = array();
    foreach($edges as $edge) {
      $edgeData[] = array(
        "data" => array(
          "source" => $edge["subject_item_id"],
          "target" => $edge["object_item_id"],
          "label" => @$propertyLabels[$edge["property_id"]],
        )
      );
    }

    // ------------------------------------ JSON data into SCRIPT tag

    $jsString=
      "var nodeData = ".json_encode($nodeData).";\n".
      "var edgeData = ".json_encode($edgeData).";\n";
    echo "<script type='text/javascript'>\n$jsString</script>";
  ?>
<?php echo foot(); ?>
