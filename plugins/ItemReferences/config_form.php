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
<?php if ($itemReferencesSelect) { ?>
  <div class="two columns alpha">
    <?php echo $view->formLabel('item_references_configure', __('Reference Element Configuration')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
        echo __('Click button below to configure the reference representation (e.g. to display reference geolocations together in a map).');
      ?>
    </p>
    <p>
      <?php
        echo "<a href='$configPage2Url' id='item_references_configure' class='green button'>" . __("Configure") . "</a>";
      ?>
    </p>
  </div>
<?php } ?>
</div>
