<div><?php echo __('Select the files to reassign to the item:'); ?></div>
<div class="drawer-contents">
  <?php
  $itemId = metadata('item', 'id');
  $fileNames = array();
  $whereClause = "";
  if ($itemId) {
    $whereClause = "WHERE f.item_id != $itemId";
  }
  $db = get_db();
  $select = "SELECT et.text AS itemName, et.record_id AS record_id, f.original_filename AS original_filename, f.item_id AS item_id
    FROM {$db->File} f
    JOIN {$db->ElementText} et
    ON f.item_id = et.record_id
    $whereClause
    ORDER BY original_filename";
  $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $fileNames[$file['original_filename']] = $file['itemName'];
  }
  ?>
  <?php
  $existingFiles = array();
  foreach ($fileNames as $key => $value) {
    //check if the item Id exists, else set the value to 0
    $itemId = ($itemId) ? $itemId : null;
    $existingFiles[$itemId] = $key.'['.$value.']';
  }
  echo get_view()->formSelect('reassignFiles-files[]', null , array('multiple' => true, 'size' => 10, 'style' => 'width: 500px;'), $existingFiles);
  ?>
</div>
