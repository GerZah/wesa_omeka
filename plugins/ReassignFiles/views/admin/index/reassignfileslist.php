<div class="add-new"><?php echo __('Select the files to reassign to the item: %s',metadata('item', array('Dublin Core', 'Title'))); ?></div>
<div class="drawer-contents">
  <?php
  $itemId = metadata('item', 'id');
  $fileNames = array();
  $db = get_db();
  $select = "SELECT et.text AS itemName, et.record_id AS record_id, f.original_filename AS original_filename, f.item_id AS item_id
             FROM {$db->File} f
             JOIN {$db->ElementText} et
             ON f.item_id = et.record_id
             where item_id !=$itemId
             ORDER BY original_filename";
  $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $fileNames[$file['original_filename']] = $file['itemName'];
  }
  ?>
  <?php
  $existing = array();
  foreach ($fileNames as $key => $value) {
    $existing[] = $key.'['.$value.']';
  }
   echo get_view()->formSelect('reassignFiles-files[]', null, array('multiple' => true, 'size' => 10, 'style' => 'width: 500px;'), $existing);
 ?>
</div>
