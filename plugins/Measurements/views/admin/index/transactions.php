<?php
  echo head(array('title' => __('Transaction Analysis'), 'bodyclass' => 'measurementsfoo'));
  echo flash();

  echo '<link href="' . css_src('transactions') . '" rel="stylesheet">';
  echo js_tag('transactions');

  foreach($transactionWeights as $idx => $val) { $$idx = $val; }

  echo "<div class='measurementCenter'>\n"
    . $this->formSelect( 'st',$sandstoneElementItemType, array(), $itemTypesSelect )
    . "<span class='oneEm'></span>"
    . $this->formSelect( 'rel', $belongsToRelation, array(), $relationsSelect )
    . "<span class='oneEm'></span>"
    . $this->formSelect( 'tr', $transactionItemType, array(), $itemTypesSelect )
    . "</div>\n"
  ;

  echo "<div class='measurementCenter'>\n"
    . $this->formLabel('idfilter', __('Filter item ID (e.g. "42-500")')) . ": "
    . $this->formText('idfilter', $idfilter, array("size" => 12) )
    . "<span class='oneEm'></span>"
    . $this->formLabel('titlefilter', __('Filter Title Text')) . ": "
    . $this->formInput("titlefilter", $titlefilter, array("type" => "text", "size" => 12 ) )
    . "<br>"
    . $this->formLabel('weightfactor', __('Weight Factor')) . ": "
    . $this->formInput("weightfactor", $weightfactor, array("type" => "text","size" => 12) )
    . "<span class='oneEm'></span>"
    . $this->formHidden( 'page', $page )
    . $this->formButton( 'applyBtn', __("Apply"), array() )
    . "</div>\n"
  ;

  $urlStub =
    "?st=$sandstoneElementItemType".
    "&rel=$belongsToRelation".
    "&tr=$transactionItemType".
    "&idfilter=".urlencode($idfilter).
    "&titlefilter=".urlencode($titlefilter).
    "&weightfactor=".urlencode($weightfactor).
    "&page="
  ;

?>

<?php
  if (!$itemDetails) {
    echo "<p>" . __("No items that match this constellation of which weights could be added.") . "</p>\n";
  }
  else {
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

<table>
<tr>
  <td colspan="6" class='measurementRight'>
    <a href="#" class="transactionShowHideAllRows">[<?php echo __("Show / Hide All"); ?>]</a>
  </td>
</tr>

<?php

    function formatWeight($weight) {
      $unit = "t";
      if ($weight<1) { $weight *= 1000; $unit = "kg"; }
      if ($weight<1) { $weight *= 1000; $unit = "g"; }
      return number_format($weight, 3, ",", ".") . " " . $unit;
    }

    foreach($itemDetails as $itemId => $transaction) {
      echo "<tr>";
      $iId = $transaction["itemId"]; // $itemId
      $transactionUrl= url('items/show/' . $iId);
      echo "<th colspan='2'>#$iId</th>";
      echo "<td><a href='$transactionUrl'>" . $transaction["itemTitle"] . "</a></td>";
      echo "<td colspan='2' class='measurementRight'>" . formatWeight($transaction["fullW"]) . "</td>";
      echo "<td class='measurementRight'>"
        . "<a href='#' class='transactionShowHideRows' data-item='$itemId'>"
        . "[" . __("Show / Hide") . "]"
        . "</a></td>"
      ;
      echo "<tbody class='itemsHiddenUpFront tr$itemId'>";
      foreach($transaction["stoneData"] as $stoneId => $stone) {
        echo "<tr>";
        $stoneUrl= url('items/show/' . $stoneId);
        echo "<td>&nbsp;</td>";
        echo "<th>#$stoneId</th>";
        echo "<td><a href='$stoneUrl'>" . $stone["t"] . "</a></td>";
        echo "<td class='measurementRight'>" . (
          $stone["n"] == 1 ? "&nbsp;" :
          $stone["n"] . " x " . formatWeight($stone["w"]) . " ="
        ) . "</td>";
        echo "<td class='measurementRight'>" . formatWeight($stone["wn"]) . "</td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>\n";
      }
      echo "</tbody>";
      echo "</tr>\n";
    }

  }
?>

</table>

<?php
// echo "<pre>-------------\n" . print_r($transactionWeights, true) . "</pre>\n";
  echo foot();
?>
