<?php
$pageTitle = __('Delete Dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <div class="field">
        <h2><?php echo __("You have successfully deleted the dependency."); ?></h2>
      </div>
    </fieldset>
  </section>
  <section class="three columns omega">
        <a href="<?php echo $this->url('conditional-elements/index'); ?>" ><?php echo __('Back'); ?></a>
  </section>
<?php echo foot(); ?>
