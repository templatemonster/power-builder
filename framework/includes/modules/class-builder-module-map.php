<?php
class Tm_Builder_Module_Map extends Tm_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Map', 'tm_builder' );
		$this->slug            = 'tm_pb_map';
		$this->icon            = 'f278';
		$this->child_slug      = 'tm_pb_map_pin';
		$this->child_item_text = esc_html__( 'Pin', 'tm_builder' );

		$this->whitelisted_fields = array(
			'address',
			'zoom_level',
			'address_lat',
			'address_lng',
			'map_center_map',
			'icon_url',
			'map_style',
			'mouse_wheel',
			'admin_label',
			'module_id',
			'module_class',
			'use_grayscale_filter',
			'grayscale_filter_amount',
		);

		$this->fields_defaults = array(
			'zoom_level'           => array( '18', 'only_default_setting' ),
			'mouse_wheel'          => array( 'on' ),
			'use_grayscale_filter' => array( 'off' ),
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
				'class' => array( 'tm_pb_address' ),
				'description'       => esc_html__( 'Enter an address for the map center point, and the address will be geocoded and displayed on the map below.', 'tm_builder' ),
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
			'icon_url' => array(
				'label'              => esc_html__( 'Icon url', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an marker', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Marker', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Marker', 'tm_builder' ),
				'description'        => esc_html__( 'Upload your marker, or type in the URL to the marker you would like to display.', 'tm_builder' ),
			),
			'map_style' => array(
				'label'           => esc_html__( 'Map style', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => get_google_map_styles(),
			),
			'mouse_wheel' => array(
				'label'           => esc_html__( 'Mouse Wheel Zoom', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on'  => esc_html__( 'On', 'tm_builder' ),
					'off' => esc_html__( 'Off', 'tm_builder' ),
				),
				'description' => esc_html__( 'Here you can choose whether the zoom level will be controlled by mouse wheel or not.', 'tm_builder' ),
			),
			/*'use_grayscale_filter' => array(
				'label'           => esc_html__( 'Use Grayscale Filter', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'     => array(
					'#tm_pb_grayscale_filter_amount',
				),
				'tab_slug' => 'advanced',
			),
			'grayscale_filter_amount' => array(
				'label'           => esc_html__( 'Grayscale Filter Amount (%)', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
			),*/
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
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$address_lat             = $this->shortcode_atts['address_lat'];
		$address_lng             = $this->shortcode_atts['address_lng'];
		$icon_url                = $this->shortcode_atts['icon_url'];
		$map_style               = $this->shortcode_atts['map_style'];
		$zoom_level              = $this->shortcode_atts['zoom_level'];
		$mouse_wheel             = $this->shortcode_atts['mouse_wheel'];
		$use_grayscale_filter    = $this->shortcode_atts['use_grayscale_filter'];
		$grayscale_filter_amount = $this->shortcode_atts['grayscale_filter_amount'];

		wp_enqueue_script( 'google-maps-api' );

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		$all_pins_content = $this->shortcode_content;

		$grayscale_filter_data = '';
		/*if ( 'on' === $use_grayscale_filter && '' !== $grayscale_filter_amount ) {
			$grayscale_filter_data = sprintf( ' data-grayscale="%1$s"', esc_attr( $grayscale_filter_amount ) );
		}
		*/
		$map_marker = '';

		if ( ! empty( $icon_url ) ){
			$map_marker_attachment_id = tm_get_image_id( $icon_url );

			if ( isset( $map_marker_attachment_id ) ) {
				$map_marker = wp_get_attachment_image_src( $map_marker_attachment_id );
				$map_marker = json_encode( $map_marker );
			}
		}

		$map_style = get_map_style_json( $map_style );

		$output = sprintf(
			'<div%5$s class="tm_pb_module tm_pb_map_container%6$s"%8$s>
				<div class="tm_pb_map" data-center-lat="%1$s" data-center-lng="%2$s" data-zoom="%3$d" data-mouse-wheel="%7$s" data-marker-icon=\'%9$s\' data-map-style=\'%10$s\'></div>
				%4$s
			</div>',
			esc_attr( $address_lat ),
			esc_attr( $address_lng ),
			esc_attr( $zoom_level ),
			$all_pins_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $mouse_wheel ),
			$grayscale_filter_data,
			$map_marker,
			$map_style
		);

		return $output;
	}
}
new Tm_Builder_Module_Map;

