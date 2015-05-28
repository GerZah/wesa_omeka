jQuery(document).ready(function () {

	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block
	var lightbox = lity(); // Lity lightbox for item selector "popup"

	var my_input = null; // Reference to currently active input
	var allItemsSortedArr = null; // Room for a pre-sorted array
	var itemTypes = null; // Room for the pre-calculated item types
	var filterItemTyp = null; // Room for filter by type
	var curListOrder = null; // Room for sort order
	var curFilter = null; // Room for filter word

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

		filterItemTyp = -1; // Default: Don't filter item type for selector
		curListOrder = "timestamp"; // Default: Sort by timestamp
		curFilter = ""; // Default: no filter

		itemTypeSelect="<select id='itemTypeIds' size='1'>"+"</select>"; // Fill all item types
		allItemsSelect="<select id='allItemIds' size='12'>"+"</select>"; // Fill all items selector

		$("#lightboxJsContent").empty().append("<div id='selectObjectId'>"+ // Generate lightbox content
																						"<p><strong>"+itemTypesTxt+":</strong> "+itemTypeSelect+"</p>"+
																						"<p><strong>"+sortWithinItemTypeByTxt+":</strong><br>"+
																						"<fieldset>"+
																						"<input type='radio' name='itemsListSort' id='selectObjectSortTimestamp' value='timestamp' checked>"+
																									"<label for='selectObjectSortTimestamp'>"+updDateDescTxt+"</label> "+
																						"<input type='radio' name='itemsListSort' id='selectObjectSortName' value='name'>"+
																									"<label for='selectObjectSortName'>"+nameAscTxt+"</label>"+
																						"</fieldset>"+
																						"</p>"+
																						"<p>"+allItemsSelect+"</p>"+
																						"<p>"+filterTxt+": <input type='text' id='filterText' size='24' maxlength='24'>"+"</p>"+
																						"</div>")

		$("#itemTypeIds").change(itemTypeIdsChange);
		$("#allItemIds").change(allItemIdsChange);
		$("#filterText").on("input",filterTextChange);

		$('input:radio[name=itemsListSort]').change(function() {
				curListOrder=this.value;
				updatelist();
			});
		selectObjectSortTimestamp(); // fill item selector
		// finally fill item type selector, based on item selector pre-processing
		$("#itemTypeIds").empty().append(itemTypeOptions());

		lightbox.open("#selectObjectId"); // and off we go

		return false;
	}

	function updatelist() {
		switch (curListOrder) {
			case "timestamp" : selectObjectSortTimestamp(); break;
			case "name" : selectObjectSortName(); break;
		}
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
		if (allItemsSortedArr==null) { // no pre-sorted array yet
			console.log("sorting");
			allItemsSortedArr=allItemsArr.slice(); // correct operation to copy an array
			allItemsSortedArr.sort(function(a,b) { // sort it via compare function
				var catdiff=a[2]-b[2];  // different category? then sort asc for that
				return ( catdiff==0 ? (b[4]-a[4]) : catdiff ); // otherwise sort desc for timestamp
			});
		}
		$("#allItemIds").empty().append(allItemsOptions(allItemsSortedArr));
		return false;
	}

	// generate <option>...</option> tages corresponding the existing item types
	function itemTypeOptions() {
		console.log("itemTypeOptions");
		var result="<option value='-1'>"+allTxt+"</option>";
		$.each(itemTypes, function (itemIndex, item) { // all items
			if (typeof item != 'undefined') {
				result+="<option value='"+itemIndex+"'>"+item+"</option>";
			}
		});
		return result;
	}

	// generate all <option>...</option> tags matching the current filter / search / sort order
	function allItemsOptions(ItemsArr) {
		console.log("allItemsOptions");

		// +#+#+# limit list output according to curFilter

		var opencat=false;
		var lastcat=-1;

		var fillItemTypes=(itemTypes==null); // no itemTypes array yet?
		if (fillItemTypes) { itemTypes=new Array(); }

		var result="<option value=''>"+selectBelowTxt+"</option>";
		$.each(ItemsArr, function (itemIndex, item) { // all items

			var showThisItem=true;

			// item type filter defined? Is current item of this type or not?
			if ( (filterItemTyp>=0) && (item[2]-filterItemTyp!=0) ) { showThisItem=false; }

			if (showThisItem) {
				if ( (opencat) && (lastcat!=item[2]) ) { // open group, but new category title?
					result+="</optgroup>";
					opencat=false;
				}
				if (!opencat) { // no currently open group?
					var groupname=( item[3]!='0' ? String(item[3]) : nATxt);
					if (fillItemTypes) { itemTypes[item[2]]=groupname; } // also store group name
					result+="<optgroup label='"+itemTypeTxt+" \""+groupname+"\"'>";
					opencat=true;
				}
				lastcat=Number(item[2]);
				result+="<option value='"+item[0]+"'>"+item[1]+"</option>";
			}

		});
		result+="</optgroup>";
		if (fillItemTypes) { console.log(itemTypes); }
		return result;
	}

	// select item type callback
	function itemTypeIdsChange() {
		console.log("itemTypeIdsChange - "+this.value);
		filterItemTyp=this.value;
		updatelist();
		return false;
	}

	// select item callback -> transfer selected item back into edit field and close
	function allItemIdsChange() {
		console.log("allItemIdsChange - "+this.value);
		$(my_input).val(this.value);
		lightbox.close();
		$("#selectObjectId").css("background-color","yellow");
		return false;
	}

	function filterTextChange() {
		console.log("filterTextChange - "+this.value);
		curFilter=this.value;
		updatelist(); // +#+#+# limit list output according to curFilter
	}

} );
