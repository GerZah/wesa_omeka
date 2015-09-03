<?php
$pageTitle = __('Add Dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<form method="post" action="<?php echo url('conditional-elements/index/save'); ?>">
  <section class="seven columns alpha">
    <?php
    # check option, 0, name
    if($_POST['dependee'] != 0 and $_POST['dependent'] != 0 )
    {
      $dependee_id = '';
      $dependent_id = '';
      $dependee_id = intval($_POST['dependee']);
      $dependent_id = intval($_POST['dependent']);
      $db = get_db();
      $selectDependent = "SELECT name FROM $db->Element WHERE id = '$dependent_id'";
      $selectDependee = "SELECT name FROM $db->Element WHERE id = '$dependee_id'";
      $dependentName = $db->fetchOne($selectDependent);
      $dependeeName = $db->fetchOne($selectDependee);
        ?>
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2>Step 3: Select Dependee Value to Affect Dependent</h2>
      <div class="field">
        <p><?php echo __("Choose one of the dependee element's possible values. If this value is selected, ".
                         "the dependent element will become visible; otherwise, it will be hidden."); ?></p>
      </div>
			<table>
				<tbody>
					<tr><th><?php echo __("Dependee"); ?>:</th><td><?php echo $dependentName; ?></td></tr>
					<tr><th><?php echo __("Term"); ?>:</th>
					<td>
          <?php
          $db = get_db();
          $select = "SELECT e.terms AS terms
          FROM  {$db->Element} es
          JOIN {$db->SimpleVocabTerm} e
          ON es.id = e.element_id
          WHERE es.id = '$dependee_id'
          ORDER BY terms";
          $results = $db->fetchAll($select);
          foreach($results as $result) {
            $terms[$result['terms']] = $result['terms'];
          }
           $term = explode("\n", $terms[$result['terms']]);

           $fullterm = array();
           foreach($term as $value)
           {
              $fullterm[] = $value;
          }
          $fullterm = array(-1 => __('Select Below')) + $fullterm;
          echo $this->formSelect('term', $fullterm , array(), $fullterm);
          ?>
					</td></tr>
					<tr><th><?php echo __("Dependent"); ?>:</th><td><?php echo $dependeeName; ?></td></tr>
				</tbody>
			</table>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
      <input type="submit" class="big green button" name="submit" value="<?php echo __('Save'); ?>">
    </div>
  </section>
  <?php
}
else {
  echo "<h3>".__('Please choose a dependee to proceed.')."</h3>\n"; ?>
  <a href="<?php echo $this->url('conditional-elements/index'); ?>" ><?php echo __('Back'); ?></a>
  <?  }  ?>
	<input type="hidden" name="dependee" value="<?php echo $_POST['dependee']; ?>">
	<input type="hidden" name="dependent" value="<?php echo $_POST['dependent']; ?>">
</form>
<?php echo foot(); ?>
