<?php
class Tm_Builder_Module_Accordion_Item extends Tm_Builder_Module {
	function init() {
		$this->name                  = esc_html__( 'Accordion', 'power-builder' );
		$this->slug                  = 'tm_pb_accordion_item';
		$this->type                  = 'child';
		$this->child_title_var       = 'title';
		$this->no_shortcode_callback = true;

		$this->whitelisted_fields = array(
			'title',
			'content_new',
			'open_toggle_background_color',
			'open_toggle_text_color',
			'closed_toggle_background_color',
			'closed_toggle_text_color',
			'icon_color',
		);

		$this->advanced_options      = array(
			'background'            => array(
				'use_background_color' => false,
			),
			'custom_margin_padding' => array(
				'use_margin' => false,
				'css' => array(
					'important' => 'all'
				)
			),
		);

		$this->custom_css_options = array(
			'toggle' => array(
				'label'    => esc_html__( 'Toggle', 'power-builder' ),
			),
			'open_toggle' => array(
				'label'    => esc_html__( 'Open Toggle', 'power-builder' ),
				'selector' => '.tm_pb_toggle_open',
			),
			'toggle_title' => array(
				'label'    => esc_html__( 'Toggle Title', 'power-builder' ),
				'selector' => '.tm_pb_toggle_title',
			),
			'toggle_icon' => array(
				'label'    => esc_html__( 'Toggle Icon', 'power-builder' ),
				'selector' => '.tm_pb_toggle_title:before',
			),
			'toggle_content' => array(
				'label'    => esc_html__( 'Toggle Content', 'power-builder' ),
				'selector' => '.tm_pb_toggle_content',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The toggle title will appear above the content and when the toggle is closed.', 'power-builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'power-builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'power-builder' ),
			),
			'open_toggle_background_color' => array(
				'label'             => esc_html__( 'Open Toggle Background Color', 'power-builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'open_toggle_text_color' => array(
				'label'             => esc_html__( 'Open Toggle Text Color', 'power-builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'closed_toggle_background_color' => array(
				'label'             => esc_html__( 'Closed Toggle Background Color', 'power-builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'closed_toggle_text_color' => array(
				'label'             => esc_html__( 'Closed Toggle Text Color', 'power-builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'power-builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
		);
		return $fields;
	}
}
new Tm_Builder_Module_Accordion_Item;

