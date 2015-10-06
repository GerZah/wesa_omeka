<div><?php echo __('Select the files to reassign to the item:'); ?></div>
<div class="drawer-contents">
  <?php
  $itemId = metadata('item', 'id');
  $fileNames = array();
  $db = get_db();
  $select = "SELECT et.text AS itemName, et.record_id AS itemId, f.original_filename AS original_filename, f.id AS fileId
  FROM {$db->File} f
  JOIN {$db->ElementText} et
  ON f.item_id = et.record_id
  WHERE f.item_id != $itemId
  AND et.element_id = 50
  GROUP BY et.record_id";
  $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $fileNames[$file['fileId']] = $file['original_filename'].' [#'.$file['itemId'].' - '.$file['itemName'].' ]';
  }
  ?>
  <?php
  echo get_view()->formSelect('reassignFilesFiles[]', null , array('multiple' => true, 'size' => 10, 'style' => 'width: 500px;'), $fileNames);
  ?>
  <input type="hidden" name="itemId" value="<?php echo $itemId; ?>">
</div>
