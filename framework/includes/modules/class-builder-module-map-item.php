<?php
class Tm_Builder_Module_Map_Item extends Tm_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Pin', 'tm_builder' );
		$this->slug                        = 'tm_pb_map_pin';
		$this->type                        = 'child';
		$this->child_title_var             = 'title';
		$this->custom_css_tab              = false;

		$this->whitelisted_fields = array(
			'title',
			'pin_address',
			'zoom_level',
			'pin_address_lat',
			'pin_address_lng',
			'map_center_map',
			'content_new',
		);

		$this->advanced_setting_title_text = esc_html__( 'New Pin', 'tm_builder' );
		$this->settings_text               = esc_html__( 'Pin Settings', 'tm_builder' );
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title will be used within the tab button for this tab.', 'tm_builder' ),
			),
			'pin_address' => array(
				'label'             => esc_html__( 'Map Pin Address', 'tm_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'class'             => array( 'tm_pb_pin_address' ),
				'description'       => esc_html__( 'Enter an address for this map pin, and the address will be geocoded and displayed on the map below.', 'tm_builder' ),
				'additional_button' => sprintf(
					'<a href="#" class="tm_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'tm_builder' )
				),
			),
			'zoom_level' => array(
				'renderer'        => 'tm_builder_generate_pin_zoom_level_input',
				'option_category' => 'basic_option',
				'class'           => array( 'tm_pb_zoom_level' ),
			),
			'pin_address_lat' => array(
				'type'  => 'hidden',
				'class' => array( 'tm_pb_pin_address_lat' ),
			),
			'pin_address_lng' => array(
				'type'  => 'hidden',
				'class' => array( 'tm_pb_pin_address_lng' ),
			),
			'map_center_map' => array(
				'renderer'              => 'tm_builder_generate_center_map_setting',
				'option_category'       => 'basic_option',
				'use_container_wrapper' => false,
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'tm_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the infobox for the pin.', 'tm_builder' ),
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $tm_pb_tab_titles;

		$title = $this->shortcode_atts['title'];
		$pin_address_lat = $this->shortcode_atts['pin_address_lat'];
		$pin_address_lng = $this->shortcode_atts['pin_address_lng'];

		$replace_htmlentities = array( '&#8221;' => '', '&#8243;' => '' );

		if ( ! empty( $pin_address_lat ) ) {
			$pin_address_lat = strtr( $pin_address_lat, $replace_htmlentities );
		}
		if ( ! empty( $pin_address_lng ) ) {
			$pin_address_lng = strtr( $pin_address_lng, $replace_htmlentities );
		}

		$content = $this->shortcode_content;

		$output = sprintf(
			'<div class="tm_pb_map_pin" data-lat="%1$s" data-lng="%2$s" data-title="%5$s">
				<h3 style="margin-top: 10px;">%3$s</h3>
				%4$s
			</div>',
			esc_attr( $pin_address_lat ),
			esc_attr( $pin_address_lng ),
			esc_html( $title ),
			( '' != $content ? sprintf( '<div class="infowindow">%1$s</div>', $content ) : '' ),
			esc_attr( $title )
		);

		return $output;
	}
}
new Tm_Builder_Module_Map_Item;

