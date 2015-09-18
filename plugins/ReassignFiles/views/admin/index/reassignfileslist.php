<div class="add-new"><?php echo __('Select the files to reassign to the item: %s',metadata('item', array('Dublin Core', 'Title'))); ?></div>
<div class="drawer-contents">
  <?php
  $itemId = metadata('item', 'id');
  $fileNames = array();
  $db = get_db();
  $select = "SELECT original_filename from $db->File where item_id !=$itemId order by original_filename";
  $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $fileNames[$file['original_filename']] = $file['original_filename'];
  }
  ?>
  <?php echo get_view()->formSelect('reassignFiles-files[]', null, array('multiple' => true, 'size' => 10, 'style' => 'width: 500px;'), $fileNames); ?>
</div>
