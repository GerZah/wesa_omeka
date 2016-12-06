<?php
  $view = get_view();
  echo '<link href="' . css_src('find-orphans-config') . '" rel="stylesheet">';
  echo js_tag('find-orphans-config');
?>
<script type="text/javascript">
//<!--
  var findOrphansTargetUrl="<?php echo $targetUrl; ?>";
//-->
</script>
<script type="text/javascript" src="<?php echo url("plugins/test.js"); ?>"></script>
<div class="field">

  <div class="two columns alpha">
    <?php echo $view->formLabel('item_type_select', __('Item Type')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation"><?php echo __('Select item type to search for orphaned items of this type.'); ?></p>
    <?php
      echo $view->formSelect('item_type_select',
        $itemTypeId,
        array(),
        $itemTypesSelect
      );
    ?>
  </div>
  <div>
    <?php
      if ($itemTypeId == -1) {
        echo "<h4>" . __("Please select an item type to search for orphaned items.") . "</h4>";
      }
      else if (!$orphans) {
        echo "<h4>" . sprintf( __("No orphaned of item type “%s” found."), $itemTypeName) . "</h4>";
      }
      else {
        $orphanCount = count($orphans);
        echo
          "<h4>"
          . sprintf( __("Orphaned items of item type “%s”"), $itemTypeName)
          . " ($orphanCount)"
          . "</h4>"
        ;
        echo "<ul style='clear:both;'>\n";
        foreach($orphans as $orphan) {
          echo "<li><a href='" . $orphan["url"] . "'>" . $orphan["title"] . "</a></li>\n";
        }
        echo "</ul>\n";
      }
    ?>
  </div>

</div>
