<div class="field">
  <div class="two columns alpha">
    <?php echo get_view()->formLabel('reassign_files_delete_orphaned_items', __('Delete Orphaned Items')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
      echo __('Check this if you want to automatically delete items that become "orphaned" after reassigning their files.<br>'.
              'This affects only items that afterwards<br>'.
              '<ul>'.
              '<li>contain no metadata (i.e. text) whatsoever</li>'.
              '<li>do not have any associates files left</li>'.
              '<li>are neither subject nor object in a relationship (if "Item Relations" is installed)</li>'.
              '</ul>');
      ?>
    </p>
    <?php echo get_view()->formCheckbox('reassign_files_delete_orphaned_items', null, array('checked' => $deleteOrphanedItems)); ?>
    <hr>
    <p class="explanation">
      <?php
      echo __('Check this to enforce checking for and deletion of orphaned items <em>once now</em>.');
      ?>
    </p>
    <?php echo get_view()->formCheckbox('reassign_files_delete_orphaned_items_now', null, array('checked' => false)); ?>
  </div>
</div>
