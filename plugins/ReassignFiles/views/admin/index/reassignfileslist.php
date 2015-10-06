<div><?php echo __('Select the files to reassign to the item:'); ?></div>
<div class="drawer-contents">
  <?php
  $itemId = metadata('item', 'id');
  echo get_view()->formSelect('reassignFilesFiles[]', null , array('multiple' => true, 'size' => 10, 'style' => 'width: 500px;'), $fileNames);
  ?>
  <input type="hidden" name="itemId" value="<?php echo $itemId; ?>">
</div>
