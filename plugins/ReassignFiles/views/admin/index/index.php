<?php
queue_js_file('items');
queue_js_file('tabs');
queue_css_file('reassignfiles');
echo head(array('title' => __('Reassign Files to items'), 'bodyclass' => 'reassignfiles'));
?>
<?php echo flash(); ?>
<div class="drawer-contents">
  <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
    <h2>Step 1: Select Items to Edit </h2>
    <div class="field">
      <p>Edit items from the following collection:</p>
    </div>
    <div class="inputs three columns omega">
      <?php
      $itemNames = array();
      $sqlDb = get_db();
      $query = "SELECT record_id, text from {$sqlDb->ElementText} order by text";
      $itemNames = $sqlDb->fetchAll($query);
      $item = array();
      foreach ($itemNames as $itemName) {
          $item[$itemName['record_id']] = $itemName['text'];
      }
      echo $this->formSelect('reassignFiles-item', $item, array('multiple' => false), $item); ?>
    </div>
  </fieldset>
  <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-fields-set' style="border: 1px solid black; padding:15px; margin:10px;">
    <h2>Step 2: Select Files to Assign </h2>
    <div class="inputs four columns omega">
      <?php
      $fileNames = array();
      $db = get_db();
      $select = "SELECT et.text AS itemName, et.record_id AS record_id, f.original_filename AS original_filename, f.item_id AS item_id
                 FROM {$db->File} f
                 JOIN {$db->ElementText} et
                 ON f.item_id = et.record_id
                 ORDER BY original_filename";
      $files = $db->fetchAll($select);
      foreach ($files as $file) {
          $fileNames[$file['original_filename']] = $file['itemName'];
      }
      $existing = array();
      foreach ($fileNames as $key => $value) {
        $existing[] = $key.'['.$value.']';
      }
      echo $this->formSelect('reassignFiles-files[]', null, array('multiple' => true, 'size' => 10, 'style' => 'width: 600px;'), $existing);
      ?>
    </div>
  </fieldset>
  <div class="field">
    <button type="submit" name="reassign-button">Reassign Files</button>
  </div>
</div>
<?php echo foot();
