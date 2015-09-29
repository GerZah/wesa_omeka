<div class="field">
    <div class="two columns alpha">
        <?php echo get_view()->formLabel('reassign_files_orphaned_items_prefixes', __('Delete orphaned items')); ?>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
            <?php
            echo __('Check this if you want to delete orphaned items.');
            ?>
        </p>
        <?php echo get_view()->formCheckbox('reassign_files_orphaned_items_prefixes', null, array('checked' => $useReassignFilesPrexifes)); ?>
    </div>
</div>
