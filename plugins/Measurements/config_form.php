<?php $view = get_view(); ?>
<div class="field">
    <div class="two columns alpha">
        <?php echo get_view()->formLabel('measurements_units', __('Measurements Units')); ?>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
            <?php echo __('Please enter all triple units that you would like to support, one per line.'); ?>
            <br>
            <?php echo __('Please use the form <em>"abc-def-ghi (1-100-10)"</em> with the numbers being the conversion rates.'); ?>
        </p>
        <?php
				echo $view->formTextarea('measurements_units', $measurementUnits, array( "rows" => 8 ) );
				?>
        <strong><?php echo __("Valid units that have been entered correctly:"); ?></strong>
        <ul>
          <?php
            foreach($saniUnits as $saniUnit) {
              echo "<li>" . $saniUnit["verb"] . "</li>\n";
            }
          ?>
        </ul>
    </div>
    <div class="two columns alpha">
      <?php echo $view->formLabel('measurements_elements', __('Measurement Elements')); ?>
    </div>
    <div class="inputs five columns omega">
      <p class="explanation"><?php echo __('Select elements to transform into measurements, i.e. that should store measurements.'); ?></p>
      <?php
        echo $view->formSelect('measurements_elements',
          $measurementElements,
          array('multiple' => true, 'size' => 10),
          $elements
        );
      ?>
    </div>
</div>
