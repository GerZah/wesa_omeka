<?php
  echo head(array('title' => __('Measurements Analysis'), 'bodyclass' => 'measurementsfoo'));
  echo flash();
  $view = get_view();

  $html = array();

  $html[] = '<link href="' . css_src('measurements-analytics') . '" rel="stylesheet">';
  $html[] = '<script type="text/javascript">';
  $html[] = 'var measurementsJsonUrl = ' . json_encode(url('measurements/lookup/')) . ';';
  $html[] = 'var measurementsTableLen = ' . MEASUREMENT_TABLE_LEN . ';';
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
        $view->formSelect('measurementsUnit', array(), array(), $measurementUnits);
?>
</div>

<table id="measurementsTable">
  <thead>
    <tr>
      <th><?php echo __("Title"); ?></th>
      <th><?php echo __("Original Value"); ?></th>
      <th><?php echo __("Converted Value"); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
      for($i=0; $i<MEASUREMENT_TABLE_LEN; $i++) {
        echo "<tr id='measurementsRow$i'>".
              "<td class='measurementsCell0'></td>".
              "<td class='measurementsCell1'></td>".
              "<td class='measurementsCell2'></td>".
              "</tr>";
      }
    ?>
  </tbody>
</table>

<div id="measurementPaginate" class="measurementCenter">
<a href="#" data-pagstep="m2" id="paginateFirst">|«</a>
<a href="#" data-pagstep="m1" id="paginatePrev">«</a>
<span id="curPage" class="pageCount"></span> / <span id="numPages" class="pageCount"></span>
<a href="#" data-pagstep="p1" id="paginateNext">»</a>
<a href="#" data-pagstep="p2" id="paginateLast">»|</a>
</div>

<?php echo foot(); ?>
