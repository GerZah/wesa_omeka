<?php
  echo head(array(
    'title' => __('Network | Remove Items from Exhibit "%s"', in_getExhibitField('title')),
  ));
?>
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <div class="field">
        <?php echo flash(); ?>
          <form method="get" action="<?php echo url('network/view'); ?>">
            <?php
            if (isset($_GET['record_id'])) {
              $record_id = intval($_GET['record_id']);
              $db = get_db();
              if ($record_id) {
                  $sql = "DELETE FROM `$db->NetworkRecord` where id = '$record_id'";
                  echo "<pre>" . print_r($sql)."</pre>"; die();
                  $db->query($sql);
                }
            }
            ?>
        <h2><?php echo __("You have successfully removed the item from exhibit."); ?></h2>
        </form>
      </div>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
     <a href="<?php echo html_escape(url('network/undo')); ?>" class="add big green button"><?php echo __('Undo'); ?></a>
     <a href="<?php echo html_escape(url('network/view')); ?>" class="add big green button"><?php echo __('Back'); ?></a>
   </div>
  </section>
<?php echo foot(); ?>
