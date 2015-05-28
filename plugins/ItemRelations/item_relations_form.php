<p>
<?php
$link = '<a href="' . url('item-relations/vocabularies/') . '">'
      . __('Browse Vocabularies') . '</a>';

echo __('Here you can relate this item to another item and delete existing '
     . 'relations. For descriptions of the relations, see the %s page. Invalid '
     . 'item IDs will be ignored.', $link
);
?>
</p>
<table>
    <thead>
    <tr>
        <th><?php echo __('Subject'); ?></th>
        <th><?php echo __('Relation'); ?></th>
        <th><?php echo __('Object'); ?></th>
        <th><?php echo __('Delete'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($subjectRelations as $subjectRelation): ?>
    <tr>
        <td><?php echo __('This Item'); ?></td>
        <td><?php echo $subjectRelation['relation_text']; ?></td>
        <td><a href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>" target="_blank"><?php echo $subjectRelation['object_item_title']; ?></a></td>
        <td><input type="checkbox" name="item_relations_item_relation_delete[]" value="<?php echo $subjectRelation['item_relation_id']; ?>" /></td>
    </tr>
    <?php endforeach; ?>
    <?php foreach ($objectRelations as $objectRelation): ?>
    <tr>
        <td><a href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>" target="_blank"><?php echo $objectRelation['subject_item_title']; ?></a></td>
        <td><?php echo $objectRelation['relation_text']; ?></td>
        <td><?php echo __('This Item'); ?></td>
        <td><input type="checkbox" name="item_relations_item_relation_delete[]" value="<?php echo $objectRelation['item_relation_id']; ?>" /></td>
    </tr>
    <?php endforeach; ?>
    <tr class="item-relations-entry">
        <td><?php echo __('This Item'); ?></td>
        <td><?php echo get_view()->formSelect('item_relations_property_id[]', null, array('multiple' => false), $formSelectProperties); ?></td>
        <td>
					<span class="item_relations_idbox">
						<?php echo __('Item ID'); ?><br>
						<a href="#" class="selectObjectIdHref">[<?php echo __('Select ID'); ?>]</a><br>
						<?php echo get_view()->formText('item_relations_item_relation_object_item_id[]', null, array('size' => 8)); ?>
					</span>
				</td>
        <td><span style="color:#ccc;"><?php echo __("[n/a]") ?></span></td>
    </tr>
    </tbody>
</table>
<button type="button" class="item-relations-add-relation"><?php echo __('Add a Relation'); ?></button>
<link href="<?php echo PUBLIC_BASE_URL; ?>/plugins/ItemRelations/lity/lity.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo PUBLIC_BASE_URL; ?>/plugins/ItemRelations/lity/lity.min.js"></script>
<link href="<?php echo PUBLIC_BASE_URL; ?>/plugins/ItemRelations/item_relations_styles.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo PUBLIC_BASE_URL; ?>/plugins/ItemRelations/item_relations_script.js"></script>
<?php
	$db = get_db();
	// Fetch all items together with their IDs, titles, and item type IDs and names
	$sql = "SELECT items.id, text, items.item_type_id, itemtypes.name, UNIX_TIMESTAMP(modified)
					FROM {$db->Item} items
					LEFT JOIN {$db->Element_Texts} elementtexts on (items.id=elementtexts.id)
					LEFT JOIN {$db->Item_Types} itemtypes on (items.item_type_id=itemtypes.id)
					WHERE true
					ORDER BY itemtypes.name ASC, text ASC";

	$items = $db->fetchAll($sql);

	# echo "<pre>"; print_r($items); echo "</pre>\n"; # DEBUG
	echo "<script type='text/javascript'>\n";
	echo "var allItemsArr=[\n"; // Put all items into a JavaScript array, that will later be used via jQuery
	foreach($items as $item) {
		foreach (array_keys($item) as $key) {
			if (!$item[$key]) { $item[$key]=0; } # Transform all empty values to zero
			if (intval($item[$key])!==$item[$key]) { $item[$key]="'".htmlspecialchars($item[$key], ENT_QUOTES)."'"; } # Non-ints i.e. string into apostrophes
		}
		echo "[[".implode("],[", $item)."]],\n"; # Item as a new array element - with its components in another array
	}
	echo "];\n";
	// echo "var allItemsTxt='".__("All Items")."';\n";
	echo "var selectBelowTxt='".__("Select Below")."';\n";
	echo "var itemTypeTxt='".__("Item Type")."';\n";
	echo "var nATxt='".__("[n/a]")."';\n";
	echo "var sortWithinItemTypeByTxt='".__("Sort within item types by")."';\n";
	echo "var updDateDescTxt='".__("Last Update (desc)")."';\n";
	echo "var nameAscTxt='".__("Name (asc)")."';\n";
	echo "var itemTypesTxt='".__("Item Types")."';\n";
	echo "var allTxt='".__("Alle")."';\n";
	echo "var searchTermTxt='".__("Search Term")."';\n";
	echo "var resetTxt='".__("Reset")."';\n";
	echo "</script>\n";
?>
<div id="lightboxJsContent" class="lity-hide"></div>