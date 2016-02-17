<?php
  $view = get_view();

  if (!defined("LITYLOADED")) {
    queue_css_file("lity.min");
    queue_js_file('lity.min');
    DEFINE("LITYLOADED", 1);
  }

  queue_css_file('measurements');
  queue_js_file('measurements');
?>
<div id="measurementsPopup" style="overflow: auto; padding: 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h2>Primary</h2>

  <p>
  <?php
  echo $view->formInput("measurementLength1",
                            null,
                            array("type" => "text",
                                  "readonly" => "true",
                                  "class" => "measurementsTextField",
                                  "size" => 4,
                                  "maxlength" => 10,
                                )
                            );
  ?>
  </p>

  <div>
    <a href="#" id="measurementsCancel" class="green button" data-lity-close><?php echo __('Cancel'); ?></a>
    <a href="#" id="measurementsClear" class="green button"><?php echo __('Clear'); ?></a>
    <a href="#" id="measurementsApply" class="green button"><?php echo __('Apply'); ?></a>
  </div>

</div>

<div id="measurementsPopup2" style="overflow: auto; padding: 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h2>Secondary</h2>
</div>
