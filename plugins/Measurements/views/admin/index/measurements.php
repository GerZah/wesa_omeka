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
  $html[] = 'var measurementsJsonUrl = ' . json_encode(url('measurements/')) . ';'; // "lookup/" / "addrel/"
  $html[] = 'var measurementsTableLen = ' . MEASUREMENT_TABLE_LEN . ';';
  $html[] = 'var measurementsBaseUrl = ' . json_encode(CURRENT_BASE_URL) . ';';
  $html[] = 'var unitsSimple = ' . json_encode($measurementUnits["simple"]) . ';';
  $html[] = '</script>';
  $html[] = js_tag('measurements-analytics');

  echo implode("\n", $html);

  // ---------------------------------

  $titleKeys = array( # Keys for table columns titles / item measurements detail page
    "l1" => __("dim1"),
    "l2" => __("dim2"),
    "l3" => __("dim3"),
    "f1" => __("face1"),
    "f2" => __("face2"),
    "f3" => __("face3"),
    "v"  => __("vol"),
  );

?>

<div class="measurementCenter">
<?php
  $measurementsArea = array(
    -1 => __("Select Below"),
    0 => __("Dimensions"),
    1 => __("Surfaces"),
    2 => __("Volume"),
  );
  echo $view->formLabel('measurementsArea', __('Measurement Analytical Area')) . ": ";
  echo $view->formSelect('measurementsArea', array(), array(), $measurementsArea);

  echo "<span class='oneEm'></span>";

  echo $view->formLabel('measurementsUnit', __('Measurement Analytical Unit')) . ": ";
  echo $view->formSelect('measurementsUnit', array(), array(), $measurementUnits["select"]);

  echo "<span class='oneEm'></span>";

  echo $view->formLabel('measurementsWeightFactor', __('Weight Factor')) . ": ";
  echo $view->formInput("measurementsWeightFactor",
                          "2,0",
                          array("type" => "text",
                                "size" => 8,
                                // "maxlength" => 16,
                              )
                          );
  echo " " . $view->formLabel('measurementsWeightFactor', __('t/m³'));

?>
</div>

<div class="measurementCenter">
  <?php
    echo $view->formLabel('measurementsIdFilter', __('Filter item ID (e.g. "42-500")')) . ": ";
    echo $view->formInput("measurementsIdFilter",
                            null,
                            array("type" => "text",
                                  "size" => 8,
                                  // "maxlength" => 16,
                                )
                            );

    echo "<span class='oneEm'></span>";

    echo $view->formLabel('measurementsRangeFilter', __('Filter target range (e.g. "20.5-50")')) . ": ";
    echo $view->formInput("measurementsRangeFilter",
                            null,
                            array("type" => "text",
                                  "size" => 10,
                                  // "maxlength" => 20,
                                )
                            );

    echo "<span class='oneEm'></span>";

    echo $view->formLabel('measurementsTitleFilter', __('Filter Title Text')) . ": ";
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
  <div class="breakdownDiv">
    <?php
      echo $view->formLabel('measurementsBreakdownNumbers', __('Breakdown Numbers')) . ": ";
      echo $view->formCheckbox('measurementsBreakdownNumbers', null, array('checked' => false));
    ?>
  </div>
  <div class="addRelDiv">
    <button type="button" id="addRelBtn" class="green button">
      <?php echo __("Add Relation"); ?>
    </button>
  </div>
  <a href="#" class="paginateFirstLast" data-pagstep="first">|«</a>
  <a href="#" class="paginateTho" data-pagstep="-1000">«<sub>1000</sub></a>
  <a href="#" class="paginateHun" data-pagstep="-100">«<sub>100</sub></a>
  <a href="#" class="paginateTen" data-pagstep="-10">«<sub>10</sub></a>
  <a href="#" class="paginateOne" data-pagstep="-1">«</sub></a>
  <span id="curPage" class="pageCount"></span> / <span id="numPages" class="pageCount"></span>
  <a href="#" class="paginateOne" data-pagstep="+1">»</a>
  <a href="#" class="paginateTen" data-pagstep="+10"><sub>10</sub>»</a>
  <a href="#" class="paginateHun" data-pagstep="+100"><sub>100</sub>»</a>
  <a href="#" class="paginateTho" data-pagstep="+1000"><sub>1000</sub>»</a>
  <a href="#" class="paginateFirstLast" data-pagstep="last">»|</a>
</div>

