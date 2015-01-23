<?php

	header('Content-type: image/png');
	header('Content-Disposition: inline');

	function base64_url_decode($input)
	{
	    return base64_decode(strtr($input, '-_,', '+/='));
	}

	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once( $parse_uri[0] . 'wp-load.php' );

	$img_hash = base64_url_decode($_REQUEST["img_hash"]);
	
	$api_key = get_option( 'emailopened_token' );
	$iv = sha1($api_key);
	$img = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $api_key, $img_hash, MCRYPT_MODE_CBC), "\0");

	echo file_get_contents("imgs/$img.png");
?>