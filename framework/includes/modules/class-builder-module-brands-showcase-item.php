<?php
class Tm_Builder_Module_Brands_Showcase_Item extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Brands Showcase', 'tm_builder' );
		$this->slug = 'tm_pb_brands_showcase_module__item';
		$this->type = 'child';
		$this->child_title_var = 'brand_name';

		$this->advanced_setting_title_text = esc_html__( 'New Brand', 'tm_builder' );

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
				'label'              => esc_html__( 'Brand Logo URL', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'tm_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'tm_builder' ),
			),
			'brand_url' => array(
				'label'           => esc_html__( 'Brand URL', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL.', 'tm_builder' ),
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'tm_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'tm_builder' ),
			),
			'brand_name' => array(
				'label'           => esc_html__( 'Brand Name', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the brand name.', 'tm_builder' ),
			),
			'brand_title' => array(
				'label'           => esc_html__( 'Brand Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the brand title.', 'tm_builder' ),
			),
			'brand_description' => array(
				'label'           => esc_html__( 'Brand Description', 'tm_builder' ),
				'type'            => 'textarea',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the description of the brand.', 'tm_builder' ),
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
