<?php
//Widget code starts from here
class BP_Birthday_Widget extends WP_Widget {

	/**
	 * Constructor method.
	 */
	public function __construct() {

		// Setup widget name & description.
		$name        = _x( 'BuddyPress Birthday', 'widget name', 'bp-birthday-greetings' );
		$description = __( 'A dynamic list of members having their bithdays on a date', 'bp-birthday-greetings' );

		// Call WP_Widget constructor.
		parent::__construct( false, $name, array(
			'description'                 => $description,
			'classname'                   => 'widget_bp_birthday_widget widget',
			'customize_selective_refresh' => true,
		) );
		
	}
	

	/**
	 * Display the widget.
	 *
	 * @since 1.0.2
	 *
	 * @see WP_Widget::widget() for description of parameters.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings, as saved by the user.
	 */
	public function widget( $args, $instance ) {

		// Get widget settings.
		$settings = $this->parse_settings( $instance );

		/**
		 * Filters the title of the Members widget.
		 * @param string $title    The widget title.
		 * @param array  $settings The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $settings['title'], $settings, $this->id_base );

		// Output before widget HTMl, title (and maybe content before & after it).
		echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
		global $wp, $bp, $wpdb;
		$bp_birthday_option_value = bp_get_option( 'bp-dob' );
		$sql = $wpdb->prepare( "SELECT profile.user_id, profile.value FROM {$bp->profile->table_name_data} profile INNER JOIN $wpdb->users users ON profile.user_id = users.id AND user_status != 1 WHERE profile.field_id = %d", $bp_birthday_option_value);
		$profileval = $wpdb->get_results($sql);
		foreach ($profileval as $profileobj) {
			$timeoffset = get_option('gmt_offset');
			if(!is_numeric($profileobj->value)) {
				$bday = strtotime($profileobj->value) + $timeoffset;
			}else {
				$bday = $profileobj->value + $timeoffset;
			}
			if ((date_i18n("n")==date("n",$bday))&&(date_i18n("j")==date("j",$bday)))
			$birthdays[] = $profileobj->user_id;
		}
		if(empty($birthdays)){
			$empty_message = apply_filters('bp_birthday_empty_message', __('No Birthdays Found Today.','bp-birthday-greetings'));
			echo $empty_message;
		}else{
			echo '<ul class="birthday-members-list">';
			foreach ($birthdays as $birthday => $members_id) {
				$member_name =  bp_core_get_user_displayname( $members_id );
				$btn = '';
				if ( bp_is_active( 'messages' ) ){
					$defaults = array(
						'id' => 'private_message-'.$members_id,
						'component' => 'messages',
						'must_be_logged_in' => true,
						'block_self' => true,
						'wrapper_id' => 'send-private-message-'.$members_id,
						'wrapper_class' =>'send-private-message',
						'link_href' => wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $members_id ) ),
						'link_title' => __( 'Send a private message to this user.', 'bp-birthday-greetings' ),
						'link_text' => __( 'Wish Happy Birthday', 'bp-birthday-greetings' ),
						'link_class' => 'send-message',
					);
					if( $members_id != bp_loggedin_user_id() ){
						$btn = bp_get_button( $defaults );
					}else{
						$btn='';
					}
				}
				$dp_width = bp_get_option( 'bp-dp-width' );
    			$dp_width = (empty($dp_width)) ? 32 : $dp_width;
				$dp_height = bp_get_option( 'bp-dp-height' );
    			$dp_height = (empty($dp_height)) ? 32 : $dp_height;
    			$dp_type = bp_get_option( 'bp-dp-type' );
    			$dp_type = (empty($dp_type)) ? 'thumb' : $dp_type;
    			$cake_img = apply_filters('bp_birthday_cake_img', '&#127874;');
				echo '<li>'.bp_core_fetch_avatar(array('item_id' => $members_id, 'type' => $dp_type, 'width' => $dp_width, 'height' => $dp_height, 'class' => 'avatar','html'=>true));
				_e('Happy Birthday','bp-birthday-greetings');
				echo ' '.$member_name.' '.$cake_img.'</li>';
				echo $btn;
			}
			echo '</ul>';
		}
		?>

		<?php echo $args['after_widget'];
		
	}

	/**
	 * Update the widget options.
	 *
	 * @since 1.0.2
	 *
	 * @param array $new_instance The new instance options.
	 * @param array $old_instance The old instance options.
	 * @return array $instance The parsed options to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']          = strip_tags( $new_instance['title'] );
		
		return $instance;
	}

	/**
	 * Output the widget options form.
	 *
	 * @since 1.0.2
	 *
	 * @param array $instance Widget instance settings.
	 * @return void
	 */
	public function form( $instance ) {
		// Get widget settings.
		$settings       = $this->parse_settings( $instance );
		$title          = strip_tags( $settings['title'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php esc_html_e( 'Title:', 'bp-birthday-greetings' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" />
			</label>
		</p>
	<?php
	}

	/**
	 * Merge the widget settings into defaults array.
	 *
	 * @since 1.0.2
	 *
	 *
	 * @param array $instance Widget instance settings.
	 * @return array
	 */
	public function parse_settings( $instance = array() ) {
		return bp_parse_args( $instance, array(
			'title' 	     => __( 'Member Birthdays', 'bp-birthday-greetings' ),
		), 'birthday_widget_settings' );
	}
}
add_action( 'widgets_init', function() {
	register_widget( 'BP_Birthday_Widget' );
},21 );