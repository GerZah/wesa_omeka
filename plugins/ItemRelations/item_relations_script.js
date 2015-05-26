jQuery(document).ready(function () { 

	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

	var lightbox = lity();

	// --- START: moved from <script> block formerly located at the bottom of item_relations_form.php
	$('.item-relations-add-relation').click(function () {
		var oldRow = $('.item-relations-entry').last();
		var newRow = oldRow.clone();
		oldRow.after(newRow);
		var inputs = newRow.find('input, select');
		inputs.val('');
	});
	// --- END: moved from <script> block formerly located at the bottom of item_relations_form.php

	itemRelationsJsInit(); // Init click events with form elements etc.

	$(".item-relations-add-relation").click( function() { // whenever adding a row, re-initialize click events etc.
		itemRelationsJsInit();
	} );

	// Init click events with form elements etc.
	function itemRelationsJsInit() {
		$(".itemRelationsIdSelector").click(itemRelationsSelectorClick); // toggle item ID / select box
	}

	// toggle item ID / select box for current new object relation
	function itemRelationsSelectorClick() {

		var isChecked=$(this).is(':checked');
		// console.log( isChecked );

		// if (isChecked) { $(this).siblings().hide(); } else { $(this).siblings().show(); }

		if (isChecked) { lightbox(); }

	}

} );
