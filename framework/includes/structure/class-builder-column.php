<?php

class Tm_Builder_Column extends Tm_Builder_Structure_Element {
	function init() {
		$this->name                       = esc_html__( 'Column', 'tm_builder' );
		$this->slug                       = 'tm_pb_column';
		$this->additional_shortcode_slugs = array( 'tm_pb_column_inner' );

		$this->whitelisted_fields = array(
			'type',
			'specialty_columns',
			'saved_specialty_column_type',
		);

		$this->fields_defaults = array(
			'type'                        => array( '4_4' ),
			'specialty_columns'           => array( '' ),
			'saved_specialty_column_type' => array( '' ),
		);
	}

	function get_fields() {
		$fields = array(
			'type' => array(
				'type' => 'skip',
			),
			'specialty_columns' => array(
				'type' => 'skip',
			),
			'saved_specialty_column_type' => array(
				'type' => 'skip',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$type                        = $this->shortcode_atts['type'];
		$specialty_columns           = $this->shortcode_atts['specialty_columns'];
		$saved_specialty_column_type = $this->shortcode_atts['saved_specialty_column_type'];

		global $tm_specialty_column_type, $tm_pb_column_backgrounds, $tm_pb_column_paddings, $tm_pb_column_inner_backgrounds, $tm_pb_column_inner_paddings, $tm_pb_columns_counter, $tm_pb_columns_inner_counter, $keep_column_padding_mobile, $tm_pb_column_parallax, $tm_pb_column_css, $tm_pb_column_inner_css, $tm_pb_column_paddings_mobile;

		if ( 'tm_pb_column_inner' !== $function_name ) {
			$tm_specialty_column_type = $type;
			$array_index = $tm_pb_columns_counter;
			$backgrounds_array = $tm_pb_column_backgrounds;
			$paddings_array = $tm_pb_column_paddings;
			$paddings_mobile_array = $tm_pb_column_paddings_mobile;
			$column_css_array = $tm_pb_column_css;
			$tm_pb_columns_counter++;
		} else {
			$array_index = $tm_pb_columns_inner_counter;
			$backgrounds_array = $tm_pb_column_inner_backgrounds;
			$paddings_array = $tm_pb_column_inner_paddings;
			$column_css_array = $tm_pb_column_inner_css;
			$tm_pb_columns_inner_counter++;
			$paddings_mobile_array = isset( $tm_pb_column_inner_paddings_mobile );
		}

		$background_color = isset( $backgrounds_array[$array_index][0] ) ? $backgrounds_array[$array_index][0] : '';
		$background_img = isset( $backgrounds_array[$array_index][1] ) ? $backgrounds_array[$array_index][1] : '';
		$padding_values = isset( $paddings_array[$array_index] ) ? $paddings_array[$array_index] : array();
		$padding_mobile_values = isset( $paddings_mobile_array[$array_index] ) ? $paddings_mobile_array[$array_index] : array();
		$parallax_method = isset( $tm_pb_column_parallax[$array_index][0] ) && 'on' === $tm_pb_column_parallax[$array_index][0] ? $tm_pb_column_parallax[$array_index][1] : '';
		$custom_css_class = isset( $column_css_array['css_class'][$array_index] ) ? ' ' . $column_css_array['css_class'][$array_index] : '';
		$custom_css_id = isset( $column_css_array['css_id'][$array_index] ) ? $column_css_array['css_id'][$array_index] : '';
		$custom_css_before = isset( $column_css_array['custom_css_before'][$array_index] ) ? $column_css_array['custom_css_before'][$array_index] : '';
		$custom_css_main = isset( $column_css_array['custom_css_main'][$array_index] ) ? $column_css_array['custom_css_main'][$array_index] : '';
		$custom_css_after = isset( $column_css_array['custom_css_after'][$array_index] ) ? $column_css_array['custom_css_after'][$array_index] : '';

		if ( '' !== $background_color && 'rgba(0,0,0,0)' !== $background_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-color:%s;',
					esc_attr( $background_color )
				),
			) );
		}

