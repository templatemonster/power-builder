<?php
class Tm_Builder_Module_CTA extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Call To Action', 'tm_builder' );
		$this->slug = 'tm_pb_cta';
		$this->icon = 'f25a';
		$this->whitelisted_fields = array(
			'title',
			'button_url',
			'url_new_window',
			'button_text',
			'use_background_color',
			'background_color',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'max_width_laptop',
			'max_width_tablet',
			'max_width_phone',
		);

		$this->fields_defaults = array(
			'url_new_window'       => array( 'off' ),
			'use_background_color' => array( 'on' ),
			'background_color'     => array( tm_builder_accent_color(), 'add_default_setting' ),
			'text_orientation'     => array( 'center' ),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_promo';
		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2",
						'important' => 'all',
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
				'use_background_color' => false,
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'tm_builder' ),
				),
			),
		);
		$this->custom_css_options = array(
			'promo_description' => array(
				'label'    => esc_html__( 'Promo Description', 'tm_builder' ),
				'selector' => '.tm_pb_promo_description',
			),
			'promo_button' => array(
				'label'    => esc_html__( 'Promo Button', 'tm_builder' ),
				'selector' => '.tm_pb_promo_button',
			),
			'promo_title' => array(
				'label'    => esc_html__( 'Promo Title', 'tm_builder' ),
				'selector' => '.tm_pb_promo_description h2',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your value to action title here.', 'tm_builder' ),
			),
			'button_url' => array(
				'label'           => esc_html__( 'Button URL', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for your CTA button.', 'tm_builder' ),
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'tm_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'tm_builder' ),
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text, or leave blank for no button.', 'tm_builder' ),
			),
			'use_background_color' => array(
				'label'           => esc_html__( 'Use Background Color', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_background_color',
				),
				'description'        => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'tm_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'depends_default'   => true,
				'description'       => esc_html__( 'Here you can define a custom background color for your CTA.', 'tm_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => tm_builder_get_text_orientation_options(),
				'description'       => esc_html__( 'This will adjust the alignment of the module text.', 'tm_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'tm_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'tm_builder' ),
			),
			'max_width' => array(
				'label'           => esc_html__( 'Max Width', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
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
				'module_id',
				'module_class',
				'title',
				'button_url',
				'button_text',
				'background_color',
				'text_orientation',
				'use_background_color',
				'url_new_window',
				'max_width',
				'max_width_laptop',
				'max_width_tablet',
				'max_width_phone',
				'button_icon',
				'custom_button',
			)
		);

		if ( is_rtl() && 'left' === $this->_var( 'text_orientation' ) ) {
			$this->_var( 'text_orientation', 'right' );
		}

		if ( '' !== $this->_var( 'max_width_tablet' )
			|| '' !== $this->_var( 'max_width_phone' )
			|| '' !== $this->_var( 'max_width_laptop' )
			|| '' !== $this->_var( 'max_width' ) ) {
			$max_width_values = array(
				'desktop' => $this->_var( 'max_width' ),
				'laptop'  => $this->_var( 'max_width_laptop' ),
				'tablet'  => $this->_var( 'max_width_tablet' ),
				'phone'   => $this->_var( 'max_width_phone' ),
			);

			$additional_css = 'center' === $this->_var( 'text_orientation' ) ? '; margin: 0 auto;' : '';

			tm_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name, $additional_css );
		}

		$this->_var( 'icon', esc_attr( tm_pb_process_font_icon( $this->_var( 'button_icon' ) ) ) );
		$icon_family = tm_builder_get_icon_family();

		if ( $icon_family ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_custom_button_icon:after',
				'declaration' => sprintf(
					'font-family: "%1$s" !important;',
					esc_attr( $icon_family )
				),
			) );
		}

		$button = '';
		if ( '' !== $this->_var( 'button_url' ) && '' !== $this->_var( 'button_text' ) ) {
			$button = $this->get_template_part( 'cta-button.php' );
		}
		$this->_var( 'button', $button );

		$classes = array(
			'tm_pb_promo',
			'tm_pb_bg_layout_light',
			'tm_pb_text_align_' . $this->_var( 'text_orientation' ),
			( 'on' !== $this->_var( 'use_background_color' ) ? ' tm_pb_no_bg' : '' ),
		);

		if ( 'on' === $this->_var( 'use_background_color' ) ) {
			$atts = array(
				'style' => 'background-color: ' . esc_attr( $this->_var( 'background_color' ) ),
			);
		} else {
			$atts = array();
		}

		$content = $this->get_template_part( 'cta.php' );
		$output  = $this->wrap_module( $content, $classes, $function_name, $atts );

		return $output;
	}
}

new Tm_Builder_Module_CTA;
