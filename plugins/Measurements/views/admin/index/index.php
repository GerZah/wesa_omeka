<?php
  echo head(array('title' => __('Measurements Analysis'), 'bodyclass' => 'measurementsfoo'));
  echo flash();

  echo "<ul>\n";

  echo
    "<li>" .
      __("Click button below to be taken to the <strong>Measurements Analysis</strong> table.") . "<br>\n" .
      "<a href='" . html_escape(url('measurements/index/table')) . "' class='green button'>" .
      __('Measurements Analysis') .
      "</a>" .
    "</li>\n"
  ;

  echo
    "<li>" .
      __("Click button below to be taken to the <strong>Transaction Analysis</strong> page.") . "<br>\n" .
      "<a href='" . html_escape(url('measurements/index/transactions')) . "' class='green button'>" .
      __('Transaction Analysis') .
      "</a>" .
    "</li>\n"
  ;

  echo "</ul>\n";

  echo foot();
?>
