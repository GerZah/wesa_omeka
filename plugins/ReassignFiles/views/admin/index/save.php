<?php
$pageTitle = __('Save Changes');
echo head(array('title'=>$pageTitle));
echo flash();
$itemId = 0;
$files = null;
if (isset($_POST['reassignFilesItem'])) { $itemId = intval($_POST['reassignFilesItem']); }
if (isset($_POST['reassignFilesFiles'])) {$files = $_POST['reassignFilesFiles']; }
?>
<section class="seven columns alpha">
  <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
    <div class="field">
      <?php
      if(($itemId<0) or (is_null($files)))
      { ?>
        <h2><?php echo __("No item/file is selected. Please select an item/file to reassign"); ?></h2>
        <a href="<?php echo html_escape(url('reassign-files/index')); ?>" class="add big green button" ><?php echo __('Back'); ?></a>
        <?php }
        else { ?>
          <h2><?php echo __("You have successfully saved the changes."); ?></h2>
          <a href="<?php echo html_escape(url('reassign-files/index')); ?>" class="add big green button" ><?php echo __('Back'); ?></a>
          <?php }; ?>
        </div>
      </fieldset>
    </section>
    <?php echo foot(); ?>
