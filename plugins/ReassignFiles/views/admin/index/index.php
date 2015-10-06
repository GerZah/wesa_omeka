<?php
queue_js_file('items');
queue_js_file('tabs');
queue_css_file('reassignfiles');
echo head(array('title' => __('Reassign Files to items'), 'bodyclass' => 'reassignfiles'));
?>
<?php echo flash(); ?>
<div class="drawer-contents">
  <form method="post" action="<?php echo url('reassign-files/index/save'); ?>">
  <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
    <h2>Step 1: Select Items to Edit </h2>
    <div class="field">
      <p>Edit items from the following collection:</p>
    </div>
    <div class="inputs three columns omega">
      <?php
      $itemNames = array();
      $sqlDb = get_db();
      $query = "SELECT record_id, text from {$sqlDb->ElementText} WHERE element_id = 50 GROUP by record_id";
      $itemNames = $sqlDb->fetchAll($query);
      $item = array(-1 => __('Select Below'));
      foreach ($itemNames as $itemName) {
          $item[$itemName['record_id']] = $itemName['text'];
      }
      echo $this->formSelect('reassignFilesItem', $item, array('multiple' => false), $item); ?>
    </div>
  </fieldset>
  <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-fields-set' style="border: 1px solid black; padding:15px; margin:10px;">
    <h2>Step 2: Select Files to Assign </h2>
    <div class="inputs four columns omega">
      <?php
			// ---vvv--- Would love to encapsulate this block in a method in ReassignFilesPlugin.php
      $fileNames = array();
      $db = get_db();
      $select = "SELECT et.text AS itemName, f.original_filename AS original_filename, f.item_id AS itemId, f.id AS fileId
                 FROM {$db->File} f
                 JOIN {$db->ElementText} et
                 ON f.item_id = et.record_id
                 WHERE et.element_id = 50
                 GROUP BY f.id"; # GROUP BY original_filename # no grouping by filename (which might be identical)
      $files = $db->fetchAll($select);
      #echo "<pre>$select\n\n"; print_r($files); die("</pre>");
      foreach ($files as $file) {
        $fileNames[$file['fileId']] = $file['original_filename'].
                                        ' [#'.$file['itemId'].
                                        ' ('.$file['fileId'].') - '.
                                        $file['itemName'].']';
      }
			// ---^^^--- But could not figure out how to call object methods from within the admin page view
      echo $this->formSelect('reassignFilesFiles[]', null, array('multiple' => true, 'size' => 10, 'style' => 'width: 600px;'), $fileNames);
      ?>
    </div>
  </fieldset>
  <div class="field">
    <button type="submit" name="reassign-button">Reassign Files</button>
  </div>
</div>
</form>
<?php echo foot();
