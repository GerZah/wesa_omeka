<?php
$pageTitle = __('Add Dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>

<form method="post" action="<?php echo url('conditional-elements/index/term'); ?>">
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2>Step 2: Select Dependee for Dependency</h2>
      <div class="field">
        <p><?php echo __("Choose a dependee element from the list below that will, based on the selection, ".
                         "will affect the dependent element to become visible or hidden."); ?></p>
        <p><?php echo __("<em>Please note:</em> You will need to supply a list of possible selections ".
                         "via \"Simple Vocabulary\" to an element to become a possible dependee."); ?></p>
        <p><?php echo __("<em>Please note:</em> One dependee can affect multiple dependents, ".
                         "based on multiple values to choose from."); ?></p>
      </div>
			<table>
				<tbody>
          <?php
          $json=get_option('conditional_elements_dependencies');
          if (!$json) { $json="null"; }
          $dependencies = json_decode($json,true);
          $db = get_db();
          if ($dependencies) {
          $ids = array();
          foreach ($dependencies as $d){
            $ids[]=$d[0];
          }
          $ids=array_unique($ids);
          $ids_verb = implode(",",$ids);
          $select = "SELECT es.name AS name, es.id AS id, e.element_id AS vocab_id
          FROM {$db->Element} es
          JOIN {$db->SimpleVocabTerm} e
          ON es.id = e.element_id
          WHERE es.id NOT in ($ids_verb) ORDER BY name";
        }
        else {
          $dependencies ="null";
          $ids_verb = '';
          $select = "SELECT es.name AS name, es.id AS id, e.element_id AS vocab_id
          FROM {$db->Element} es
          JOIN {$db->SimpleVocabTerm} e
          ON es.id = e.element_id ORDER BY name";
        }
          $results = $db->fetchAll($select);
          $dependent = array();
          foreach($results as $result) {
            $dependee[$result['name']] = $result['name'];
          }
          echo "<tr><th>".__("Dependee").":</th>\n<td>\n";
          $dependee = array('' => __('Select Below')) + $dependee;
					echo $this->formSelect('dependee', null , array(), $dependee);
					echo "</td></tr>\n";
          ?>
					<tr><th><?php echo __("Dependent");?>:</th><td><?php echo $_POST['dependent']; ?></td></tr>
				</tbody>
			</table>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
      <a href="<?php echo html_escape(url('conditional-elements/index/add')); ?>" class="add big green button"><?php echo __('Previous'); ?></a>
      <input type="submit" class="big green button" name="submit" value="<?php echo __('Next'); ?>">
    </div>
  </section>
	<input type="hidden" name="dependent" value="<?php echo $_POST['dependent']; ?>">
</form>
<?php echo foot(); ?>
