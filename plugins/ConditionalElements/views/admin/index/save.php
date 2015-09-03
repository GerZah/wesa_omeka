<?php
$pageTitle = __('Add Dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <div class="field">
        <?php
        if($_POST['term'] == '' || $_POST['term'] == -1 )
        { ?>
          <h2><?php echo __("No term is selected. Please try creating the dependency again."); ?></h2>
          <a href="<?php echo $this->url('conditional-elements/index/term', array('dependent_id' => $_POST['dependent'], 'dependee_id' => $_POST['dependee'] )); ?>" ><?php echo __('Back'); ?></a>
        <?php }
        else { ?>
        <h2><?php echo __("You have successfully saved the dependency."); ?></h2>
          <?php }; ?>
      </div>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
     <a href="<?php echo html_escape(url('conditional-elements/index')); ?>" class="add big green button"><?php echo __('Back'); ?></a>
   </div>
  </section>
<?php echo foot(); ?>
