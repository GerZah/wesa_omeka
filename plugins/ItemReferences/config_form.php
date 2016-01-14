<?php $view = get_view(); ?>
<div class="field">
  <div class="two columns alpha">
    <?php echo $view->formLabel('item_references_select', __('Reference Elements')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation"><?php echo __('Select elements to transform into item references, i.e. that should represent references to other items.'); ?></p>
    <?php
      echo $view->formSelect('item_references_select',
        $itemReferencesSelect,
        array('multiple' => true, 'size' => 10),
        $elements
      );
    ?>
  </div>
  <div class="field">
      <div class="two columns alpha">
          <label for="item_references_show_maps"><?php echo __('Display Reference Map'); ?></label>
      </div>
      <div class="inputs five columns omega">
          <p class="explanation"><?php echo __('Check this if you want geolocations from referenced items to be displayed together in a combined map on the items/show page.'); ?></p>
          <?php
            echo $view->formCheckbox('item_references_show_maps',
              true,
              array('checked' => $itemReferencesShowMaps)
            );
          ?>
      </div>
  </div>
  <div class="field">
      <div class="two columns alpha">
          <label for="item_references_show_lines"><?php echo __('Connect References with a Line'); ?></label>
      </div>
      <div class="inputs five columns omega">
          <p class="explanation"><?php echo __('In case you selected to display the reference map, you may check this if you want the geolocations to be connected by a line.'); ?></p>
          <?php
            echo $view->formCheckbox('item_references_show_lines',
              true,
              array('checked' => $itemReferencesShowLines)
            );
          ?>
      </div>
  </div>
  <div class="field">
      <div class="two columns alpha">
          <label for="item_references_map_height"><?php echo __('Height for Reference Map'); ?></label>
      </div>
      <div class="inputs five columns omega">
          <p class="explanation"><?php echo __('The height of the map displayed on your items/show page. If left blank, the default height of 300px will be used.'); ?></p>
          <?php echo $view->formText('item_references_map_height', $itemReferencesMapHeight); ?>
      </div>
  </div>
</div>
