<?php
  $pageTitle = __('Reorder Text Elements');
  echo head(array('title'=>$pageTitle));
  # echo flash();

  $elements = ReorderElementTextsPlugin::checkItemElement();
  if ($elements) {
    // echo "<pre>" . print_r($_GET,true) . "</pre>";
    // echo "<pre>Elements: " . print_r($elements,true) . "</pre>";
    $itemId = intval($_GET["item"]);
	  $elementId = intval($_GET["element"]);

    $order = json_decode($_GET["reorderElementTextsOrder"]);
    // echo "<pre>Order: " . print_r($order,true) . "</pre>";

    if (count($order) != count($elements)) {
      $returnLink = "<a href='javascript:window.history.back();'>" .
  	                __("Please return to the referring page.").
  	                "</a>";
      // echo __("Mismatching number of elements.") . " " . $returnLink;
    }

    else {

      $index = array();
      foreach($elements as $idx => $element) {
        $index[$element["id"]] = $idx;
      }
      // echo "<pre>Index: " . print_r($index,true) . "</pre>";

      $newOrder = array();
      foreach($order as $txt) {
        $newOrder[] = array(
                        "text" => $elements[$index[$txt]]["text"],
                        "html" => $elements[$index[$txt]]["html"]
                      );
      }
      // echo "<pre>NewOrder: " . print_r($newOrder,true) . "</pre>";

      $db = get_db();

      foreach($elements as $idx => $element) {
        $sql = "UPDATE $db->ElementTexts".
                " SET text='".addslashes($newOrder[$idx]["text"])."',".
                " html=".$newOrder[$idx]["html"].
                " WHERE id=".$element["id"];
        $db->query($sql);
      }

      echo "<p>Done.</p>";

      $backUrl=url("items/show/".$itemId);
      echo "<p><a href='".$backUrl."' class='green button'>".__("Back")."</a></p>";

    }

  }

  echo foot();
?>
