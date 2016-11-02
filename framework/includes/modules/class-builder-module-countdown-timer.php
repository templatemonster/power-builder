<?php
class Tm_Builder_Module_Countdown_Timer extends Tm_Builder_Module {

	public $function_name;

	function init() {
		$this->name = esc_html__( 'Countdown Timer', 'tm_builder' );
		$this->slug = 'tm_pb_countdown_timer';
		$this->icon = 'f073';

		$this->whitelisted_fields = array(
			'title',
			'date_time',
			'use_background_color',
			'background_color',
			'timer_layout',
			'circle_background',
			'circle_size',
			'circle_size_laptop',
			'circle_size_tablet',
			'circle_size_phone',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'use_background_color' => array( 'on' ),
			'background_color'     => array( tm_builder_accent_color(), 'only_default_setting' ),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_countdown_timer';
		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h4",
					),
				),
				'numbers' => array(
					'label'    => esc_html__( 'Numbers', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .section span.value",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'label' => array(
					'label'    => esc_html__( 'Label', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .section span.label",
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
				'use_background_color' => false,
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);
		$this->custom_css_options = array(
			'container' => array(
				'label'    => esc_html__( 'Container', 'tm_builder' ),
				'selector' => '.tm_pb_countdown_timer_container',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'tm_builder' ),
				'selector' => '.title',
			),
			'timer_section' => array(
				'label'    => esc_html__( 'Timer Section', 'tm_builder' ),
				'selector' => '.section',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Countdown Timer Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This is the title displayed for the countdown timer.', 'tm_builder' ),
			),
			'date_time' => array(
				'label'           => esc_html__( 'Countdown To', 'tm_builder' ),
				'type'            => 'date_picker',
				'option_category' => 'basic_option',
				'description'     => tm_get_safe_localization( sprintf( __( 'This is the date the countdown timer is counting down to. Your countdown timer is based on your timezone settings in your <a href="%1$s" target="_blank" title="WordPress General Settings">WordPress General Settings</a>', 'tm_builder' ), esc_url( admin_url( 'options-general.php' ) ) ) ),
			),
			'use_background_color' => array(
				'label'           => esc_html__( 'Use Background Color', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'on' => esc_html__( 'Yes', 'tm_builder' ),
					'off'  => esc_html__( 'No', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_background_color',
				),
				'description' => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'tm_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'depends_default'   => true,
				'description'       => esc_html__( 'Here you can define a custom background color for your countdown timer.', 'tm_builder' ),
			),
			'timer_layout' => array(
				'label'             => esc_html__( 'Layout', 'tm_builder' ),
				'type'              => 'select',
				'options'           => array(
					'flat'   => esc_html__( 'Flat', 'tm_builder' ),
					'circle' => esc_html__( 'Circle', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_circle_background',
					'#tm_pb_circle_size',
				),
			),
			'circle_background' => array(
				'label'               => esc_html__( 'Circle Background Color', 'tm_builder' ),
				'type'                => 'color-alpha',
				'depends_show_if_not' => 'flat',
			),
			'circle_size' => array(
				'label'           => esc_html__( 'Circle Size', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '90',
				'range_settings' => array(
					'min'  => 50,
					'max'  => 250,
					'step' => 1,
				),
				'depends_show_if_not' => 'flat',
				'mobile_options'      => true,
				'mobile_global'       => true,
			),
			'circle_size_laptop' => array(
				'type' => 'skip',
			),
			'circle_size_tablet' => array(
				'type' => 'skip',
			),
			'circle_size_phone' => array(
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

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array(
				'title',
				'date_time',
				'background_color',
				'timer_layout',
				'circle_background',
				'circle_size',
				'circle_size_laptop',
				'circle_size_tablet',
				'circle_size_phone',
				'use_background_color',
			)
		);

		$this->function_name = $function_name;

		$end_date = gmdate( 'M d, Y H:i:s', strtotime( $this->_var( 'date_time' ) ) );

		$gmt_offset        = get_option( 'gmt_offset' );
		$gmt_divider       = '-' === substr( $gmt_offset, 0, 1 ) ? '-' : '+';
		$gmt_offset_hour   = str_pad( abs( intval( $gmt_offset ) ), 2, "0", STR_PAD_LEFT );
		$gmt_offset_minute = str_pad( ( ( abs( $gmt_offset ) * 100 ) % 100 ) * ( 60 / 100 ), 2, "0", STR_PAD_LEFT );
		$gmt               = "GMT{$gmt_divider}{$gmt_offset_hour}{$gmt_offset_minute}";

		if ( $this->_var( 'background_color' ) && 'on' == $this->_var( 'use_background_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_attr( $this->_var( 'background_color' ) )
				),
			) );
		}

		$classes = array( 'tm_pb_bg_layout_light' );
		$atts    = array(
			'data-end-timestamp' => esc_attr( strtotime( "{$end_date} {$gmt}" ) ),
		);

		if ( 'circle' === $this->_var( 'timer_layout' ) ) {
			$classes[] = 'tm_pb_countdown_timer_circle_layout';
		}

		if ( 'circle' === $this->_var( 'timer_layout' ) && $this->_var( 'circle_background' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .section.values',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_attr( $this->_var( 'circle_background' ) )
				),
			) );
		}

		if ( 'circle' === $this->_var( 'timer_layout' ) && $this->_var( 'circle_size' ) ) {
			$this->set_circle_size();
		}

		$content = $this->get_template_part( 'countdown-timer.php' );
		$output  = $this->wrap_module( $content, $classes, $function_name, $atts );

		return $output;
	}

