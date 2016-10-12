<?php
queue_js_file('items');
queue_js_file('tabs');
queue_css_file('reassignfiles');
echo head(array('title' => __('Reassign Files to Item'), 'bodyclass' => 'reassignfiles'));
$numLatest = ( isset($_GET["numlatest"]) ? intval($_GET["numlatest"]) : 50 );
$numLatest = ( $numLatest < 50 ? 50 : $numLatest );
?>
<?php echo flash(); ?>
<div class="drawer-contents">
  <form method="post" action="<?php echo url('reassign-files/index/save'); ?>">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2><?php echo __("Step 1: Select Item"); ?></h2>
      <div class="field">
        <p><?php echo __("Please select an existing item to reassign files to."); ?></p>
        <p>
        <?php
          $extension = 50;
          $newUrl = $_SERVER['REDIRECT_URL'] . "?numlatest=" . ($numLatest + $extension);
          echo
            sprintf(__("<em>Please note:</em> Currently displaying %d latest modified items."), $numLatest)
            . " <a href='$newUrl'>"
            . "[" . sprintf(__("Click here to display %d more."), $extension) . "]"
            . "</a>"
          ;
        ?>
        </p>
      </div>
      <div class="inputs three columns omega">
        <?php
          $itemNames = array();
          $sqlDb = get_db();
          $item = array(-1 => __('Select Below'));
          $query = "
            SELECT id
            FROM `$sqlDb->Items`
            ORDER BY modified DESC
            LIMIT $numLatest
          ";
          $itemIds = $sqlDb->fetchAll($query);
          foreach ($itemIds as $itemId) {
            $curItem = get_record_by_id('Item', $itemId["id"]);
            $item[$itemId["id"]] = metadata($curItem, array('Dublin Core', 'Title')) . " [#".$itemId["id"]."]";
          }
          echo $this->formSelect('reassignFilesItem', $item, array('multiple' => false), $item);
        ?>
      </div>
      <div>
      </div>
    </fieldset>
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-fields-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2><?php echo __("Step 2: Select Files to Reassign"); ?></h2>
      <div class="field">
        <p><?php echo __("Please select one or more files to be reassigned to the above selected item."); ?></p>
      </div>
      <div class="inputs four columns omega">
        <?php echo $this->formSelect('reassignFilesFiles[]', null, array('multiple' => true, 'size' => 10, 'style' => 'width: 600px;'), $files);
        ?>
      </div>
    </fieldset>
    <div class="field">
      <button type="submit" name="reassign-button" class="add big green button"><?php echo __("Reassign Files"); ?></button>
    </div>
  </div>
</form>
<?php echo foot();
