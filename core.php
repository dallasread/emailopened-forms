<?php

	include("widget.php");

	// add_action('wp_head', 'emailopened_public_tag');
	add_action( 'wp_enqueue_scripts', 'eo_public_scripts' );

	// function emailopened_public_tag() {
	// 	echo '<script type="text/javascript" src="'.EO_FORMS_URL.'assets/js/cp.js"></script>';
	// }
	
	function eo_public_scripts() {
		wp_enqueue_script('eo-forms', plugins_url( '/emailopened-forms/assets/js/eo-forms.js' ), array( 'jquery' ));
		wp_enqueue_style('eo-forms', plugins_url( '/emailopened-forms/assets/css/eo-forms.css' ));
	}

?>