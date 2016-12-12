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

  queue_js_file('jquery.qtip.min');
  queue_css_file('jquery.qtip.min');
  queue_js_file('cytoscape-qtip');

  $db = get_db();

  // ----- Figure out whether or not the user usually sees non-public items or not

  $currentUser = current_user();
  $isLoggedOn = !!($currentUser);
  $userRole = ( $isLoggedOn ? $currentUser["role"] : false );
  $seesNonPublic = intval(
    $userRole
    ? in_array( $userRole, array("super", "admin", "researcher", "contributor") )
    : false
  );
  // echo "<pre>seesNonPublic: $seesNonPublic</pre>";

  // ----- Get display configuration switches

  $selectSwitches = "
    SELECT graph_structure, all_items, all_relations, all_references, color_item_types, sticky_node_selection, nonpublic_items
    FROM `$db->NetworkExhibit`
    WHERE id = $exhibit_id
  ";
  $switches = $db->fetchAll($selectSwitches);
  $graphStructure = intval($switches[0]["graph_structure"]); // Which graph balancing method
  $nonPublicItems = intval($switches[0]["nonpublic_items"]); // How should
  $colorItemTypes = intval(!!$switches[0]["color_item_types"]); // Display different item types in different colors
  $stickyNodeSelection = intval(!!$switches[0]["sticky_node_selection"]); // Keep highlighted nodes highlighted until clicking the background
  $allItems = intval(!!$switches[0]["all_items"]); // Display all items, as opposed to limiting to participating ones
  $allRelations = intval(!!$switches[0]["all_relations"]); // Display all relations (if selected or by default)
  $allReferences = intval(!!$switches[0]["all_references"]); // Display all references (if selected or by default)

  if ($graphStructure==1) {
    queue_js_file('cytoscape-spread');
  }
  // echo "<pre>nonPublicItems: $nonPublicItems</pre>";

  // ----- Inject necessary JavaScript variables into code

  queue_js_string(
    'var cytoBaseUrl = ' . json_encode(CURRENT_BASE_URL) . "; " .
    'var omekaBaseUrl = ' . json_encode(url("/")) . "; " .
    'var cytoGraphStructure = ' . $graphStructure . ";".
    'var nonPublicItems = ' . $nonPublicItems . ";".
    'var seesNonPublic = ' . $seesNonPublic . ";".
    'var stickyNodeSelection = ' . $stickyNodeSelection . ";"
  );


  echo head(
    array(
      'title' => in_getExhibitField('title'),
      'bodyclass' => 'network show'
    )
  );
?>
<!-- Exhibit title: -->
<h1><?php echo in_getExhibitField('title'); ?></h1>
<hr>
<div id="cy"></div>
  <?php
    // ------------------------------------ Fetch network exhibit data

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

    // Item subquery -- actually fetches all items that were imported into this exhibit
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

    // ----- Get actual references
    if (NetworkPlugin::itemReferencesActive()) {

      $referenceElements = "";
      if ($allReferences) {
        $referenceElementsJson=get_option('item_references_select');
        if (!$referenceElementsJson) { $referenceElementsJson="[]"; }
        $referenceElements = json_decode($referenceElementsJson,true);
        $referenceElements = implode(",", $referenceElements);
      }
      else {
        $selectReferences = "
          SELECT selected_references
          FROM `$db->NetworkExhibit`
          WHERE id = $exhibit_id
        ";
        $referenceElements = $db->fetchOne($selectReferences);
      }

      if ($referenceElements) {

        // fetch reference elements texts for imported items -- limit to references inside the imported collection
        $selectReferencedElements = "
          SELECT record_id as subject_item_id, element_id as reference_property_id, text as object_item_id
          FROM `$db->ElementTexts`
          WHERE element_id IN ($referenceElements)
          AND record_id IN ($selectItemIds)
          AND text IN ($selectItemIds)
        ";
        // As "text" contains only the target's numerical value, this works just fine
        $refEdges = $db->fetchAll($selectReferencedElements);

        foreach($refEdges as $refEdge) {
          $edges[] = array(
            "subject_item_id" => $refEdge["subject_item_id"],
            "property_id" => "R".$refEdge["reference_property_id"], // add a "R" prefix for "reference property"
            "object_item_id" => $refEdge["object_item_id"]
          );
        }

        $referenceElementTitles = NetworkPlugin::referenceElementTitles(explode(",", $referenceElements));
        // Add the reference element titles as edge label properties -- again with the "R" prefix
        foreach($referenceElementTitles as $key => $val) { $propertyLabels["R".$key] = $val; }

      }

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

    // ----- Fetch items' titles and item type IDs and ultimately item colors

    if ($items) {
      $itemIds = implode(",", array_keys($itemIds));
      $selectItemTitles = "
        SELECT nr.item_id, nr.item_title, nr.item_type_id, it.public
        FROM `$db->NetworkRecord` nr
        JOIN `$db->Items` it ON nr.item_id = it.id
        WHERE exhibit_id = $exhibit_id
        AND item_id IN ($itemIds)
      ";
      $itemDetails = $db->fetchAll($selectItemTitles);
      // echo "<pre>" . print_r($itemDetails,true) . "</pre>"; die();
      $itemTypes = array();
      foreach($itemDetails as $itemDetail) {
        if ((!$itemDetail["public"]) and (!$seesNonPublic) and ($nonPublicItems == 2) ) {
          unset($items[$itemDetail["item_id"]]);
        }
        else {
          $items[$itemDetail["item_id"]]["item_title"] = $itemDetail["item_title"]; // store item title
          $items[$itemDetail["item_id"]]["item_type_id"] = $itemDetail["item_type_id"]; // store full item type ID
          $items[$itemDetail["item_id"]]["public"] = $itemDetail["public"]; // store if whether or not item is public
          $itemTypes[$itemDetail["item_type_id"]] = $itemDetail["item_type_id"]; // collect all item type IDs
        }
      }
      $itemTypes=array_flip(array_keys($itemTypes)); // keep just 0..n for all used item type IDs
      foreach(array_keys($items) as $idx) { // now replace the database item type ids with color codes 0..x
        $items[$idx]["item_color"] = @$itemTypes[$items[$idx]["item_type_id"]] % 8; // 0..7
        unset($items[$idx]["item_type_id"]); // away with the database item type id
      }
    }
    // echo "<pre>" . print_r($items,true) . "</pre>"; die();

    // ------------------------------------ Create arrays for JSON transfer

    $nodeData = array();
    foreach($items as $item) {
      $nodeData[] = array (
        "data" => array(
          "id" => $item["item_id"],
          "name" => @$item["item_title"],
          "color" => ( $colorItemTypes ? @$item["item_color"] : 0 ),
          "public" => @intval($item["public"])
        )
      );
    }

    $edgeData = array();
    foreach($edges as $edge) {
      $edgeData[] = array(
        "data" => array(
          "source" => $edge["subject_item_id"],
          "target" => $edge["object_item_id"],
          "label" => @$propertyLabels[$edge["property_id"]], // works both for relations and references due to "R" prefix
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
