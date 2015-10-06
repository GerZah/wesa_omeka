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
  JOIN {$db->ElementText} et
  ON f.item_id = et.record_id
  WHERE et.element_id = 50
  $filterItemInfix
  GROUP BY f.id";
  # die("<pre>$select</pre>");

  $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $fileNames[$file['fileId']] = $file['original_filename'].
    ' - '.$file['itemName'].
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
        $sql = "UPDATE `$db->File` set item_id = $itemID where id IN ($fileIDs)";
        $db->query($sql); // let's do this

        # $errMsg = $sql;
      }
      else { $errMsg = __('Please choose files to reassign.'); }
    }
    else { $errMsg = __('Please choose an existing item to reassign.'); }
  }
  else { $errMsg = __('Please choose an item to reassign.'); }

  return $errMsg;
}
