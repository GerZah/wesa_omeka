<?php
$pageTitle = __('Add Dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>

<form method="post" action="<?php echo url('conditional-elements/index/save'); ?>">
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2>Step 3: Select Dependee Value to Affect Dependent</h2>
      <div class="field">
        <p><?php echo __("Choose one of the dependee element's possible values. If this value is selected, ".
                         "the dependent element will become visible; otherwise, it will be hidden."); ?></p>
      </div>
			<table>
				<tbody>
					<tr><th><?php echo __("Dependee"); ?>:</th><td><?php echo $_POST['dependee']; ?></td></tr>
					<tr><th><?php echo __("Term"); ?>:</th>
					<td>
          <?php
          $dependeeName = $_POST['dependee'];
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
          $fullterm = array('' => __('Select Below')) + $fullterm;
          echo $this->formSelect('term', null, array(), $fullterm);
          ?>
					</td></tr>
					<tr><th><?php echo __("Dependent"); ?>:</th><td><?php echo $_POST['dependent']; ?></td></tr>
				</tbody>
			</table>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
      <input type="submit" class="big green button" name="submit" value="<?php echo __('Save'); ?>">
    </div>
  </section>
	<input type="hidden" name="dependee" value="<?php echo $_POST['dependee']; ?>">
	<input type="hidden" name="dependent" value="<?php echo $_POST['dependent']; ?>">
</form>
<?php echo foot(); ?>
