<?php
class Tm_Builder_Module_Brands_Showcase_Item extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Brands Showcase', 'power-builder' );
		$this->slug = 'tm_pb_brands_showcase_module__item';
		$this->type = 'child';
		$this->child_title_var = 'brand_name';

		$this->advanced_setting_title_text = esc_html__( 'New Brand', 'power-builder' );

		$this->whitelisted_fields = array(
			'brand_logo',
			'brand_url',
			'url_new_window',
			'brand_name',
			'brand_title',
			'brand_description',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'url_new_window'    => array( 'off' ),
			'background_color'  => array( tm_builder_accent_color(), 'add_default_setting' ),
		);

		$css_prefix = 'tm_pb_brands_showcase_module__item';

		$this->main_css_element = "%%order_class%%.{$css_prefix}__wrapper";
	}

	function get_fields() {
		$fields = array(
			'brand_logo' => array(
				'label'              => esc_html__( 'Brand Logo URL', 'power-builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'power-builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'power-builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'power-builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'power-builder' ),
			),
			'brand_url' => array(
				'label'           => esc_html__( 'Brand URL', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL.', 'power-builder' ),
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'power-builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'power-builder' ),
					'on'  => esc_html__( 'In The New Tab', 'power-builder' ),
				),
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'power-builder' ),
			),
			'brand_name' => array(
				'label'           => esc_html__( 'Brand Name', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the brand name.', 'power-builder' ),
			),
			'brand_title' => array(
				'label'           => esc_html__( 'Brand Title', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the brand title.', 'power-builder' ),
			),
			'brand_description' => array(
				'label'           => esc_html__( 'Brand Description', 'power-builder' ),
				'type'            => 'textarea',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the description of the brand.', 'power-builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $tm_pb_brands_showcase;

		$this->set_vars( array(
			'brand_logo',
			'brand_url',
			'url_new_window',
			'brand_name',
			'brand_title',
			'brand_description',
			'module_id',
			'module_class',
		) );

		$cols = array(
			'columns',
			'columns_laptop',
			'columns_tablet',
			'columns_phone',
		);

		foreach ( $cols as $col ) {
			$this->_var( $col, $tm_pb_brands_showcase[ $col ] );
		}

		return $this->get_template_part( sprintf( 'brands-showcase/%s/brands-showcase-item.php', $tm_pb_brands_showcase['template'] ) );
	}
}
new Tm_Builder_Module_Brands_Showcase_Item;
