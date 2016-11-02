<?php
class Tm_Builder_Module_Number_Counter extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Number Counter', 'tm_builder' );
		$this->slug = 'tm_pb_number_counter';
		$this->icon = 'f295';

		$this->whitelisted_fields = array(
			'title',
			'number',
			'percent_sign',
			'counter_color',

			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'number'            => array( '0' ),
			'percent_sign'      => array( 'on' ),
			'counter_color'     => array( tm_builder_accent_color(), 'add_default_setting' ),
		);

		$this->custom_css_options = array(
			'percent' => array(
				'label'    => esc_html__( 'Percent', 'tm_builder' ),
				'selector' => '.percent',
			),
			'number_counter_title' => array(
				'label'    => esc_html__( 'Number Counter Title', 'tm_builder' ),
				'selector' => 'h3',
			),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_number_counter';
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
					'css'      => array(
						'main' => "{$this->main_css_element} .percent",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'use_margin' => false,
				'css' => array(
					'important' => 'all',
				),
			),
		);

		if ( tm_is_builder_plugin_active() ) {
			$this->advanced_options['fonts']['number']['css']['important'] = 'all';
		}
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for the counter.', 'tm_builder' ),
			),
			'number' => array(
				'label'           => esc_html__( 'Number', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( "Define a number for the counter. (Don't include the percentage sign, use the option below.)", 'tm_builder' ),
			),
			'percent_sign' => array(
				'label'             => esc_html__( 'Percent Sign', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'On', 'tm_builder' ),
					'off' => esc_html__( 'Off', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Here you can choose whether the percent sign should be added after the number set above.', 'tm_builder' ),
			),
			'counter_color' => array(
				'label'             => esc_html__( 'Counter Text Color', 'tm_builder' ),
				'type'              => 'color',
				'description'       => esc_html__( 'This will change the fill color for the bar.', 'tm_builder' ),
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
				'title',
				'counter_color',
			)
		);

		if ( tm_is_builder_plugin_active() ) {
			wp_enqueue_script( 'fittext' );
		}

		$this->_var( 'number', str_ireplace( '%', '', $this->_var( 'number' ) ) );

		$classes = array( 'tm_pb_bg_layout_light' );
		$atts    = array( 'data-number-value' => $this->_var( 'number' ) );
		$content = $this->get_template_part( 'number-counter.php' );

		TM_Builder_Element::set_style( $function_name, array(
			'selector'    => '%%order_class%% .percent',
			'declaration' => sprintf(
				'color: %1$s;',
				esc_attr( $this->_var( 'counter_color' ) )
			),
		) );

		$output = $this->wrap_module( $content, $classes, $function_name, $atts );

		return $output;
	}

	/**
	 * Returns percent sign
	 *
	 * @param  string $sign
	 * @return string
	 */
	public function nc_sign( $sign = '%' ) {
		if ( 'on' === $this->_var( 'percent_sign' ) ) {
			return $sign;
		}
	}
}

new Tm_Builder_Module_Number_Counter;
