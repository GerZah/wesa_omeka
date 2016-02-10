<?php
  if (!defined("LITYLOADED")) {
    queue_css_file("lity.min");
    queue_js_file('lity.min');
    DEFINE("LITYLOADED", 1);
  }
  ?>
<div id="item-reference-selector" style="overflow: auto; padding: 20px; border-radius: 6px; background: #fff" class="lity-hide">
      <p><label for="new_relation_object_item_typeReference_id"><?php echo __('Item Types'); ?>: </label>
      <?php echo $this->formSelect('new_relation_object_item_typeReference_id', null, array('multiple' => false), $itemTypesList); ?></p>

      <p><?php echo __('Item Sort'); ?>:
          <fieldset>
              <input type="radio" name="itemsListsortReference" id="new_selectObjectsortReferenceTimestamp" value="timestamp" checked>
              <label for="selectObjectSortTimeStamp"><?php echo __("Most recently updated"); ?></label>
              <input type="radio" name="itemsListsortReference" id="new_selectObjectsortReferenceName" value="name">
              <label for="selectObjectSortName"><?php echo __("Alphabetically"); ?></label>
          </fieldset>
      </p>

      <p><?php echo __('Object Title'); ?>: <span id="object_titleReference"></span></p>
      <input id="new_reference_object_item_id" type="hidden">
      <label for="partialReference_object_titleReference"><?php echo __('Partial Object Title'); ?>: </label>
      <input id="partialReference_object_titleReference">

      <br>
      <ul class="pagination">
          <li id="selector-previous-pageReference" class="pg_disabledReference pagination_previous"><a href="#">&lt;</a></li>
          <li id="selector-next-pageReference" class="pg_disabledReference pagination_next"><a href="#">&gt;</a></li>
      </ul>

      <br>
      <ul id="lookup-resultsReference"></ul>

  <a href="#" id="add-relation" class="green button" data-lity-close><?php echo __('Select'); ?></a>
  </div>
