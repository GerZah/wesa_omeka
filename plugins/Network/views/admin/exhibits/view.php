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
        <th class="clickable"><?php echo __('Item Id'); ?></th>
        <th class="clickable"><?php echo __('Item Title'); ?></th>
        <th><?php echo __('Item Type'); ?></th>
        <th><?php echo __('Actions'); ?></th>
      </tr>
      <tbody>
        <?php
        $db = get_db();
        $exhibitId = in_getExhibitField('id');
        $select = "
          SELECT
            nr.item_id as recordItemId,
            nr.item_title as itemName,
            it.name as typeName,
            nr.id as recordId
          FROM {$db->NetworkRecord} nr
          LEFT JOIN {$db->Item_Types} it ON nr.item_type_id = it.id
          WHERE nr.exhibit_id = $exhibitId
          ORDER BY nr.item_title
        ";

        $elements = $db->fetchAll($select);
        if($elements) {
          foreach($elements as $element) {
            $itemUrl = url('items/show/' . $element['recordItemId']);
            ?>
              <tr>
                <td><a href="<?php echo $itemUrl; ?>"><?php echo $element['recordItemId']; ?></a></td>
                <td><a href="<?php echo $itemUrl; ?>"><?php echo $element['itemName']; ?></a></td>
                <td><?php echo $element['typeName']; ?></td>
                <td>
                  <a class="confirm"
                    href="<?php echo $this->url('network/remove/', array('record_id' => $element['recordId'])); ?>">
                    <?php echo __('Remove'); ?>
                  </a>
                </td>
              </tr>
            <?php
          };
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
      ?>
      </tbody>
  </table>
  <script>
  jQuery(document).ready(function() {
    var $ = jQuery;
    $('th.clickable').click(function(){
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
    function getCellValue(row, index){ return $(row).children('td').eq(index).text() }
  });
  </script>

</div>

<?php echo foot(); ?>