	/**
	 * Set sircle size values
	 */
	public function set_circle_size() {

		$circle_size_d  = intval( $this->_var( 'circle_size' ) );
		$circle_size_l  = intval( $this->_var( 'circle_size_laptop' ) );
		$circle_size_t  = intval( $this->_var( 'circle_size_tablet' ) );
		$circle_size_ph = intval( $this->_var( 'circle_size_phone' ) );

		if ( ! $circle_size_l ) {
			$circle_size_l = $circle_size_d;
		}

		if ( ! $circle_size_t ) {
			$circle_size_t = $circle_size_l;
		}

		if ( ! $circle_size_ph ) {
			$circle_size_ph = $circle_size_t;
		}


		if ( '' !== $this->_var( 'circle_size_tablet' ) || '' !== $this->_var( 'circle_size_laptop' ) || '' !== $this->_var( 'circle_size_phone' ) || '' !== $this->_var( 'circle_size' ) ) {
			$max_width_values = array(
				'desktop' => $this->_var( 'circle_size' ),
				'laptop'  => $this->_var( 'circle_size_laptop' ),
				'tablet'  => $this->_var( 'circle_size_tablet' ),
				'phone'   => $this->_var( 'circle_size_phone' ),
			);


		}

		if ( ! empty( $circle_size_d ) || ! empty( $circle_size_l ) || ! empty( $circle_size_t ) || ! empty( $circle_size_ph ) ) {

			$radius_d  = round( $circle_size_d / 2 );
			$radius_l  = round( $circle_size_l / 2 );
			$radius_t  = round( $circle_size_t / 2 );
			$radius_ph = round( $circle_size_ph / 2 );

			$sizes = array(
				'desktop' => $circle_size_d,
				'laptop'  => $circle_size_l,
				'tablet'  => $circle_size_t,
				'phone'   => $circle_size_ph,
			);

			$radius = array(
				'desktop' => $radius_d,
				'laptop'  => $radius_l,
				'tablet'  => $radius_t,
				'phone'   => $radius_ph,
			);

			tm_pb_generate_responsive_css( $sizes, '%%order_class%% .section.values', 'width', $this->function_name );
			tm_pb_generate_responsive_css( $sizes, '%%order_class%% .section.values', 'height', $this->function_name );
			tm_pb_generate_responsive_css( $radius, '%%order_class%% .section.values', 'border-radius', $this->function_name );
		}

	}

}

new Tm_Builder_Module_Countdown_Timer;
