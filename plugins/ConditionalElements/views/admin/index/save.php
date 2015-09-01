<?php
$pageTitle = __('Add Dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <div class="field">
        <?php
        # check option, 0, name
        if($_POST['term'] == '' )
        { ?>
          <h2><?php echo __("No term is selected. Please try creating the dependency again."); ?></h2>
          <a href="<?php echo $this->url('conditional-elements/index'); ?>" ><?php echo __('Back'); ?></a>
        <?php }
        else { ?>
        <h2><?php echo __("You have successfully saved the dependency."); ?></h2>
          <?php }; ?>
      </div>
    </fieldset>
  </section>
  <section class="three columns omega">
      <a href="<?php echo $this->url('conditional-elements/index'); ?>" ><?php echo __('Back'); ?></a>
  </section>
<?php echo foot(); ?>
