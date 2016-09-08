<?php
  echo head(array('title' => __('Transaction Analysis'), 'bodyclass' => 'measurementsfoo'));
  echo flash();

  echo '<link href="' . css_src('transactions') . '" rel="stylesheet">';
  echo js_tag('transactions');

  foreach($transactionWeights as $idx => $val) { $$idx = $val; }

  echo
    $this->formSelect( 'st',$sandstoneElementItemType, array(), $itemTypesSelect )
    . " "
    . $this->formSelect( 'rel', $belongsToRelation, array(), $relationsSelect )
    . " "
    . $this->formSelect( 'tr', $transactionItemType, array(), $itemTypesSelect )
    // . " "
    // . $this->formButton( 'applyBtn', __("Apply"), array() )
  ;

  $urlStub = "?st=$sandstoneElementItemType&rel=$belongsToRelation&tr=$transactionItemType&page=";

?>

<div class="measurementCenter">
  <?php if ($maxPage>=1) { ?><a href="<?php echo $urlStub."0"; ?>">|«</a><?php } ?>
  <?php if ($maxPage>=1000) { ?><a href="<?php echo $urlStub.($page-1000); ?>">«<sub>1000</sub></a><?php } ?>
  <?php if ($maxPage>=100) { ?><a href="<?php echo $urlStub.($page-100); ?>">«<sub>100</sub></a><?php } ?>
  <?php if ($maxPage>=10) { ?><a href="<?php echo $urlStub.($page-10); ?>">«<sub>10</sub></a><?php } ?>
  <?php if ($maxPage>=1) { ?><a href="<?php echo $urlStub.($page-1); ?>">«</sub></a><?php } ?>
  <span id="curPage" class="pageCount"><?php echo $page+1; ?></span> / <span id="numPages" class="pageCount"><?php echo $maxPage+1; ?></span>
  <?php if ($maxPage>=1) { ?><a href="<?php echo $urlStub.($page+1); ?>">»</a><?php } ?>
  <?php if ($maxPage>=10) { ?><a href="<?php echo $urlStub.($page+10); ?>"><sub>10</sub>»</a><?php } ?>
  <?php if ($maxPage>=100) { ?><a href="<?php echo $urlStub.($page+100); ?>"><sub>100</sub>»</a><?php } ?>
  <?php if ($maxPage>=1000) { ?><a href="<?php echo $urlStub.($page+1000); ?>"><sub>1000</sub>»</a><?php } ?>
  <?php if ($maxPage>=1) { ?><a href="<?php echo $urlStub.$maxPage; ?>">»|</a><?php } ?>
</div>

<?

  echo "<ul>\n";
  foreach($itemDetails as $itemId => $transaction) {
    $iId = $transaction["itemId"]; // $itemId
    $transactionUrl= url('items/show/' . $iId);
    echo "<li><a href='$transactionUrl'>" .
      $transaction["itemTitle"] .
      "</a> (#$iId): <strong>" .
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

  // echo "<pre>-------------\n" . print_r($transactionWeights, true) . "</pre>\n";

  echo foot();
?>
