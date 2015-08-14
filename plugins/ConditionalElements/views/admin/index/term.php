<?php
$pageTitle = __('Add dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<form method="post" action="">
    <section class="seven columns alpha">
      <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
         <h2>Step 3: Select term </h2>
         <div class="field">
           <p>You have chosen the dependent and dependee:</p>
           <?php
             echo $_POST['existingdependent'];
             echo $_POST['existingdependee'];
             ?>
           </div>
      <div class="field">
            <?php echo $this->formLabel('term', __('Choose term')); ?>
          <div class="inputs six columns omega">
           <?php  $json=get_option('conditional_elements_dependencies');
            if (!$json) { $json="null"; }
            $dependencies = json_decode($json);
            echo $this->formSelect('term', null, array(), $dependencies);
             ?>
          </div>
      </div>
      </fieldset>
  </section>
  <section class="three columns omega">
      <div id="save" class="panel">
        <a href="<?php echo html_escape(url('conditional-elements/index/add')); ?>" class="add big green button"><?php echo __('Previous'); ?></a>
        <input type="submit" class="big green button" name="submit" value="<?php echo __('Save'); ?>">
      </div>
      </section>
      </form>
<?php echo foot(); ?>
