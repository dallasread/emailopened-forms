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
define('EO_SUBDOMAIN', 'staging');
define('EO_PROTOCOL', EO_SUBDOMAIN == 'app' ? 'https://' : 'http://');


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

?>