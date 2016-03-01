<?php $view = get_view(); ?>
<div class="field">
    <div class="two columns alpha">
        <?php echo get_view()->formLabel('measurements_units', __('Measurements Units')); ?>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
            <?php echo __('Please enter all triple units that you would like to support, one per line.'); ?>
            <br>
            <?php echo __('Please use the form <em>"abc-def-ghi (1-100-10)"</em> with the numbers being the conversion rates.'); ?><br>
            <?php echo __('You may also group multiple triple units by adding a group name like this: <em>"[groupname] abc-def-ghi (1-100-10)"</em>.'); ?>
        </p>
        <?php
				echo $view->formTextarea('measurements_units', $measurementUnits, array( "rows" => 8 ) );
				?>
        <strong><?php echo __("Valid units that have been entered correctly:"); ?></strong>
        <ul>
          <?php
            foreach($saniUnits as $groupName => $saniUnitsGroup) {
              echo "<li>$groupName<ul>";
              foreach($saniUnitsGroup as $saniUnit) {
                echo "<li>" . $saniUnit["verb"] . "</li>\n";
              }
              echo "</ul></li>\n";
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

<div class="field">
  <div class="two columns alpha">
      <?php echo get_view()->formLabel('measurements_trigger_reindex', __('Trigger Re-indexing of Existing Content')); ?>
  </div>
  <div class="inputs five columns omega">
      <p class="explanation">
          <?php
            echo __('<strong>Please note:</strong> Checking this box will re-generate the index <em>now</em> and '.
                    'exactly <em>once</em>. This action will be carried out as soon as you click on "Save Changes".');
          ?>
      </p>
      <?php echo get_view()->formCheckbox('measurements_trigger_reindex', null, array('checked' => false)); ?>
      <p class="explanation">
          <?php
            echo __('<em>Explanation:</em> Measurements relies on a search index that is being created during content'.
                    ' maintenance in the background. However, existing content will not be re-indexed automatically. '.
                    'So if you have existing content or modify your settings, you should re-generate the search index.');
          ?>
      </p>
  </div>
</div>

<script type="text/javascript">
// <!--
  jQuery(document).ready(function() {
    var $ = jQuery; // use noConflict version of jQuery as the short $ within this block
    $("#measurements_units").change( function() { activateReindexCheckbox(); } );
    $("#measurements_elements").change( function() { activateReindexCheckbox(); } );
    function activateReindexCheckbox() { $("#measurements_trigger_reindex").prop('checked', true); }
  } );
// -->
</script>

<?php if (isset($debugOutput)) { ?>
  <div class="field">
    <div class="two columns alpha">
        <?php echo get_view()->formLabel('measurements_debug_output', __('Debug Output')); ?>
    </div>
    <div class="inputs five columns omega">
        <?php echo get_view()->formCheckbox('measurements_debug_output', null, array('checked' => $debugOutput)); ?>
    </div>
  </div>
<?php } ?>
