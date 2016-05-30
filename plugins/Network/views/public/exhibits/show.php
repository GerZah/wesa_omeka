<?php
queue_js_file('cytoscape.min');
queue_js_file('jquery-2.0.3.min');
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

    // ----- Get items

    $selectItems = "
      SELECT item_id
      FROM `$db->NetworkRecord`
      WHERE exhibit_id = $exhibit_id
    ";

    $items = $db->fetchAll($selectItems);
    $itemList = array();
    foreach(array_keys($items) as $idx) { // Get items' titles
      $items[$idx]["item_title"] = metadata(
        get_record_by_id('Item', $items[$idx]["item_id"]), array('Dublin Core', 'Title')
      );
      $itemList[] = $items[$idx]["item_id"];
    }
    $itemList = ( $itemList ? implode(",", $itemList) : "-1" );
    // echo "<pre>$selectItems\n" . print_r($items,true) . "\n$itemList\n</pre>";


    // ----- Get required relations

    $selectRelations = "
      SELECT selected_relations
      FROM `$db->NetworkExhibit`
      WHERE id = $exhibit_id
    ";

    $relations = $db->fetchOne($selectRelations);
    // echo "<pre>selectRelations: $relations</pre>";

    $relationIds = array();
    if ($relations) {
      $relationIds = json_decode($relations);
    }

    // ----- Get required relations

    $relationList = ( $relationIds ? implode(",", $relationIds) : "" );

    $relationInfix = ($relationList
      ? "AND property_id IN ($relationList)"
      : ""
    );

    $selectEdges = "
      SELECT subject_item_id, object_item_id, property_id
      FROM `$db->ItemRelationsRelations`
      WHERE subject_item_id IN ($itemList)
      AND object_item_id IN ($itemList)
      $relationInfix
      ORDER BY subject_item_id
    ";

    $edges = $db->fetchAll($selectEdges);
    // echo "<pre>$selectEdges\n" . print_r($edges,true) . "</pre>";

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
    // echo "<pre>nodeData\n" . print_r($nodeData,true) . "</pre>";
    // echo "<pre>nodeData\n" . json_encode($nodeData) . "</pre>";

    $edgeData = array();
    foreach($edges as $edge) {
      $edgeData[] = array(
        "data" => array(
          "source" => $edge["subject_item_id"],
          "target" => $edge["object_item_id"]
        )
      );
    }
    // echo "<pre>edgeData\n" . print_r($edgeData,true) . "</pre>";
    // echo "<pre>edgeData\n" . json_encode($edgeData) . "</pre>";

    // ------------------------------------ JSON data into SCRIPT tag

    $jsString=
      "var nodeData = ".json_encode($nodeData).";\n".
      "var edgeData = ".json_encode($edgeData).";\n";
    // echo "<pre>$jsString</pre>";
    echo "<script type='text/javascript'>$jsString</script>";
  ?>
<?php echo foot(); ?>
