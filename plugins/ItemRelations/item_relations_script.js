jQuery(document).ready(function () { 

	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

	var lightbox = lity(); // Lity lightbox for item selector "popup"

	var my_input = null; // Reference to currently active input

	// --- START: moved from <script> block formerly located at the bottom of item_relations_form.php
	$('.item-relations-add-relation').click(function () {
		var oldRow = $('.item-relations-entry').last();
		var newRow = oldRow.clone();
		oldRow.after(newRow);
		var inputs = newRow.find('input, select');
		inputs.val('');
		// additions: whenever adding a row, re-initialize click events etc.
		console.log("my item-relations-add-relation");
		itemRelationsJsInit();
	});
	// --- END: moved from <script> block formerly located at the bottom of item_relations_form.php

	itemRelationsJsInit(); // Init click events with form elements etc.

	// Init click events with form elements etc.
	function itemRelationsJsInit() {
		console.log("itemRelationsJsInit");
		$(".selectObjectIdHref").click(selectObjectIdHrefClick);
		$("#allItemIds").change(allItemIdsChange);
	}

	// Init "popup" i.e. lightbox with values
	function selectObjectIdHrefClick() {
		console.log("selectObjectIdHrefClick");
		my_input=$(this).closest(".item_relations_idbox").find("input");
		lightbox.open("#selectObjectId");
		return false;
	}

	function allItemIdsChange() {
		console.log("allItemIdsChange - "+this.value);
		$(my_input).val(this.value);
		lightbox.close();
		return false;
	}

} );
