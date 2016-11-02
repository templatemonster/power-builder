<?php
class Tm_Builder_Module_Divider extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Divider', 'tm_builder' );
		$this->slug = 'tm_pb_divider';
		$this->icon = 'f07d';

		$this->defaults = array(
			'divider_style'   => 'solid',
			'divider_width'   => '100',
			'height'          => '1',
			'height_laptop'   => '1',
			'height_tablet'   => '1',
			'height_phone'    => '1',
		);

		// Show divider options is modifieable via customizer
		$this->show_divider_options = array(
			'off' => esc_html__( "Don't Show Divider", 'tm_builder' ),
			'on'  => esc_html__( 'Show Divider', 'tm_builder' ),
		);

		if ( ! tm_is_builder_plugin_active() && true === tm_get_option( 'tm_pb_divider-show_divider', false ) ) {
			$this->show_divider_options = array_reverse( $this->show_divider_options );
			$show_divider_default = 'on';
		} else {
			$show_divider_default = 'off';
		}

		$this->whitelisted_fields = array(
			'color',
			'show_divider',
			'height',
			'height_laptop',
			'height_tablet',
			'height_phone',
			'admin_label',
			'module_id',
			'module_class',
			'divider_style',
			'divider_width',
		);

		$this->fields_defaults = array(
			'color'           => array( '#ffffff', 'only_default_setting' ),
			'show_divider'    => array( $show_divider_default ),
			'height'          => array( '1' ),
			'height_laptop'   => array( '1' ),
			'height_tablet'   => array( '1' ),
			'height_phone'    => array( '1' ),
			'divider_width'   => array( '100' ),
		);

		$this->advanced_options = array(
			'custom_margin_padding' => array(
				'use_padding' => false,
				'css' => array(
					'important' => 'all',
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'color' => array(
				'label'       => esc_html__( 'Color', 'tm_builder' ),
				'type'        => 'color-alpha',
				'description' => esc_html__( 'This will adjust the color of the 1px divider line.', 'tm_builder' ),
			),
			'show_divider' => array(
				'label'             => esc_html__( 'Visibility', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => $this->show_divider_options,
				'affects' => array(
					'#tm_pb_divider_style',
					'#tm_pb_divider_width',
				),
				'description'        => esc_html__( 'This settings turns on and off the 1px divider line, but does not affect the divider height.', 'tm_builder' ),
			),
			'height' => array(
				'label'           => esc_html__( 'Height', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'default'         => '1',
				'range_settings' => array(
					'min'  => 1,
					'max'  => 100,
					'step' => 1,
				),
				'mobile_options'      => true,
				'mobile_global'       => true,
				'description'     => esc_html__( 'Define how much space should be added below the divider.', 'tm_builder' ),
			),
			'height_laptop' => array(
				'type' => 'skip',
			),
			'height_tablet' => array(
				'type' => 'skip',
			),
			'height_phone' => array(
				'type' => 'skip',
			),
			'divider_style' => array(
				'label'             => esc_html__( 'Divider Style', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => tm_builder_get_border_styles(),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
			),
			'divider_width' => array(
				'label'             => esc_html__( 'Divider Width', 'tm_builder' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'default'           => '100',
				'range_settings' => array(
					'min'  => 1,
					'max'  => 200,
					'step' => 1,
				),
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
		$module_id        = $this->shortcode_atts['module_id'];
		$module_class     = $this->shortcode_atts['module_class'];
		$color            = $this->shortcode_atts['color'];
		$show_divider     = $this->shortcode_atts['show_divider'];
		$height           = $this->shortcode_atts['height'];
		$height_laptop    = $this->shortcode_atts['height_laptop'];
		$height_tablet    = $this->shortcode_atts['height_tablet'];
		$height_phone     = $this->shortcode_atts['height_phone'];
		$divider_style    = $this->shortcode_atts['divider_style'];
		$divider_width    = $this->shortcode_atts['divider_width'];

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $height ) {
			$height_values = array(
				'desktop' => $height,
				'laptop'  => $height_laptop,
				'tablet'  => $height_tablet,
				'phone'   => $height_phone,
			);

			tm_pb_generate_responsive_css(
				$height_values,
				'%%order_class%%',
				'height',
				$function_name
			);

			tm_pb_generate_responsive_css(
				$height_values,
				'%%order_class%%:before',
				'border-top-width',
				$function_name
			);
		}

		$style = '';

		if ( '' !== $color && 'on' === $show_divider ) {
			$style .= sprintf( ' border-top-color: %s;',
				esc_attr( $color )
			);

			$divider_style = ( $this->defaults['divider_style'] !== $divider_style ) ? $divider_style : $this->defaults['divider_style'];
			$style .= sprintf( ' border-top-style: %s;',
				esc_attr( $divider_style )
			);

			$divider_width = ( $this->defaults['divider_width'] !== $divider_width ) ? $divider_width : $this->defaults['divider_width'];
			$style .= sprintf( ' width: %1$s%%; margin-left: -%2$s%%;',
				esc_attr( $divider_width ),
				esc_attr( ( int )$divider_width / 2 )
			);

			if ( '' !== $style ) { TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%:before',
					'declaration' => ltrim( $style )
				) );
			}
		}

		$output = sprintf(
			'<hr%2$s class="tm_pb_module tm_pb_space%1$s%3$s" />',
			( 'on' === $show_divider ? ' tm_pb_divider' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' )
		);

		return $output;
	}
}

new Tm_Builder_Module_Divider;
