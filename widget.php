<?php
/**
 * Adds Foo_Widget widget.
 */
add_action( 'widgets_init', 'eo_widgets' );

function eo_widgets() {
	register_widget( 'EmailOpened_Widget' );
}
// register Foo_Widget widget



class EmailOpened_Widget extends WP_Widget {
	
	function EmailOpened_Widget()
	{
		parent::__construct( false, 'EmailOpened' );
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		if ( !empty( $instance['desc'] ) ) { echo $instance['desc']; }
		$current_eoform = $instance['webform'];
		
		if ( ! empty( $current_eoform ) )
		{
			$current_forms = get_option( 'eoforms' );
			
			foreach($current_forms as $eoform)
			{
				if ($eoform['id'] == $current_eoform)
				{
					//Captcha
					if ( !empty( $instance['captcha'] ) && $instance['captcha'] == "checked" ) {
						preg_match('/<input type="submit" value="(.*)">/', $eoform["embed"], $button);
						$eoform["embed"] = preg_replace('/<input type="submit" value="(.*)">/', '<div id="cap_text" style="padding: 5px;"><label for="button_replace">Human Check *</label><script type="text/javascript">DrawBotBoot()</script></div><div id="button_replace" style="padding: 5px;"><button type="button" onClick="javascript:ValidBotBoot(\''.$button[1].'\', this)">'.$instance['vali'].'</button></div>', $eoform["embed"]);
					}
					
					// $eoform["embed"] = str_replace("<form", '<form target="'.$instance['redirect'].'"', $eoform["embed"]);
					// $eoform["embed"] = str_replace("<label", '<label style="display: block;"', $eoform["embed"]);
					// $eoform["embed"] = str_replace('<div class="field"', '<div style="padding: 5px;"', $eoform["embed"]);
					
					$alignment = "";
					$style = "";
					if ( !empty( $instance['align'] ) ) { $alignment = 'text-align: '.$instance['align'].';'; }
					if ( !empty( $instance['style'] ) ) { $style = 'class = "'.$instance['style'].'"'; }
					$eoform["embed"] = str_replace('<form', '<div '.$style.' style="'.$alignment.'"><form', $eoform["embed"]); 
					echo $eoform["embed"];
				}
					
				
			}
		}
		else
			echo "EmailOpened form is missing.";
		echo $after_widget;
	}


	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['desc'] = strip_tags( $new_instance['desc'] );
		$instance['webform'] = strip_tags( $new_instance['webform'] );
		$instance['align'] = strip_tags( $new_instance['align'] );
		$instance['style'] = strip_tags( $new_instance['style'] );
		$instance['captcha'] = strip_tags( $new_instance['captcha'] );
		$instance['redirect'] = strip_tags( $new_instance['redirect'] );
		$instance['vali'] = strip_tags( $new_instance['vali'] );

		return $instance;
	}


	function form( $instance ) {
		$defaults = array( 'title' => __('EO Form', 'eof'), 'webform' => __('', 'eof'), 'desc' => __('', 'eof'), 'align' => __('', 'eof'), 'style' => __('', 'eof'), 'captcha' => __('', 'eof'), 'redirect' => __('_blank', 'eof'), 'vali' => __('Validate', 'eof') );  
		$instance = wp_parse_args( (array) $instance, $defaults );
		//print_r($instance);
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance[ 'title' ] ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'desc' ); ?>"><?php _e( 'Description:' ); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'desc' ); ?>" height="4" name="<?php echo $this->get_field_name( 'desc' ); ?>"><?php echo $instance[ 'desc' ] ?></textarea>
		</p>
		<hr style="width: 75%; align: center;">
		<?php
		$current_forms = get_option( 'eoforms' );
		
		if (isset($current_forms) && count($current_forms) > 0)
		{
			echo '<p><label for="'.$this->get_field_id( 'webform' ).'">'._e( 'Choose a Form:' ).'</label><select id='.$this->get_field_id( 'webform' ).' name='.$this->get_field_name( 'webform' ).'>';
			foreach($current_forms as $eoform)
			{
				echo '<option id="'.$eoform["id"].'" name="'.$eoform["id"].'" value="'.$eoform["id"].'"';
				if ($instance[ 'webform' ] == $eoform["id"])
					echo " selected";
				echo '>'.$eoform["name"].'</option>';
				
			}
			echo '</select></p>';
		}
		else
			echo '<p class="center" style="margin-top: 20px; color: red; border: 1px solid red;">	
				<b>You currently have no Forms.<br />Please check your API Key and build forms at <a href="' . EO_PROTOCOL . EO_SUBDOMAIN . '.emailopened.com/forms/new" target="_blank">' . EO_SUBDOMAIN . '.emailopened.com/forms</a>!</b>
			</p>'; 
		
		echo '<p><label for="'.$this->get_field_name( 'align' ).'">'._e( 'Form Alignment:' ).'</label>&nbsp;&nbsp;<input type="radio" name='.$this->get_field_name( 'align' ).' value="left"';
		if ($instance[ 'align' ] == "left")
			echo " checked ";
		echo '>Left <input type="radio" name='.$this->get_field_name( 'align' ).' value="center"';
		if ($instance[ 'align' ] == "center")
			echo " checked ";
		echo '>Center <input type="radio" name='.$this->get_field_name( 'align' ).' value="right"';
		if ($instance[ 'align' ] == "right")
			echo " checked ";
		echo '>Right</p>';
		
		echo '<hr style="width: 75%; align: center;">';
		
		echo '<p><input type="checkbox" id="'.$this->get_field_id( 'captcha' ).'" name="'.$this->get_field_name( 'captcha' ).'" value="checked" '.$instance[ 'captcha' ].' >Use Captcha</p>';
		
		echo '<p><label for="'.$this->get_field_name( 'vali' ).'">'._e( 'Captcha "Validate" button text:' ).'</label><input id="'.$this->get_field_id( 'vali' ).'" name="'.$this->get_field_name( 'vali' ).'" type="text" value="'.$instance[ 'vali' ].'" /></p>';
		
		echo '<hr style="width: 75%; align: center;">';
			
		echo '<p"><label for="'.$this->get_field_name( 'redirect' ).'">'._e( 'Where to Redirect:' ).'</label>&nbsp;&nbsp;<input type="radio" name='.$this->get_field_name( 'redirect' ).' value="_blank"';
		if ($instance[ 'redirect' ] == "_blank")
			echo " checked ";
		echo '>_blank <input type="radio" name='.$this->get_field_name( 'redirect' ).' value="_self"';
		if ($instance[ 'redirect' ] == "_self")
			echo " checked ";
		echo '>_self</p>';
		
		echo '<p><label for="'.$this->get_field_id( 'style' ).'">'._e( 'Custom style Class:' ).'</label>&nbsp;&nbsp;<input id="'.$this->get_field_id( 'style' ).'" name="'.$this->get_field_name( 'style' ).'" type="text" value="'.$instance[ 'style' ].'" /></p>';
		
	}

}


?>
