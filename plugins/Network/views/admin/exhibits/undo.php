<?php
$pageTitle = __('Re-add deleted item to Exhibit');
echo head(array('title'=>$pageTitle));
echo flash();
?>
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
    <form method="post" action="<?php echo url('network/view'); ?>">
      <?php
      if (isset($_POST['id'])) { $dependent_id = intval($_POST['id']); }
      else if (isset($_GET['id'])) { $dependent_id = intval($_GET['id']); }
    
        #if ($item_id) {
          #$sql = "insert into from `$db->NetworkRecord` where item_id=$item_id";
          #$db->query($sql);
        #}
        }
      ?>
      <div class="field">
        <h2><?php echo __("You have successfully readded the item to exhibit."); ?></h2>
      </div>
</form>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
     <a href="<?php echo html_escape(url('network/view')); ?>" class="add big green button"><?php echo __('Back'); ?></a>
   </div>
  </section>
<?php echo foot(); ?>
