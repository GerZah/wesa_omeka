<div class="field">
    <div class="two columns alpha">
        <?php echo $this->formLabel('time_search_search', __('Time Search')); ?>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
        <?php
        echo __('Time Search Explanation');
        ?>
        </p>
        <p>
            <?php echo $this->formText('time_search_term', @$_GET['time_search_term'], array('size' => 10)); ?>
        </p>
    </div>
</div>
