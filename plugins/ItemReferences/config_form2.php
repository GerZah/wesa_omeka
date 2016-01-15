<?php
  $view = get_view();
  echo $view->formHidden('configPage', 2, null, null, null);
?>
<h2><?php echo __('Reference Element Configuration'); ?></h2>
<div class="field">
  <div class="two columns alpha">
    <label for="item_references_map_height"><?php echo __('Height for Reference Map'); ?></label>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation"><?php echo __('The height of the map displayed on your items/show page. If left blank, the default height of 300px will be used.'); ?></p>
    <?php echo $view->formText('item_references_map_height', $itemReferencesMapHeight); ?>
  </div>
  <div class="two columns alpha">
    <label for="item_references_representations"><?php echo __('References Representation'); ?></label>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation"><?php echo __('Please select for each reference element how it should be represented. It will always be displayed in the form of a list of clickable item titles. But in addition to that, you may decide whether or not you would like to display the referenced items\' geolocations together in a map, and if the markers should be connected with a multi-segment line or not.'); ?></p>
  </div>
  <?php
    foreach($itemReferencesArr as $itemReference) {
      $elementId = $itemReference["id"];
      $elementName = $itemReference["name"];
      echo '<div class="two columns alpha">';
      echo "$elementName";
      echo "</div>";
      echo '<div class="inputs five columns omega">';
      echo $view->formRadio(
        "item_references_$elementId",
        $itemReferencesConfiguration[$elementId],
        array(),
        array(
          0 => __("Reference List only"),
          1 => __("Reference Map"),
          2 => __("Reference Map with Line")
        )
      );
      echo "</div>";
    }
  ?>
  <!-- <div class="field">
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
  </div> -->
</div>
<?php
  // echo "<pre style='clear:both;'>" . print_r($itemReferencesArr,true) . "</pre>";
?>
