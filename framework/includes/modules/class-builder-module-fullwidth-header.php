<?php
class Tm_Builder_Module_Fullwidth_Header extends Tm_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Fullwidth Header', 'tm_builder' );
		$this->slug             = 'tm_pb_fullwidth_header';
		$this->fullwidth        = true;
		$this->main_css_element = '%%order_class%%';

		$this->whitelisted_fields = array(
			'title',
			'subhead',
			'background_layout',
			'text_orientation',
			'header_fullscreen',
			'header_scroll_down',
			'scroll_down_icon',
			'scroll_down_icon_color',
			'scroll_down_icon_size',
			'scroll_down_icon_size_tablet',
			'scroll_down_icon_size_phone',
			'title_font_color',
			'subhead_font_color',
			'content_font_color',
			'max_width',
			'max_width_tablet',
			'max_width_phone',
			'button_one_text',
			'button_one_url',
			'button_two_text',
			'button_two_url',
			'background_url',
			'background_color',
			'background_overlay_color',
			'parallax',
			'parallax_method',
			'logo_image_url',
			'logo_title',
			'logo_alt_text',
			'content_orientation',
			'header_image_url',
			'image_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'background_layout'   => array( 'light' ),
			'text_orientation'    => array( 'left' ),
			'header_fullscreen'   => array( 'off' ),
			'header_scroll_down'  => array( 'off' ),
			'scroll_down_icon'    => array( '%%3%%', 'add_default_setting' ),
			'parallax'            => array( 'off' ),
			'parallax_method'     => array( 'off' ),
			'content_orientation' => array( 'center' ),
			'image_orientation'   => array( 'center' ),
		);

		$this->options_toggles = array(
			'advanced' => array(
				'settings' => array(
					'toggles_disabled' => true,
				),
				'toggles' => array(
					'title_styles'   => esc_html__( 'Title Styling', 'tm_builder' ),
					'subhead_styles' => esc_html__( 'Subhead Styling', 'tm_builder' ),
					'content_styles' => esc_html__( 'Content Styling', 'tm_builder' ),
				),
			),
		);
		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'tm_builder' ),
					'css'      => array(
						'main' => "%%order_class%%.tm_pb_fullwidth_header .header-content h1",
					),
					'font_size' => array(
						'toggle_slug'  => 'title_styles',
						'default'      => '30px',
					),
					'font' => array(
						'toggle_slug'  => 'title_styles',
					),
					'hide_line_height'    => true,
					'hide_text_color'     => true,
					'hide_letter_spacing' => true,
				),
				'subhead' => array(
					'label'    => esc_html__( 'Subhead', 'tm_builder' ),
					'css'      => array(
						'main' => "%%order_class%%.tm_pb_fullwidth_header .tm_pb_fullwidth_header_subhead",
					),
					'font_size' => array(
						'toggle_slug'  => 'subhead_styles',
					),
					'font' => array(
						'toggle_slug'  => 'subhead_styles',
					),
					'hide_line_height'    => true,
					'hide_text_color'     => true,
					'hide_letter_spacing' => true,
				),
				'content' => array(
					'label'    => esc_html__( 'Content', 'tm_builder' ),
					'css'      => array(
						'main' => "%%order_class%%.tm_pb_fullwidth_header p",
					),
					'font_size' => array(
						'toggle_slug'  => 'content_styles',
						'default'      => '14px',
					),
					'font' => array(
						'toggle_slug'  => 'content_styles',
					),
					'hide_line_height'    => true,
					'hide_text_color'     => true,
					'hide_letter_spacing' => true,
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'tm_builder' ),
					'css'      => array(
						'main' => ".tm_pb_slider {$this->main_css_element}.tm_pb_slide .tm_pb_button",
					),
				),
			),

			'button' => array(
				'button_one' => array(
					'label' => esc_html__( 'Button One', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_button_one.tm_pb_button",
					),
				),
				'button_two' => array(
					'label' => esc_html__( 'Button Two', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_button_two.tm_pb_button",
					),
				),
			),
		);

		$this->custom_css_options = array(
			'header_container' => array(
				'label'    => esc_html__( 'Header Container', 'tm_builder' ),
				'selector' => '.tm_pb_fullwidth_header_container',
			),
			'header_image' => array(
				'label'    => esc_html__( 'Header Image', 'tm_builder' ),
				'selector' => '.tm_pb_fullwidth_header_container .header-image img',
			),
			'logo' => array(
				'label'    => esc_html__( 'Logo', 'tm_builder' ),
				'selector' => '.header-content img',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'tm_builder' ),
				'selector' => '.header-content h1',
			),
			'subtitle' => array(
				'label'    => esc_html__( 'Subtitle', 'tm_builder' ),
				'selector' => '.header-content .tm_pb_fullwidth_header_subhead',
			),
			'button_1' => array(
				'label'    => esc_html__( 'Button One', 'tm_builder' ),
				'selector' => '.header-content .tm_pb_button_one',
			),
			'button_2' => array(
				'label'    => esc_html__( 'Button Two', 'tm_builder' ),
				'selector' => '.header-content .tm_pb_button_two',
			),
			'scroll_button' => array(
				'label'    => esc_html__( 'Scroll Down Button', 'tm_builder' ),
				'selector' => '.tm_pb_fullwidth_header_scroll a .et-pb-icon',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter your page title here.', 'tm_builder' ),
			),
			'subhead' => array(
				'label'           => esc_html__( 'Subheading Text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to use a subhead, add it here. Your subhead will appear below your title in a small font.', 'tm_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'tm_builder' ),
					'dark'  => esc_html__( 'Light', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'tm_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text & Logo Orientation', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => tm_builder_get_text_orientation_options(),
				'description'       => esc_html__( 'This controls the how your text is aligned within the module.', 'tm_builder' ),
			),

			'header_fullscreen' => array(
				'label'           => esc_html__( 'Make Fullscreen', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_content_orientation',
				),
				'description'       => esc_html__( 'Here you can choose whether the header is expanded to fullscreen size.', 'tm_builder' ),
			),
			'header_scroll_down' => array(
				'label'           => esc_html__( 'Show Scroll Down Button', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_scroll_down_icon',
				),
				'description'       => esc_html__( 'Here you can choose whether the scroll down button is shown.', 'tm_builder' ),
			),
			'scroll_down_icon' => array(
				'label'               => esc_html__( 'Icon', 'tm_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'tm-pb-font-icon' ),
				'renderer'            => 'tm_pb_get_font_down_icon_list',
				'renderer_with_field' => true,
				'description'         => esc_html__( 'Choose an icon to display for the scroll down button.', 'tm_builder' ),
				'depends_show_if'     => 'on',
			),
			'scroll_down_icon_color' => array(
				'label'             => esc_html__( 'Scroll Down Icon Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'scroll_down_icon_size' => array(
				'label'           => esc_html__( 'Scroll Down Icon Size', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'mobile_options'  => true,
				'tab_slug'        => 'advanced',
			),
			'scroll_down_icon_size_tablet' => array(
				'type' => 'skip',
			),
			'scroll_down_icon_size_phone' => array(
				'type' => 'skip',
			),
			'title_font_color' => array(
				'label'             => esc_html__( 'Title Font Color', 'tm_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'title_styles',
			),
			'subhead_font_color' => array(
				'label'             => esc_html__( 'Subhead Font Color', 'tm_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'subhead_styles',
			),
			'content_font_color' => array(
				'label'             => esc_html__( 'Content Font Color', 'tm_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'content_styles',
			),
			'max_width' => array(
				'label'           => esc_html__( 'Text Max Width', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'max_width_tablet' => array(
				'type' => 'skip',
			),
			'max_width_phone' => array(
				'type' => 'skip',
			),
			'button_one_text' => array(
				'label'           => sprintf( esc_html__( 'Button %1$s Text', 'tm_builder' ), '#1' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the text for the Button.', 'tm_builder' ),
			),
			'button_one_url' => array(
				'label'           => sprintf( esc_html__( 'Button %1$s URL', 'tm_builder' ), '#1' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the URL for the Button.', 'tm_builder' ),
			),
			'button_two_text' => array(
				'label'           => sprintf( esc_html__( 'Button %1$s Text', 'tm_builder' ), '#2' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the text for the Button.', 'tm_builder' ),
			),
			'button_two_url' => array(
				'label'           => sprintf( esc_html__( 'Button %1$s URL', 'tm_builder' ), '#2' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the URL for the Button.', 'tm_builder' ),
			),
			'background_url' => array(
				'label'              => esc_html__( 'Background Image URL', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'tm_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'tm_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
			),
			'background_overlay_color' => array(
				'label'             => esc_html__( 'Background Overlay Color', 'tm_builder' ),
				'type'              => 'color-alpha',
			),
			'parallax' => array(
				'label'           => esc_html__( 'Use Parallax effect', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off'  => esc_html__( 'No', 'tm_builder' ),
					'on' => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_parallax_method',
				),
				'description'        => esc_html__( 'If enabled, your background images will have a fixed position as your scroll, creating a fun parallax-like effect.', 'tm_builder' ),
			),
			'parallax_method' => array(
				'label'           => esc_html__( 'Parallax method', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'CSS', 'tm_builder' ),
					'on'  => esc_html__( 'True Parallax', 'tm_builder' ),
				),
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Define the method, used for the parallax effect.', 'tm_builder' ),
			),

			'logo_image_url' => array(
				'label'              => esc_html__( 'Logo Image URL', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'tm_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'tm_builder' ),
			),
			'logo_alt_text' => array(
				'label'           => esc_html__( 'Logo Image Alternative Text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'tm_builder' ),
			),
			'logo_title' => array(
				'label'           => esc_html__( 'Logo Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the HTML Title text.', 'tm_builder' ),
			),
			'content_orientation' => array(
				'label'           => esc_html__( 'Text Vertical Alignment', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'center'  => esc_html__( 'Center', 'tm_builder' ),
					'bottom' => esc_html__( 'Bottom', 'tm_builder' ),
				),
				'description'        => esc_html__( 'This setting determines the vertical alignment of your content. Your content can either be vertically centered, or aligned to the bottom.', 'tm_builder' ),
				'depends_show_if'    => 'on',
			),

			'header_image_url' => array(
				'label'              => esc_html__( 'Header Image URL', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'tm_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'tm_builder' ),
			),
			'image_orientation' => array(
				'label'           => esc_html__( 'Image Vertical Alignment', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'center'  => esc_html__( 'Vertically Centered', 'tm_builder' ),
					'bottom' => esc_html__( 'Bottom', 'tm_builder' ),
				),
				'description'        => esc_html__( 'This controls the orientation of the image within the module.', 'tm_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'tm_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the infobox for the pin.', 'tm_builder' ),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'tm_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'tm_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'tm_builder' ),
					'desktop' => esc_html__( 'Desktop', 'tm_builder' ),
				),
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
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                    = $this->shortcode_atts['module_id'];
		$module_class                 = $this->shortcode_atts['module_class'];
		$title                        = $this->shortcode_atts['title'];
		$subhead                      = $this->shortcode_atts['subhead'];
		$background_layout            = $this->shortcode_atts['background_layout'];
		$text_orientation             = $this->shortcode_atts['text_orientation'];
		$title_font_color             = $this->shortcode_atts['title_font_color'];
		$subhead_font_color           = $this->shortcode_atts['subhead_font_color'];
		$content_font_color           = $this->shortcode_atts['content_font_color'];
		$button_one_text              = $this->shortcode_atts['button_one_text'];
		$button_one_url               = $this->shortcode_atts['button_one_url'];
		$button_two_text              = $this->shortcode_atts['button_two_text'];
		$button_two_url               = $this->shortcode_atts['button_two_url'];
		$header_fullscreen            = $this->shortcode_atts['header_fullscreen'];
		$header_scroll_down           = $this->shortcode_atts['header_scroll_down'];
		$scroll_down_icon             = $this->shortcode_atts['scroll_down_icon'];
		$scroll_down_icon_color       = $this->shortcode_atts['scroll_down_icon_color'];
		$scroll_down_icon_size        = $this->shortcode_atts['scroll_down_icon_size'];
		$scroll_down_icon_size_tablet = $this->shortcode_atts['scroll_down_icon_size_tablet'];
		$scroll_down_icon_size_phone  = $this->shortcode_atts['scroll_down_icon_size_phone'];
		$background_url               = $this->shortcode_atts['background_url'];
		$background_color             = $this->shortcode_atts['background_color'];
		$background_overlay_color     = $this->shortcode_atts['background_overlay_color'];
		$parallax                     = $this->shortcode_atts['parallax'];
		$parallax_method              = $this->shortcode_atts['parallax_method'];
		$logo_image_url               = $this->shortcode_atts['logo_image_url'];
		$header_image_url             = $this->shortcode_atts['header_image_url'];
		$content_orientation          = $this->shortcode_atts['content_orientation'];
		$image_orientation            = $this->shortcode_atts['image_orientation'];
		$custom_icon_1                = $this->shortcode_atts['button_one_icon'];
		$button_custom_1              = $this->shortcode_atts['custom_button_one'];
		$custom_icon_2                = $this->shortcode_atts['button_two_icon'];
		$button_custom_2              = $this->shortcode_atts['custom_button_two'];
		$max_width                    = $this->shortcode_atts['max_width'];
		$max_width_tablet             = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone              = $this->shortcode_atts['max_width_phone'];
		$logo_title                   = $this->shortcode_atts['logo_title'];
		$logo_alt_text                = $this->shortcode_atts['logo_alt_text'];

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_tablet,
				'phone'   => $max_width_phone,
			);


			$additional_css = ' !important;';

			tm_pb_generate_responsive_css( $max_width_values, '%%order_class%% .header-content', 'max-width', $function_name, $additional_css );
		}

		if ( '' !== $title_font_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_header .header-content h1',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $title_font_color )
				),
			) );
		}

		if ( '' !== $subhead_font_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_header .tm_pb_fullwidth_header_subhead',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $subhead_font_color )
				),
			) );
		}

		if ( '' !== $content_font_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_header p',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $content_font_color )
				),
			) );
		}

		if ( '' !== $scroll_down_icon_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_header .tm_pb_fullwidth_header_scroll a .et-pb-icon',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $scroll_down_icon_color )
				),
			) );
		}

		if ( '' !== $scroll_down_icon_size || '' !== $scroll_down_icon_size_tablet || '' !== $scroll_down_icon_size_phone ) {
			$icon_size_values = array(
				'desktop' => $scroll_down_icon_size,
				'tablet'  => $scroll_down_icon_size_tablet,
				'phone'   => $scroll_down_icon_size_phone,
			);

			tm_pb_generate_responsive_css( $icon_size_values, '%%order_class%%.tm_pb_fullwidth_header .tm_pb_fullwidth_header_scroll a .et-pb-icon', 'font-size', $function_name );
		}

		if ( '' !== $background_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_header',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_color )
				),
			) );
		}

		if ( '' !== $background_overlay_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_header .tm_pb_fullwidth_header_overlay',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_overlay_color )
				),
			) );
		}

		if ( '' !== $background_url && 'off' === $parallax ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_header',
				'declaration' => sprintf(
					'background-image: url(%1$s);',
					esc_url( $background_url )
				),
			) );
		}

		$button_output = '';
		if ( '' !== $button_one_text ) {

			$icon_1        = esc_attr( tm_pb_process_font_icon( $custom_icon_1 ) );
			$icon_family_1 = tm_builder_get_icon_family();

			if ( $icon_family_1 ) {
				TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_pb_custom_button_icon:before, %%order_class%% .tm_pb_custom_button_icon:after',
					'declaration' => sprintf(
						'font-family: "%1$s" !important;',
						esc_attr( $icon_family_1 )
					),
				) );
			}

			$button_output .= sprintf(
				'<a href="%2$s" class="tm_pb_more_button tm_pb_button tm_pb_button_one%4$s"%3$s>%1$s</a>',
				( '' !== $button_one_text ? esc_attr( $button_one_text ) : '' ),
				( '' !== $button_one_url ? esc_url( $button_one_url ) : '#' ),
				( '' !== $icon_1 && 'on' === $button_custom_1 ? sprintf( ' data-icon="%1$s"', $icon_1 ) : '' ),
				'' !== $custom_icon_1 && 'on' === $button_custom_1 ? ' tm_pb_custom_button_icon' : ''
			);
		}

		if ( '' !== $button_two_text ) {

			$icon_2        = esc_attr( tm_pb_process_font_icon( $custom_icon_2 ) );
			$icon_family_2 = tm_builder_get_icon_family();

			if ( $icon_family_2 ) {
				TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_pb_custom_button_icon:before, %%order_class%% .tm_pb_custom_button_icon:after',
					'declaration' => sprintf(
						'font-family: "%1$s" !important;',
						esc_attr( $icon_family_2 )
					),
				) );
			}

			$button_output .= sprintf(
				'<a href="%2$s" class="tm_pb_more_button tm_pb_button tm_pb_button_two%4$s"%3$s>%1$s</a>',
				( '' !== $button_two_text ? esc_attr( $button_two_text ) : '' ),
				( '' !== $button_two_url ? esc_url( $button_two_url ) : '#' ),
				( '' !== $icon_2 && 'on' === $button_custom_2 ? sprintf( ' data-icon="%1$s"', $icon_2 ) : '' ),
				'' !== $custom_icon_2 && 'on' === $button_custom_2 ? ' tm_pb_custom_button_icon' : ''
			);

		}

		$class = " tm_pb_module tm_pb_bg_layout_{$background_layout} tm_pb_text_align_{$text_orientation}";

		$header_content = '';
		if ( '' !== $title || '' !== $subhead || '' !== $content || '' !== $button_output || '' !== $logo_image_url ) {
			$logo_image = '';
			if ( '' !== $logo_image_url ){
				$logo_image = sprintf(
					'<img src="%1$s" alt="%2$s"%3$s />',
					esc_url( $logo_image_url ),
					esc_attr( $logo_alt_text ),
					( '' !== $logo_title ? sprintf( ' title="%1$s"', esc_attr( $logo_title ) ) : '' )
				);
			}
			$header_content = sprintf(
				'<div class="header-content-container%6$s">
					<div class="header-content">
						%3$s
						%1$s
						%2$s
						%4$s
						%5$s
					</div>
				</div>',
				( $title ? sprintf( '<h1>%1$s</h1>', $title ) : '' ),
				( $subhead ? sprintf( '<span class="tm_pb_fullwidth_header_subhead">%1$s</span>', $subhead ) : '' ),
				$logo_image,
				( '' !== $content ? sprintf( '<p>%1$s</p>', $this->shortcode_content ) : '' ),
				( '' !== $button_output ? $button_output : '' ),
				( '' !== $content_orientation ? sprintf( ' %1$s', $content_orientation ) : '' )
			);
		}

		$header_image = '';
		if ( '' !== $header_image_url ) {
			$header_image = sprintf(
				'<div class="header-image-container%2$s">
					<div class="header-image">
						<img src="%1$s" />
					</div>
				</div>',
				( '' !== $header_image_url ? esc_url( $header_image_url ) : ''),
				( '' !== $image_orientation ? sprintf( ' %1$s', $image_orientation ) : '' )
			);

			$module_class .= ' tm_pb_header_with_image';

		}

		$scroll_down_output = '';
		if ( 'off' !== $header_scroll_down || '' !== $scroll_down_icon ) {
			$scroll_down_output .= sprintf(
				'<a href="#"><span class="scroll-down tm-pb-icon">%1$s</span></a>',
				esc_html( tm_pb_process_font_icon( $scroll_down_icon, 'tm_pb_get_font_down_icon_symbols' ) )
			);
		}

		$output = sprintf(
			'<section%9$s class="tm_pb_fullwidth_header%1$s%7$s%8$s%10$s">
				%6$s
				<div class="tm_pb_fullwidth_header_container%5$s">
					%2$s
					%3$s
				</div>
				<div class="tm_pb_fullwidth_header_overlay"></div>
				<div class="tm_pb_fullwidth_header_scroll">%4$s</div>
			</section>',
			( 'off' !== $header_fullscreen ? ' tm_pb_fullscreen' : '' ),
			( '' !== $header_content ? $header_content : '' ),
			( '' !== $header_image ? $header_image : '' ),
			( 'off' !== $header_scroll_down ? $scroll_down_output : '' ),
			( '' !== $text_orientation ? sprintf( ' %1$s', esc_attr( $text_orientation ) ) : '' ),
			( '' !== $background_url && 'on' === $parallax
				? sprintf(
					'<div class="tm_parallax_bg%2$s" style="background-image: url(%1$s);"></div>',
					esc_attr( $background_url ),
					( 'off' === $parallax_method ? ' tm_pb_parallax_css' : '' )
				)
				: ''
			),
			( '' !== $background_url && 'on' === $parallax ? ' tm_pb_section_parallax' : '' ),
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new Tm_Builder_Module_Fullwidth_Header;

