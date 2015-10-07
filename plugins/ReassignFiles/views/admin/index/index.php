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
        $query = "SELECT id as record_id,
                    ( SELECT text from {$sqlDb->ElementText} et
                      WHERE element_id = 50
                      AND et.record_id = it.id
                      GROUP by et.record_id
                    ) as text
                  FROM {$sqlDb->Items} it";
        $itemNames = $sqlDb->fetchAll($query);
        $item = array(-1 => __('Select Below'));
        foreach ($itemNames as $itemName) {
          $item[$itemName['record_id']] = ( $itemName['text'] ? $itemName['text'] : "[".__("Untitled Item")."]" );
        }
        echo $this->formSelect('reassignFilesItem', $item, array('multiple' => false), $item); ?>
      </div>
    </fieldset>
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-fields-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2>Step 2: Select Files to Assign </h2>
      <div class="inputs four columns omega">
        <?php echo $this->formSelect('reassignFilesFiles[]', null, array('multiple' => true, 'size' => 10, 'style' => 'width: 600px;'), $files);
        ?>
      </div>
    </fieldset>
    <div class="field">
      <button type="submit" name="reassign-button">Reassign Files</button>
    </div>
  </div>
</form>
<?php echo foot();
