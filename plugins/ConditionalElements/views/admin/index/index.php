<?php
$pageTitle = __('Browse Dependents');
echo head(array('title' => $pageTitle,'bodyclass' => 'dependent')); ?>
<div class="table-actions">
  <a href="<?php echo html_escape(url('conditional-elements/index/add')); ?>" class="add green button"><?php echo __('Add dependency'); ?></a>
</div>
<table>
  <thead>
    <tr>
      <th><?php echo __('Dependee'); ?></th>
      <th><?php echo __('Terms'); ?></th>
      <th><?php echo __('Dependent'); ?></th>
      <th><?php echo __('Actions'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $json=get_option('conditional_elements_dependencies');
    if (!$json) { $json="null"; }
    $dependencies = json_decode($json,true);
    $ids = array();
    foreach ($dependencies as $d){
      $ids[]=$d[0];
      $ids[]=$d[2];
    }
    $ids=array_unique($ids);
    $ids_verb = implode(",",$ids);
    $db = get_db();
    $select = "SELECT id, name FROM $db->Element WHERE id in ($ids_verb)";
    $results = $db->fetchAll($select);
    $data = array();

    foreach($results as $result) {
      $data[$result['id']] = $result['name'];
    }
    foreach ($dependencies as $dep){
      $dependee_id = $dep[0];
      $term = $dep[1];
      $dependent_id = $dep[2];

      if ((isset($data[$dependee_id])) and (isset($data[$dependent_id])) ) {
        ?>
        <tr>
          <td><?php echo $data[$dependee_id]; ?>
          </td>
          <td><?php echo $term; ?></td>
          <td><?php echo $data[$dependent_id]; ?>
          </td>
          <td>
            <a class="confirm" href="<?php echo $this->url('conditional-elements/index/confirm', array('dependee_id' => $dependee_id, 'term' => $term, 'dependent_id' => $dependent_id)); ?>" ><?php echo __('Delete'); ?></a>
          </td>
        </tr>
        <?php
      }
    }; ?>
  </tbody>
</table>
<script>
jQuery(document).ready(function()
{
  var $ = jQuery;
  $('th').click(function(){
    var table = $(this).parents('table').eq(0)
    var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
    this.asc = !this.asc
    if (!this.asc){rows = rows.reverse()}
    for (var i = 0; i < rows.length; i++){table.append(rows[i])}
  })
  function comparer(index) {
    return function(a, b) {
      var valA = getCellValue(a, index), valB = getCellValue(b, index)
      return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB)
    }
  }
  function getCellValue(row, index){ return $(row).children('td').eq(index).html() }
});
</script>
<script src="/javascript/jquery-1.7.1.min.js"></script>
<script src="/javascript/configuration.js"></script>
<div class="table-actions">
  <a href="<?php echo html_escape(url('conditional-elements/index/add')); ?>" class="add green button"><?php echo __('Add dependency'); ?></a>
</div>
<?php echo foot(); ?>
