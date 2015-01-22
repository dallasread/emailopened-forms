<?php
include("widget.php");
add_action('wp_head', 'emailopened_public_tag');
function emailopened_public_tag() {
	echo '<script type="text/javascript" src="'.EO_FORMS_URL.'assets/js/cp.js"></script>';
}


?>