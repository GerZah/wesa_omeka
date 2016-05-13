<?php
  if (!defined("LITYLOADED")) {
    queue_css_file("lity.min");
    queue_js_file('lity.min');
    DEFINE("LITYLOADED", 1);
  }

  echo head(array('title' => __('Measurements Analysis'), 'bodyclass' => 'measurementsfoo'));
  echo flash();
  $view = get_view();

  $html = array();

  $html[] = '<link href="' . css_src('measurements-analytics') . '" rel="stylesheet">';
  $html[] = '<script type="text/javascript">';
  $html[] = 'var measurementsJsonUrl = ' . json_encode(url('measurements/lookup/')) . ';';
  $html[] = 'var measurementsTableLen = ' . MEASUREMENT_TABLE_LEN . ';';
  $html[] = 'var measurementsBaseUrl = ' . json_encode(CURRENT_BASE_URL) . ';';
  $html[] = 'var unitsSimple = ' . json_encode($measurementUnits["simple"]) . ';';
  $html[] = '</script>';
  $html[] = js_tag('measurements-analytics');

  echo implode("\n", $html);
?>

<div class="measurementCenter">
<?php
  $measurementsArea = array(
    -1 => __("Select Below"),
    0 => __("Dimensions"),
    1 => __("Surfaces"),
    2 => __("Volume"),
  );
  echo $view->formLabel('measurementsArea', __('Measurement Analytical Area')) . ": ".
        $view->formSelect('measurementsArea', array(), array(), $measurementsArea);

  echo "<span class='oneEm'></span>";

  echo $view->formLabel('measurementsUnit', __('Measurement Analytical Unit')) . ": ".
        $view->formSelect('measurementsUnit', array(), array(), $measurementUnits["select"]);
?>
</div>

<div class="measurementCenter">
  <?php
    echo __('Filter item ID (e.g. "42-500")') . ": ";
    echo $view->formInput("measurementsIdFilter",
                            null,
                            array("type" => "text",
                                  "size" => 8,
                                  // "maxlength" => 16,
                                )
                            );

    echo "<span class='oneEm'></span>";

    echo __('Filter target range (e.g. "20.5-50")') . ": ";
    echo $view->formInput("measurementsRangeFilter",
                            null,
                            array("type" => "text",
                                  "size" => 10,
                                  // "maxlength" => 20,
                                )
                            );

    echo "<span class='oneEm'></span>";

    echo __('Filter Title Text') . ": ";
    echo $view->formInput("measurementsTitleFilter",
                            null,
                            array("type" => "text",
                                  "size" => 10,
                                  // "maxlength" => 30,
                                )
                            );
  ?>
</div>

<div id="measurementPaginate" class="measurementCenter">
<a href="#" data-pagstep="m2" id="paginateFirst">|«</a>
<a href="#" data-pagstep="m1" id="paginatePrev">«</a>
<span id="curPage" class="pageCount"></span> / <span id="numPages" class="pageCount"></span>
<a href="#" data-pagstep="p1" id="paginateNext">»</a>
<a href="#" data-pagstep="p2" id="paginateLast">»|</a>
</div>

<table id="measurementsTable">
  <thead>
    <tr>
      <th><?php echo __("Title"); ?></th>
      <th colspan="7" class="measOrig"><?php echo __("Original Values"); ?></th>
      <th colspan="7" class="measCalc"><?php echo __("Converted Values"); ?></th>
    </tr>
    <tr>
      <th></th>
      <?php
        $keys = array(
          "measl1" => __("dim1"),
          "measl2" => __("dim2"),
          "measl3" => __("dim3"),
          "measf1" => __("face1"),
          "measf2" => __("face2"),
          "measf3" => __("face3"),
          "measv" => __("vol"),
        );
        for($i=1; ($i<=2); $i++) {
          foreach($keys as $class => $key) {
            $cl = $class . ($i == 2 ? "c" : "");
            echo "<th class='$cl'>".__($key)."</th>";
          }
        }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php
      for($i=0; $i<MEASUREMENT_TABLE_LEN; $i++) {
        echo "<tr id='measurementsRow$i'>";
        echo "<td class='measurementsCell0'></td>";
        foreach(array("", "c") as $suffix) {
          foreach(array("l1", "l2", "l3", "f1", "f2", "f3", "v") as $key) {
            echo "<td class='measurementValue meas".$key.$suffix."'></td>";
          }
        }
        echo "</tr>";
      }
    ?>
  </tbody>
</table>

<div id="measurementsAnalysisPopup" style="overflow: auto; padding: 0 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <table>
    <?php
      # +#+#+# Here: real key names (from $keys up here)
      foreach(array("", "c") as $suffix) {
        foreach(array("l1", "l2", "l3", "f1", "f2", "f3", "v") as $key) {
          echo "<tr>";
          echo "<th>$key$suffix</th><td></td>";
          echo "</tr>";
        }
      }
    ?>
  </table>
</div>

<?php echo foot(); ?>
