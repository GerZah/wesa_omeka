<div class="field">
    <div class="two columns alpha">
        <?php echo get_view()->formLabel('date_search_use_gregjul_prefixes', __('Use Gregorian / Julian Prefixes')); ?>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
            <?php
            echo __('Check this if you want to use [G] / [J] prefixes to indicate that a date '
										.'is meant to be specifying a Gregorian or a Julian date or timespan.');
            ?>
        </p>
        <?php echo get_view()->formCheckbox('date_search_use_gregjul_prefixes', null, array('checked' => $useGregJulPrexifes)); ?>
    </div>
</div>
