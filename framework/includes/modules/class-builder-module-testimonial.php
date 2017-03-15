<?php
class Tm_Builder_Module_Testimonial extends Tm_Builder_Module {
	function init() {

		$this->name             = esc_html__( 'Testimonial', 'power-builder' );
		$this->slug             = 'tm_pb_testimonial';
		$this->icon             = 'f10d';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'author',
			'job_title',
			'company_name',
			'testi_date',
			'url',
			'url_new_window',
			'portrait_url',
			'quote_icon',
			'font_icon',
			'use_background_color',
			'background_color',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'quote_icon_color',
			'portrait_border_radius',
			'portrait_width',
			'portrait_height',
		);

		$this->fields_defaults = array(
			'url_new_window'       => array( 'off' ),
			'quote_icon'           => array( 'on' ),
			'use_background_color' => array( 'on' ),
			'background_color'     => array( '#f5f5f5', 'add_default_setting' ),
			'text_orientation'     => array( 'left' ),
			'font_icon'            => array( 'f10d' ),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'body'   => array(
					'label' => esc_html__( 'Body', 'power-builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} *",
					),
				),
			),
			'background' => array(
				'use_background_color' => false,
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
			'testimonial_portrait' => array(
				'label'    => esc_html__( 'Testimonial Portrait', 'power-builder' ),
				'selector' => '.tm_pb_testimonial_portrait',
			),
			'testimonial_description' => array(
				'label'    => esc_html__( 'Testimonial Description', 'power-builder' ),
				'selector' => '.tm_pb_testimonial_description',
			),
			'testimonial_author' => array(
				'label'    => esc_html__( 'Testimonial Author', 'power-builder' ),
				'selector' => 'tm_pb_testimonial_author',
			),
			'testimonial_meta' => array(
				'label'    => esc_html__( 'Testimonial Meta', 'power-builder' ),
				'selector' => '.tm_pb_testimonial p:last-of-type',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'author' => array(
				'label'           => esc_html__( 'Author Name', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the testimonial author.', 'power-builder' ),
			),
			'job_title' => array(
				'label'           => esc_html__( 'Job Title', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the job title.', 'power-builder' ),
			),
			'company_name' => array(
				'label'           => esc_html__( 'Company Name', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the company.', 'power-builder' ),
			),
			'testi_date' => array(
				'label'           => esc_html__( 'Date', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the testimonial date', 'power-builder' ),
			),
			'url' => array(
				'label'           => esc_html__( 'Author/Company URL', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the website of the author or leave blank for no link.', 'power-builder' ),
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'URLs Open', 'power-builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'power-builder' ),
					'on'  => esc_html__( 'In The New Tab', 'power-builder' ),
				),
				'description'     => esc_html__( 'Choose whether or not the URL should open in a new window.', 'power-builder' ),
			),
			'portrait_url' => array(
				'label'              => esc_html__( 'Portrait Image URL', 'power-builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'power-builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'power-builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'power-builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'power-builder' ),
			),
			'quote_icon' => array(
				'label'           => esc_html__( 'Quote Icon', 'power-builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'power-builder' ),
					'on'  => esc_html__( 'Yes', 'power-builder' ),
				),
				'affects'     => array(
					'#tm_pb_font_icon',
				),
				'description'     => esc_html__( 'Choose whether or not the quote icon should be visible.', 'power-builder' ),
			),
			'font_icon' => array(
				'label'               => esc_html__( 'Icon', 'power-builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'class'               => array( 'tm-pb-font-icon' ),
				'renderer'            => 'tm_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'depends_default'     => true,
				'description'         => esc_html__( 'Choose an icon to display with your testimonial.', 'power-builder' ),
			),
			'use_background_color' => array(
				'label'           => esc_html__( 'Use Background Color', 'power-builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'power-builder' ),
					'off' => esc_html__( 'No', 'power-builder' ),
				),
				'affects'           => array(
					'#tm_pb_background_color',
				),
				'description'     => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'power-builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'power-builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom background color for your CTA.', 'power-builder' ),
				'depends_default'   => true,
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'power-builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => tm_builder_get_text_orientation_options(),
				'description'       => esc_html__( 'This will adjust the alignment of the module text.', 'power-builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'power-builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'power-builder' ),
			),
			'quote_icon_color' => array(
				'label'             => esc_html__( 'Quote Icon Color', 'power-builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'portrait_border_radius' => array(
				'label'           => esc_html__( 'Portrait Border Radius', 'power-builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
			),
			'portrait_width' => array(
				'label'           => esc_html__( 'Portrait Width', 'power-builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
			),
			'portrait_height' => array(
				'label'           => esc_html__( 'Portrait Height', 'power-builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
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

		$this->set_vars(
			array(
				'author',
				'job_title',
				'portrait_url',
				'company_name',
				'testi_date',
				'url',
				'quote_icon',
				'font_icon',
				'url_new_window',
				'use_background_color',
				'background_color',
				'text_orientation',
				'quote_icon_color',
				'portrait_border_radius',
				'portrait_width',
				'portrait_height',
			)
		);

		if ( '' !== $this->_var( 'portrait_border_radius' ) ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .tm_pb_testimonial_portrait, %%order_class%% .tm_pb_testimonial_portrait:before',
					'declaration' => sprintf(
						'-webkit-border-radius: %1$s; -moz-border-radius: %1$s; border-radius: %1$s;',
						esc_html( tm_builder_process_range_value( $this->_var( 'portrait_border_radius' ) ) )
					),
				)
			);
		}

		if ( '' !== $this->_var( 'portrait_width' ) ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .tm_pb_testimonial_portrait',
					'declaration' => sprintf(
						'width: %1$s;',
						esc_html( tm_builder_process_range_value( $this->_var( 'portrait_width' ) ) )
					),
				)
			);
		}

		if ( '' !== $this->_var( 'portrait_height' ) ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .tm_pb_testimonial_portrait',
					'declaration' => sprintf(
						'height: %1$s;',
						esc_html( tm_builder_process_range_value( $this->_var( 'portrait_height' ) ) )
					),
				)
			);
		}

		$style = '';

		if ( 'on' === $this->_var( 'use_background_color' ) && $this->fields_defaults['background_color'][0] !== $this->_var( 'background_color' ) ) {
			$style .= sprintf(
				'background-color: %1$s !important; ',
				esc_html( $this->_var( 'background_color' ) )
			);
		}

		if ( '' !== $style ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%%.tm_pb_testimonial',
					'declaration' => rtrim( $style ),
				)
			);
		}

		if ( '' !== $this->_var( 'quote_icon_color' ) ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%%.tm_pb_testimonial:before',
					'declaration' => sprintf(
						'color: %1$s;',
						esc_html( $this->_var( 'quote_icon_color' ) )
					),
				)
			);
		}

		if ( is_rtl() && 'left' === $this->_var( 'text_orientation' ) ) {
			$this->_var( 'text_orientation', 'right' );
		}

		$portrait_image = '';

		if ( '' !== $this->_var( 'portrait_url' ) ) {
			$portrait_image = sprintf(
				'<div class="tm_pb_testimonial_portrait" style="background-image: url(%1$s);">
				</div>',
				esc_attr( $this->_var( 'portrait_url' ) )
			);
		}

		$this->_var( 'portrait_image', $portrait_image );

		if ( '' !== $this->_var( 'url' ) && ( '' !== $this->_var( 'company_name' ) || '' !== $this->_var( 'author' ) ) ) {
			$link_output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				tm_builder_tools()->render_url( $this->_var( 'url' ) ),
				( '' !== $this->_var( 'company_name' ) ? esc_html( $this->_var( 'company_name' ) ) : esc_html( $this->_var( 'author' ) ) ),
				( 'on' === $this->_var( 'url_new_window' ) ? ' target="_blank"' : '' )
			);

			if ( '' !== $this->_var( 'company_name' ) ) {
				$this->_var( 'company_name', $link_output );
			} else {
				$this->_var( 'author', $link_output );
			}
		}

		$content = $this->get_template_part( 'testimonial.php' );

		$classes = array(
			'tm_pb_bg_layout_light',
			'tm_pb_text_align_' . $this->_var( 'text_orientation' ),
			( 'off' === $this->_var( 'use_background_color' ) ? ' tm_pb_testimonial_no_bg' : '' ),
			( '' === $this->_var( 'portrait_image' ) ? ' tm_pb_testimonial_no_image' : '' ),
			( 'off' === $this->_var( 'quote_icon' ) ? ' tm_pb_icon_off' : '' ),
			'clearfix',
		);

		if ( ! isset( $atts['quote_icon'] ) ) {
			$classes = 'tm_pb_testimonial_old_layout';
		}

		$atts = array();

		if ( 'on' === $this->_var( 'use_background_color' ) ) {
			$atts = array(
				'style' => sprintf( 'background-color: %1$s;', esc_attr( $this->_var( 'background_color' ) ) )
			);
		}

		$icon        = esc_attr( tm_pb_process_font_icon( $this->_var( 'font_icon' ) ) );
		$icon_family = tm_builder_get_icon_family();

		if ( $icon_family ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:before',
				'declaration' => sprintf(
					'font-family: "%1$s" !important;',
					esc_attr( $icon_family )
				),
			) );
		}

		$atts['data-icon'] = ( $this->_var( 'font_icon' ) !== $this->fields_defaults['font_icon'][0] ) ? esc_attr( tm_pb_process_font_icon( $this->_var( 'font_icon' ) ) ) : esc_attr( tm_pb_process_font_icon( $this->fields_defaults['font_icon'][0] ) );

		$output = $this->wrap_module( $content, $classes, $function_name, $atts );

		return $output;
	}
}

new Tm_Builder_Module_Testimonial;
