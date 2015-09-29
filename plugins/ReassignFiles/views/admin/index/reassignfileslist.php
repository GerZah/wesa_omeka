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
    ORDER BY original_filename";
    $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $Id = $file['itemId'];
    $fileNames[$file['original_filename']] = $file['itemName'];
  }
  ?>
  <?php
  $existingFiles = array();
  foreach ($fileNames as $key => $value) {
    #echo "<pre>"; print_r($Id); die("</pre>");
      $existingFiles[$key] = $key.' [#Id: '.$Id.' - Title: '.$value.']';
  }
  echo get_view()->formSelect('reassignFiles-files[]', null , array('multiple' => true, 'size' => 10, 'style' => 'width: 500px;'), $existingFiles);
  ?>
</div>
