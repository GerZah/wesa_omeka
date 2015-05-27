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
	}

	// Init "popup" i.e. lightbox with values
	function selectObjectIdHrefClick() {
		console.log("selectObjectIdHrefClick");

		my_input=$(this).closest(".item_relations_idbox").find("input"); // Find and store target ID box

		allItemsSelect="<select id='allItemIds'>"+"</select>"; // Fill all items selector

		$("#lightboxJsContent").empty().append("<div id='selectObjectId'>"+ // Generate lightbox content
																						"<p><a href='#' id='selectObjectSortName'>[Name]</a> / "+ // +#+#+# missing i18n
																						"<a href='#' id='selectObjectSortTimestamp'>[Timestamp]</a></p>"+ // +#+#+# missing i18n
																						allItemsTxt+": "+
																						allItemsSelect+
																						"</div>")

		$("#allItemIds").change(allItemIdsChange);
		$("#selectObjectSortName").click(selectObjectSortName);
		$("#selectObjectSortTimestamp").click(selectObjectSortTimestamp);
		selectObjectSortName();

		lightbox.open("#selectObjectId"); // and off we go

		return false;
	}

	// Fill the select box as given in PHP Array
	function selectObjectSortName() {
		console.log("selectObjectSortName");
		$("#allItemIds").empty().append(allItemsOptions(allItemsArr));
		return false;
	}

	// Fill the select box by descending timestamp within each item type
	function selectObjectSortTimestamp() {
		console.log("selectObjectSortTimestamp");
		var tmparr=allItemsArr.slice();
		tmparr.sort(function(a,b) {
			var catdiff=a[2]-b[2];
			return ( catdiff==0 ? (b[4]-a[4]) : catdiff );
		});
		$("#allItemIds").empty().append(allItemsOptions(tmparr));
		return false;
	}

	function allItemsOptions(allItemsArr_loc) {
		console.log("allItemsOptions");

		var opencat=false;
		var lastcat=-1;

		var result="<option value=''>"+selectBelowTxt+"</option>";
		$.each(allItemsArr_loc, function (itemIndex, item) { // all items

				if ( (opencat) && (lastcat!=item[2]) ) { // open group, but new category title?
					result+="</optgroup>";
					opencat=false;
				}
				if (!opencat) { // no currently open group?
					var groupname=( item[3]!='0' ? item[3] : nATxt);
					result+="<optgroup label='"+itemTypeTxt+" \""+groupname+"\"'>";
					opencat=true;
				}
				lastcat=Number(item[2]);
				result+="<option value='"+item[0]+"'>"+item[1]+"</option>";

			} );
		result+="</optgroup>";
		return result;
	}

	function allItemIdsChange() {
		console.log("allItemIdsChange - "+this.value);
		$(my_input).val(this.value);
		lightbox.close();
		$("#selectObjectId").css("background-color","yellow");
		return false;
	}

} );
