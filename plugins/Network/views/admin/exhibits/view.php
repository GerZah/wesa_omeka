<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php
  echo head(array(
    'title' => __('Network | View Items in "%s"', in_getExhibitField('title')),
  ));
?>

<div id="primary">
  <?php echo flash(); ?>
  <table>
    <thead>
      <tr>
        <th><?php echo __('Item Id'); ?></th>
        <th><?php echo __('Item Title'); ?></th>
        <th><?php echo __('Item Type'); ?></th>
        <th><?php echo __('Actions'); ?></th>
      </tr>
      <tbody>
        <?php
        $db = get_db();
        $select = "SELECT item_id, item_title FROM $db->NetworkRecord";
        $elements = $db->fetchAll($select);
        $data = array();
        foreach($elements as $element) {
        $data[0] = $element['item_id'];
        $data[1] =  $element['item_title'];
       }
       echo "<pre>" . print_r($data) . "</pre>"; die();
       if($elements){
         ?>
           <tr>
          <td><?php echo $data[0]; ?>
          </td>
          <td><?php echo $data[1]; ?>
          </td>
          <td><?php echo $data[1]; ?>
          </td>
          <td>
            <a class="confirm" href="" ><?php echo __('Delete'); ?></a>
          </td>
        </tr>
        <?php
      }
  else {
    $elements ="null";
    ?>
    <tr>
      <td><?php echo __("[n/a]"); ?></td>
      <td><?php echo __("[n/a]"); ?></td>
      <td><?php echo __("[n/a]"); ?></td>
      <td><?php echo __("[n/a]"); ?></td>
    </tr>
    <?php

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

</div>

<?php echo foot(); ?>
