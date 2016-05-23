<?php
/**
 * Measurements
 * AddRelationController
 */

/**
 * Add Relation controller.
 */
class Measurements_AddRelationController extends Omeka_Controller_AbstractActionController {

  public function indexAction() {
    $result = array();
    $result["success"] = false; // Sanity

    // Sanity states

    $subjectItemId = -1;
    $objectItemIds = array();
    $relationComment = "";
    $selectedRelation = -1;

    // Get AJAX parameters

    if ($this->_hasParam("subjectItemId")) { $subjectItemId = intval($this->_getParam('subjectItemId')); }
    if ($this->_hasParam("objectItemIds")) { $objectItemIds = $this->_getParam('objectItemIds'); }
    if ($this->_hasParam("selectedRelation")) { $selectedRelation = intval($this->_getParam('selectedRelation')); }
    if ($this->_hasParam("relationComment")) { $relationComment = $this->_getParam('relationComment'); }

    // Mirror response (for checking the wires) -- can/should be removed after regular function is established
    // $result["subjectItemId"] = $subjectItemId;
    // $result["objectItemIds"] = $objectItemIds;
    // $result["selectedRelation"] = $selectedRelation;
    // $result["relationComment"] = $relationComment;

    // Plausibility check of all values

    $db = get_db();
    $provideRelationComments = !!get_option('item_relations_provide_relation_comments');

    #  $realSubjectItemId will become false in case $subjectItemId does not exist as an item
    $realSubjectItemId = $db->fetchOne("SELECT id FROM `$db->Items` WHERE id=$subjectItemId");

    # $realObjectItemIds will contain only those elements of $objectItemIds that exist as an item -- or be an empty array
    $realObjectItemIds = array();
    if (is_array($objectItemIds)) {
      $checkObjectItemIds = array();
      foreach($objectItemIds as $objectItemId) {
        if (intval($objectItemId)) { $checkObjectItemIds[$objectItemId] = true; }
      }
      $checkObjectItemIds = array_keys($checkObjectItemIds);
      if ($checkObjectItemIds) {
        $checkObjectItemIds = "(" . implode(",", $checkObjectItemIds) . ")";
        $fetchObjectItemIds = $db->fetchAll("SELECT id from `$db->Items` WHERE id in $checkObjectItemIds");
        foreach($fetchObjectItemIds as $fetchObjectItemId) { $realObjectItemIds[] = $fetchObjectItemId["id"]; }
      }
    }

    #  $realSelectedRelation will become false in case $selectedRelation does not exist as a relation
    $realSelectedRelation = $db->fetchOne("SELECT id FROM `$db->ItemRelationsProperty` WHERE id=$selectedRelation");

    # Comment might become empty
    $realRelationComment = mysql_real_escape_string(trim($relationComment));

    // Processed mirror response (for checking the wires) -- can/should be removed after regular function is established
    // $result["realSubjectItemId"] = $realSubjectItemId;
    // $result["realObjectItemIds"] = $realObjectItemIds;
    // $result["realSelectedRelation"] = $realSelectedRelation;
    // $result["realRelationComment"] = $realRelationComment;

    if ( ($realSubjectItemId) and ($realObjectItemIds) and ($realSelectedRelation) ) {

      $dataSet = array("subject_item_id", "object_item_id", "property_id");
      if ($provideRelationComments) { $dataSet[] = "relation_comment"; }
      $dataSet = "(" . implode(",", $dataSet) . ")";

      $dataTuples = array();
      foreach($realObjectItemIds as $realObjectItemId) {
        $dataTuple = array( $realSubjectItemId, $realObjectItemId, $realSelectedRelation);
        if ($provideRelationComments) { $dataTuple[] = "'$realRelationComment'"; ; }
        $dataTuples[] = implode(",", $dataTuple);
      }

      $qu = "INSERT INTO `$db->ItemRelationsRelations` $dataSet VALUES ".
            "(" . implode("), (", $dataTuples) . ")";
      // $result["query"] = $qu;

      try {
        $db->query($qu);
        $result["success"] = true;
      }
      catch (Exception $e) { }
    }

    $this->_helper->json($result);
  }

}
