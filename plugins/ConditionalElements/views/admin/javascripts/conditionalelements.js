jQuery(document).bind("omeka:elementformload", function() {

	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

	// ------------------------------------------
	// An array of dependencies:
	// Each dependency is represented by a "dependent", a "term", and a "dependee".
	// ... meaning: If and only if the "dependent"'s value equals the "term", the "dependee" will be visible.
	var dependencies=[
		["54", "- Anderer -", "60"], // Example: Only if element 54 contains "- Anderer -", field 60 will become visible.
	];
	// ... Ultimately, this array should be created and populated within ConditionalElementsPlugin.php inside the
	// hookAdminHead() hook, right before including this script.
	// ------------------------------------------

	$.each(dependencies, function(i, dependency) {
		establishDependency(dependency[0], dependency[1], dependency[2]);
	});

	function establishDependency(dependent, showTerm, dependee) {
		showHideDependency(dependent, showTerm, dependee);
		$("#element-"+dependent+" select").change(function() { showHideDependency(dependent, showTerm, dependee); });
	}

	function showHideDependency(dependent, showTerm, dependee) {
		if (dependent && showTerm && dependee) {
			var hideAndEmptyDependee = true;
			$("#element-"+dependent+" select").each(function(index) {
				var val = $(this).val();
				if (val == showTerm) { hideAndEmptyDependee = false; }
			});
			if (hideAndEmptyDependee) {
				$("#element-"+dependee+" textarea").each(function(index) { $(this).val(""); });
				$("#element-"+dependee).hide(200);
			}
			else { $("#element-"+dependee).show(200); }
		}
	}

});
