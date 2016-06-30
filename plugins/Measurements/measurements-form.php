<?php
  $view = get_view();

  if (!defined("LITYLOADED")) {
    queue_css_file("lity.min");
    queue_js_file('lity.min');
    DEFINE("LITYLOADED", 1);
  }

  $i18nStrings = array(
    "selectTriple" => __("Please start by selecting a triple unit."),
    "enterNumerical" => __("Please enter numerical values into all fields (or leave them empty)."),
    "enterNumber" => __("Please enter the number of items (or leave the field empty)."),
    "unitVerb" => __("Unit"),
    "lengthVerb" => __("Dimension"),
    "faceVerb" => __("Face"),
    "volumeVerb" => __("Volume"),
    "numberVerb" => __("Number"),
    "enteredData" => __("Entered Data"),
    "derivedData" => __("Derived Data"),
  );

  queue_css_file('measurements');
  queue_js_file('measurements');
  queue_js_string(
    "var measurementsUnits=".json_encode($ungroupedSaniUnits,true).";".
    "var measurementsI18n=".json_encode($i18nStrings).";"
  );

  function editField($args) {
    foreach(array("view", "id", "title", "class", "readonly", "exp", "span") as $key) {
      $$key = @$args[$key];
    }
    $argArray = array(
      "type" => "text",
      "class" => $class,
      "size" => 6,
      "maxlength" => 10,
      "data-title" => $title,
      "data-exp" => $exp,
    );
    if ($readonly) { $argArray["readonly"] = true; }
    return $view->formInput( "$id", null, $argArray )." <span class='".$span."'></span> ";
  }
?>

<?php /* ----------------------------------------------------------------- */ ?>

<div id="measurementsPopup" style="overflow: auto; padding: 0 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h3><?php echo __("Measurements"); ?></h3>

  <p>
    <?php
      echo "<strong>" . __("Triple Unit") . ":</strong> ";
      echo $view->formSelect('measurementUnits', -1, array(), $tripleSelect);
    ?>
  </p>

  <h4><?php echo __("Data Entry"); ?></h4>

  <p>
    <?php
      echo "<strong>" . __("Dimension") . " 1/2/3" . ":</strong> ";
      for($i=1; $i<=3; $i++) {
        echo editField(array(
          "view" => $view,
          "id" => "measurementLength$i",
          "title" => __("Dimension")." $i",
          "class" => "measurementsTextField",
          "readonly" => true,
          "exp" => 1,
          "span" => "measurementsLenghtUnit3", // all least significant unit that we will convert to
        ));
      }
    ?>
  </p>
  <p>
    <?php
      echo "<strong>" . __("Face") . " 1/2/3" . ":</strong> ";
      for($i=1; $i<=3; $i++) {
        echo editField(array(
          "view" => $view,
          "id" => "measurementFace$i",
          "title" => __("Face")." $i",
          "class" => "measurementsTextField",
          "readonly" => true,
          "exp" => 2,
          "span" => "measurementsLenghtUnit3 measurementsFaceUnit",
        ));
      }
    ?>
  </p>
  <p>
    <?php
      echo "<strong>" . __("Volume") . ":</strong> ";
      echo editField(array(
        "view" => $view,
        "id" => "measurementVolume",
        "title" => __("Volume"),
        "class" => "measurementsTextField",
        "readonly" => true,
        "exp" => 3,
        "span" => "measurementsLenghtUnit3 measurementsVolumeUnit",
      ));
      echo "<span style='display:inline-block; width:3em;'></span> <strong>" . __("Number") . ":</strong> ";
      echo editField(array(
        "view" => $view,
        "id" => "measurementNumber",
        "title" => __("Number")
      ));
    ?>
  </p>

  <h4><?php echo __("Derived Data"); ?></h4>

  <p>
    <?php
      echo "<strong>" . __("Dimension") . " 1/2/3" . ":</strong> ";
      for($i=1; $i<=3; $i++) {
        echo editField(array(
          "view" => $view,
          "id" => "measurementLength$i"."Derived",
          "title" => __("Dimension")." $i",
          "class" => "measurementsTextFieldDerived",
          "readonly" => true,
          "exp" => 1,
          "span" => "measurementsLenghtUnit3", // all least significant unit that we will convert to
        ));
      }
    ?>
  </p>
  <p>
    <?php
    echo "<strong>" . __("Face") . " 1/2/3" . ":</strong> ";
    for($i=1; $i<=3; $i++) {
      echo editField(array(
        "view" => $view,
        "id" => "measurementFace$i"."Derived",
        "title" => __("Face")." $i",
        "class" => "measurementsTextFieldDerived",
        "readonly" => true,
        "exp" => 2,
        "span" => "measurementsLenghtUnit3 measurementsFaceUnit",
      ));
    }
    ?>
  </p>
  <p>
    <?php
      echo "<strong>" . __("Volume") . ":</strong> ";
      echo editField(array(
        "view" => $view,
        "id" => "measurementVolumeDerived",
        "title" => __("Volume"),
        "class" => "measurementsTextFieldDerived",
        "readonly" => true,
        "exp" => 3,
        "span" => "measurementsLenghtUnit3 measurementsVolumeUnit",
      ));
    ?>
  </p>

  <div class="centerButtons">
    <a href="#" id="measurementsApply" class="green button"><?php echo __('Apply'); ?></a>
    <a href="#" id="measurementsClear" class="green button"><?php echo __('Clear'); ?></a>
    <a href="#" id="measurementsCancel" class="green button" data-lity-close><?php echo __('Cancel'); ?></a>
  </div>

</div>

<?php /* ----------------------------------------------------------------- */ ?>

<div id="measurementsPopup2" style="overflow: auto; padding: 0 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <h3><?php echo __("Triple Unit Data"); ?></h3>
  <p>
    <span id="measurementsTripleEditTitle"></span>:
    <?php
      for($i=1; $i<=3; $i++) {
        echo editField(array(
          "view" => $view,
          "id" => "measurementValue$i",
          "title" => __("Dimension")." $i",
          "span" => "measurementValue measurementsLenghtUnit".$i,
        ));
      }
    ?>
  </p>
  <div class="centerButtons">
    <a href="#" id="measurementsValuesApply" class="green button"><?php echo __('Apply'); ?></a>
    <a href="#" id="measurementsValuesClear" class="green button"><?php echo __('Clear'); ?></a>
    <a href="#" id="measurementsValuesCancel" class="green button" data-lity-close><?php echo __('Cancel'); ?></a>
  </div>
</div>
