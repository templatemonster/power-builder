<?php
class Tm_Builder_Module_Bar_Counters extends Tm_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Bar Counters', 'tm_builder' );
		$this->icon            = 'f0ae';
		$this->slug            = 'tm_pb_counters';
		$this->child_slug      = 'tm_pb_counter';
		$this->child_item_text = esc_html__( 'Bar Counter', 'tm_builder' );

		$this->whitelisted_fields = array(
			'background_color',
			'bar_bg_color',
			'use_percentages',
			'admin_label',
			'module_id',
			'module_class',
			'bar_top_padding',
			'bar_bottom_padding',
			'border_radius',
			'bar_bottom_padding_laptop',
			'bar_top_padding_laptop',
			'bar_bottom_padding_tablet',
			'bar_top_padding_tablet',
			'bar_bottom_padding_phone',
			'bar_top_padding_phone',
		);

		$this->fields_defaults = array(
			'background_color'  => array( tm_builder_secondary_color(), 'add_default_setting' ),
			'bar_bg_color'      => array( tm_builder_accent_color(), 'add_default_setting' ),
			'use_percentages'   => array( 'on' ),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_counters';
		$this->defaults         = array(
			'border_radius' => '0',
		);
		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_counter_title",
					),
				),
				'percent'   => array(
					'label'    => esc_html__( 'Percent', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_counter_amount",
					),
				),
			),
			'border' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .tm_pb_counter_container",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
		);
		$this->custom_css_options = array(
			'counter_title' => array(
				'label'    => esc_html__( 'Counter Title', 'tm_builder' ),
				'selector' => '.tm_pb_counter_title',
			),
			'counter_container' => array(
				'label'    => esc_html__( 'Counter Container', 'tm_builder' ),
				'selector' => '.tm_pb_counter_container',
			),
			'counter_amount' => array(
				'label'    => esc_html__( 'Counter Amount', 'tm_builder' ),
				'selector' => '.tm_pb_counter_amount',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'This will adjust the color of the empty space in the bar (currently gray).', 'tm_builder' ),
			),
			'bar_bg_color' => array(
				'label'             => esc_html__( 'Bar Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'This will change the fill color for the bar.', 'tm_builder' ),
			),
			'use_percentages' => array(
				'label'             => esc_html__( 'Use Percentages', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'On', 'tm_builder' ),
					'off' => esc_html__( 'Off', 'tm_builder' ),
				),
			),
			'bar_top_padding' => array(
				'label'           => esc_html__( 'Bar Top Padding', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'bar_bottom_padding' => array(
				'label'           => esc_html__( 'Bar Bottom Padding', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'border_radius' => array(
				'label'             => esc_html__( 'Border Radius', 'tm_builder' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'tab_slug'          => 'advanced',
			),
			'bar_bottom_padding_laptop' => array(
				'type' => 'skip',
			),
			'bar_top_padding_laptop' => array(
				'type' => 'skip',
			),
			'bar_bottom_padding_tablet' => array(
				'type' => 'skip',
			),
			'bar_bottom_padding_phone' => array(
				'type' => 'skip',
			),
			'bar_top_padding_tablet' => array(
				'type' => 'skip',
			),
			'bar_top_padding_phone' => array(
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

	function pre_shortcode_content() {
		global $tm_pb_counters_settings;

		$background_color          = $this->shortcode_atts['background_color'];
		$bar_bg_color              = $this->shortcode_atts['bar_bg_color'];
		$use_percentages           = $this->shortcode_atts['use_percentages'];
		$bar_bottom_padding_laptop = $this->shortcode_atts['bar_bottom_padding_laptop'];
		$bar_top_padding_laptop    = $this->shortcode_atts['bar_top_padding_laptop'];
		$bar_top_padding           = $this->shortcode_atts['bar_top_padding'];
		$bar_bottom_padding        = $this->shortcode_atts['bar_bottom_padding'];
		$bar_top_padding_tablet    = $this->shortcode_atts['bar_top_padding_tablet'];
		$bar_bottom_padding_tablet = $this->shortcode_atts['bar_bottom_padding_tablet'];
		$bar_top_padding_phone     = $this->shortcode_atts['bar_top_padding_phone'];
		$bar_bottom_padding_phone  = $this->shortcode_atts['bar_bottom_padding_phone'];
		$border_radius             = $this->shortcode_atts['border_radius'];

		$tm_pb_counters_settings = array(
			'background_color'          => $background_color,
			'bar_bg_color'              => $bar_bg_color,
			'use_percentages'           => $use_percentages,
			'bar_bottom_padding_laptop' => $bar_bottom_padding_laptop,
			'bar_top_padding_laptop'    => $bar_top_padding_laptop,
			'bar_top_padding'           => $bar_top_padding,
			'bar_bottom_padding'        => $bar_bottom_padding,
			'bar_top_padding_tablet'    => $bar_top_padding_tablet,
			'bar_bottom_padding_tablet' => $bar_bottom_padding_tablet,
			'bar_top_padding_phone'     => $bar_top_padding_phone,
			'bar_bottom_padding_phone'  => $bar_bottom_padding_phone,
			'border_radius'             => $border_radius,
		);
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$output = $this->wrap_module(
			$this->shortcode_content,
			array( 'tm_pb_bg_layout_light', 'tm-waypoint' ),
			$function_name
		);

		return $output;
	}
}

new Tm_Builder_Module_Bar_Counters;
