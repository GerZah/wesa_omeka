<?php
  $view = get_view();

  if (!defined("LITYLOADED")) {
    queue_css_file("lity.min");
    queue_js_file('lity.min');
    DEFINE("LITYLOADED", 1);
  }

  $i18nStrings = array(
    "selectTriple" => "Please start by selecting a triple unit.",
    "enterNumerical" => "Please enter numerical values into all fields (or leave them empty).",
  );

  queue_css_file('measurements');
  queue_js_file('measurements');
  queue_js_string(
    "var measurementsUnits=".json_encode(SELF::$_saniUnits,true).";".
    "var measurementsI18n=".json_encode($i18nStrings).";"
  );
?>
<div id="measurementsPopup" style="overflow: auto; padding: 0 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h3><?php echo __("Measurements"); ?></h3>

  <p>
    <?php
      echo "<strong>" . __("Triple Unit") . ":</strong> ";
      echo $view->formSelect('measurementUnits', -1, array(), $tripleSelect)
    ?>
  </p>

  <p>
    <?php
      echo "<strong>" . __("Length 1/2/3") . ":</strong> ";
      for($i=1; $i<=3; $i++) {
        echo $view->formInput("measurementLength$i",
                                  null,
                                  array("type" => "text",
                                        "readonly" => "true",
                                        "class" => "measurementsTextField",
                                        "size" => 4,
                                        "maxlength" => 10,
                                        "data-title" => __("Length")." $i",
                                      )
                                  );
        echo " <span class='measurementsLenghtUnit3'></span> "; // all unit 3 (smallest unit)
      }
    ?>
  </p>

  <div class="centerButtons">
    <a href="#" id="measurementsCancel" class="green button" data-lity-close><?php echo __('Cancel'); ?></a>
    <a href="#" id="measurementsClear" class="green button"><?php echo __('Clear'); ?></a>
    <a href="#" id="measurementsApply" class="green button"><?php echo __('Apply'); ?></a>
  </div>

</div>

<div id="measurementsPopup2" style="overflow: auto; padding: 0 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h3><?php echo __("Triple Unit Data"); ?></h3>
  <p>
    <span id="measurementsTripleEditTitle"></span>:
    <?php
      for($i=1; $i<=3; $i++) {
        echo $view->formInput("measurementValue$i",
                                  null,
                                  array("type" => "text",
                                        "size" => 4,
                                        "maxlength" => 10,
                                      )
                                  );
        echo " <span class='measurementsLenghtUnit".$i."'></span> ";
      }
    ?>
  </p>
  <div class="centerButtons">
    <a href="#" id="measurementsValuesCancel" class="green button" data-lity-close><?php echo __('Cancel'); ?></a>
    <a href="#" id="measurementsValuesClear" class="green button"><?php echo __('Clear'); ?></a>
    <a href="#" id="measurementsValuesApply" class="green button"><?php echo __('Apply'); ?></a>
  </div>
</div>
