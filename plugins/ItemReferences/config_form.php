<div class="field">
  <div class="two columns alpha">
    <?php echo get_view()->formLabel('item_references_local_enable', __('Enable References in Item Editor')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
      echo __('Check this if you want to have the item references functionality on the "Item Type Metadata" tab inside the admin item editor. ');
      ?>
    </p>
    <?php echo get_view()->formCheckbox('item_references_local_enable', null, array('checked' => $localItemReferences)); ?>
  </div>
  <div class="two columns alpha">
    <?php echo get_view()->formLabel('item_references_select', __('Select elements to transform into references')); ?>
  </div>
  <div class="inputs five columns omega">
      <?php
      $sqlDb = get_db();
      $select = "
      SELECT es.name AS element_set_name, e.id AS element_id,
      e.name AS element_name, it.name AS item_type_name
      FROM {$sqlDb->ElementSet} es
      JOIN {$sqlDb->Element} e ON es.id = e.element_set_id
      LEFT JOIN {$sqlDb->ItemTypesElements} ite ON e.id = ite.element_id
      LEFT JOIN {$sqlDb->ItemType} it ON ite.item_type_id = it.id
      ORDER BY e.name";
      $records = $sqlDb->fetchAll($select);
      $elements = array();
      foreach ($records as $record) {
          $optGroup = $record['item_type_name']
                    ? __('Item Type') . ': ' . __($record['item_type_name'])
                    : __($record['element_set_name']);
          $value = __($record['element_name']);
          $elements[$optGroup][$record['element_id']] = $value;
      }

      echo get_view()->formSelect('item_references_select', $itemReferencesSelect, array('multiple' => true, 'size' => 10, 'style' => 'width: 600px;'), $elements);

    ?>
  </div>
  </div>
