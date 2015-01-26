<?php
/*
Plugin Name: Emailopened Forms
Description: Add subscription forms from EmailOpened
Version: 1.0
Author: Janith Peduruge
Author URI: http://emailopened.com/
Plugin URI: http://emailopened.com/emailopened-forms
*/

// ini_set('display_startup_errors',1);
// ini_set('display_errors',1);
// error_reporting(-1);

define('EO_FORMS_DIR', plugin_dir_path(__FILE__));
define('EO_FORMS_URL', plugin_dir_url(__FILE__));
// define('EO_URL', 'http://localhost:3000');
// define('EO_URL', 'https://app.emailopened.com');
define('EO_URL', 'http://staging.emailopened.com');

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

function base64_url_encode($input)
{
    return strtr(base64_encode($input), '+/=', '-_,');
}

function eo_generate_captcha($instance, $form_id) {
	$captcha_imgs_string = "";
	$captcha_imgs = array("bird", "cat", "dog", "fire", "fish", "flower", "key", "scissors", "snail", "umbrella");
	$random_img_keys = array_rand($captcha_imgs, 3);
	$selected_key = rand(0, 2);
	$api_key = substr(get_option( 'emailopened_token' ), 0, 32);
	$iv = substr(md5($api_key), 0, 16);
	$selected_word = "";
	
	foreach ($random_img_keys as $index => $key) {
		$img_key = $captcha_imgs[$key];
		$img_hash = base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $api_key, $img_key, MCRYPT_MODE_CBC, $iv));
		$img_url = plugins_url( "/emailopened-forms/captcha/img.php?img_hash=$img_hash" );
		$captcha_imgs_string .= "<img src=\"$img_url\">";

		if ($index == $selected_key) {
			$selected_word = $img_key;
			$selected_hash = base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $api_key, "\x00" . $img_key, MCRYPT_MODE_CBC, $iv));
			$selected_hash_again = base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $api_key, "\x00\x00" . $img_key, MCRYPT_MODE_CBC, $iv));
		}
	}
	
	if ($instance["captcha"]) {
		$captcha = "<div class=\"captcha\">
			<p>To verify that you are human, please select the <b>$selected_word</b>.</p>
			<div class=\"captcha_imgs\">
				$captcha_imgs_string
				<input type=\"hidden\" name=\"verified\" value=\"$selected_hash\">
				<input type=\"hidden\" name=\"verifier\" value=\"\">
				<div class=\"eo_clear\"></div>
			</div>
		</div>";
	}	else {
		$captcha = "<input type=\"hidden\" name=\"verified\" value=\"$selected_hash\">
			<input type=\"hidden\" name=\"verifier\" value=\"$selected_hash_again\">";
	}
	
	return $captcha;
}

?>