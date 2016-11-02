<?php
class Tm_Builder_Module_Pricing_Tables extends Tm_Builder_Module {
	function init() {

		$this->name             = esc_html__( 'Pricing Tables', 'tm_builder' );
		$this->slug             = 'tm_pb_pricing_tables';
		$this->icon             = 'f0ce';
		$this->main_css_element = '%%order_class%%.tm_pb_pricing';
		$this->child_slug       = 'tm_pb_pricing_table';
		$this->child_item_text  = esc_html__( 'Pricing Table', 'tm_builder' );

		$this->whitelisted_fields = array(
			'admin_label',
			'module_id',
			'module_class',
			'featured_table_background_color',
			'header_background_color',
			'featured_table_header_background_color',
			'featured_table_header_text_color',
			'featured_table_subheader_text_color',
			'featured_table_price_color',
			'featured_table_text_color',
			'show_bullet',
			'bullet_color',
			'featured_table_bullet_color',
			'remove_featured_drop_shadow',
			'center_list_items',
		);

		$this->fields_defaults = array(
			'show_bullet'                 => array( 'on' ),
			'remove_featured_drop_shadow' => array( 'off' ),
			'center_list_items'           => array( 'off' ),
		);

		$this->additional_shortcode = 'tm_pb_pricing_item';
		$this->main_css_element = '%%order_class%%';
		$this->custom_css_options = array(
			'pricing_heading' => array(
				'label'    => esc_html__( 'Pricing Heading', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_heading',
			),
			'pricing_title' => array(
				'label'    => esc_html__( 'Pricing Title', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_heading h2',
			),
			'pricing_subtitle' => array(
				'label'    => esc_html__( 'Pricing Subtitle', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_heading .tm_pb_best_value',
			),
			'pricing_top' => array(
				'label'    => esc_html__( 'Pricing Top', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_content_top',
			),
			'price' => array(
				'label'    => esc_html__( 'Price', 'tm_builder' ),
				'selector' => '.tm_pb_tm_price',
			),
			'currency' => array(
				'label'    => esc_html__( 'Currency', 'tm_builder' ),
				'selector' => '.tm_pb_dollar_sign',
			),
			'frequency' => array(
				'label'    => esc_html__( 'Frequency', 'tm_builder' ),
				'selector' => '.tm_pb_frequency',
			),
			'pricing_content' => array(
				'label'    => esc_html__( 'Pricing Content', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_content',
			),
			'pricing_item' => array(
				'label'    => esc_html__( 'Pricing Item', 'tm_builder' ),
				'selector' => 'ul.tm_pb_pricing li',
			),
			'pricing_item_excluded' => array(
				'label'    => esc_html__( 'Excluded Item', 'tm_builder' ),
				'selector' => 'ul.tm_pb_pricing li.tm_pb_not_available',
			),
			'pricing_button' => array(
				'label'    => esc_html__( 'Pricing Button', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_table_button',
			),
			'featured_table' => array(
				'label'    => esc_html__( 'Featured Table', 'tm_builder' ),
				'selector' => '.tm_pb_featured_table',
			),
		);
		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_pricing_heading h2",
						'important' => 'all',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'subheader' => array(
					'label'    => esc_html__( 'Subheader', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_best_value",
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'currency_frequency' => array(
					'label'    => esc_html__( 'Currency &amp; Frequency', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_dollar_sign, {$this->main_css_element} .tm_pb_frequency",
					),
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_sum",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_pricing li",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'font_size' => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'background' => array(
				'use_background_image' => false,
				'css' => array(
					'main' => "{$this->main_css_element} .tm_pb_pricing_table",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .tm_pb_pricing_table",
				),
				'additional_elements' => array(
					"{$this->main_css_element} .tm_pb_pricing_content_top" => array( 'bottom' ),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'tm_builder' ),
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'featured_table_background_color' => array(
				'label'             => esc_html__( 'Featured Table Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'priority'          => 23,
			),
			'header_background_color' => array(
				'label'             => esc_html__( 'Table Header Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'featured_table_header_background_color' => array(
				'label'             => esc_html__( 'Featured Table Header Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'priority'          => 21,
			),
			'featured_table_header_text_color' => array(
				'label'             => esc_html__( 'Featured Table Header Text Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'priority'          => 20,
			),
			'featured_table_subheader_text_color' => array(
				'label'             => esc_html__( 'Featured Table Subheader Text Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'priority'          => 20,
			),
			'featured_table_price_color' => array(
				'label'             => esc_html__( 'Featured Table Price Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'priority'          => 20,
			),
			'featured_table_text_color' => array(
				'label'             => esc_html__( 'Featured Table Body Text Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'priority'          => 22,
			),
			'show_bullet' => array(
				'label'           => esc_html__( 'Show Bullet', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'tab_slug' => 'advanced',
				'affects'           => array(
					'#tm_pb_bullet_color',
				),
			),
			'bullet_color' => array(
				'label'             => esc_html__( 'Bullet Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'depends_show_if'   => 'on',
			),
			'featured_table_bullet_color' => array(
				'label'             => esc_html__( 'Featured Table Bullet Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'priority'          => 22,
			),
			'remove_featured_drop_shadow' => array(
				'label'           => esc_html__( 'Remove Featured Table Drop Shadow', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug' => 'advanced',
				'priority'          => 24,
			),
			'center_list_items' => array(
				'label'           => esc_html__( 'Center List Items', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug' => 'advanced',
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

	function pre_shortcode_content() {
		global $tm_pb_pricing_tables_num, $tm_pb_pricing_tables_icon;

		$button_custom = $this->shortcode_atts['custom_button'];
		$custom_icon   = $this->shortcode_atts['button_icon'];

		$tm_pb_pricing_tables_num = 0;

		$tm_pb_pricing_tables_icon = 'on' === $button_custom ? $custom_icon : '';
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                              = $this->shortcode_atts['module_id'];
		$module_class                           = $this->shortcode_atts['module_class'];
		$featured_table_background_color        = $this->shortcode_atts['featured_table_background_color'];
		$featured_table_text_color              = $this->shortcode_atts['featured_table_text_color'];
		$header_background_color                = $this->shortcode_atts['header_background_color'];
		$featured_table_header_background_color = $this->shortcode_atts['featured_table_header_background_color'];
		$featured_table_header_text_color       = $this->shortcode_atts['featured_table_header_text_color'];
		$featured_table_subheader_text_color    = $this->shortcode_atts['featured_table_subheader_text_color'];
		$featured_table_price_color             = $this->shortcode_atts['featured_table_price_color'];
		$bullet_color                           = $this->shortcode_atts['bullet_color'];
		$featured_table_bullet_color            = $this->shortcode_atts['featured_table_bullet_color'];
		$remove_featured_drop_shadow            = $this->shortcode_atts['remove_featured_drop_shadow'];
		$center_list_items                      = $this->shortcode_atts['center_list_items'];
		$show_bullet                            = $this->shortcode_atts['show_bullet'];

		global $tm_pb_pricing_tables_num, $tm_pb_pricing_tables_icon;

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( 'on' === $remove_featured_drop_shadow ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_featured_table',
				'declaration' => '-moz-box-shadow: none; -webkit-box-shadow: none; box-shadow: none;',
			) );
		}

		if ( 'off' === $show_bullet ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_pricing li:before',
				'declaration' => 'display: none;',
			) );
		}

		if ( 'on' === $center_list_items ) {
			$module_class .= ' tm_pb_centered_pricing_items';
		}

		if ( '' !== $featured_table_background_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_featured_table',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $featured_table_background_color )
				),
			) );
		}

		if ( '' !== $header_background_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_pricing_heading',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $header_background_color )
				),
			) );
		}

		if ( '' !== $featured_table_header_background_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_featured_table .tm_pb_pricing_heading',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $featured_table_header_background_color )
				),
			) );
		}

		if ( '' !== $featured_table_header_text_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_featured_table .tm_pb_pricing_heading h2',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $featured_table_header_text_color )
				),
			) );
		}

		if ( '' !== $featured_table_subheader_text_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_featured_table .tm_pb_best_value',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $featured_table_subheader_text_color )
				),
			) );
		}

		if ( '' !== $featured_table_price_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_featured_table .tm_pb_sum',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $featured_table_price_color )
				),
			) );
		}

		if ( '' !== $featured_table_text_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_featured_table .tm_pb_pricing_content',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $featured_table_text_color )
				),
			) );
		}

		if ( '' !== $bullet_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_pricing li:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $bullet_color )
				),
			) );
		}

		if ( '' !== $featured_table_bullet_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_featured_table .tm_pb_pricing li:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $featured_table_bullet_color )
				),
			) );
		}

		$content = $this->shortcode_content;

		$output = sprintf(
			'<div%3$s class="tm_pb_module tm_pb_pricing clearfix%2$s%4$s">
				%1$s
			</div>',
			$content,
			esc_attr( " tm_pb_pricing_{$tm_pb_pricing_tables_num}" ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' )
		);

		return $output;
	}

	function additional_shortcode_callback( $atts, $content = null, $function_name ) {
		$attributes = shortcode_atts( array(
			'available' => 'on',
		), $atts );

		$output = sprintf( '<li%2$s><span>%1$s</span></li>',
			$content,
			( 'on' !== $attributes['available'] ? ' class="tm_pb_not_available"' : '' )
		);
		return $output;
	}
}

new Tm_Builder_Module_Pricing_Tables;
