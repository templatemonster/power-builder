<?php
class Tm_Builder_Module_Button extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Button', 'power-builder' );
		$this->slug = 'tm_pb_button';
		$this->icon = 'f0a6';

		$this->whitelisted_fields = array(
			'button_url',
			'url_new_window',
			'simple_link',
			'button_text',
			'button_alignment',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'url_new_window'        => array( 'off' ),
			'simple_link'           => array( 'off' ),
			'background_color'      => array( tm_builder_accent_color(), 'add_default_setting' ),
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_options = array(
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'power-builder' ),
					'css' => array(
						'main' => $this->main_css_element,
					),
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'main' => $this->main_css_element,
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'button_url' => array(
				'label'           => esc_html__( 'Button URL', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for your button.', 'power-builder' ),
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'power-builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'power-builder' ),
					'on'  => esc_html__( 'In The New Tab', 'power-builder' ),
				),
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'power-builder' ),
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text.', 'power-builder' ),
			),
			'button_alignment' => array(
				'label'           => esc_html__( 'Button alignment', 'power-builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'left'   => esc_html__( 'Left', 'power-builder' ),
					'center' => esc_html__( 'Center', 'power-builder' ),
					'right'  => esc_html__( 'Right', 'power-builder' ),
				),
				'description'     => esc_html__( 'Here you can define the alignemnt of Button', 'power-builder' ),
			),
			'simple_link' => array(
				'label'           => esc_html__( 'Simple link', 'power-builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'power-builder' ),
					'on'  => esc_html__( 'Yes', 'power-builder' ),
				),
				'description' => esc_html__( 'Button simple link view.', 'power-builder' ),
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
		$module_id             = $this->shortcode_atts['module_id'];
		$module_class          = $this->shortcode_atts['module_class'];
		$button_url            = $this->shortcode_atts['button_url'];
		$button_text           = $this->shortcode_atts['button_text'];
		$url_new_window        = $this->shortcode_atts['url_new_window'];
		$simple_link           = $this->shortcode_atts['simple_link'];
		$custom_icon           = $this->shortcode_atts['button_icon'];
		$button_custom         = $this->shortcode_atts['custom_button'];
		$button_alignment      = $this->shortcode_atts['button_alignment'];
		$button_icon_placement = $this->shortcode_atts['button_icon_placement'];

		// Nothing to output if neither Button Text nor Button URL defined
		if ( '' === $button_text && '' === $button_url ) {
			return;
		}

		if ( '' === $custom_icon) {
			$custom_icon = 'f18e';
		}

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		$module_class .= " tm_pb_module";

		$icon_position = $this->get_module_cache( $function_name, 'button_icon_pos', 'right' );
		if ( 'right' === $icon_position ) {
			$module_class .= ' tm_pb_icon_right';
		} else {
			$module_class .= ' tm_pb_icon_left';
		}


		$icon        = esc_attr( tm_pb_process_font_icon( $custom_icon ) );
		$icon_family = tm_builder_get_icon_family();

		$icon_marker = ( '' !== $icon && 'on' === $button_custom ) ? '<span class="tm_pb_button_icon">' . $icon . '</span>' : '';

		if ( $icon_family ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_custom_button_icon .tm_pb_button_icon',
				'declaration' => sprintf( 'font-family: "%1$s" !important;', esc_attr( $icon_family ) ),
			) );
		}

		if ( 'on' === $simple_link ) {

		}

		$output = sprintf(
			'<div class="tm_pb_button_module_wrapper tm_pb_module%8$s">
				<a class="tm_pb_button%5$s%7$s%9$s%10$s" href="%1$s"%3$s%4$s%6$s>%2$s</a>
			</div>',
			tm_builder_tools()->render_url( $button_url ),
			sprintf(
				'%1$s%2$s%3$s',
				'left' === $button_icon_placement ? $icon_marker : '',
				'' !== $button_text ? esc_html( $button_text ) : tm_builder_tools()->render_url( $button_url ),
				'right' === $button_icon_placement ? $icon_marker : ''
			),
			( 'on' === $url_new_window ? ' target="_blank"' : '' ),
			sprintf( ' data-icon="%1$s"', $icon ),
			' tm_pb_custom_button_icon',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'right' === $button_alignment || 'center' === $button_alignment ? sprintf( ' tm_pb_button_alignment_%1$s', esc_attr( $button_alignment ) ) : '',
			'left' === $button_icon_placement ? sprintf( ' tm_pb_button_icon_alignment_%1$s', esc_attr( $button_icon_placement ) ) : '',
			'on' === $simple_link ? ' tm_pb_button_simple_link' : ''
		);

		return $output;
	}
}
new Tm_Builder_Module_Button;
