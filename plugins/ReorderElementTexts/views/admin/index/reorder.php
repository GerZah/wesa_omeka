<?php
  $pageTitle = __('Reorder Text Elements');
  echo head(array('title'=>$pageTitle));
  # echo flash();

  if ($elements) {
    // echo "<pre>" . print_r($elements,true) . "</pre>";
    $itemId = intval($_GET["item"]);
	  $elementId = intval($_GET["element"]);

    echo "<h3>".__("Please select new text element order")."</h3>";
    echo "<p>".__("Simply drag the text element with your mouse.")."</p>";

    $backUrl=url("items/show/".$itemId);
    echo "<p><a href='".$backUrl."' class='green button'>".__("Cancel")."</a></p>";

    echo "<ul id='sortable'>";
    foreach($elements as $element) {
      echo "<li class='ui-state-default dragitems' data-id='".$element["id"]."'>".
            $element["text"].
            "</li>";
    }
    echo "</ul>";

    echo "<form action='".url('reorder-element-texts/index/update')."' method='get'>";
    echo "<input name='item' type='hidden' value='".$itemId."'>";
    echo "<input name='element' type='hidden' value='".$elementId."'>";
    echo "<input id='reorderElementTextsOrder' name='reorderElementTextsOrder' type='hidden' value=''>";
    echo "<input type='submit' value='".__("Reorder Inputs")."'>";
    echo "</form>";
  }

  else { echo $output; }

  echo foot();
?>
