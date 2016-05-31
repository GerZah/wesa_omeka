<?php
queue_js_file('cytoscape.min');
// queue_js_file('jquery-2.0.3.min'); // not necessary, works with Omeka's jQuery (currently 1.12.0)
queue_js_file('network');
queue_css_file('network');
/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */
 // queue_js_string("
 //   var networkDataUrl = ".json_encode(url('network/exhibits/')).";
 // ");
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

    // ----- Get required relation IDs (if set)

    $selectRelations = "
      SELECT selected_relations
      FROM `$db->NetworkExhibit`
      WHERE id = $exhibit_id
    ";
    $relations = $db->fetchOne($selectRelations);

    // ----- Get actual relations

    // relation filter infix
    $relationInfix = ($relations
      ? "AND property_id IN ($relations)"
      : ""
    );

    // Item subquery -- actually fetches all items that were imported into this exhibt
    $selectItems = "
      SELECT item_id
      FROM `$db->NetworkRecord`
      WHERE exhibit_id = $exhibit_id
    ";

    // full query including item subquery / relation infix
    $selectEdges = "
      SELECT subject_item_id, object_item_id, property_id
      FROM `$db->ItemRelationsRelations`
      WHERE subject_item_id IN ($selectItems)
      AND object_item_id IN ($selectItems)
      $relationInfix
      ORDER BY subject_item_id
    ";
    $edges = $db->fetchAll($selectEdges);

    // ----- Generate item list that contains only those that are actually related

    $items = array();
    foreach($edges as $edge) { // might add items multiple times, but won't create duplicates
      $idx = $edge["subject_item_id"];
      $items[$idx] = array( "item_id" => $idx );
      $idx = $edge["object_item_id"];
      $items[$idx] = array( "item_id" => $idx );
    }

    // ----- Fetch items' titles

    foreach(array_keys($items) as $idx) {
      $items[$idx]["item_title"] = metadata(
        get_record_by_id('Item', $items[$idx]["item_id"]), array('Dublin Core', 'Title')
      );
    }

    // ------------------------------------ Create arrays for JSON transfer

    $nodeData = array();
    foreach($items as $item) {
      $nodeData[] = array (
        "data" => array(
          "id" => $item["item_id"],
          "name" => $item["item_title"]
        )
      );
    }

    $edgeData = array();
    foreach($edges as $edge) {
      $edgeData[] = array(
        "data" => array(
          "source" => $edge["subject_item_id"],
          "target" => $edge["object_item_id"]
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
