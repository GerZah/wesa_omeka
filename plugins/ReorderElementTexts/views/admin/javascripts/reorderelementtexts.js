var reorderElementTextsAlreadyDone = false;

jQuery(document).bind("omeka:elementformload", function() {
	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

	if (reorderElementTextsAlreadyDone) return;
	reorderElementTextsAlreadyDone = true;

	var url = location.href;
	var itemId = url.substr( url.lastIndexOf("/")+1 );

	if (!isNaN(itemId)) {

		var inputs = $(".field");

		inputs.each( function(input) {
			var inputBlocks = $(this).find(".inputs .input-block");
			var size = $(inputBlocks).size();

			if (size>1) {
				var button = $(this).find("input:submit").first();

				var idBearer = $(button).parents().eq(3);
				var rawID = $(idBearer).attr("id");

				if (typeof rawID !== "undefined") {
					var elementId = rawID.substr( rawID.lastIndexOf("-")+1 );

					var div = idBearer.children(":first");
					$(div).append("<a href="+reorderElementTextsUrl+"?item="+itemId+"&element="+elementId+
													" class='blue button'>"+reorderElementTestsButton+
													"</a>");
				}
			}
		} );

	}

} );
