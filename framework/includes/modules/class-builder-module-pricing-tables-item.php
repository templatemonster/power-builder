<?php
class Tm_Builder_Module_Pricing_Tables_Item extends Tm_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Pricing Table', 'tm_builder' );
		$this->slug                        = 'tm_pb_pricing_table';
		$this->main_css_element 		   = '%%order_class%%.tm_pb_pricing';
		$this->type                        = 'child';
		$this->child_title_var             = 'title';

		$this->whitelisted_fields = array(
			'featured',
			'title',
			'subtitle',
			'currency',
			'per',
			'sum',
			'button_url',
			'button_text',
			'content_new',
		);

		$this->fields_defaults = array(
			'featured' => array( 'off' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Pricing Table', 'tm_builder' );
		$this->settings_text               = esc_html__( 'Pricing Table Settings', 'tm_builder' );
		$this->main_css_element = '%%order_class%%';
		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_pricing_heading h2",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'subheader' => array(
					'label'    => esc_html__( 'Subheader', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_best_value",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'currency_frequency' => array(
					'label'    => esc_html__( 'Currency &amp; Frequency', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_dollar_sign, {$this->main_css_element} .tm_pb_frequency",
					),
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_sum",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_pricing li",
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
				'use_background_image' => false,
				'css' => array(
					'main' => "{$this->main_css_element}.tm_pb_pricing_table",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_button",
					),
				),
			),
		);

		$this->custom_css_options = array(
			'pricing_heading' => array(
				'label'    => esc_html__( 'Pricing Heading', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_heading',
			),
			'pricing_title' => array(
				'label'    => esc_html__( 'Pricing Title', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_heading h2',
			),
			'pricing_subtitle' => array(
				'label'    => esc_html__( 'Pricing Subtitle', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_heading .tm_pb_best_value',
			),
			'pricing_top' => array(
				'label'    => esc_html__( 'Pricing Top', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_content_top',
			),
			'price' => array(
				'label'    => esc_html__( 'Price', 'tm_builder' ),
				'selector' => '.tm_pb_tm_price',
			),
			'currency' => array(
				'label'    => esc_html__( 'Currency', 'tm_builder' ),
				'selector' => '.tm_pb_dollar_sign',
			),
			'frequency' => array(
				'label'    => esc_html__( 'Frequency', 'tm_builder' ),
				'selector' => '.tm_pb_frequency',
			),
			'pricing_content' => array(
				'label'    => esc_html__( 'Pricing Content', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_content',
			),
			'pricing_item' => array(
				'label'    => esc_html__( 'Pricing Item', 'tm_builder' ),
				'selector' => 'ul.tm_pb_pricing li',
			),
			'pricing_item_excluded' => array(
				'label'    => esc_html__( 'Excluded Item', 'tm_builder' ),
				'selector' => 'ul.tm_pb_pricing li.tm_pb_not_available',
			),
			'pricing_button' => array(
				'label'    => esc_html__( 'Pricing Button', 'tm_builder' ),
				'selector' => '.tm_pb_pricing_table_button',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'featured' => array(
				'label'           => esc_html__( 'Make This Table Featured', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'description' => esc_html__( 'Featuring a table will make it stand out from the rest.', 'tm_builder' ),
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a title for the pricing table.', 'tm_builder' ),
			),
			'subtitle' => array(
				'label'           => esc_html__( 'Subtitle', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a sub title for the table if desired.', 'tm_builder' ),
			),
			'currency' => array(
				'label'           => esc_html__( 'Currency', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired currency symbol here.', 'tm_builder' ),
			),
			'per' => array(
				'label'           => esc_html__( 'Per', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If your pricing is subscription based, input the subscription payment cycle here.', 'tm_builder' ),
			),
			'sum' => array(
				'label'           => esc_html__( 'Price', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the value of the product here.', 'tm_builder' ),
			),
			'button_url' => array(
				'label'           => esc_html__( 'Button URL', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for the signup button.', 'tm_builder' ),
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Adjust the text used from the signup button.', 'tm_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'tm_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => sprintf(
					'%1$s<br/> + %2$s<br/> - %3$s',
					esc_html__( 'Input a list of features that are/are not included in the product. Separate items on a new line, and begin with either a + or - symbol: ', 'tm_builder' ),
					esc_html__( 'Included option', 'tm_builder' ),
					esc_html__( 'Excluded option', 'tm_builder' )
				),
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		global $tm_pb_pricing_tables_num, $tm_pb_pricing_tables_icon;

		$this->set_vars(
			array(
				'featured',
				'title',
				'subtitle',
				'currency',
				'per',
				'sum',
				'button_url',
				'button_text',
				'custom_button',
				'button_icon',
			)
		);

		$this->_var( 'content', $content );

		$tm_pb_pricing_tables_num++;

		$module_class = TM_Builder_Element::add_module_order_class( '', $function_name );

		$this->_var( 'module_class', $module_class );

		if ( 'on' === $this->_var( 'custom_button' ) && '' !== $this->_var( 'button_icon' ) ) {
			$custom_table_icon = $this->_var( 'button_icon' );
		} else {
			$custom_table_icon = $tm_pb_pricing_tables_icon;
		}

		if ( '' !== $this->_var( 'button_url' ) && '' !== $this->_var( 'button_text' ) ) {

			$icon        = esc_attr( tm_pb_process_font_icon( $custom_table_icon ) );
			$icon_family = tm_builder_get_icon_family();

			if ( '&#x;' !== $icon && '&amp;#x;' !== $icon ) {
				$this->_var( 'icon', $icon );
			}

			if ( $icon_family ) {
				TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_pb_custom_button_icon:before, %%order_class%% .tm_pb_custom_button_icon:after',
					'declaration' => sprintf(
						'font-family: "%1$s" !important;',
						esc_attr( $icon_family )
					),
				) );
			}

		}

		$output = $this->get_template_part( 'pricing-table/item.php' );

		return $output;
	}

	/**
	 * Returns pricing table features list HTML-markup.
	 *
	 * @return string
	 */
	public function pricing_table_features_list() {
		return do_shortcode( tm_pb_fix_shortcodes( tm_pb_extract_items( $this->_var( 'content' ) ) ) );
	}

	/**
	 * Returns pricing table item block classes
	 *
	 * @return string
	 */
	public function pricing_table_item_classes() {

		$classes = array(
			'tm_pb_pricing_table',
			esc_attr( $this->_var( 'module_class' ) )
		);

		if ( 'off' !== $this->_var( 'featured' ) ) {
			$classes[] = 'tm_pb_featured_table';
		}

		return implode( ' ', $classes );
	}

}

new Tm_Builder_Module_Pricing_Tables_Item;
