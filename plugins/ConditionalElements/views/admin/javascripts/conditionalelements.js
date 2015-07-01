jQuery(document).bind("omeka:elementformload", function() {

	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

	if (typeof conditionalElementsDep !== 'undefined') { 
		$.each(conditionalElementsDep, function(i, dependency) {
			establishDependency(dependency[0], dependency[1], dependency[2]);
		});
	}

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
				$("#element-"+dependee+" textarea").each(function(index) { $(this).val("").change(); });
				$("#element-"+dependee+" select").each(function(index) { $(this).val(null).change(); });
				$("#element-"+dependee).hide(200);
			}
			else { $("#element-"+dependee).show(200); }
		}
	}

});
