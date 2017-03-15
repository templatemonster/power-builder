<?php
class Tm_Builder_Module_Image extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Image', 'power-builder' );
		$this->slug = 'tm_pb_image';
		$this->icon = 'f03e';

		$this->whitelisted_fields = array(
			'src',
			'alt',
			'title_text',
			'show_in_lightbox',
			'url',
			'url_new_window',
			'animation',
			'sticky',
			'align',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'force_fullwidth',
			'always_center_on_mobile',
			'use_overlay',
			'overlay_icon_color',
			'hover_overlay_color',
			'hover_icon',
			'max_width_laptop',
			'max_width_tablet',
			'max_width_phone',
		);

		$this->fields_defaults = array(
			'show_in_lightbox'        => array( 'off' ),
			'url_new_window'          => array( 'off' ),
			'animation'               => array( 'left' ),
			'sticky'                  => array( 'off' ),
			'align'                   => array( 'left' ),
			'force_fullwidth'         => array( 'off' ),
			'always_center_on_mobile' => array( 'on' ),
			'use_overlay'             => array( 'off' ),
		);

		$this->advanced_options = array(
			'border'                => array(),
			'custom_margin_padding' => array(
				'use_padding' => false,
				'css' => array(
					'important' => 'all',
				),
			),
		);

		$this->custom_css_options = array(
			'image' => array(
				'label'    => esc_html__( 'Image', 'power-builder' ),
				'selector' => 'img',
			),
			'lightbox_link' => array(
				'label'    => esc_html__( 'Lightbox link', 'power-builder' ),
				'selector' => '.tm_pb_lightbox_image',
			),
		);
	}

	function get_fields() {
		// List of animation options
		$animation_options_list = array(
			'left'    => esc_html__( 'Left To Right', 'power-builder' ),
			'right'   => esc_html__( 'Right To Left', 'power-builder' ),
			'top'     => esc_html__( 'Top To Bottom', 'power-builder' ),
			'bottom'  => esc_html__( 'Bottom To Top', 'power-builder' ),
			'fade_in' => esc_html__( 'Fade In', 'power-builder' ),
			'off'     => esc_html__( 'No Animation', 'power-builder' ),
		);

		$animation_option_name       = sprintf( '%1$s-animation', $this->slug );
		$default_animation_direction = TM_Global_Settings::get_value( $animation_option_name );

		// If user modifies default animation option via Customizer, we'll need to change the order
		if ( 'left' !== $default_animation_direction && ! empty( $default_animation_direction ) && array_key_exists( $default_animation_direction, $animation_options_list ) ) {
			// The options, sans user's preferred direction
			$animation_options_wo_default = $animation_options_list;
			unset( $animation_options_wo_default[ $default_animation_direction ] );

			// All animation options
			$animation_options = array_merge(
				array( $default_animation_direction => $animation_options_list[$default_animation_direction] ),
				$animation_options_wo_default
			);
		} else {
			// Simply copy the animation options
			$animation_options = $animation_options_list;
		}

		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Image URL', 'power-builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'power-builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'power-builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'power-builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'power-builder' ),
			),
			'alt' => array(
				'label'           => esc_html__( 'Image Alternative Text', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'power-builder' ),
			),
			'title_text' => array(
				'label'           => esc_html__( 'Image Title Text', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the HTML Title text.', 'power-builder' ),
			),
			'show_in_lightbox' => array(
				'label'             => esc_html__( 'Open in Lightbox', 'power-builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( "No", 'power-builder' ),
					'on'  => esc_html__( 'Yes', 'power-builder' ),
				),
				'affects'           => array(
					'#tm_pb_url',
					'#tm_pb_url_new_window',
					'#tm_pb_use_overlay'
				),
				'description'       => esc_html__( 'Here you can choose whether or not the image should open in Lightbox. Note: if you select to open the image in Lightbox, url options below will be ignored.', 'power-builder' ),
			),
			'url' => array(
				'label'           => esc_html__( 'Link URL', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'affects'         => array(
					'#tm_pb_use_overlay',
				),
				'description'     => esc_html__( 'If you would like your image to be a link, input your destination URL here. No link will be created if this field is left blank.', 'power-builder' ),
			),
			'url_new_window' => array(
				'label'             => esc_html__( 'Url Opens', 'power-builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'In The Same Window', 'power-builder' ),
					'on'  => esc_html__( 'In The New Tab', 'power-builder' ),
				),
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'power-builder' ),
			),
			'use_overlay' => array(
				'label'             => esc_html__( 'Image Overlay', 'power-builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Off', 'power-builder' ),
					'on'  => esc_html__( 'On', 'power-builder' ),
				),
				'affects'           => array(
					'#tm_pb_overlay_icon_color',
					'#tm_pb_hover_overlay_color',
					'#tm_pb_hover_icon',
				),
				'depends_default'   => true,
				'description'       => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the image', 'power-builder' ),
			),
			'overlay_icon_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'power-builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay icon', 'power-builder' ),
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'power-builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'power-builder' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'power-builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'tm-pb-font-icon' ),
				'renderer'            => 'tm_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'depends_show_if'     => 'on',
				'description'       => esc_html__( 'Here you can define a custom icon for the overlay', 'power-builder' ),
			),
			'animation' => array(
				'label'             => esc_html__( 'Animation', 'power-builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => $animation_options,
				'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'power-builder' ),
			),
			'sticky' => array(
				'label'             => esc_html__( 'Remove Space Below The Image', 'power-builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off'     => esc_html__( 'No', 'power-builder' ),
					'on'      => esc_html__( 'Yes', 'power-builder' ),
				),
				'description'       => esc_html__( 'Here you can choose whether or not the image should have a space below it.', 'power-builder' ),
			),
			'align' => array(
				'label'           => esc_html__( 'Image Alignment', 'power-builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'left'   => esc_html__( 'Left', 'power-builder' ),
					'center' => esc_html__( 'Center', 'power-builder' ),
					'right'  => esc_html__( 'Right', 'power-builder' ),
				),
				'description'       => esc_html__( 'Here you can choose the image alignment.', 'power-builder' ),
			),
			'max_width' => array(
				'label'           => esc_html__( 'Image Max Width', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'force_fullwidth' => array(
				'label'             => esc_html__( 'Force Fullwidth', 'power-builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( "No", 'power-builder' ),
					'on'  => esc_html__( 'Yes', 'power-builder' ),
				),
				'tab_slug'    => 'advanced',
			),
			'always_center_on_mobile' => array(
				'label'             => esc_html__( 'Always Center Image On Mobile', 'power-builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'power-builder' ),
					'off' => esc_html__( "No", 'power-builder' ),
				),
				'tab_slug'    => 'advanced',
			),
			'max_width_laptop' => array(
				'type' => 'skip',
			),
			'max_width_tablet' => array(
				'type' => 'skip',
			),
			'max_width_phone' => array(
				'type' => 'skip',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'power-builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => tm_pb_media_breakpoints(),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'power-builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'power-builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'power-builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$src                     = $this->shortcode_atts['src'];
		$alt                     = $this->shortcode_atts['alt'];
		$title_text              = $this->shortcode_atts['title_text'];
		$animation               = $this->shortcode_atts['animation'];
		$url                     = $this->shortcode_atts['url'];
		$url_new_window          = $this->shortcode_atts['url_new_window'];
		$show_in_lightbox        = $this->shortcode_atts['show_in_lightbox'];
		$sticky                  = $this->shortcode_atts['sticky'];
		$align                   = $this->shortcode_atts['align'];
		$max_width               = $this->shortcode_atts['max_width'];
		$max_width_laptop        = $this->shortcode_atts['max_width_laptop'];
		$max_width_tablet        = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone         = $this->shortcode_atts['max_width_phone'];
		$force_fullwidth         = $this->shortcode_atts['force_fullwidth'];
		$always_center_on_mobile = $this->shortcode_atts['always_center_on_mobile'];
		$overlay_icon_color      = $this->shortcode_atts['overlay_icon_color'];
		$hover_overlay_color     = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon              = $this->shortcode_atts['hover_icon'];
		$use_overlay             = $this->shortcode_atts['use_overlay'];

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( 'on' === $always_center_on_mobile ) {
			$module_class .= ' tm_always_center_on_mobile';
		}

		// overlay can be applied only if image has link or if lightbox enabled
		$is_overlay_applied = 'on' === $use_overlay && ( 'on' === $show_in_lightbox || ( 'off' === $show_in_lightbox && '' !== $url ) ) ? 'on' : 'off';

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width || '' !== $max_width_laptop ) {
			$max_width_values = array(
				'desktop' => $max_width,
				'laptop'  => $max_width_laptop,
				'tablet'  => $max_width_tablet,
				'phone'   => $max_width_phone,
			);

			tm_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name );
		}

		if ( 'on' === $force_fullwidth ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% img',
				'declaration' => 'width: 100%;',
			) );
		}

		if ( $this->fields_defaults['align'][0] !== $align ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'text-align: %1$s;',
					esc_html( $align )
				),
			) );
		}

		if ( 'center' !== $align ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'margin-%1$s: 0;',
					esc_html( $align )
				),
			) );
		}

		if ( 'on' === $is_overlay_applied ) {

			if ( '' !== $overlay_icon_color ) { TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_overlay:before',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $overlay_icon_color )
					),
				) );
			}

			if ( '' !== $hover_overlay_color ) { TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_overlay',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $hover_overlay_color )
					),
				) );
			}

			$icon        = esc_attr( tm_pb_process_font_icon( $hover_icon ) );
			$icon_family = tm_builder_get_icon_family();

			if ( $icon_family ) {
				TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_pb_inline_icon:before',
					'declaration' => sprintf(
						'font-family: "%1$s" !important;',
						esc_attr( $icon_family )
					),
				) );
			}

			$data_icon = '' !== $icon ? sprintf( ' data-icon="%1$s"', $icon ) : '';

			$overlay_output = sprintf(
				'<span class="tm_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' tm_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		$output = sprintf(
			'<img src="%1$s" alt="%2$s"%3$s />
			%4$s',
			esc_url( $src ),
			esc_attr( $alt ),
			( '' !== $title_text ? sprintf( ' title="%1$s"', esc_attr( $title_text ) ) : '' ),
			'on' === $is_overlay_applied ? $overlay_output : ''
		);

		if ( 'on' === $show_in_lightbox ) {
			$output = sprintf( '<a href="%1$s" class="tm_pb_lightbox_image" title="%3$s">%2$s</a>',
				esc_url( $src ),
				$output,
				esc_attr( $alt )
			);
		} else if ( '' !== $url ) {
			$output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				tm_builder_tools()->render_url( $url ),
				$output,
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		}

		$animation = '' === $animation ? TM_Global_Settings::get_value( 'tm_pb_image-animation' ) : $animation;

		$output = sprintf(
			'<div%5$s class="tm_pb_module tm-waypoint tm_pb_image%2$s%3$s%4$s%6$s">
				%1$s
			</div>',
			$output,
			esc_attr( " tm_pb_animation_{$animation}" ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			( 'on' === $sticky ? esc_attr( ' tm_pb_image_sticky' ) : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			'on' === $is_overlay_applied ? ' tm_pb_has_overlay' : ''
		);

		return $output;
	}
}
new Tm_Builder_Module_Image;

