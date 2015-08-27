<?php
$pageTitle = __('Add dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<?php
if(!isset($_SESSION))
{
  session_start();
}
$_SESSION['conditional_elements_dependent'] = $_POST['dependent'];
?>
<form method="post" action="<?php echo url('conditional-elements/index/term'); ?>">
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2>Step 2: Select dependee to add </h2>
      <div class="field">
        <?php echo $this->formLabel('dependee', __('Choose an existing dependee')); ?>
        <div class="inputs six columns omega">
          <?php
          $json=get_option('conditional_elements_dependencies');
          if (!$json) { $json="null"; }
          $dependencies = json_decode($json,true);
          $ids = array();
          foreach ($dependencies as $d){
            $ids[]=$d[0];
          }
          $ids=array_unique($ids);
          $ids_verb = implode(",",$ids);
          $db = get_db();
          $select = "SELECT es.name AS name, es.id AS id, e.element_id AS vocab_id
          FROM {$db->Element} es
          JOIN {$db->SimpleVocabTerm} e
          ON es.id = e.element_id
          WHERE es.id NOT in ($ids_verb) ORDER BY name";
          $results = $db->fetchAll($select);
          $dependent = array();
          foreach($results as $result) {
            $dependee[$result['name']] = $result['name'];
          }
          echo $this->formSelect('dependee', null , array(), $dependee);
          ?>
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
