<?php
class Tm_Builder_Module_Bar_Counters_Item extends Tm_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Bar Counter', 'tm_builder' );
		$this->slug                        = 'tm_pb_counter';
		$this->type                        = 'child';
		$this->child_title_var             = 'content_new';

		$this->whitelisted_fields = array(
			'content_new',
			'percent',
			'background_color',
			'bar_background_color',
			'label_color',
			'percentage_color',
		);

		$this->fields_defaults = array(
			'percent' => array( '0' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Bar Counter', 'tm_builder' );
		$this->settings_text               = esc_html__( 'Bar Counter Settings', 'tm_builder' );
		$this->defaults                    = array(
			'border_radius' => '0',
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
			'content_new' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for your bar.', 'tm_builder' ),
			),
			'percent' => array(
				'label'           => esc_html__( 'Percent', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a percentage for this bar.', 'tm_builder' ),
			),
			'background_color' => array(
				'label'        => esc_html__( 'Background Color', 'tm_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'bar_background_color' => array(
				'label'        => esc_html__( 'Bar Background Color', 'tm_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'label_color' => array(
				'label'        => esc_html__( 'Label Color', 'tm_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'percentage_color' => array(
				'label'        => esc_html__( 'Percentage Color', 'tm_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $tm_pb_counters_settings;

		$percent              = $this->shortcode_atts['percent'];
		$background_color     = $this->shortcode_atts['background_color'];
		$bar_background_color = $this->shortcode_atts['bar_background_color'];
		$label_color          = $this->shortcode_atts['label_color'];
		$percentage_color     = $this->shortcode_atts['percentage_color'];

		$module_class = TM_Builder_Element::add_module_order_class( '', $function_name );

		// Add % only if it hasn't been added to the attribute
		if ( '%' !== substr( trim( $percent ), -1 ) ) {
			$percent .= '%';
		}

		$background_color_style = $bar_color_style = '';

		if ( '' === $background_color && isset( $tm_pb_counters_settings['background_color'] ) && '' !== $tm_pb_counters_settings['background_color'] ) {
			$background_color_style = sprintf(
				' style="background-color: %1$s;"',
				esc_attr( $tm_pb_counters_settings['background_color'] )
			);
		}

		if ( '' === $bar_background_color && isset( $tm_pb_counters_settings['bar_bg_color'] ) && '' !== $tm_pb_counters_settings['bar_bg_color'] ) {
			$bar_color_style = sprintf(
				' background-color: %1$s;', esc_attr( $tm_pb_counters_settings['bar_bg_color'] )
			);
		}

		if ( ! empty( $tm_pb_counters_settings['border_radius'] ) && $this->defaults['border_radius'] !== $tm_pb_counters_settings['border_radius'] ) {
				TM_Builder_Element::set_style(
					$function_name, array(
						'selector'    => '%%order_class%% .tm_pb_counter_container, %%order_class%% .tm_pb_counter_amount',
						'declaration' => sprintf(
							'-moz-border-radius: %1$s; -webkit-border-radius: %1$s; border-radius: %1$s;',
							esc_html( tm_builder_process_range_value( $tm_pb_counters_settings['border_radius'] ) )
						),
				)
			);
		}


		if ( ( isset( $tm_pb_counters_settings['bar_top_padding'] ) && '' !== $tm_pb_counters_settings['bar_top_padding'] ) || ( isset( $tm_pb_counters_settings['bar_top_padding_tablet'] ) && '' !== $tm_pb_counters_settings['bar_top_padding_tablet'] ) || ( isset( $tm_pb_counters_settings['bar_top_padding_phone'] ) && '' !== $tm_pb_counters_settings['bar_top_padding_phone'] ) ) {

			$padding_values = array(
				'desktop' => isset( $tm_pb_counters_settings['bar_top_padding'] ) ? $tm_pb_counters_settings['bar_top_padding'] : '',
				'laptop'  => isset( $tm_pb_counters_settings['bar_top_padding_laptop'] ) ? $tm_pb_counters_settings['bar_top_padding_laptop'] : '',
				'tablet'  => isset( $tm_pb_counters_settings['bar_top_padding_tablet'] ) ? $tm_pb_counters_settings['bar_top_padding_tablet'] : '',
				'phone'   => isset( $tm_pb_counters_settings['bar_top_padding_phone'] ) ? $tm_pb_counters_settings['bar_top_padding_phone'] : '',
			);


			tm_pb_generate_responsive_css(
				$padding_values,
				'%%order_class%% .tm_pb_counter_amount',
				'padding-top',
				$function_name
			);
		}

		if ( ( isset( $tm_pb_counters_settings['bar_bottom_padding'] ) && '' !== $tm_pb_counters_settings['bar_bottom_padding'] ) || ( isset( $tm_pb_counters_settings['bar_bottom_padding_tablet'] ) && '' !== $tm_pb_counters_settings['bar_bottom_padding_tablet'] ) || ( isset( $tm_pb_counters_settings['bar_bottom_padding_phone'] ) && '' !== $tm_pb_counters_settings['bar_bottom_padding_phone'] ) ) {

			$padding_values = array(
				'desktop' => isset( $tm_pb_counters_settings['bar_bottom_padding'] ) ? $tm_pb_counters_settings['bar_bottom_padding'] : '',
				'laptop'  => isset( $tm_pb_counters_settings['bar_bottom_padding_laptop'] ) ? $tm_pb_counters_settings['bar_bottom_padding_laptop'] : '',
				'tablet'  => isset( $tm_pb_counters_settings['bar_bottom_padding_tablet'] ) ? $tm_pb_counters_settings['bar_bottom_padding_tablet'] : '',
				'phone'   => isset( $tm_pb_counters_settings['bar_bottom_padding_phone'] ) ? $tm_pb_counters_settings['bar_bottom_padding_phone'] : '',
			);

			tm_pb_generate_responsive_css(
				$padding_values,
				'%%order_class%% .tm_pb_counter_amount',
				'padding-bottom',
				$function_name
			);
		}

		if ( '' !== $background_color ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .tm_pb_counter_container',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $background_color )
					),
				)
			);
		}

		if ( '' !== $bar_background_color ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .tm_pb_counter_amount',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $bar_background_color )
					),
				)
			);
		}

		if ( '' !== $label_color ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .tm_pb_counter_title',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $label_color )
					),
				)
			);
		}

		if ( '' !== $percentage_color ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .tm_pb_counter_amount',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $percentage_color )
					),
				)
			);
		}

		$this->_var( 'content', sanitize_text_field( $content ) );
		$this->_var( 'percent', esc_attr( $percent ) );
		$this->_var( 'background_color_style', sanitize_text_field( $background_color_style ) );
		$this->_var( 'bar_color_style', sanitize_text_field( $bar_color_style ) );
		$this->_var( 'module_class', esc_attr( ltrim( $module_class ) ) );

		if ( isset( $tm_pb_counters_settings['use_percentages'] )
			&& 'on' === $tm_pb_counters_settings['use_percentages'] ) {
			$this->_var( 'percent_label', esc_html( $percent ) );
		}

		$output = $this->get_template_part( 'bar-counter.php' );

		return $output;
	}
}

new Tm_Builder_Module_Bar_Counters_Item;
