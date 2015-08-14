<?php
$pageTitle = __('Add dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<form method="post" action="<?php echo url('conditional-elements/index/term'); ?>">
    <section class="seven columns alpha">
      <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
         <h2>Step 2: Select dependee to add </h2>
         <div class="field">
           <p>You have chosen the dependent:</p>
           <?php
           echo $this->formText('existingdependent', $_POST['existingdependent']); ?>
           </div>
      <div class="field">
            <?php echo $this->formLabel('dependee', __('Choose an existing dependee')); ?>
          <div class="inputs six columns omega">
           <?php  $json=get_option('conditional_elements_dependencies');
            if (!$json) { $json="null"; }
            $dependencies = json_decode($json);
            echo $this->formSelect('existingdependee', null, array(), $dependencies);
             ?>
          </div>
      </div>
      <div class="field">
        <div class="one column alpha">
          <?php echo $this->formLabel('newdependee', __('Add new Dependee')); ?>
        </div>
          <div class="inputs six columns omega">
            <?php // do a matching to check for existing dependents ?>
            <input type="text" name="newdependee" id="newdependee" class="textinput" />
          </div>
      </div>
          </fieldset>
  </section>
  <section class="three columns omega">
      <div id="save" class="panel">
          <a href="<?php echo html_escape(url('conditional-elements/index/add')); ?>" class="add big green button"><?php echo __('Previous'); ?></a>
          <input type="submit" class="big green button" name="submit" value="<?php echo __('Next'); ?>">
      </div>
      </section>
      </form>
<?php echo foot(); ?>
