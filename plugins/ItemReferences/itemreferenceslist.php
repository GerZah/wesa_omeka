<div class="field" id="type-select">
  <div class="two columns alpha">
    <?php echo $this->formLabel('item-type', __('Element')); ?>
  </div>
  <div class="inputs five columns omega">
    <?php echo $this->formText('reference', null, array('size' => 10)); ?>
  </div>
  <a href="#item-relation-selector" class="green button" data-lity><?php echo __('Select'); ?></a>
</div>
<?php
$db = get_db();
$sql = "SELECT id, name from {$db->Item_Types} ORDER BY name";
$itemtypes = $db->fetchAll($sql);
$m = array(
  '-1' => "- ".__('All')." -",
);
foreach ($itemtypes as $type) {
  $m[$type['id']] = $type['name'];
}
?>
<div id="item-relation-selector" style="overflow: auto; padding: 20px; border-radius: 6px; background: #fff" class="lity-hide">
  <p><label for="new_relation_item_item_type_id"><?php echo __('Item Types'); ?>: </label>
    <?php echo $this->formSelect('new_relation_item_item_type_id', null, array('multiple' => false), $m); ?></p>

    <p>
      <?php echo __('Item Sort'); ?>:
      <fieldset>
        <input type="radio" name="itemsListSort" id="new_selectItemSortTimestamp" value="timestamp" checked>
        <label for="selectItemSortTimeStamp"><?php echo __("Most recently updated"); ?></label>

        <input type="radio" name="itemsListSort" id="new_selectItemSortName" value="name">
        <label for="selectItemSortName"><?php echo __("Alphabetically"); ?></label>
      </fieldset>
    </p>

    <p><?php echo __('Item Title'); ?>: <span id="item_title"></span></p>
    <input id="new_relation_item_item_id" type="hidden">

    <label for="partial_item_title"><?php echo __('Partial Item Title'); ?>: </label>
    <input id="partial_item_title">

    <br>
    <ul class="pagination">
      <li id="selector-previous-page" class="pg_disabled pagination_previous"><a href="#">&lt;</a></li>
      <li id="selector-next-page" class="pg_disabled pagination_next"><a href="#">&gt;</a></li>
    </ul>
    <br>

    <ul id="lookup-results">
    </ul>

    <a href="#" id="add-relation" class="green button" data-lity-close><?php echo __('Add'); ?></a>
  </div>

  <?php if (!defined("LITYLOADED")) { ?>
    <link href="<?php echo PUBLIC_BASE_URL; ?>/plugins/ItemRelations/lity/lity.min.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo PUBLIC_BASE_URL; ?>/plugins/ItemRelations/lity/lity.min.js"></script>
    <?php DEFINE("LITYLOADED", 1); } ?>
    <link href="<?php echo PUBLIC_BASE_URL; ?>/plugins/ItemRelations/item_relations_styles.css" rel="stylesheet">
    <script type="text/javascript">
    var url = '<?php echo url('item-relations/lookup/'); ?>';
    </script>
    <script type="text/javascript" src="<?php echo PUBLIC_BASE_URL; ?>/plugins/ItemRelations/item_relations_script.js"></script>
