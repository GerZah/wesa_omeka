<div class="field">
    <div class="two columns alpha">
        <?php echo $this->formLabel('date_search_search', __('Date Search')); ?>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
        <?php
        echo __('Date Search Explanation');
        ?>
        </p>
        <p>
            <?php echo $this->formText('date_search_term', @$_GET['date_search_term'], array('size' => 10)); ?>
        </p>
    </div>
</div>
