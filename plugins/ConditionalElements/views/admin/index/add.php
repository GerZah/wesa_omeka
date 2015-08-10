<?php
$pageTitle = __('Add dependent');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<form method="post" action="<?php echo url('conditional-elements/index/save'); ?>">
  <section class="seven columns alpha">
      <div class="field">
          <div class="one column alpha">
            <?php echo $this->formLabel('dependent', __('Dependent')); ?>
                </div>
          <div class="inputs six columns omega">
            <input type="text" name="dependent" id="dependent" class="textinput" />
          </div>
      </div>
      <div class="field">
        <div class="one column alpha">
          <?php echo $this->formLabel('term', __('Term')); ?>
        </div>
          <div class="inputs six columns omega">
            <input type="text" name="term" id="term" class="textinput" />
          </div>
      </div>
      <div class="field">
        <div class="one column alpha">
          <?php echo $this->formLabel('dependee', __('Dependee')); ?>
        </div>
          <div class="inputs six columns omega">
            <input type="text" name="dependee" id="dependee" class="textinput" />
          </div>
      </div>
  </section>
  <section class="three columns omega">
      <div id="save" class="panel">
        <input type="submit" class="big green button" name="submit" value="<?php echo __('Add dependencies'); ?>">
      </div>
      </section>
      </form>
<?php echo foot(); ?>
