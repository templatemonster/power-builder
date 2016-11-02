<?php
class Tm_Builder_Module_Sidebar extends Tm_Builder_Module {

	function init() {

		$this->name = esc_html__( 'Sidebar', 'tm_builder' );
		$this->slug = 'tm_pb_sidebar';
		$this->icon = 'f0db';

		$this->whitelisted_fields = array(
			'orientation',
			'area',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'orientation'       => array( 'left' ),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_widget_area';
		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h3, {$this->main_css_element} h4, {$this->main_css_element} .widget-title",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element}, {$this->main_css_element} li, {$this->main_css_element} li:before, {$this->main_css_element} a",
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
		);
		$this->custom_css_options = array(
			'widget' => array(
				'label'    => esc_html__( 'Widget', 'tm_builder' ),
				'selector' => '.tm_pb_widget',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'tm_builder' ),
				'selector' => 'h4.widgettitle',
			),
		);
	}

	/**
	 * Maybe add custom sidebar into sidebar widgets array to show it in builder module.
	 */
	function maybe_set_custom_sidebar() {

		if ( ! $this->_var( 'area' ) ) {
			return;
		}

		if ( class_exists( 'Cherry_Custom_Sidebars_Methods' ) ) {
			$custom_sidebars_methods = new Cherry_Custom_Sidebars_Methods();
		} elseif ( class_exists( 'Cherry_Sidebar_Utils' ) ) {
			$custom_sidebars_methods = new Cherry_Sidebar_Utils();
		} else {
			return;
		}

		global $wp_registered_sidebars;

		if ( isset( $wp_registered_sidebars[ $this->_var( 'area' ) ] ) ) {
			return;
		}

		$custom_sidebar = $custom_sidebars_methods->get_custom_sidebar_array();

		if ( ! isset( $custom_sidebar[ $this->_var( 'area' ) ] ) ) {
			return;
		}

		$wp_registered_sidebars[ $this->_var( 'area' ) ] = $custom_sidebar[ $this->_var( 'area' ) ];

	}

	function get_fields() {
		$fields = array(
			'orientation' => array(
				'label'             => esc_html__( 'Orientation', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'left'  => esc_html__( 'Left', 'tm_builder' ),
					'right' => esc_html__( 'Right', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Choose which side of the page your sidebar will be on. This setting controls text orientation and border position.', 'tm_builder' ),
			),
			'area' => array(
				'label'           => esc_html__( 'Widget Area', 'tm_builder' ),
				'renderer'        => 'tm_builder_get_widget_areas',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Select a widget-area that you would like to display. You can create new widget areas within the Appearances > Widgets tab.', 'tm_builder' )
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
		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$orientation       = $this->shortcode_atts['orientation'];
		$area              = "" === $this->shortcode_atts['area'] ? $this->get_default_area() : $this->shortcode_atts['area'];

		$this->_var( 'area', $area );

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		$widgets = '';

		ob_start();

		$this->maybe_set_custom_sidebar();

		if ( is_active_sidebar( $area ) ) {
			dynamic_sidebar( $area );
		}

		$widgets = ob_get_contents();

		ob_end_clean();

		$class = " tm_pb_module tm_pb_bg_layout_light";

		$output = sprintf(
			'<div%4$s class="tm_pb_widget_area %6$s %2$s clearfix%3$s%5$s">
				%1$s
			</div> <!-- .tm_pb_widget_area -->',
			$widgets,
			esc_attr( "tm_pb_widget_area_{$orientation}" ),
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			$area
		);

		return $output;
	}

	function get_default_area() {
		global $wp_registered_sidebars;

		if ( ! empty( $wp_registered_sidebars ) ) {
			// Pluck sidebar ids
			$sidebar_ids = wp_list_pluck( $wp_registered_sidebars, 'id' );

			// Return first sidebar id
			return array_shift( $sidebar_ids );
		}

		return "";
	}
}
new Tm_Builder_Module_Sidebar;
