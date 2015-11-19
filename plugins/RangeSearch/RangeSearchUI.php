<?php
  queue_css_file("lity.min");
  queue_js_file('lity.min');

  queue_js_file('rangesearch');

  $rangeEntry   = __("Range Entry");
  $selectFirst = __("Please select a target text area first.");

  queue_js_string("
    var rangeSearchRangeEntry='$rangeEntry';
    var rangeSearchSelectFirst='$selectFirst';
  ");
?>

<div id="range-search-popup" style="overflow: auto; padding: 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h2>Foo</h2>
  <a href="#" id="add-relation" class="green button" data-lity-close>Bar</a>
</div
