"use strict";

function install() {
	try {
		var home = document.getElementById('home').value;
		window.external.AddSearchProvider('generate_opensearch.php?home=' + home);
	} catch (e) {
		alert("You need to use Internet Explorer, Firefox, Chrome to install the OpenSearch plug-in.");
	}
}
