<?php

class TM_Builder_Module_Team_Member extends TM_Builder_Module {

	static $social_fields = null;
	static $socials = null;

	function init() {

		$this->name             = esc_html__( 'Person', 'tm_builder' );
		$this->icon             = 'f183';
		$this->slug             = 'tm_pb_team_member';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		// Init social fields
		$this->get_socials();

		$this->whitelisted_fields = array_merge(
			array(
				'name',
				'position',
				'custom_url',
				'image_url',
				'animation',
				'background_layout',
				'text_alignment',
				'content_new',
				'admin_label',
				'module_id',
				'module_class',
				'icon_color',
				'icon_hover_color',
				'icon_bg_color',
				'icon_bg_hover_color',
			),
			$this->get_social_field_names()
		);

		$this->fields_defaults = array(
			'animation'         => array( 'off' ),
			'background_layout' => array( 'light' ),
			'text_alignment'    => array( 'left' ),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h4",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} *",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);
		$this->custom_css_options = array(
			'member_image' => array(
				'label'    => esc_html__( 'Member Image', 'tm_builder' ),
				'selector' => '.tm_pb_team_member_image',
			),
			'member_description' => array(
				'label'    => esc_html__( 'Member Description', 'tm_builder' ),
				'selector' => '.tm_pb_team_member_description',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'tm_builder' ),
				'selector' => '.tm_pb_team_member_description h4',
			),
			'member_position' => array(
				'label'    => esc_html__( 'Member Position', 'tm_builder' ),
				'selector' => '.tm_pb_member_position',
			),
			'member_social_links' => array(
				'label'    => esc_html__( 'Member Social Links', 'tm_builder' ),
				'selector' => '.tm_pb_member_social_links',
			),
		);
	}

	function get_fields() {

		$fields_top = array(
			'name' => array(
				'label'           => esc_html__( 'Name', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the person', 'tm_builder' ),
			),
			'position' => array(
				'label'           => esc_html__( 'Position', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( "Input the person's position.", 'tm_builder' ),
			),
			'custom_url' => array(
				'label'           => esc_html__( 'Custom URL', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( "Custom URL to the person's page.", 'tm_builder' ),
			),
			'image_url' => array(
				'label'              => esc_html__( 'Image URL', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'tm_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'tm_builder' ),
			),
			'animation' => array(
				'label'             => esc_html__( 'Animation', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off'     => esc_html__( 'No Animation', 'tm_builder' ),
					'fade_in' => esc_html__( 'Fade In', 'tm_builder' ),
					'left'    => esc_html__( 'Left To Right', 'tm_builder' ),
					'right'   => esc_html__( 'Right To Left', 'tm_builder' ),
					'top'     => esc_html__( 'Top To Bottom', 'tm_builder' ),
					'bottom'  => esc_html__( 'Bottom To Top', 'tm_builder' ),
				),
				'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'tm_builder' ),
			),
			'text_alignment' => array(
				'label'           => esc_html__( 'Text alignment', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'left'   => esc_html__( 'Left', 'tm_builder' ),
					'center' => esc_html__( 'Center', 'tm_builder' ),
					'right'  => esc_html__( 'Right', 'tm_builder' ),
				),
				'description'     => esc_html__( 'Here you can define the alignemnt of text', 'tm_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'           => array(
					'light' => esc_html__( 'Dark', 'tm_builder' ),
					'dark'  => esc_html__( 'Light', 'tm_builder' ),
				),
				'description' => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'tm_builder' ),
			),
		);

		$fields_social = array();

		foreach ( $this->get_socials() as $net => $data ) {
			$fields_social[ $net . '_url' ] = array(
				'label'           => sprintf( esc_html__( '%s Profile Url', 'tm_builder' ), $data['label'] ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => sprintf( esc_html__( 'Input %s Profile Url.', 'tm_builder' ), $data['label'] ),
			);
		}

		$fields_bottom = array(
			'content_new' => array(
				'label'           => esc_html__( 'Description', 'tm_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'tm_builder' ),
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'tm_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'icon_bg_color' => array(
				'label'             => esc_html__( 'Icon Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'icon_hover_color' => array(
				'label'             => esc_html__( 'Icon Hover Color', 'tm_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'icon_bg_hover_color' => array(
				'label'             => esc_html__( 'Icon Hover Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'tm_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => tm_pb_media_breakpoints(),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'tm_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'tm_builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
		);

		return array_merge( $fields_top, $fields_social, $fields_bottom );
	}

	/**
	 * Set social networks array
	 */
	public function get_socials() {

		if ( null !== self::$socials ) {
			return self::$socials;
		}

		/**
		 * Filter available socials list array.
		 *
		 * @param  array Allowed socials list.
		 */
		self::$socials = apply_filters(
			'tm_builder_person_socials_list',
			array(
				'facebook' => array(
					'label' => esc_html__( 'Facebook', 'tm_builder' ),
					'icon'  => 'f09a',
				),
				'twitter'  => array(
					'label' => esc_html__( 'Twitter', 'tm_builder' ),
					'icon'  => 'f099',
				),
				'google'   => array(
					'label' => esc_html__( 'Google+', 'tm_builder' ),
					'icon'  => 'f0d5',
				),
				'linkedin' => array(
					'label' => esc_html__( 'LinkedIn', 'tm_builder' ),
					'icon'  => 'f0e1',
				),
			)
		);

		return self::$socials;
	}

	/**
	 * Returns attribute names array for socials
	 *
	 * @return array
	 */
	public function get_social_field_names() {

		if ( null !== self::$social_fields ) {
			return self::$social_fields;
		}

		self::$social_fields = array_keys( $this->get_socials() );
		array_walk( self::$social_fields, array( $this, 'social_fields_cb' ) );

		return self::$social_fields;
	}

	/**
	 * Callback funcion for get_social_field_names walker
	 *
	 * @param  string &$social Social slug.
	 */
	public function social_fields_cb( &$social ) {
		$social .= '_url';
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array_merge(
				array(
					'name',
					'position',
					'custom_url',
					'image_url',
					'animation',
					'background_layout',
					'text_alignment',
					'icon_color',
					'icon_hover_color',
					'icon_bg_color',
					'icon_bg_hover_color',
				),
				$this->get_social_field_names()
			)
		);

		$image = $social_links = '';

		if ( '' !== $this->_var( 'icon_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_member_social_links a',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->_var( 'icon_color' ) )
				),
			) );
		}

		if ( '' !== $this->_var( 'icon_bg_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_member_social_links a',
				'declaration' => sprintf(
					'background: %1$s;',
					esc_html( $this->_var( 'icon_bg_color' ) )
				),
			) );
		}

		if ( '' !== $this->_var( 'icon_hover_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_member_social_links a:hover',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->_var( 'icon_hover_color' ) )
				),
			) );
		}

		if ( '' !== $this->_var( 'icon_bg_hover_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_member_social_links a:hover',
				'declaration' => sprintf(
					'background: %1$s;',
					esc_html( $this->_var( 'icon_bg_hover_color' ) )
				),
			) );
		}

		TM_Builder_Element::set_style( $function_name, array(
			'selector'    => '%%order_class%% .tm_pb_team_member_description',
			'declaration' => sprintf(
				'text-align: %1$s;', esc_html( $this->_var( 'text_alignment' ) )
			),
		) );

		foreach ( $this->get_socials() as $social => $data ) {

			if ( ! $this->_var( $social . '_url' ) ) {
				continue;
			}

			$this->_var( 'current_social', esc_attr( $social ) );
			$this->_var( 'current_url', esc_url( $this->_var( $social . '_url' ) ) );
			$this->_var( 'current_label', $data['label'] );
			$this->_var( 'current_icon', tm_pb_process_font_icon( $data['icon'] ) );

			$social_links .= $this->get_template_part( 'person-social.php' );
		}

		if ( '' !== $social_links ) {
			$social_links = sprintf( '<ul class="tm_pb_member_social_links">%1$s</ul>', $social_links );
		}

		if ( '' !== $this->_var( 'image_url' ) ) {
			$image = sprintf(
				'<div class="tm_pb_team_member_image et-waypoint%3$s">
					<img src="%1$s" alt="%2$s" />
				</div>',
				esc_url( $this->_var( 'image_url' ) ),
				esc_attr( $this->_var( 'name' ) ),
				esc_attr( ' tm_pb_animation_' . $this->_var( 'animation' ) )
			);
		}

		$this->_var( 'image', $image );
		$this->_var( 'social_links', $social_links );

		$classes = array(
			( '' === $image ? ' tm_pb_team_member_no_image' : '' ),
			'tm_pb_bg_layout_' . $this->_var( 'background_layout' ),
			'clearfix',
		);

		$content = $this->get_template_part( 'person.php' );

		$output = $this->wrap_module( $content, $classes, $function_name );

		return $output;
	}
}

new TM_Builder_Module_Team_Member;
