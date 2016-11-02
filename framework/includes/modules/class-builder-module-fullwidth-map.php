<?php
class Tm_Builder_Module_Fullwidth_Map extends Tm_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Fullwidth Map', 'tm_builder' );
		$this->slug            = 'tm_pb_fullwidth_map';
		$this->fullwidth       = true;
		$this->child_slug      = 'tm_pb_map_pin';
		$this->child_item_text = esc_html__( 'Pin', 'tm_builder' );

		$this->whitelisted_fields = array(
			'address',
			'zoom_level',
			'address_lat',
			'address_lng',
			'map_center_map',
			'mouse_wheel',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'zoom_level'  => array( '18', 'only_default_setting' ),
			'mouse_wheel' => array( 'on' ),
		);
	}

	function get_fields() {
		$fields = array(
			'address' => array(
				'label'             => esc_html__( 'Map Center Address', 'tm_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'additional_button' => sprintf(
					' <a href="#" class="tm_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'tm_builder' )
				),
				'class'       => array( 'tm_pb_address' ),
				'description' => esc_html__( 'Enter an address for the map center point, and the address will be geocoded and displayed on the map below.', 'tm_builder' ),
			),
			'zoom_level' => array(
				'type'    => 'hidden',
				'class'   => array( 'tm_pb_zoom_level' ),
			),
			'address_lat' => array(
				'type'  => 'hidden',
				'class' => array( 'tm_pb_address_lat' ),
			),
			'address_lng' => array(
				'type'  => 'hidden',
				'class' => array( 'tm_pb_address_lng' ),
			),
			'map_center_map' => array(
				'renderer'              => 'tm_builder_generate_center_map_setting',
				'use_container_wrapper' => false,
				'option_category'       => 'basic_option',
			),
			'mouse_wheel' => array(
				'label'           => esc_html__( 'Mouse Wheel Zoom', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'tm_builder' ),
					'off' => esc_html__( 'Off', 'tm_builder' ),
				),
				'description' => esc_html__( 'Here you can choose whether the zoom level will be controlled by mouse wheel or not.', 'tm_builder' ),
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
		$module_id    = $this->shortcode_atts['module_id'];
		$module_class = $this->shortcode_atts['module_class'];
		$address_lat  = $this->shortcode_atts['address_lat'];
		$address_lng  = $this->shortcode_atts['address_lng'];
		$zoom_level   = $this->shortcode_atts['zoom_level'];
		$mouse_wheel  = $this->shortcode_atts['mouse_wheel'];

		wp_enqueue_script( 'google-maps-api' );

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		$all_pins_content = $this->shortcode_content;

		$output = sprintf(
			'<div%5$s class="tm_pb_module tm_pb_map_container%6$s">
				<div class="tm_pb_map" data-center-lat="%1$s" data-center-lng="%2$s" data-zoom="%3$d" data-mouse-wheel="%7$s"></div>
				%4$s
			</div>',
			esc_attr( $address_lat ),
			esc_attr( $address_lng ),
			esc_attr( $zoom_level ),
			$all_pins_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $mouse_wheel )
		);

		return $output;
	}
}
new Tm_Builder_Module_Fullwidth_Map;