		if ( '' !== $background_img && '' === $parallax_method ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-image:url(%s);',
					esc_attr( $background_img )
				),
			) );
		}

		if ( ! empty( $padding_values ) ) {
			foreach( $padding_values as $position => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '%%order_class%%',
						'declaration' => sprintf(
							'%1$s:%2$s;',
							esc_html( $position ),
							esc_html( tm_builder_process_range_value( $value ) )
						),
					);

					if ( 'on' !== $keep_column_padding_mobile ) {
						$element_style['media_query'] = TM_Builder_Element::get_media_query( 'min_width_981' );
					} TM_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				tm_pb_generate_responsive_css( $padding_mobile_values_processed, '%%order_class%%', '', $function_name );
			}
		}

		if ( '' !== $custom_css_before ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:before',
				'declaration' => trim( $custom_css_before ),
			) );
		}

		if ( '' !== $custom_css_main ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => trim( $custom_css_main ),
			) );
		}

		if ( '' !== $custom_css_after ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:after',
				'declaration' => trim( $custom_css_after ),
			) );
		}

		if ( 'tm_pb_column_inner' === $function_name ) {
			$tm_specialty_column_type = '' !== $saved_specialty_column_type ? $saved_specialty_column_type : $tm_specialty_column_type;
		}


		switch ( $type ) {
			case '4_4':
				$grid_class = ' ' . apply_filters( 'tm_builder_4_4_column_layout', 'col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12' );
				break;
			case '1_2':
				$grid_class = ' ' . apply_filters( 'tm_builder_1_2_column_layout', 'col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6' );
				break;
			case '1_3':
				$grid_class = ' ' . apply_filters( 'tm_builder_1_3_column_layout', 'col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4' );
				break;
			case '1_4':
				$grid_class = ' ' . apply_filters( 'tm_builder_1_4_column_layout', 'col-xs-12 col-sm-6 col-md-6 col-lg-3 col-xl-3' );
				break;
			case '2_3':
				$grid_class = ' ' . apply_filters( 'tm_builder_2_3_column_layout', 'col-xs-12 col-sm-12 col-md-8 col-lg-8 col-xl-8' );
				break;
			case '3_4':
				$grid_class = ' ' . apply_filters( 'tm_builder_3_4_column_layout', 'col-xs-12 col-sm-6 col-md-6 col-lg-9 col-xl-9' );
				break;
		}

		$inner_class = 'tm_pb_column_inner' === $function_name ? ' tm_pb_column_inner' : '';

		$class = 'tm_pb_column_' . $type . $inner_class . $custom_css_class;

		$class = TM_Builder_Element::add_module_order_class( $class, $function_name );

		$inner_content = do_shortcode( tm_pb_fix_shortcodes( $content ) );
		$class .= '' == trim( $inner_content ) ? ' tm_pb_column_empty' : '';

		$class .= 'tm_pb_column_inner' !== $function_name && '' !== $specialty_columns ? ' tm_pb_specialty_column' : '';

		$class .= isset( $grid_class ) ? $grid_class: '';

		$output = sprintf(
			'<div class="tm_pb_column %1$s%3$s"%5$s>
				%4$s
				%2$s
			</div> <!-- .tm_pb_column -->',
			esc_attr( $class ),
			$inner_content,
			( '' !== $parallax_method ? ' tm_pb_section_parallax' : '' ),
			( '' !== $background_img && '' !== $parallax_method
				? sprintf(
					'<div class="tm_parallax_bg%2$s" style="background-image: url(%1$s);"></div>',
					esc_attr( $background_img ),
					( 'off' === $parallax_method ? ' tm_pb_parallax_css' : '' )
				)
				: ''
			),
			'' !== $custom_css_id ? sprintf( ' id="%1$s"', esc_attr( $custom_css_id ) ) : ''
		);

		return $output;

	}

}
new Tm_Builder_Column;
