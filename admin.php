<?php

/** Step 2 (from text above). */
add_action( 'admin_menu', 'emailopened_menu' );
add_action('admin_head', 'emailopened_tag');
add_action( 'wp_head' , 'emailopened_tag' );

/** Step 1. */
function emailopened_menu() {
	add_menu_page( 'EmailOpened Forms Options', 'EmailOpened', 'manage_options', 'emailopened', 'emailopened_options' );
}
function emailopened_tag() {
	echo '<link rel="stylesheet" href="'.EO_FORMS_URL.'assets/css/emailopened_shared.css">';
	echo '<script type="text/javascript" >
		function signup()
		{
			console.log(document.getElementById("sign_in"));
			document.getElementById("sign_in").style.display = "none";
			document.getElementById("sign_up").style.display = "block";
		}
		function signin()
		{
			document.getElementById("sign_up").style.display = "none";
			document.getElementById("sign_in").style.display = "block";
		}
		function eoinstructions()
		{
			document.getElementById("eoforms").style.display = "none";
			document.getElementById("instructions").style.display = "block";
		}
		function eoforms()
		{
			document.getElementById("instructions").style.display = "none";
			document.getElementById("eoforms").style.display = "block";
		}
		</script>';
}

function getPosts($url, $token) {
	$ch = curl_init($url);
	$headers = array(
	                    'Authorization: Token token="'.$token.'"');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
	$info = curl_exec($ch);
	curl_close($ch);
	return($info);
}

/** Step 3. */
function emailopened_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$eoforms = array();
	$eouser = "";

	$notice = "";
	$key_update = 1;

	if ($_POST && $_POST["emailopened"]["token"] != "" && $_POST["emailopened"]["token"] != NULL )
	{
		if (get_option( 'emailopened_token' ) == $_POST["emailopened"]["token"] && get_option( 'emailopened_token' ) != "" && get_option( 'emailopened_token' ) != NULL) {
			$notice = $notice . " Your API Key is <b>installed</b>! ";
		}
		else if (get_option( 'emailopened_token' ) != $_POST["emailopened"]["token"] && $_POST["emailopened"]["token"] != "" && $_POST["emailopened"]["token"] != NULL) {
			update_option( 'emailopened_token', $_POST["emailopened"]["token"] );
			$notice = $notice . " Your API Key is <b>installed</b>! ";
			$key_update = 1;
		}
		else
		{
			$notice = $notice . "<font color=\"#DF0101\">Please enter a valid <b>API Key</b>!</font>";
		}
		
	}
	else if (get_option( 'emailopened_token' ) != "" && get_option( 'emailopened_token' ) != NULL)
	{
		$notice = $notice . " Your API Key is <b>installed</b>! ";
	}
	
	
	if ($key_update == 1)
	{
		$page = getPosts("https://app.emailopened.com/api/forms", get_option( 'emailopened_token' ));
	
		if (strpos($page, "Access denied") && strpos($page, "company_id") === false)
		{
			$notice = "<font color=\"#DF0101\">Your token is <b>Invalid</b>!</font>";
		}
		else
		{
			$notice = $notice . "Your token is <b>Valid</b>! ";
			$page_info = json_decode($page, true);
			
			$counter = 0;
			foreach($page_info as $k => $section)
			{
				if ($counter == 0)
				{
					$looper = 0;
					foreach($section as $l => $form)
					{
						if ($form["publish"] == 1)
						{
							//print_r($form);
							//print_r("<br />****************<br />");
							$thisform = array();
							$thisform["name"] = $form["name"];
							$thisform["id"] = $form["token"]."eo";
							$thisform["embed"] = $form["embed"];
							$thisform["eourl"] = "https://app.emailopened.com/webforms/".$form["token"];
				
							$eoforms[$thisform["id"]] = $thisform;
							$looper++;
						}
					}
				}
				else
				{
					$eouser = $section["name"];
				}
				$counter++;
			}
			update_option( 'eoforms', $eoforms);
			update_option( 'eouser', $eouser);
		}
	}
	echo '<script language="JavaScript">
    function myLocation() {
	alert(document.frames[1].location);
       // alert(document.all.myFrame.contentWindow.location);
    }
