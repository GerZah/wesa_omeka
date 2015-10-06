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
