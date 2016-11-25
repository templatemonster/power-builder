<?php
class Tm_Builder_Module_Blurb extends Tm_Builder_Module {

	public $function_name;

	function init() {
		$this->name = esc_html__( 'Blurb', 'tm_builder' );
		$this->icon = 'f27b';
		$this->slug = 'tm_pb_blurb';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'title',
			'url',
			'url_new_window',
			'use_icon',
			'font_icon',
			'icon_color',
			'use_circle',
			'circle_color',
			'circle_size',
			'use_circle_border',
			'circle_border_color',
			'circle_border_width',
			'image',
			'alt',
			'icon_placement',
			'animation',
			'text_orientation',
			'use_button',
			'button_type',
			'button_text',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'use_icon_font_size',
			'icon_font_size',
			'circle_size_laptop',
			'circle_size_tablet',
			'circle_size_phone',
			'max_width_laptop',
			'max_width_tablet',
			'max_width_phone',
			'icon_font_size_laptop',
			'icon_font_size_tablet',
			'icon_font_size_phone',
		);

		$tm_accent_color    = tm_builder_accent_color();
		$tm_secondary_color = tm_builder_secondary_color();

		$this->fields_defaults = array(
			'url_new_window'      => array( 'off' ),
			'use_icon'            => array( 'off' ),
			'icon_color'          => array( $tm_accent_color, 'add_default_setting' ),
			'use_circle'          => array( 'off' ),
			'circle_color'        => array( $tm_secondary_color, 'only_default_setting' ),
			'use_circle_border'   => array( 'off' ),
			'circle_border_color' => array( $tm_accent_color, 'only_default_setting' ),
			'icon_placement'      => array( 'top' ),
			'animation'           => array( 'top' ),
			'text_orientation'    => array( 'center' ),
			'use_icon_font_size'  => array( 'off' ),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h4, {$this->main_css_element} h4 a",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
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
			'blurb_image' => array(
				'label'    => esc_html__( 'Blurb Image', 'tm_builder' ),
				'selector' => '.tm_pb_main_blurb_image',
			),
			'blurb_title' => array(
				'label'    => esc_html__( 'Blurb Title', 'tm_builder' ),
				'selector' => 'h4',
			),
			'blurb_content' => array(
				'label'    => esc_html__( 'Blurb Content', 'tm_builder' ),
				'selector' => '.tm_pb_blurb_content',
			),
		);
	}

	function get_fields() {
		$tm_accent_color = tm_builder_accent_color();

		$image_icon_placement = array(
			'top' => esc_html__( 'Top', 'tm_builder' ),
		);

		if ( ! is_rtl() ) {
			$image_icon_placement['left'] = esc_html__( 'Left', 'tm_builder' );
		} else {
			$image_icon_placement['right'] = esc_html__( 'Right', 'tm_builder' );
		}

		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title of your blurb will appear in bold below your blurb image.', 'tm_builder' ),
			),
			'url' => array(
				'label'           => esc_html__( 'Url', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to make your blurb a link, input your destination URL here.', 'tm_builder' ),
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'tm_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'tm_builder' ),
				),
				'description' => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'tm_builder' ),
			),
			'use_icon' => array(
				'label'           => esc_html__( 'Use Icon', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'     => array(
					'#tm_pb_font_icon',
					'#tm_pb_use_circle',
					'#tm_pb_icon_color',
					'#tm_pb_image',
					'#tm_pb_alt',
				),
				'description' => esc_html__( 'Here you can choose whether icon set below should be used.', 'tm_builder' ),
			),
			'font_icon' => array(
				'label'               => esc_html__( 'Icon', 'tm_builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'class'               => array( 'tm-pb-font-icon' ),
				'renderer'            => 'tm_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'description'         => esc_html__( 'Choose an icon to display with your blurb.', 'tm_builder' ),
				'depends_default'     => true,
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom color for your icon.', 'tm_builder' ),
				'depends_default'   => true,
			),
			'use_circle' => array(
				'label'           => esc_html__( 'Circle Icon', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_use_circle_border',
					'#tm_pb_circle_color',
					'#tm_pb_circle_size',
				),
				'description' => esc_html__( 'Here you can choose whether icon set above should display within a circle.', 'tm_builder' ),
				'depends_default'   => true,
			),
			'circle_color' => array(
				'label'           => esc_html__( 'Circle Color', 'tm_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for the icon circle.', 'tm_builder' ),
				'depends_default' => true,
			),
			'use_circle_border' => array(
				'label'           => esc_html__( 'Show Circle Border', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_circle_border_color',
					'#tm_pb_circle_border_width',
				),
				'description' => esc_html__( 'Here you can choose whether if the icon circle border should display.', 'tm_builder' ),
				'depends_default'   => true,
			),
			'circle_border_color' => array(
				'label'           => esc_html__( 'Circle Border Color', 'tm_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for the icon circle border.', 'tm_builder' ),
				'depends_default' => true,
			),
			'image' => array(
				'label'              => esc_html__( 'Image', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'tm_builder' ),
				'depends_show_if'    => 'off',
				'description'        => esc_html__( 'Upload an image to display at the top of your blurb.', 'tm_builder' ),
			),
			'alt' => array(
				'label'           => esc_html__( 'Image Alt Text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'tm_builder' ),
				'depends_show_if' => 'off',
			),
			'icon_placement' => array(
				'label'             => esc_html__( 'Image/Icon Placement', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => $image_icon_placement,
				'description'       => esc_html__( 'Here you can choose where to place the icon.', 'tm_builder' ),
			),
			'animation' => array(
				'label'             => esc_html__( 'Image/Icon Animation', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'top'    => esc_html__( 'Top To Bottom', 'tm_builder' ),
					'left'   => esc_html__( 'Left To Right', 'tm_builder' ),
					'right'  => esc_html__( 'Right To Left', 'tm_builder' ),
					'bottom' => esc_html__( 'Bottom To Top', 'tm_builder' ),
					'off'    => esc_html__( 'No Animation', 'tm_builder' ),
				),
				'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'tm_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => tm_builder_get_text_orientation_options(),
				'description'       => esc_html__( 'This will control how your blurb text is aligned.', 'tm_builder' ),
			),
			'use_button' => array(
				'label'           => esc_html__( 'Use Read More button', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'     => array(
					'#tm_pb_button_type',
					'#tm_pb_button_text',
				),
				'description' => esc_html__( 'Here you can choose show or hide read more button.', 'tm_builder' ),
			),
			'button_type' => array(
				'label'             => esc_html__( 'Button type', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'link'   => esc_html__( 'Link', 'tm_builder' ),
					'button' => esc_html__( 'Button', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Select button display type.', 'tm_builder' ),
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define button text.', 'tm_builder' ),
			),
			'content_new' => array(
				'label'             => esc_html__( 'Content', 'tm_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'tm_builder' ),
			),
			'max_width' => array(
				'label'           => esc_html__( 'Image Max Width', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'use_icon_font_size' => array(
				'label'           => esc_html__( 'Use Icon Font Size', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'font_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'     => array(
					'#tm_pb_icon_font_size',
				),
				'tab_slug' => 'advanced',
			),
			'icon_font_size' => array(
				'label'           => esc_html__( 'Icon Font Size', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'font_option',
				'tab_slug'        => 'advanced',
				'default'         => '96px',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'  => true,
				'depends_default' => true,
			),
			'circle_size' => array(
				'label'           => esc_html__( 'Circle Size', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'default'         => '100',
				'range_settings' => array(
					'min'  => '40',
					'max'  => '260',
					'step' => '1',
				),
				'description'     => esc_html__( 'Here you can define a custom diameter for the icon circle.', 'tm_builder' ),
				'mobile_options'  => true,
				'depends_default' => true,
			),
			'circle_border_width' => array(
				'label'           => esc_html__( 'Circle Border Width', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'default'         => '2',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '20',
					'step' => '1',
				),
				'description'     => esc_html__( 'Here you can define a custom width for the icon circle border.', 'tm_builder' ),
				'depends_default' => true,
			),
			'circle_size_laptop' => array(
				'type' => 'skip',
			),
			'circle_size_tablet' => array(
				'type' => 'skip',
			),
			'circle_size_phone' => array(
				'type' => 'skip',
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
			'icon_font_size_laptop' => array(
				'type' => 'skip',
			),
			'icon_font_size_tablet' => array(
				'type' => 'skip',
			),
			'icon_font_size_phone' => array(
				'type' => 'skip',
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
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array(
				'title',
				'url',
				'image',
				'url_new_window',
				'alt',
				'text_orientation',
				'animation',
				'use_button',
				'button_type',
				'button_text',
				'icon_placement',
				'font_icon',
				'use_icon',
				'use_circle',
				'use_circle_border',
				'icon_color',
				'circle_color',
				'circle_size',
				'circle_size_laptop',
				'circle_size_tablet',
				'circle_size_phone',
				'circle_border_color',
				'circle_border_width',
				'max_width',
				'max_width_laptop',
				'max_width_tablet',
				'max_width_phone',
				'use_icon_font_size',
				'icon_font_size',
				'icon_font_size_laptop',
				'icon_font_size_tablet',
				'icon_font_size_phone',
			)
		);

		$this->function_name = $function_name;

		if ( 'off' !== $this->_var( 'use_icon_font_size' ) ) {
			$font_size_values = array(
				'desktop' => $this->_var( 'icon_font_size' ),
				'laptop'  => $this->_var( 'icon_font_size_laptop' ),
				'tablet'  => $this->_var( 'icon_font_size_tablet' ),
				'phone'   => $this->_var( 'icon_font_size_phone' ),
			);

			tm_pb_generate_responsive_css(
				$font_size_values,
				'%%order_class%% .tm-pb-icon',
				'font-size',
				$function_name
			);
		}

		if ( '' !== $this->_var( 'max_width_tablet' ) || '' !== $this->_var( 'max_width_phone' ) || '' !== $this->_var( 'max_width' ) ) {
			$max_width_values = array(
				'desktop' => $this->_var( 'max_width' ),
				'laptop'  => $this->_var( 'max_width_laptop' ),
				'tablet'  => $this->_var( 'max_width_tablet' ),
				'phone'   => $this->_var( 'max_width_phone' ),
			);

			tm_pb_generate_responsive_css( $max_width_values, '%%order_class%% .tm_pb_main_blurb_image img', 'max-width', $function_name );
		}

		if ( is_rtl() && 'left' === $this->_var( 'text_orientation' ) ) {
			$this->_var( 'text_orientation', 'right' );
		}

		if ( is_rtl() && 'left' === $this->_var( 'icon_placement' ) ) {
			$this->_var( 'icon_placement', 'right' );
		}

		if ( '' !== $this->_var( 'title' ) && '' !== $this->_var( 'url' ) ) {
			$this->_var( 'title', sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				tm_builder_tools()->render_url( $this->_var( 'url' ) ),
				esc_html( $this->_var( 'title' ) ),
				( 'on' === $this->_var( 'url_new_window' ) ? ' target="_blank"' : '' )
			) );
		}

		$animation = $this->_var( 'animation' );

		if ( '' !== $this->_var( 'title' ) ) {
			$this->_var( 'title', '<h4>' . $this->_var( 'title' ) . '</h4>' );
		}

		if ( '' !== trim( $this->_var( 'image' ) ) || '' !== $this->_var( 'font_icon' ) ) {
			if ( 'off' === $this->_var( 'use_icon' ) ) {
				$this->_var( 'image', sprintf(
					'<img src="%1$s" alt="%2$s" class="tm-waypoint%3$s" />',
					esc_url( $this->_var( 'image' ) ),
					esc_attr( $this->_var( 'alt' ) ),
					esc_attr( ' tm_pb_animation_' . $animation )
				) );
			} else {
				$icon_style = sprintf( 'color: %1$s;', esc_attr( $this->_var( 'icon_color' ) ) );

				if ( 'on' === $this->_var( 'use_circle' ) ) {
					$icon_style .= sprintf( ' background-color: %1$s;', esc_attr( $this->_var( 'circle_color' ) ) );

					if ( 'on' === $this->_var( 'use_circle_border' ) ) {
						$icon_style .= sprintf(
							' border-color: %1$s;',
							esc_attr( $this->_var( 'circle_border_color' ) )
						);
					}

					if ( '' !== $this->_var( 'circle_border_width' ) ) {
						$icon_style .= sprintf(
							' border-width: %1$spx;',
							esc_attr( $this->_var( 'circle_border_width' ) )
						);
					}

					$this->set_circle_size();
				}

				$icon        = esc_attr( tm_pb_process_font_icon( $this->_var( 'font_icon' ) ) );
				$icon_family = tm_builder_get_icon_family();

				if ( $icon_family ) {
					TM_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%% .tm-pb-icon:before',
						'declaration' => sprintf(
							'font-family: "%1$s" !important;',
							esc_attr( $icon_family )
						),
					) );
				}

				$this->_var( 'image', sprintf(
					'<span class="tm-pb-icon tm-waypoint%2$s%3$s%4$s" style="%5$s" data-icon="%1$s"></span>',
					$icon,
					esc_attr( ' tm_pb_animation_' . $animation ),
					( 'on' === $this->_var( 'use_circle' ) ? ' tm-pb-icon-circle' : '' ),
					( 'on' === $this->_var( 'use_circle' ) && 'on' === $this->_var( 'use_circle_border' ) ? ' tm-pb-icon-circle-border' : '' ),
					$icon_style
				) );
			}

			$this->_var( 'image', sprintf(
				'<div class="tm_pb_main_blurb_image">%1$s</div>',
				( '' !== $this->_var( 'url' )
					? sprintf(
						'<a href="%1$s"%3$s>%2$s</a>',
						tm_builder_tools()->render_url( $this->_var( 'url' ) ),
						$this->_var( 'image' ),
						( 'on' === $this->_var( 'url_new_window' ) ? ' target="_blank"' : '' )
					)
					: $this->_var( 'image' )
				)
			) );
		}

		$classes = array(
			'tm_pb_bg_layout_light',
			'tm_pb_text_align_' . $this->_var( 'text_orientation' ),
			sprintf( ' tm_pb_blurb_position_%1$s', esc_attr( $this->_var( 'icon_placement' ) ) ),
		);

		$content = $this->get_template_part( 'blurb.php' );

		$output = $this->wrap_module( $content, $classes, $function_name );

		return $output;
	}

	/**
	 * Returns blurb button HTML markup.
	 *
	 * @return string
	 */
	public function get_blurb_button() {

		if ( 'on' !== $this->_var( 'use_button' ) ) {
			return;
		}

		$text  = $this->_var( 'button_text' );
		$text  = ! empty( $text ) ? esc_html( $text ) : esc_html__( 'Read More', 'tm_builder' );
		$url   = esc_url( $this->_var( 'url' ) );
		$class = ( 'link' === $this->_var( 'button_type' ) ) ? 'tm_pb_link' : 'tm_pb_button';

		return sprintf(
			apply_filters( 'tm_pb_blurb_button_format', '<a href="%2$s" class="%3$s">%1$s</a>' ),
			$text,
			$url,
			$class
		);

	}

	/**
	 * Set sircle size values
	 */
	public function set_circle_size() {

		$circle_size_d  = intval( $this->_var( 'circle_size' ) );
		$circle_size_l  = intval( $this->_var( 'circle_size_laptop' ) );
		$circle_size_t  = intval( $this->_var( 'circle_size_tablet' ) );
		$circle_size_ph = intval( $this->_var( 'circle_size_phone' ) );

		if ( ! $circle_size_l ) {
			$circle_size_l = $circle_size_d;
		}

		if ( ! $circle_size_t ) {
			$circle_size_t = $circle_size_l;
		}

		if ( ! $circle_size_ph ) {
			$circle_size_ph = $circle_size_t;
		}

		if ( '' !== $this->_var( 'circle_size_tablet' ) || '' !== $this->_var( 'circle_size_laptop' ) || '' !== $this->_var( 'circle_size_phone' ) || '' !== $this->_var( 'circle_size' ) ) {
			$max_width_values = array(
				'desktop' => $this->_var( 'circle_size' ),
				'laptop'  => $this->_var( 'circle_size_laptop' ),
				'tablet'  => $this->_var( 'circle_size_tablet' ),
				'phone'   => $this->_var( 'circle_size_phone' ),
			);


		}

		if ( ! empty( $circle_size_d ) || ! empty( $circle_size_l ) || ! empty( $circle_size_t ) || ! empty( $circle_size_ph ) ) {

			$radius_d  = round( $circle_size_d / 2 );
			$radius_l  = round( $circle_size_l / 2 );
			$radius_t  = round( $circle_size_t / 2 );
			$radius_ph = round( $circle_size_ph / 2 );

			$sizes = array(
				'desktop' => $circle_size_d,
				'laptop'  => $circle_size_l,
				'tablet'  => $circle_size_t,
				'phone'   => $circle_size_ph,
			);

			$radius = array(
				'desktop' => $radius_d,
				'laptop'  => $radius_l,
				'tablet'  => $radius_t,
				'phone'   => $radius_ph,
			);

			tm_pb_generate_responsive_css( $sizes, '%%order_class%% .tm-pb-icon', 'width', $this->function_name );
			tm_pb_generate_responsive_css( $sizes, '%%order_class%% .tm-pb-icon', 'height', $this->function_name );
			tm_pb_generate_responsive_css( $radius, '%%order_class%% .tm-pb-icon', 'border-radius', $this->function_name );
		}

	}
}

new Tm_Builder_Module_Blurb;