</script>';
	echo '<div class="wrap">';
	echo '<div class="full_width dark_blue">

						<h1>&nbsp;&nbsp;
		<a href="http://www.emailopened.com" target="_blank">
			<img src="http://www.emailopened.com/wp-content/themes/emailopened/imgs/logo.png" alt="Email Opened">
		</a>
	</h1></div>';
	if ($notice != "") {
		echo '<div style="color: green; background: #C0F2C9; border: 1px solid green; border-radius: 5px; padding: 0px 10px; margin-bottom: 20px; ">'.$notice.'</div>';
	}
	echo '<h2>Welcome '.get_option('eouser').'!</h2>';
	echo '<br />
	<div style="width: 100%; poition: relative; margin: 0 auto;">
		<div style="width: 45%; float:left;">
			<div class="section" style="margin-top: 0; ">
				<div class="form" style="padding: 10px;">
					<div class="header dark_blue">
						<h4>API Key</h4><h5>We need your API Key to grab your Forms from our system.</h5>
					</div>
					<form action="admin.php?page=emailopened" method="post">';
					echo '<div class="field">
						<br /><label for="emailopened_token">API Key</label><input type="text" name="emailopened[token]" id="emailopened_token" value="'.get_option( 'emailopened_token' ).'" />
					</div>
					<div class="center" style="margin-top: 20px; ">
		<input type="submit" value="Submit" class="button-primary" />
					</div></form>
				</div>
			</div>
			<div style="padding: 10px; text-align: center;"><a href="javascript:eoinstructions();" class="button-primary">Instructions</a>   <a href="javascript:eoforms();" class="button-primary" >Current Forms</a></div>
			<div id="instructions" class="section" style="margin-top: 0; display: none;">
				<div class="form" style="padding: 10px;">
					<div class="header dark_blue">
							<h4>Instructions</h4>
						</div>
						<div style="text-align: left; margin-top: 3px;">
						<h3>EmailOpened Side</h3><hr>
						<div style="padding: 5px; border-style:solid; border-width:2px;">
							<ol>
								<li>Sign-up or Login to <a href="http://app.emailopened.com" target="_blank">http://app.EmailOpened.com</a></li>
								<li>First goto <b>"My Account"</b> in the top navigation bar and scroll to the bottom of the page to find your <b>API Key</b></li>
								<li>Copy that API Key to the above field and <b>"Submit"</b> to Link your EmailOpened to this site</li>
							</ol>
						</div><br /><div style="padding: 5px; border-style:solid; border-width:2px;">
							<ol>
								<li>Then goto <b>"Lists"</b> in the top navigation bar and create <b>"New List"</b></li>
								<li>Name the list appropriately and upload any initial contacts you want to add</li>
								<li>Click on <b>"Show Advanced Options"</b> above the "Save List" button</li>
								<li>Under "Fields" add any <b>new fields</b> you wish to have on your form by clicking <b>"Add Field"</b></li>
								<li>When done, click on <b>"Save List"</b> and goto <b>"Forms"</b> on the top navigation bar</li>
								<li>Click on <b>"New Form"</b> and choose the list you created under "Which list should signups be added to?"</li>
								<li>Then edit the form and add any fields from the fields you previously created to the form</li>
							</ol>
							<ul>
								<li><b>NOTE:</b> If you wish to change fields goto "Lists"-> click on the list you wish to edit -> click on "Edit List" on top-left sub-menu</li>
							</ul>
							</div>
						</div>
						<div style="text-align: left; margin-top: 3px;">
						<h3>Wordpress Side</h3><hr>
						<div style="padding: 5px; border-style:solid; border-width:2px;">
							<ol>
								<li>If you don\'t have your API Key yet, goto <b>"My Account"</b> in the top navigation bar and scroll to the bottom of the page to find your <b>API Key</b> and copy that API Key to the above field and <b>"Submit"</b> to Link your EmailOpened to this site</li>
							</ol>
						</div><br /><div style="padding: 5px; border-style:solid; border-width:2px;">
							<ol>
								<li>Goto your <b>"Widgets"</b> area and drag & drop the <b>"EmailOpened"</b> widget to any "Widget Area" and pick a form you have created and style by creating and adding a CSS class to Style Class field! </li>
							</ol>
							<ul>
								<li><b>NOTE:</b> If you wish to change the redirecting upon submitting the form, place the link to that redirect page on the last section of "Forms" page on EmailOpened side</li>
							</ul>
							</div>
						</div>

				</div>
			</div>
			<div id="eoforms" class="section" style="margin-top: 0; display: none;">
				<div class="form" style="padding: 10px;">
					<div class="header dark_blue">
							<h4>Your Published Forms</h4>
						</div>';
						$eoforms = get_option( 'eoforms' );
						if (count($eoforms) > 0)
						{
							echo '<div class="center" style="margin-top: 3px;"><ul>';
							foreach($eoforms as $eoform)
							{
								echo '<li><a class="button-secondary" href="'.$eoform["eourl"].'" target="_blank">'.$eoform["name"].'</a></li>';
							}
							echo '</ul></div>';
						}
						else
							echo '<div class="center" style="margin-top: 3px;">	
								You currently have no Forms.
							</div>';
					echo '</div>
			</div>
		</div>
		<div class="message_div" style="width: 45%; float: right; position: relative;">
			If you currently <b>don\'t have an ACCOUNT</b> with us, please <b>sign-up</b> using the form below <b>for FREE!</b>. Currently, our system is completely free. As early adopters signing up now will give you lot of benefits in the future!<br/><br />OR<br/><br />If you <b>have an ACCOUNT</b> with us, please <b>sign-in</b> and goto <b>the bottom of "My Account"</b> page in <b>our system</b> to get your API Key.<div style="padding: 10px; text-align: center;"><a href="javascript:signup();" class="button-primary">Sign-up Free!</a>   <a href="javascript:signin();" class="button-primary" >Sign-in</a></div>';
	//echo '<iframe id="myFrame" src="http://staging.emailopened.com/signin?iframe=true" style="width:100%; height:600px;"></iframe><br><a href="#" onclick="javascript:myLocation();">Get iframe location.</a> ';
	//echo '</div>';
	?>
	
	<div id="sign_up" class="section" style="margin-top: 0; display: none;">
		<div class="form" style="padding: 10px;">
			<div class="header dark_blue">
					<h4>Sign-up with EmailOpened</h4>
				</div>
			<form accept-charset="UTF-8" action="https://app.emailopened.com/users" class="new_user" id="new_user" method="post" target="_blank">

				<input name="utf8" type="hidden" value="&#x2713;" />
				<input id="user_referring_url" name="user[referring_url]" type="hidden" value="http://www.emailopened.com/landing2" />
				<input id="user_referral_token" name="user[referral_token]" type="hidden" value="landing2" />
				<br />
				<div class="field">
					<label for="user_name">Name</label>
					<input id="user_name" name="user[name]" type="text" />
				</div>

				<div class="field">
					<label for="user_email">Email</label>
					<input id="user_email" name="user[email]" type="text" />
				</div>

				<div class="field">
					<label for="user_password">Password</label>
					<span style="font-size: 9px; color: #aaa; float: right; margin-top: -10px; ">8 characters long (include at least 1 uppercase, 1 number, and 1 symbol)</span>
					<input id="user_password" name="user[password]" type="password" />
				</div>

				<div class="field">
					<label for="password_confirmation">Please Confirm Your Password</label>
					<input id="user_password_confirmation" name="user[password_confirmation]" type="password" />
				</div>

				<div class="center" style="margin-top: 20px; ">	
					<input class="button-primary" name="commit" type="submit" value="Sign up" />
				</div>

		  </form>
		</div>
	</div>
	<div id="sign_in" class="section" style="margin-top: 0; display: none;">
		<div class="form" style="padding: 10px;">
			<div class="header dark_blue">
					<h4>Sign-in to EmailOpened</h4>
				</div>
			<form accept-charset="UTF-8" action="https://app.emailopened.com/sessions" class="new_session" id="new_session" method="post" novalidate="novalidate" target="_blank">
				<br />
				<div class="field">
					<label for="email">Email</label>
					<input id="email" name="email" type="text" class="valid" />
				</div>

				<div class="field">
					<label for="password">Password</label>
					<input id="password" name="password" type="password" />
				</div>

				<div class="center" style="margin-top: 20px; ">	
					<input class="button-primary" name="commit" type="submit" value="Sign in" />
				</div>

		  </form>
		</div>
	</div>
	
</div>
<div style="clear: both;"></div>
</div>
<?php
}

?>