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
?>
    <tbody>
      <tr>
        <td>
        <?php
          if(isset($data[$dependee_id])){
            echo $data[$dependee_id];
          }
          else {
            echo "";
          }
         ?>
        </td>
        <td>
        <?php echo $term; ?>
        </td>
        <td>
        <?php
          if(isset($data[$dependent_id])){
            echo $data[$dependent_id];
          }
          else {
            echo "";
          }
         ?>
        </td>
        <td>
          <ul class="action-links group">
          <li>
                <a href="#" data-href="<?php echo $this->url('conditional-elements/index/delete', array('dependent_id'=>$dependent_id)); ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
          </li>
          </ul>
        <?php }; ?>
          </td>
      </tr>
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
    <div class="table-actions">
      <a href="<?php echo html_escape(url('conditional-elements/index/add')); ?>" class="add green button"><?php echo __('Add dependency'); ?></a>
  </div>
<?php echo foot(); ?>
