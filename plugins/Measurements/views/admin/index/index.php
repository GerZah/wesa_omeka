<?php
echo head(array('title' => __('Measurements Analysis'), 'bodyclass' => 'measurementsfoo'));
?>
<?php echo flash(); ?>

<?php

  $html = '<link href="' . css_src('measurements-analytics') . '" rel="stylesheet">';
  $html .= '<script type="text/javascript">';
  $html .= 'var measurementsJsonUrl = ' . json_encode(url('measurements/lookup/')) . ';';
  $html .= '</script>';
  $html .= js_tag('measurements-analytics');

  echo $html;

?>

<?php echo foot(); ?>
