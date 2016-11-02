<?php
class Tm_Builder_Module_Tabs_Item extends Tm_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Tab', 'tm_builder' );
		$this->slug                        = 'tm_pb_tab';
		$this->type                        = 'child';
		$this->child_title_var             = 'title';

		$this->whitelisted_fields = array(
			'title',
			'content_new',
		);

		$this->advanced_setting_title_text = esc_html__( 'New Tab', 'tm_builder' );
		$this->settings_text               = esc_html__( 'Tab Settings', 'tm_builder' );
		$this->main_css_element = '%%order_class%%';
		$this->advanced_options = array(
			'fonts' => array(
				'tab' => array(
					'label'    => esc_html__( 'Tab', 'tm_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element}.tab-control",
						'color'     => "{$this->main_css_element}.tab-control a",
						'important' => 'all',
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
						'main'        => "{$this->main_css_element}.tab-content",
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
				'css' => array(
					'main' => "{$this->main_css_element}.tab-content",
					'important' => 'all',
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'       => esc_html__( 'Title', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'The title will be used within the tab button for this tab.', 'tm_builder' ),
			),
			'content_new' => array(
				'label'       => esc_html__( 'Content', 'tm_builder' ),
				'type'        => 'tiny_mce',
				'description' => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'tm_builder' ),
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		global $tm_pb_tab_titles;
		global $tm_pb_tab_classes;

		$title = $this->shortcode_atts['title'];

		$module_class = TM_Builder_Element::add_module_order_class( '', $function_name );

		$i = 0;

		$title               = '' !== $title ? $title : esc_html__( 'Tab', 'tm_builder' );
		$tm_pb_tab_titles[]  = $title;
		$tm_pb_tab_classes[] = $module_class;
		$module_class       .= ( 1 === count( $tm_pb_tab_titles ) ? ' tm_pb_active_content' : '' );

		$this->_var( 'title', $title );
		$this->_var( 'module_class', esc_attr( $module_class ) );

		$output = $this->get_template_part( 'tabs/tabs-item.php' );

		return $output;
	}
}

new Tm_Builder_Module_Tabs_Item;
