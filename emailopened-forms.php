<?php
/*
Plugin Name: Emailopened Forms
Description: Add subscription forms from EmailOpened
Version: 1.0
Author: Janith Peduruge
Author URI: http://emailopened.com/
Plugin URI: http://emailopened.com/emailopened-forms
*/

define('EO_FORMS_DIR', plugin_dir_path(__FILE__));
define('EO_FORMS_URL', plugin_dir_url(__FILE__));
// define('EO_URL', 'http://localhost:3000');
define('EO_URL', 'https://app.emailopened.com');


function eo_forms_load(){
		
    if(is_admin()) //load admin files only in admin
        require_once(EO_FORMS_DIR.'admin.php');
        
    require_once(EO_FORMS_DIR.'core.php');
}

eo_forms_load();

register_activation_hook(__FILE__, 'eo_forms_activation');
register_deactivation_hook(__FILE__, 'eo_forms_deactivation');

function eo_forms_activation() {
    
	//actions to perform once on plugin activation go here 
	
	//register uninstaller
	register_uninstall_hook(__FILE__, 'oe_forms_uninstall');  
}

function eo_forms_deactivation() {    
	// actions to perform once on plugin deactivation go here	    
}

function eo_forms_uninstall(){
    
    //actions to perform once on plugin uninstall go here	    
}

function eo_generate_captcha($form_id) {
	$captcha_imgs = array("bird", "cat", "dog", "fire", "fish", "flower", "key", "scissors", "snail", "umbrella");
	$random_img_keys = array_rand($captcha_imgs, 3);
	
	$captcha = '<div class="captcha">
		<input type="hidden" name="wp" value="1">
		<input type="hidden name="verifier" value="@initial">
		<input type="hidden name="verified" value="encrpytor.encrypt_and_sign(correct)">
		<p>To verify that you are human, please select the <b>#{correct.capitalize}</b>.</p>
		<div class="captcha_imgs">
	';
	
	foreach ($random_img_keys as $key) {
		$img_url = EO_URL . "/webforms/$form_id/imgs/" . $captcha_imgs[$key];
		$captcha .= "$img_url" . "<br>" . "<br>";
	}
		
	$captcha .= '</div></div>';
	
	return $captcha;
}

?>