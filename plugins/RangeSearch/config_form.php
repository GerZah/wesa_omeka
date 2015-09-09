<div class="field">
    <div class="two columns alpha">
        <?php echo get_view()->formLabel('date_search_units', __('Units')); ?>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
            <?php
            echo __('Please enter all units that you would like to support, one per line.<br>'.
										'<em>Please note:</em> Units name may not be longer than 20 characters.');
            ?>
        </p>
        <?php
				# ./application/libraries/Zend/View/Helper/FormTextarea.php
				# public function formTextarea($name, $value = null, $attribs = null)
				echo get_view()->formTextarea('range_search_units', $rangeSearchUnits, array( "rows" => 8 ) );
				?>
    </div>
</div>
