<?php

/**
* Returns all fileNames as data source for the multi-select box
*/
function reassignFiles_getFileNames($filterItemID = 0)
{
  $filterItemId = intval($filterItemID);
  $filterItemInfix = ( $filterItemId > 0 ? "AND f.item_id <> $filterItemID" : "" );

  $fileNames = array();
  $db = get_db();
  $select = "SELECT et.text AS itemName, f.original_filename AS original_filename, f.item_id AS itemId, f.id AS fileId
  FROM {$db->File} f
  LEFT JOIN {$db->ElementText} et
  ON f.item_id = et.record_id
  WHERE et.element_id = 50 or et.element_id is null
  $filterItemInfix
  GROUP BY f.id";
  # die("<pre>$select</pre>");

  $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $fileNames[$file['fileId']] = $file['original_filename'].
    ' - '.( $file['itemName'] ? $file['itemName'] : "[".__("Untitled Item")."]" ).
    ' [#'.$file['itemId'].
    '/'.$file['fileId'].']';
  }
  return $fileNames;
}

/**
* Do the actual work: Reassign the $files (specified by their file IDs towards one target item ID
*/
function reassignFiles_reassignFiles($itemID, $files) {
  $errMsg = false;
  $itemID = intval($itemID); // typecast / filter item ID for strange characters

  if ($itemID) {
    $db = get_db();
    // make sure that the target item actually exists in the database
    $targetExists = $db->fetchOne("SELECT count(*) FROM `$db->Items` where id=$itemID");

    if ($targetExists) {
      $fileIDs = array();
      foreach($files as $file) {
        $fileID = intval($file); // typecast / filter file IDs for strange characters
        if ($fileID) { $fileIDs[] = $fileID; }
      }

      if ($fileIDs) { // at least one?
        $fileIDs = implode(",", $fileIDs);
        $deleteOrphanedItems = (int)(boolean) get_option('reassign_files_delete_orphaned_items');

        // 1st: If applicable, figure out which items might be orphaned after the reassign
        $potentialOrphans = array();
        if ($deleteOrphanedItems) {
          $sql = "SELECT item_id from `$db->File` where id IN ($fileIDs)";
          $potentialOrphans = $db->fetchAll($sql);
        }

        // 2nd: Actually reassign the files
        $sql = "UPDATE `$db->File` set item_id = $itemID where id IN ($fileIDs)";
        $db->query($sql); // let's do this

        // 3rd: If applicable, take care of orphans, i.e. delete them
        if ($deleteOrphanedItems) { reassignFiles_deleteOrphans($potentialOrphans); }

        # $errMsg = $sql;
      }
      else { $errMsg = __('Please choose files to reassign.'); }
    }
    else { $errMsg = __('Please choose an existing item to reassign.'); }
  }
  else { $errMsg = __('Please choose an item to reassign.'); }

  return $errMsg;
}

/**
* If applicable, check if items who just had files assigned to them are now "empty" and, if so, delete them
*/
function reassignFiles_deleteOrphans($potentialOrphans, $reprocess = false) {
 	$db = get_db();

  // DEBUG: if $reprocess == true, suspect all (!) items to be potential orphans
  if ($reprocess) { $potentialOrphans = $db->fetchAll("SELECT id FROM `$db->Items` WHERE true"); }

  $justIds = array();
  foreach($potentialOrphans as $potentialOrphan) { $justIds[] = $potentialOrphan["id"]; }
  
  if ($justIds) {
    $justIdString = implode(",", $justIds);
    
    // +#+#+# Check which items are actually there in omeka_items
    // +#+#+# Check which of those do not have elements AT ALL in omeka_eleemnt_texts
    // +#+#+# Check which of those do not have files left in omeka_files
    // +#+#+# If applicable: Check which of those do not take place in relations in omeka_item_relations_relations
  }

  #die("<pre>$justIdString\n".print_r($justIds, true)."</pre>");
}