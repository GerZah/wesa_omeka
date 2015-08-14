<?php
$pageTitle = __('Add dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<form method="post" action="<?php echo url('conditional-elements/index/dependee'); ?>">
    <section class="seven columns alpha">
      <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
         <h2>Step 1: Select dependent to add </h2>
         <div class="field">
           <p>Choose dependents from the existing list:</p>
         </div>
      <div class="field">
          <div class="one column alpha">
            <?php echo $this->formLabel('dependent', __('Dependent')); ?>
          </div>
          <div class="inputs six columns omega">
            <?php
            $json=get_option('conditional_elements_dependencies');
            if (!$json) { $json="null"; }
            $dependencies = json_decode($json);
            echo $this->formSelect('existingdependent', null, array(), $dependencies);
            ?>
          </div>
      </div>
      <div class="field">
        <div class="one column alpha">
          <?php echo $this->formLabel('newdependent', __('Add new Dependent')); ?>
        </div>
          <div class="inputs six columns omega">
            <?php // do a matching to check for existing dependents ?>
            <input type="text" name="newdependent" id="newdependent" class="textinput" />
          </div>
      </div>
          </fieldset>
  </section>
  <section class="three columns omega">
      <div id="save" class="panel">
        <input type="submit" class="big green button" name="submit" value="<?php echo __('Next'); ?>">
      </div>
      </section>
      </form>
<?php echo foot(); ?>
