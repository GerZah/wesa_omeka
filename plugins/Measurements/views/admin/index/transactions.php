<?php
  echo head(array('title' => __('Transaction Analysis'), 'bodyclass' => 'measurementsfoo'));
  echo flash();
?>

<?php
  echo "<ul>\n";
  foreach($transactionWeights["itemDetails"] as $itemId => $transaction) {
    $transactionUrl= url('items/show/' . $itemId);
    echo "<li><a href='$transactionUrl'>" .
      $transaction["itemTitle"] .
      "</a> (#$itemId): <strong>" .
      round($transaction["fullW"],4) .
      " t</strong>"
    ;
    echo "<ul>\n";
    foreach($transaction["stoneData"] as $stoneId => $stone) {
      $stoneUrl= url('items/show/' . $stoneId);
      echo "<li><a href='$stoneUrl'>" .
        $stone["t"] .
        "</a> (#$stoneId)<br>" .
        round($stone["w"],4) .
        " t" .
        ( $stone["n"] == 1 ? "" :
          " <em>x " . $stone["n"] .
          "</em> = " .
          round($stone["wn"],4) .
          " t"
        ) .
        "</li>";
      ;
    }
    echo "</ul>\n";
    echo "</li>\n";
  }
  echo "</ul>\n";

  echo "<pre>-------------\n" . print_r($transactionWeights, true) . "</pre>\n";
?>

<?php
  echo foot();
?>
