<div class="field">
  <div class="two columns alpha">
    <?php echo get_view()->formLabel('object_references_local_enable', __('Enable References in Item Editor')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
      echo __('Check this if you want to have the object references functionality on the "Item Type Metadata" tab inside the admin item editor. ');
      ?>
    </p>
    <?php echo get_view()->formCheckbox('object_references_local_enable', null, array('checked' => $localObjectReferences)); ?>
  </div>
  </div>
