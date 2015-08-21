<?php
$pageTitle = __('Add dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<form method="post" action="<?php echo url('conditional-elements/index/save'); ?>">
    <section class="seven columns alpha">
      <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
         <h2>Step 3: Select term </h2>
         <div class="field">
           <p>You have chosen the dependent and dependee:</p>
           <?php
           $dependeeName = $_POST['dependee'];
           $dependentName =$_POST['dependent'];
           echo $this->formLabel('dependentName', $dependentName);
           echo $this->formLabel('dependeeName', $dependeeName); ?>
          </div>
          <div class="field">
            <?php echo $this->formLabel('term', __('Choose term')); ?>
            <div class="inputs six columns omega">
            <?php
            $db = get_db();
            $select = "SELECT e.terms AS terms
            FROM  {$db->Element} es
            JOIN {$db->SimpleVocabTerm} e
            ON es.id = e.element_id
            WHERE es.name = '$dependeeName'
            ORDER BY terms";
            $results = $db->fetchAll($select);
            foreach($results as $result) {
             $terms = $result['terms'];
            }
            $term = explode("\n", $terms);
            echo $this->formSelect('term', null, array(), $term);
             ?>
          </div>
        </div>
      </fieldset>
  </section>
  <section class="three columns omega">
      <div id="save" class="panel">
        <a href="<?php echo html_escape(url('conditional-elements/index/dependee')); ?>" class="add big green button"><?php echo __('Previous'); ?></a>
        <input type="submit" class="big green button" name="submit" value="<?php echo __('Save'); ?>">
      </div>
      </section>
      </form>
<?php echo foot(); ?>
