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
$_SESSION['conditional_elements_dependee'] = $_POST['dependee'];
?>
<form method="post" action="<?php echo url('conditional-elements/index/save'); ?>">
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2>Step 3: Select term </h2>
      <div class="field">
        <p>You have chosen the dependent:</p>
        <?php
        echo $_SESSION['conditional_elements_dependent'];?>
        <p>You have chosen the dependee:</p>
        <?php
        echo $_SESSION['conditional_elements_dependee'];
        ?>
      </div>
      <div class="field">
        <?php echo $this->formLabel('term', __('Choose term')); ?>
        <div class="inputs six columns omega">
          <?php
          $dependeeName = $_SESSION['dependee'];
          $db = get_db();
          $select = "SELECT e.terms AS terms
          FROM  {$db->Element} es
          JOIN {$db->SimpleVocabTerm} e
          ON es.id = e.element_id
          WHERE es.name = '$dependeeName'
          ORDER BY terms";
          $results = $db->fetchAll($select);
          foreach($results as $result) {
            $terms= $result['terms'];
          }
          $term = explode("\n", $terms);
          $fullterm = array();
          foreach($term as $value)
          {
             $fullterm[$value] = $value;
           }
          echo $this->formSelect('term', null, array(), $fullterm);
          ?>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
      <input type="submit" class="big green button" name="submit" value="<?php echo __('Save'); ?>">
    </div>
  </section>
</form>
<?php echo foot(); ?>
