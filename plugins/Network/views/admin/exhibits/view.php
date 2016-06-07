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
    'title' => __('Network') . " | " . __('View Items in "%s"', in_getExhibitField('title')),
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
        $select = "
        SELECT
          nr.item_id as recordItemId,
          nr.item_title as itemName,
          ty.name as typeName,
          nr.id as recordId
        FROM {$db->NetworkRecord} nr
        LEFT JOIN {$db->Item_Types} ty ON nr.item_type_id = ty.id
        WHERE 1
        ";

        $elements = $db->fetchAll($select);
        $data = array();
        foreach($elements as $element) {
          $data['recordItemId'] =  $element['recordItemId'];
          $data['itemName'] =  $element['itemName'];
          $data['typeName'] =  $element['typeName'];
          $record_id =  $element['recordId'];
          if($elements) {
            ?>
            <tr>
              <td><?php echo $data['recordItemId'] ?></td>
              <td><?php echo $data['itemName'] ?></td>
              <td><?php echo $data['typeName'] ?></td>
              <td>
                <a class="confirm"
                  href="<?php echo $this->url('network/remove/', array('record_id' => $record_id)); ?>">
                  <?php echo __('Remove'); ?>
                </a>
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

</div>

<?php echo foot(); ?>
