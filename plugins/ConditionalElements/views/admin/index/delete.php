<?php
$pageTitle = __('Delete dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<form method="post" action="<?php echo url('conditional-elements/index'); ?>">
  <section class="seven columns alpha">
      <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
           <div class="field">
             <?php
             echo $this->formLabel('id', $_POST['dependent_id']); ?>
           <h2>You have successfully deleted the dependency</h2>
         </div>
      </fieldset>
  </section>
  <section class="three columns omega">
      <div id="save" class="panel">
        <input type="submit" class="big green button" name="submit" value="<?php echo __('Back'); ?>">
      </div>
      </section>
</form>
<?php echo foot(); ?>
