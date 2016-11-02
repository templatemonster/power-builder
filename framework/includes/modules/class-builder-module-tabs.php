<?php
class Tm_Builder_Module_Tabs extends Tm_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Tabs', 'tm_builder' );
		$this->slug            = 'tm_pb_tabs';
		$this->child_slug      = 'tm_pb_tab';
		$this->icon            = 'f085';
		$this->child_item_text = esc_html__( 'Tab', 'tm_builder' );

		$this->whitelisted_fields = array(
			'admin_label',
			'module_id',
			'module_class',
			'active_tab_background_color',
			'inactive_tab_background_color',
		);

		$this->main_css_element = '%%order_class%%.tm_pb_tabs';
		$this->advanced_options = array(
			'fonts' => array(
				'tab' => array(
					'label'    => esc_html__( 'Tab', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_tabs_controls li",
						'color' => "{$this->main_css_element} .tm_pb_tabs_controls li a",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_all_tabs",
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
			'background' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .tm_pb_all_tabs",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
		);
		$this->custom_css_options = array(
			'tabs_controls' => array(
				'label'    => esc_html__( 'Tabs Controls', 'tm_builder' ),
				'selector' => '.tm_pb_tabs_controls',
			),
			'tab' => array(
				'label'    => esc_html__( 'Tab', 'tm_builder' ),
				'selector' => '.tm_pb_tabs_controls li',
			),
			'active_tab' => array(
				'label'    => esc_html__( 'Active Tab', 'tm_builder' ),
				'selector' => '.tm_pb_tabs_controls li.tm_pb_tab_active',
			),
			'tabs_content' => array(
				'label'    => esc_html__( 'Tabs Content', 'tm_builder' ),
				'selector' => '.tm_pb_all_tabs',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'active_tab_background_color' => array(
				'label'             => esc_html__( 'Active Tab Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'inactive_tab_background_color' => array(
				'label'             => esc_html__( 'Inactive Tab Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
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

		$active_tab_background_color   = $this->shortcode_atts['active_tab_background_color'];
		$inactive_tab_background_color = $this->shortcode_atts['inactive_tab_background_color'];

		$all_tabs_content = $this->shortcode_content;

		global $tm_pb_tab_titles;
		global $tm_pb_tab_classes;

		if ( '' !== $inactive_tab_background_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_tabs_controls li',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $inactive_tab_background_color )
				),
			) );
		}

		if ( '' !== $active_tab_background_color ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_tabs_controls li.tm_pb_tab_active',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $active_tab_background_color )
				),
			) );
		}

		$nav_items = '';
		$i         = 0;

		if ( ! empty( $tm_pb_tab_titles ) ) {
			foreach ( $tm_pb_tab_titles as $tab_title ) {

				++$i;
				$classes = ltrim( $tm_pb_tab_classes[ $i-1 ] );
				$classes .= ( 1 == $i ? ' tm_pb_tab_active' : '' );

				$this->_var( 'tab_title', esc_html( $tab_title ) );
				$this->_var( 'classes', esc_attr( $classes ) );

				$nav_items .= $this->get_template_part( 'tabs/nav-item.php' );
			}
		}

		$tm_pb_tab_titles = $tm_pb_tab_classes = array();

		$this->_var( 'nav_items', $nav_items );
		$this->_var( 'tabs_content', $all_tabs_content );

		$content = $this->get_template_part( 'tabs/tabs.php' );

		$output = $this->wrap_module( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Module_Tabs;
