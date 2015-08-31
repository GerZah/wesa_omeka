<?php
$pageTitle = __('Add Dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>

<form method="post" action="<?php echo url('conditional-elements/index/dependee'); ?>">
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2><?php echo __("Step 1: Select Dependent for Dependency"); ?></h2>
      <div class="field">
        <p><?php echo __("Choose a dependent element from the list below that should become ".
                          "visible or hidden based on the selection for some other element."); ?></p>
        <p><?php echo __("<em>Please note:</em> One element can be dependent only from one other element."); ?></p>
      </div>
      <div class="field">
        <div class="inputs six columns omega">
          <?php
          $json=get_option('conditional_elements_dependencies');
          if (!$json) { $json="null"; }
          $dependencies = json_decode($json,true);
          $db = get_db();
          if ($dependencies) {
          $ids = array();
          foreach ($dependencies as $d){
            $ids[]=$d[2];
          }
          $ids=array_unique($ids);
          $ids_verb = implode(",",$ids);
          $select = "SELECT id, name FROM $db->Element WHERE id NOT in ($ids_verb) ORDER BY name";
        }
        else {
          $dependencies ="null";
          $ids_verb = '';
          $select = "SELECT id, name FROM $db->Element ORDER BY name";
        }
          $results = $db->fetchAll($select);
          $dependent = array();
          foreach($results as $result) {
            $dependent[$result['name']] = $result['name'];
          }
          $dependent = array('' => __('Select Below')) + $dependent;
          echo $this->formSelect('dependent', null , array(), $dependent);
          ?>
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
