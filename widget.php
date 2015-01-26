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
		$current_eoform = $instance['webform'];
		
		if ( ! empty( $current_eoform ) )
		{
			$before_widget = str_replace("class=\"", "class=\"$instance[style] ", $before_widget);
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			
			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
			if ( !empty( $instance['desc'] ) ) { echo "<p class=\"eo_description\">" . $instance['desc'] . "</p>"; }
			
			$align = $instance['align'];
			$current_forms = get_option( 'eoforms' );
			
			foreach($current_forms as $eoform)
			{
				if ($eoform['id'] == $current_eoform)
				{
					$captcha = eo_generate_captcha( $instance, $eoform["id"] );
					$eoform["embed"] = str_replace("<form ", "<form class=\"eo-embedded-subscribe-form widget-content eo-align-$align\" ", $eoform["embed"]);
					$eoform["embed"] = str_replace('type="email"', 'type="text"', $eoform["embed"]);
					$eoform["embed"] = str_replace('</form>', '<div class="eo_response"></div></form>', $eoform["embed"]);
					$eoform["embed"] = str_replace('<input type="submit"', "$captcha<input type=\"submit\" class=\"btn btn-primary\" type=\"submit\"", $eoform["embed"]);
					echo $before_widget . $eoform["embed"] . $after_widget;
				}
			}
		}
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
				<b>You currently have no Forms.<br />Please check your API Key and build forms at <a href="' . EO_URL . '/forms/new" target="_blank">' . EO_URL . '/forms</a>!</b>
			</p>'; 
			
		
		echo '<p><label for="'.$this->get_field_name( 'align' ).'">'._e( 'Form Alignment:' ).'</label>';
			
		$alignments = array('Left', 'Center', 'Right', 'None');
		$align_field_name = $this->get_field_name( 'align' );
		
		foreach ($alignments as $alignment) {
			$checked = $instance['align'] == strtolower($alignment) ? " checked=\"checked\"" : "";
			echo "<input type=\"radio\" name=\"" . $align_field_name . "\" value=\"" . strtolower($alignment) . "\"$checked> $alignment &nbsp; ";
		}
		
		echo "</p>";
		
		echo '<p>
			<input type="checkbox" id="'.$this->get_field_id( 'captcha' ).'" name="'.$this->get_field_name( 'captcha' ).'" value="checked" '.$instance[ 'captcha' ].' >Use Captcha
		</p>';
		
		echo '<p><label for="'.$this->get_field_id( 'style' ).'">'._e( 'Custom style Class:' ).'</label>&nbsp;&nbsp;<input id="'.$this->get_field_id( 'style' ).'" name="'.$this->get_field_name( 'style' ).'" type="text" value="'.$instance[ 'style' ].'" /></p>';
		
	}

}


?>
