<?php
class Tm_Builder_Row_Inner extends Tm_Builder_Structure_Element {
	function init() {
		$this->name = esc_html__( 'Row', 'tm_builder' );
		$this->slug = 'tm_pb_row_inner';

		$this->advanced_options = array(
			'custom_margin_padding' => array(
				'use_padding'       => false,
				'css'               => array(
					'important' => 'all',
				),
				'custom_margin'     => array(
					'priority' => 1,
				),
			),
		);

		$this->whitelisted_fields = array(
			'custom_padding',
			'custom_padding_laptop',
			'custom_padding_tablet',
			'custom_padding_phone',
			'padding_mobile',
			'column_padding_mobile',
			'use_custom_gutter',
			'gutter_width',
			'module_id',
			'module_class',
			'make_equal',
			'columns',
			'background_color_1',
			'background_color_2',
			'background_color_3',
			'bg_img_1',
			'bg_img_2',
			'bg_img_3',
			'padding_top_1',
			'padding_right_1',
			'padding_bottom_1',
			'padding_left_1',
			'padding_top_2',
			'padding_right_2',
			'padding_bottom_2',
			'padding_left_2',
			'padding_top_3',
			'padding_right_3',
			'padding_bottom_3',
			'padding_left_3',
			'padding_1_tablet',
			'padding_2_tablet',
			'padding_3_tablet',
			'padding_1_phone',
			'padding_2_phone',
			'padding_3_phone',
			'parallax_1',
			'parallax_method_1',
			'parallax_2',
			'parallax_method_2',
			'parallax_3',
			'parallax_method_3',
			'module_id_1',
			'module_id_2',
			'module_id_3',
			'module_class_1',
			'module_class_2',
			'module_class_3',
			'custom_css_before_1',
			'custom_css_before_2',
			'custom_css_before_3',
			'custom_css_main_1',
			'custom_css_main_2',
			'custom_css_main_3',
			'custom_css_after_1',
			'custom_css_after_2',
			'custom_css_after_3',
		);

		$this->fields_defaults = array(
			'padding_mobile'        => array( 'off' ),
			'column_padding_mobile' => array( 'off' ),
			'use_custom_gutter'     => array( 'off' ),
			'gutter_width'          => array( '3', 'only_default_setting' ),
			'make_equal'            => array( 'off' ),
			'background_color_1'    => array( '' ),
			'background_color_2'    => array( '' ),
			'background_color_3'    => array( '' ),
			'bg_img_1'              => array( '' ),
			'bg_img_2'              => array( '' ),
			'bg_img_3'              => array( '' ),
			'padding_top_1'         => array( '' ),
			'padding_right_1'       => array( '' ),
			'padding_bottom_1'      => array( '' ),
			'padding_left_1'        => array( '' ),
			'padding_top_2'         => array( '' ),
			'padding_right_2'       => array( '' ),
			'padding_bottom_2'      => array( '' ),
			'padding_left_2'        => array( '' ),
			'padding_top_3'         => array( '' ),
			'padding_right_3'       => array( '' ),
			'padding_bottom_3'      => array( '' ),
			'padding_left_3'        => array( '' ),
			'parallax_1'            => array( 'off' ),
			'parallax_method_1'     => array( 'on' ),
			'parallax_2'            => array( 'off' ),
			'parallax_method_2'     => array( 'on' ),
			'parallax_3'            => array( 'off' ),
			'parallax_method_3'     => array( 'on' ),
			'custom_padding_laptop' => array( '' ),
			'custom_padding_tablet' => array( '' ),
			'custom_padding_phone'  => array( '' ),
		);
	}

