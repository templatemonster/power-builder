<?php
class Tm_Builder_Module_Circle_Counter extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Circle Counter', 'tm_builder' );
		$this->slug = 'tm_pb_circle_counter';
		$this->icon = 'f1ce';

		$this->whitelisted_fields = array(
			'title',
			'number',
			'percent_sign',
			'rounded_bar',
			'circle_width',
			'circle_size',
			'bar_bg_color',
			'admin_label',
			'module_id',
			'module_class',
			'circle_color',
			'circle_color_alpha',
		);

		$this->fields_defaults = array(
			'number'            => array( '50' ),
			'percent_sign'      => array( 'on' ),
			'circle_width'      => array( 5 ),
			'bar_bg_color'      => array( tm_builder_accent_color(), 'add_default_setting' ),
			'circle_size'       => array( 20 ),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_circle_counter';
		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h3",
					),
				),
				'number'   => array(
					'label'    => esc_html__( 'Number', 'tm_builder' ),
					'hide_line_height' => true,
					'css'      => array(
						'main' => "{$this->main_css_element} .percent p",
					),
				),
			),
		);
		$this->custom_css_options = array(
			'percent' => array(
				'label'    => esc_html__( 'Percent Container', 'tm_builder' ),
				'selector' => '.percent',
			),
			'circle_counter_title' => array(
				'label'    => esc_html__( 'Circle Counter Title', 'tm_builder' ),
				'selector' => 'h3',
			),
			'percent_text' => array(
				'label'    => esc_html__( 'Percent Text', 'tm_builder' ),
				'selector' => '.percent p',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description' => esc_html__( 'Input a title for the circle counter.', 'tm_builder' ),
			),
			'number' => array(
				'label'             => esc_html__( 'Number', 'tm_builder' ),
				'type'              => 'range',
				'option_category'   => 'basic_option',
				'range_settings'    => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'default'           => 50,
				'number_validation' => true,
				'description'       => tm_get_safe_localization( __( "Define a number for the circle counter.", 'tm_builder' ) ),
			),
			'percent_sign' => array(
				'label'           => esc_html__( 'Percent Sign', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'tm_builder' ),
					'off' => esc_html__( 'Off', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Here you can choose whether the percent sign should be added after the number set above.', 'tm_builder' ),
			),
			'rounded_bar' => array(
				'label'           => esc_html__( 'Rounded bar', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'circle_width' => array(
				'label'           => esc_html__( 'Circle line width', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '50',
					'step' => '1',
				),
				'default'         => 5,
				'description'     => __( 'Width of the bar line in px', 'tm_builder' ),
			),
			'circle_size' => array(
				'label'           => esc_html__( 'Circle size', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'default'         => 20,
				'range_settings'  => array(
					'min'  => '20',
					'max'  => '300',
					'step' => '1',
				),
			),
			'bar_bg_color' => array(
				'label'             => esc_html__( 'Bar Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'This will change the fill color for the bar.', 'tm_builder' ),
			),
			'circle_color' => array(
				'label'             => esc_html__( 'Circle Color', 'tm_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'circle_color_alpha' => array(
				'label'           => esc_html__( 'Circle Color Opacity', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'range_settings'  => array(
					'min'  => '0.1',
					'max'  => '1.0',
					'step' => '0.05',
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

	function shortcode_callback( $atts, $content = null, $function_name ) {

		wp_enqueue_script( 'easypiechart' );

		$this->set_vars(
			array(
				'number',
				'percent_sign',
				'rounded_bar',
				'circle_width',
				'circle_size',
				'title',
				'module_id',
				'module_class',
				'bar_bg_color',
				'circle_color',
				'circle_color_alpha',
			)
		);

		if ( ! intval( $this->_var( 'circle_size' ) ) ) {
			$this->_var( 'circle_size', 110 );
		}

		if ( intval( $this->_var( 'circle_size' ) ) <= 2 * intval( $this->_var( 'circle_width' ) ) ) {
			return $this->wrap_module(
				esc_html__( 'Incorrect settings: circle size to small', 'tm_builder' ),
				array( 'tm_pb_bg_layout_light' ),
				$function_name
			);
		}

		TM_Builder_Element::set_style( $function_name, array(
			'selector'    => '%%order_class%%',
			'declaration' => sprintf(
				'width: %1$spx;',
				intval( $this->_var( 'circle_size' ) )
			),
		) );

		$content = $this->get_template_part( 'circle-counter.php' );

		$output = $this->wrap_module( $content, array( 'tm_pb_bg_layout_light' ), $function_name );

		return $output;
	}

	/**
	 * Return string with circle counter data attributes.
	 *
	 * @return string
	 */
	public function circle_data_atts() {

		$atts = array(
			'data-number-value' => intval( $this->_var( 'number' ) ),
			'data-bar-bg-color' => esc_attr( $this->_var( 'bar_bg_color' ) ),
			'data-size'         => intval( $this->_var( 'circle_size' ) ),
			'data-bar-type'     => ( 'on' === $this->_var( 'rounded_bar' ) ) ? 'round' : 'butt',
		);

		$optional_atts = array(
			'data-color'        => 'circle_color',
			'data-alpha'        => 'circle_color_alpha',
			'data-circle-width' => 'circle_width',
		);

		foreach ( $optional_atts as $attr => $var ) {
			if ( '' !== $this->_var( $var ) ) {
				$atts[ $attr ] = esc_attr( $this->_var( $var ) );
			}
		}

		$atts = apply_filters( 'tm_builder_circle_counter_data_atts', $atts );

		if ( empty( $atts ) ) {
			return;
		}

		$result = '';

		foreach ( $atts as $attr => $value ) {
			$result .= sprintf( ' %1$s="%2$s"', $attr, $value );
		}

		return $result;

	}

	/**
	 * Return symbol after number if allowed. % by default, can be passed custom.
	 *
	 * @param  string $sign Sign to show.
	 * @return string
	 */
	public function circle_sign( $sign = '%' ) {
		if ( 'on' === $this->_var( 'percent_sign' ) ) {
			return $sign;
		}
	}

}

new Tm_Builder_Module_Circle_Counter;
