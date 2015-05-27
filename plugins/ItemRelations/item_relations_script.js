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

		my_input=$(this).closest(".item_relations_idbox").find("input"); // Find and store target ID box


		// Initialize All Items selector from array
		$("#allItemIds").empty();

		$("#allItemIds").append("<option value=''>"+selectBelowTxt+"</option>");
		var opencat=false;
		var lastcat=null;

		var allItemsOptions="";
		$.each(allItemsArr, function (itemIndex, item) { // all items

				if ( (opencat) && (String(lastcat)!==String(item[2])) ) { // open group, but new category title?
					allItemsOptions+="</optgroup>";
					opencat=false;
				}
				if (!opencat) { // no currently open group?
					var groupname=( item[2]!='0' ? item[2] : nATxt);
					allItemsOptions+="<optgroup label='"+itemTypeTxt+" \""+groupname+"\"'>";
					opencat=true;
				}
				lastcat=item[2];
				allItemsOptions+="<option value='"+item[0]+"'>"+item[1]+"</option>";

			} );
		allItemsOptions+="</optgroup>";

		$("#allItemIds").append(allItemsOptions);

		lightbox.open("#selectObjectId"); // and off we go
		return false;
	}

	function allItemIdsChange() {
		console.log("allItemIdsChange - "+this.value);
		$(my_input).val(this.value);
		lightbox.close();
		$("#selectObjectId").css("background-color","yellow");
		return false;
	}

} );
