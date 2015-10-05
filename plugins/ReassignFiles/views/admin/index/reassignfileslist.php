<div><?php echo __('Select the files to reassign to the item:'); ?></div>
<div class="drawer-contents">
  <?php
  $itemId = metadata('item', 'id');
  $fileNames = array();
  $db = get_db();
  $select = "SELECT et.text AS itemName, et.record_id AS itemId, f.original_filename AS original_filename
    FROM {$db->File} f
    JOIN {$db->ElementText} et
    ON f.item_id = et.record_id
    WHERE f.item_id != $itemId
    AND et.element_id = 50
    ORDER BY original_filename";
    $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $fileNames[$file['itemId']] = $file['original_filename'];
  }
  ?>
  <?php
  $existingFiles = array();
  foreach ($fileNames as $key => $value) {
    $existingFiles[$key] = $value.' [#'.$key.' - '.' ]';
  }
  echo get_view()->formSelect('reassignFilesFiles[]', null , array('multiple' => true, 'size' => 10, 'style' => 'width: 500px;'), $existingFiles);
  ?>
<input type="hidden" name="itemId" value="<?php echo $itemId; ?>">
</div>