	function get_fields() {
		$fields = array(
			'custom_padding' => array(
				'label'           => esc_html__( 'Custom Padding', 'tm_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'tm_builder' ),
			),
			'custom_padding_laptop' => array(
				'type' => 'skip',
			),
			'custom_padding_tablet' => array(
				'type' => 'skip',
			),
			'custom_padding_phone' => array(
				'type' => 'skip',
			),
			'padding_mobile' => array(
				'label'             => esc_html__( 'Keep Custom Padding on Mobile', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Allow custom padding to be retained on mobile screens', 'tm_builder' ),
			),
			'use_custom_gutter' => array(
				'label'             => esc_html__( 'Use Custom Gutter Width', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_gutter_width',
				),
				'description'       => esc_html__( 'Enable this option to define custom gutter width for this row.', 'tm_builder' ),
			),
			'gutter_width' => array(
				'label'           => esc_html__( 'Gutter Width', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'range_settings'  => array(
					'min'  => 1,
					'max'  => 4,
					'step' => 1,
				),
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'Adjust the spacing between each column in this row.', 'tm_builder' ),
			),
			'make_equal' => array(
				'label'             => esc_html__( 'Equalize Column Heights', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'          => 'advanced',
			),
			'columns' => array(
				'type'            => 'column_settings',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
			),
			'column_padding_mobile' => array(
				'label'             => esc_html__( 'Keep Column Padding on Mobile', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'background_color_1' => array(
				'type' => 'skip',
			),
			'background_color_2' => array(
				'type' => 'skip',
			),
			'background_color_3' => array(
				'type' => 'skip',
			),
			'bg_img_1' => array(
				'type' => 'skip',
			),
			'bg_img_2' => array(
				'type' => 'skip',
			),
			'bg_img_3' => array(
				'type' => 'skip',
			),
			'padding_top_1' => array(
				'type' => 'skip',
			),
			'padding_right_1' => array(
				'type' => 'skip',
			),
			'padding_bottom_1' => array(
				'type' => 'skip',
			),
			'padding_left_1' => array(
				'type' => 'skip',
			),
			'padding_top_2' => array(
				'type' => 'skip',
			),
			'padding_right_2' => array(
				'type' => 'skip',
			),
			'padding_bottom_2' => array(
				'type' => 'skip',
			),
			'padding_left_2' => array(
				'type' => 'skip',
			),
			'padding_top_3' => array(
				'type' => 'skip',
			),
			'padding_right_3' => array(
				'type' => 'skip',
			),
			'padding_bottom_3' => array(
				'type' => 'skip',
			),
			'padding_left_3' => array(
				'type' => 'skip',
			),
			'parallax_1' => array(
				'type' => 'skip',
			),
			'parallax_method_1' => array(
				'type' => 'skip',
			),
			'parallax_2' => array(
				'type' => 'skip',
			),
			'parallax_method_2' => array(
				'type' => 'skip',
			),
			'parallax_3' => array(
				'type' => 'skip',
			),
			'parallax_method_3' => array(
				'type' => 'skip',
			),
			'padding_1_tablet' => array(
				'type' => 'skip',
			),
			'padding_2_tablet' => array(
				'type' => 'skip',
			),
			'padding_3_tablet' => array(
				'type' => 'skip',
			),
			'padding_1_phone' => array(
				'type' => 'skip',
			),
			'padding_2_phone' => array(
				'type' => 'skip',
			),
			'padding_3_phone' => array(
				'type' => 'skip',
			),
			'module_id_1' => array(
				'type' => 'skip',
			),
			'module_id_2' => array(
				'type' => 'skip',
			),
			'module_id_3' => array(
				'type' => 'skip',
			),
			'module_class_1' => array(
				'type' => 'skip',
			),
			'module_class_2' => array(
				'type' => 'skip',
			),
			'module_class_3' => array(
				'type' => 'skip',
			),
			'custom_css_before_1' => array(
				'type' => 'skip',
			),
			'custom_css_before_2' => array(
				'type' => 'skip',
			),
			'custom_css_before_3' => array(
				'type' => 'skip',
			),
			'custom_css_main_1' => array(
				'type' => 'skip',
			),
			'custom_css_main_2' => array(
				'type' => 'skip',
			),
			'custom_css_main_3' => array(
				'type' => 'skip',
			),
			'custom_css_after_1' => array(
				'type' => 'skip',
			),
			'custom_css_after_2' => array(
				'type' => 'skip',
			),
			'custom_css_after_3' => array(
				'type' => 'skip',
			),
			'columns_css' => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'priority'        => '20',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'tm_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => tm_pb_media_breakpoints(),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'tm_builder' ),
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
			'columns_css_fields' => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$background_color_1      = $this->shortcode_atts['background_color_1'];
		$background_color_2      = $this->shortcode_atts['background_color_2'];
		$background_color_3      = $this->shortcode_atts['background_color_3'];
		$bg_img_1                = $this->shortcode_atts['bg_img_1'];
		$bg_img_2                = $this->shortcode_atts['bg_img_2'];
		$bg_img_3                = $this->shortcode_atts['bg_img_3'];
		$padding_top_1           = $this->shortcode_atts['padding_top_1'];
		$padding_right_1         = $this->shortcode_atts['padding_right_1'];
		$padding_bottom_1        = $this->shortcode_atts['padding_bottom_1'];
		$padding_left_1          = $this->shortcode_atts['padding_left_1'];
		$padding_top_2           = $this->shortcode_atts['padding_top_2'];
		$padding_right_2         = $this->shortcode_atts['padding_right_2'];
		$padding_bottom_2        = $this->shortcode_atts['padding_bottom_2'];
		$padding_left_2          = $this->shortcode_atts['padding_left_2'];
		$padding_top_3           = $this->shortcode_atts['padding_top_3'];
		$padding_right_3         = $this->shortcode_atts['padding_right_3'];
		$padding_bottom_3        = $this->shortcode_atts['padding_bottom_3'];
		$padding_left_3          = $this->shortcode_atts['padding_left_3'];
		$padding_1_tablet        = $this->shortcode_atts['padding_1_tablet'];
		$padding_2_tablet        = $this->shortcode_atts['padding_2_tablet'];
		$padding_3_tablet        = $this->shortcode_atts['padding_3_tablet'];
		$padding_1_phone         = $this->shortcode_atts['padding_1_phone'];
		$padding_2_phone         = $this->shortcode_atts['padding_2_phone'];
		$padding_3_phone         = $this->shortcode_atts['padding_3_phone'];
		$gutter_width            = $this->shortcode_atts['gutter_width'];
		$make_equal              = $this->shortcode_atts['make_equal'];
		$custom_padding          = $this->shortcode_atts['custom_padding'];
		$custom_padding_laptop   = $this->shortcode_atts['custom_padding_laptop'];
		$custom_padding_tablet   = $this->shortcode_atts['custom_padding_tablet'];
		$custom_padding_phone    = $this->shortcode_atts['custom_padding_phone'];
		$padding_mobile          = $this->shortcode_atts['padding_mobile'];
		$column_padding_mobile   = $this->shortcode_atts['column_padding_mobile'];
		$global_module           = $this->shortcode_atts['global_module'];
		$use_custom_gutter       = $this->shortcode_atts['use_custom_gutter'];
		$parallax_1              = $this->shortcode_atts['parallax_1'];
		$parallax_method_1       = $this->shortcode_atts['parallax_method_1'];
		$parallax_2              = $this->shortcode_atts['parallax_2'];
		$parallax_method_2       = $this->shortcode_atts['parallax_method_2'];
		$parallax_3              = $this->shortcode_atts['parallax_3'];
		$parallax_method_3       = $this->shortcode_atts['parallax_method_3'];
		$module_id_1             = $this->shortcode_atts['module_id_1'];
		$module_id_2             = $this->shortcode_atts['module_id_2'];
		$module_id_3             = $this->shortcode_atts['module_id_3'];
		$module_class_1          = $this->shortcode_atts['module_class_1'];
		$module_class_2          = $this->shortcode_atts['module_class_2'];
		$module_class_3          = $this->shortcode_atts['module_class_3'];
		$custom_css_before_1     = $this->shortcode_atts['custom_css_before_1'];
		$custom_css_before_2     = $this->shortcode_atts['custom_css_before_2'];
		$custom_css_before_3     = $this->shortcode_atts['custom_css_before_3'];
		$custom_css_main_1       = $this->shortcode_atts['custom_css_main_1'];
		$custom_css_main_2       = $this->shortcode_atts['custom_css_main_2'];
		$custom_css_main_3       = $this->shortcode_atts['custom_css_main_3'];
		$custom_css_after_1      = $this->shortcode_atts['custom_css_after_1'];
		$custom_css_after_2      = $this->shortcode_atts['custom_css_after_2'];
		$custom_css_after_3      = $this->shortcode_atts['custom_css_after_3'];

		global $tm_pb_column_inner_backgrounds, $tm_pb_column_inner_paddings, $tm_pb_columns_inner_counter, $keep_column_padding_mobile, $tm_pb_column_parallax, $tm_pb_column_inner_css, $tm_pb_column_inner_paddings_mobile;

		$keep_column_padding_mobile = $column_padding_mobile;

		if ( '' !== $global_module ) {
			$global_content = tm_pb_load_global_module( $global_module, $function_name );

			if ( '' !== $global_content ) {
				return do_shortcode( $global_content );
			}
		}

		$padding_mobile_values = array(
			'laptop' => explode( '|', $custom_padding_laptop ),
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		);

		$tm_pb_columns_inner_counter = 0;
		$tm_pb_column_inner_backgrounds = array(
			array( $background_color_1, $bg_img_1 ),
			array( $background_color_2, $bg_img_2 ),
			array( $background_color_3, $bg_img_3 ),
		);
		$tm_pb_column_inner_paddings = array(
			array(
				'padding-top'    => $padding_top_1,
				'padding-right'  => $padding_right_1,
				'padding-bottom' => $padding_bottom_1,
				'padding-left'   => $padding_left_1
			),
			array(
				'padding-top'    => $padding_top_2,
				'padding-right'  => $padding_right_2,
				'padding-bottom' => $padding_bottom_2,
				'padding-left'   => $padding_left_2
			),
			array(
				'padding-top'    => $padding_top_3,
				'padding-right'  => $padding_right_3,
				'padding-bottom' => $padding_bottom_3,
				'padding-left'   => $padding_left_3
			),
		);

		$tm_pb_column_parallax = array(
			array( $parallax_1, $parallax_method_1 ),
			array( $parallax_2, $parallax_method_2 ),
			array( $parallax_3, $parallax_method_3 ),
		);

		$tm_pb_column_inner_paddings_mobile = array(
			array(
				'tablet' => explode( '|', $padding_1_tablet ),
				'phone'  => explode( '|', $padding_1_phone ),
			),
			array(
				'tablet' => explode( '|', $padding_2_tablet ),
				'phone'  => explode( '|', $padding_2_phone ),
			),
			array(
				'tablet' => explode( '|', $padding_3_tablet ),
				'phone'  => explode( '|', $padding_3_phone ),
			),
		);

		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of Rows support only top and bottom padding, so we need to handle it along with the full padding in the recent version
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right' => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left' => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '.tm_pb_column %%order_class%%',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					if ( 'on' !== $padding_mobile ) {
						$element_style['media_query'] = TM_Builder_Element::get_media_query( 'min_width_981' );
					} TM_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) || ! empty( $padding_values['laptop'] ) ) {

			$padding_mobile_values_processed = array();

			foreach( array( 'laptop', 'tablet', 'phone' ) as $device ) {
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
				tm_pb_generate_responsive_css( $padding_mobile_values_processed, '.tm_pb_column %%order_class%%', '', $function_name );
			}
		}

		$tm_pb_column_inner_css = array(
			'css_class'         => array( $module_class_1, $module_class_2, $module_class_3 ),
			'css_id'            => array( $module_id_1, $module_id_2, $module_id_3 ),
			'custom_css_before' => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3 ),
			'custom_css_main'   => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3 ),
			'custom_css_after'  => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3 ),
		);

		$module_class .= 'row tm_pb_row_inner';

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		$inner_content = do_shortcode( tm_pb_fix_shortcodes( $content ) );
		$module_class .= '' == trim( $inner_content ) ? ' tm_pb_row_empty' : '';

		$module_class .= 'on' === $make_equal ? ' tm_pb_equal_columns' : '';

		if ( 'on' === $use_custom_gutter ) {
			$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter to 1 if 0 entered by user
			$module_class .= ' tm_pb_gutters' . $gutter_width;
		}

		$output = sprintf(
			'<div%4$s class="%2$s">
				%1$s
			</div> <!-- .%3$s -->',
			$inner_content,
			esc_attr( $module_class ),
			esc_html( $function_name ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' )
		);

		return $output;
	}
}
new Tm_Builder_Row_Inner;
