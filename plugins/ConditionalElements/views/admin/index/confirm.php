<?php
$pageTitle = __('Confirm delete');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<form method="post" name="confirm" >
        <div class="field">
          <?php
        echo $this->formLabel('confirm', __('Are you sure you wish to delete this dependency?'));
        $dependee_id = $_GET['dependee_id'];
        $term = $_GET['term'];
        $dependent_id = $_GET['dependent_id'];
        $ids = array();
        $ids[0]=$dependee_id;
        $ids[1]=$dependent_id;
        $ids=array_unique($ids);
        $ids_verb = implode(",",$ids);
        $db = get_db();
        $select = "SELECT id, name FROM $db->Element WHERE id in ($ids_verb)";
        $results = $db->fetchAll($select);
        $data = array();
        foreach($results as $result) {
         $data[$result['id']] = $result['name'];
        }
        echo $this->formLabel('dependeeName', $data[$dependee_id]);
        echo $this->formLabel('termName', $term);
        echo $this->formLabel('dependentName', $data[$result['id']]); ?>
          <a href="<?php echo html_escape(url('conditional-elements/index/delete')); ?>" class="button remove flr mrr4" data-id="<?php echo $dependent_id; ?>">Yes</a>
          <a href="<?php echo html_escape(url('conditional-elements/index')); ?>" class="button buttonGreen cancel flr">No</a>
    </div>
  </form>
<?php echo foot(); ?>
