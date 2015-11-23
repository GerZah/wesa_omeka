<?php
  if (!defined("LITYLOADED")) {
    queue_css_file("lity.min");
    queue_js_file('lity.min');
    DEFINE("LITYLOADED", 1);
  }

  queue_js_file('rangesearch');
  queue_css_file('rangesearch');

  $rangeEntry   = __("Range Entry");
  $selectFirst = __("Please select a target text area first.");

  queue_js_string("
    var rangeSearchSelectFirst='$selectFirst';
  ");
?>

<div id="range-search-popup" style="overflow: auto; padding: 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h2>Foo</h2>
  <div>
  <?php
    $units = array( -1 => __("Select below")) + SELF::_fetchUnitArray();

    $view = get_view();
    echo $view->formSelect('units', -1, array(), $units);
  ?>
  </div>
  <p>
    <a href="#" id="range-search-close" class="green button" data-lity-close>Bar</a>
  </p>
</div>

<div id="range-search-controls" style="display:none;">
  <div class='rangeSearchButtons field'>
    <label><?php echo $rangeEntry; ?>:</label>
    <button class='rangeSearchBtn'><?php echo __("Entry"); ?></button>
  </div>
</div>