<table id="measurementsTable">
  <thead>
    <tr>
      <th><?php echo __("Title"); ?></th>
      <th colspan="7" class="measOrig"><?php echo __("Original Values"); ?></th>
      <th colspan="8" class="measCalc"><?php echo __("Converted Values"); ?></th>
      <th><?php echo __("Number"); ?></th>
    </tr>
    <tr>
      <th></th>
      <?php
        foreach(array("", "c") as $suffix) {
          foreach($titleKeys as $id => $key) {
            $cl = "meas$id$suffix";
            echo "<th class='$cl'>".__($key)."</th>";
          }
          if ($suffix=="c") { echo "<th class='measw'>".__("Weight")."</th>"; }
        }
      ?>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php
      for($i=0; $i<MEASUREMENT_TABLE_LEN; $i++) {
        echo "<tr id='measurementsRow$i'>";
        echo "<td class='measurementsCell0'></td>";
        foreach(array("", "c") as $suffix) {
          foreach(array_keys($titleKeys) as $key) {
            echo "<td class='measurementValue meas".$key.$suffix."'></td>";
          }
          if ($suffix=="c") { echo "<td class='measurementValue measw'></td>"; }
        }
        echo "<td class='measurementValue measn'></td>";
        echo "</tr>";
      }
    ?>
  </tbody>
</table>

<div id="measurementsAnalysisPopup" style="overflow: auto; padding: 0 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h3 id="detailsTitle"></h3>
  <table id="detailsTable">
    <tr>
      <th></th>
      <?php
        foreach(array("", "c") as $suffix) {
          $valuesTitle = ( $suffix == "" ? __("Original Values") : __("Converted Values") );
          echo "<th>$valuesTitle</th>\n";
        }
      ?>
    </tr>
    <?php
      foreach($titleKeys as $id => $key) {
        echo "<tr><th>$key</th>";
        foreach(array("", "c") as $suffix) {
          $cellId = "details$id$suffix";
          echo "<td id='$cellId'></td>";
        }
        echo "</tr>\n";
      }
      // echo "<tr><th>".__("Number")."</th><td id='detailsn'></td><td>-</td></tr>";
      // echo "<tr><th>".__("Weight")."</th><td>-</td><td id='detailsw'></td></tr>";
      echo "<tr><th></th><th>".__("Number")."</th><th>".__("Weight")." (".__("single / all").")</th></tr>";
      echo "<tr><th></th><td id='detailsn'></td><td id='detailsw'></td></tr>";
    ?>
  </table>
  <p class="measurementCenter">
    <a href="#" class="green button detailsItemLink"><?php echo __("Open Item"); ?></a>
    <a href="#" class="green button detailsItemLink" target="_blank"><?php echo __("Open Item in New Window"); ?></a>
    <button type="button" class="green button measurementsCancelBtn"><?php echo __("Cancel"); ?></button>
  </p>
</div>

<div id="measurementsAnalysisAddRel" style="overflow: auto; width: 90%; margin: auto; padding: 0 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h3><?php echo __("Add Relationship"); ?></h3>
  <div id="addRelRegularForm">
    <p>
      <?php
        echo __("The item that you selected last (which highlighted in a slightly different color) ".
                "will be the subject of the newly added relationship, while the one or more other highlighted ".
                "item(s) will become object(s) of that relationship.");
      ?>
    </p>
    <p><strong><?php echo __("Subject Item"); ?>:</strong> <span id="addRelSubjectItem"></span></p>
    <h5><?php echo __("Object Item(s)"); ?></h5>
    <p id="addRelObjectItems"></p>
    <?php
      if (!MeasurementsPlugin::itemRelationsActive()) {
        $measurementsRelations = array();
      }
      else {
        $measurementsRelations = get_table_options('ItemRelationsProperty');
      }
      echo "<strong>" .
            $view->formLabel('measurementsRelations', __('Item Relations')) .
            ":</strong> " .
            $view->formSelect('measurementsRelations', array(), array(), $measurementsRelations);
    ?>
    <?php
      $provideRelationComments = !!get_option('item_relations_provide_relation_comments');
      if ($provideRelationComments) {
        echo "<p><strong>" . __("Relationship Comment") . ":</strong> ".
              $this->formText('relationComment', null, array("size" => 60, "maxlength" => 60)).
              "</p>\n";

      }
    ?>
    <p class="measurementCenter">
      <button type="button" class="green button" id="doAddRelBtn"><?php echo __("Add Relationship"); ?></button>
      <button type="button" class="green button measurementsCancelBtn"><?php echo __("Cancel"); ?></button>
    </p>
  </div>
  <div id="addRelResult">
    <p id="addRelResultSuccess"><?php echo __("Relation(s) created successfully."); ?></p>
    <p id="addRelResultFail"><?php echo __("Error while creating relation(s)."); ?></p>
    <p class="measurementCenter">
      <button type="button" class="green button measurementsCancelBtn"><?php echo __("Close"); ?></button>
    </p>
  </div>
</div>

<?php echo foot(); ?>
