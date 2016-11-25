<?php
class Tm_Builder_Module_Brands_Showcase extends Tm_Builder_Module {

	function init() {
		$this->name = esc_html__( 'Brands Showcase', 'tm_builder' );
		$this->slug = 'tm_pb_brands_showcase_module';
		$this->icon = 'f135';

		$this->global_settings_slug = 'tm_pb_brands_showcase_module';

		$this->child_slug = 'tm_pb_brands_showcase_module__item';
		$this->child_item_text = esc_html__( 'Brand', 'tm_builder' );

		$this->whitelisted_fields = array(
			'super_title',
			'title',
			'sub_title',
			'divider',
			'admin_label',
			'module_id',
			'module_class',
			'divider_color',
			'divider_height',
			'divider_height_laptop',
			'divider_height_tablet',
			'divider_height_phone',
			'divider_style',
			'divider_width',
			'divider_hide_on_mobile',
			'template',
			'carousel_settings',
			'columns',
			'columns_laptop',
			'columns_tablet',
			'columns_phone',
			'autoplay',
			'navigate_button',
			'navigate_pagination',
			'slides_per_view',
			'centered_slides',
		);

		$this->defaults = array(
			'divider_style'           => 'solid',
			'divider_width'           => '100',
			'divider_height'          => '1',
			'divider_height_laptop'   => '1',
			'divider_height_tablet'   => '1',
			'divider_height_phone'    => '1',
			'template'                => array( 'grid' ),
			'autoplay'                => array( 'on' ),
			'navigate_button'         => array( 'on' ),
			'navigate_pagination'     => array( 'on' ),
			'slides_per_view'         => '3',
			'columns'                 => '4',
			'columns_laptop'          => '4',
			'columns_tablet'          => '4',
			'columns_phone'           => '4',
		);

		$this->fields_defaults = array(
			'template'                => array( 'grid' ),
			'divider_color'           => array( '#000000', 'only_default_setting' ),
			'divider_hide_on_mobile'  => array( 'on' ),
			'divider_height'          => array( '1' ),
			'divider_height_laptop'   => array( '1' ),
			'divider_height_tablet'   => array( '1' ),
			'divider_height_phone'    => array( '1' ),
			'divider_width'           => array( '100' ),
			'autoplay'                => array( 'on' ),
			'navigate_button'         => array( 'on' ),
			'navigate_pagination'     => array( 'on' ),
			'slides_per_view'         => array( '3' ),
			'columns'                 => array( '4' ),
			'columns_laptop'          => array( '4' ),
			'columns_tablet'          => array( '4' ),
			'columns_phone'           => array( '4' ),
		);

		$css_prefix = 'tm_pb_brands_showcase_module';

		$this->main_css_element = "%%order_class%%.{$css_prefix}__wrapper";
		$this->advanced_options = array(
			'fonts' => array(
				'super_title' => array(
					'label' => esc_html__( 'Super Title', 'tm_builder' ),
					'font_size' => array(
						'default' => '20px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css' => array(
						'main' => "{$this->main_css_element} .{$css_prefix}__super-title"
					)
				),
				'title' => array(
					'label' => esc_html__( 'Title', 'tm_builder' ),
					'font_size' => array(
						'default' => '24px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css' => array(
						'main' => "{$this->main_css_element} .{$css_prefix}__title"
					)
				),
				'sub_title' => array(
					'label' => esc_html__( 'Sub Title', 'tm_builder' ),
					'font_size' => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css' => array(
						'main' => "{$this->main_css_element} .{$css_prefix}__sub-title"
					)
				),
				'brand_name' => array(
					'label' => esc_html__( 'Brand Name', 'tm_builder' ),
					'font_size' => array(
						'default' => '20px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css' => array(
						'main' => "{$this->main_css_element} .{$css_prefix}__item__title"
					)
				),
				'brand_description' => array(
					'label' => esc_html__( 'Brand Description', 'tm_builder' ),
					'font_size' => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css' => array(
						'main' => "{$this->main_css_element} .{$css_prefix}__item__description"
					)
				),
			),
			'divider_custom_margin_padding' => array(
				'use_padding' => false,
				'css' => array(
					'important' => 'all',
				),
			),
			'divider_height' => array(
				'divider_height' => array(
					'css' => array(
						'main' => "{$this->main_css_element} .{$css_prefix}__divider"
					),
				),
			),
		);

		$this->custom_css_options = array(
			'super_title' => array(
				'label' => esc_html__( 'Super Title', 'tm_builder' ),
				'selector' => '.tm_pb_brands_showcase__super-title',
			),
			'title' => array(
				'label' => esc_html__( 'Title', 'tm_builder' ),
				'selector' => '.tm_pb_brands_showcase__title'
			),
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 9 );
	}

	function get_fields() {
		$fields = array(
			'super_title' => array(
				'label'           => esc_html__( 'Super Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the super title for your brands showcase.', 'tm_builder' ),
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the title for your brands showcase.', 'tm_builder' ),
			),
			'sub_title' => array(
				'label'           => esc_html__( 'Sub Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the sub title for your brands showcase.', 'tm_builder' )
			),
			'divider' => array(
				'label'           => esc_html__( 'Show Divider', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'description'     => esc_html__( 'Toggle a separator between title & brands.', 'tm_builder' ),
				'affects'         => array(
					'#tm_pb_divider_color',
					'#tm_pb_divider_height',
					'#tm_pb_divider_hide_on_mobile',
				),
			),
			'divider_color' => array(
				'label'           => esc_html__( 'Divider Color', 'tm_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'This will adjust the color of the 1px divider line.', 'tm_builder' ),
				'depends_show_if' => 'on',
			),
			'divider_height' => array(
				'label'           => esc_html__( 'Divider Height', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'default'         => '1',
				'range_settings' => array(
					'min'  => 1,
					'max'  => 100,
					'step' => 1,
				),
				'mobile_options'      => true,
				'mobile_global'       => true,
				'description'     => esc_html__( 'Define how much space should be added below the divider.', 'tm_builder' ),
				'depends_show_if' => 'on',
			),
			'divider_height_laptop' => array(
				'type' => 'skip',
			),
			'divider_height_tablet' => array(
				'type' => 'skip',
			),
			'divider_height_phone' => array(
				'type' => 'skip',
			),
			'divider_hide_on_mobile' => array(
				'label'             => esc_html__( 'Hide Divider On Mobile', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'tab_slug'          => 'advanced',
				'depends_show_if'   => 'on',
			),
			'template' => array(
				'label'             => esc_html__( 'Template', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'grid'       => esc_html__( 'Grid', 'tm_builder' ),
					'carousel'   => esc_html__( 'Carousel', 'tm_builder' ),
				),
				'default'           => 'grid',
				'description'       => esc_html__( 'Here you can choose the look of the brands showcase.', 'tm_builder' ),
				'affects'           => array(
					'#tm_pb_columns',
					'#tm_pb_show_pagination',
					'#tm_pb_autoplay',
					'#tm_pb_navigate_button',
					'#tm_pb_navigate_pagination',
					'#tm_pb_slides_per_view',
					'#tm_pb_centered_slides',
				),
			),
			'columns' => array(
				'label'           => esc_html__( 'Columns', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '4',
				'range_settings' => array(
					'min'  => 1,
					'max'  => 6,
					'step' => 1,
				),
				'mobile_options'      => true,
				'mobile_global'       => true,
				'depends_show_if'     => 'grid',
				'tab_slug'            => 'advanced',
			),
			'columns_laptop' => array(
				'type' => 'skip',
			),
			'columns_tablet' => array(
				'type' => 'skip',
			),
			'columns_phone' => array(
				'type' => 'skip',
			),
			'disabled_on' => array(
				'label'					=> esc_html__( 'Disable on', 'tm_builder' ),
				'type'					=> 'multiple_checkboxes',
				'options'				=> tm_pb_media_breakpoints(),
				'additional_att'		=> 'disable_on',
				'option_category'		=> 'configuration',
				'description'			=> esc_html__( 'This will disable the module on selected devices', 'tm_builder' ),
			),
			'autoplay' => array(
				'label'					=> esc_html__( 'Autoplay', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'off'		=> esc_html__( 'No', 'tm_builder' ),
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'				=> 'advanced',
				'depends_show_if'       => 'carousel',
			),
			'navigate_button' => array(
				'label'					=> esc_html__( 'Display next/prev buttons', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
					'off'		=> esc_html__( 'No', 'tm_builder' ),
				),
				'tab_slug'				=> 'advanced',
				'depends_show_if'       => 'carousel',
			),
			'navigate_pagination' => array(
				'label'					=> esc_html__( 'Display pagination buttons', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
					'off'		=> esc_html__( 'No', 'tm_builder' ),
				),
				'tab_slug'				=> 'advanced',
				'depends_show_if'       => 'carousel',
			),
			'centered_slides' => array(
				'label'					=> esc_html__( 'Display first item in center', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'off'		=> esc_html__( 'No', 'tm_builder' ),
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'				=> 'advanced',
				'depends_show_if'       => 'carousel',
			),
			'slides_per_view' => array(
				'label'					=> esc_html__( 'Multi Column slides layout', 'tm_builder' ),
				'option_category'		=> 'configuration',
				'type'					=> 'range',
				'default'				=> '3',
				'tab_slug'				=> 'advanced',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '6',
					'step' => '1',
				),
				'depends_show_if'       => 'carousel',
			),
		);
		return $fields;
	}

	private function init_divider_styles( $module_class, $function_name ) {

		$divider               = $this->shortcode_atts['divider'];
		$divider_color         = $this->shortcode_atts['divider_color'];
		$divider_height        = $this->shortcode_atts['divider_height'];
		$divider_height_laptop = $this->shortcode_atts['divider_height_laptop'];
		$divider_height_tablet = $this->shortcode_atts['divider_height_tablet'];
		$divider_height_phone  = $this->shortcode_atts['divider_height_phone'];

		$divider_module_class = str_replace( ' ', '.', $module_class ) . ' .tm_pb_brands_showcase_module__divider';

		if ( '' !== $divider_height ) {
			$divider_height_values = array(
				'desktop' => $divider_height,
				'laptop'  => $divider_height_laptop,
				'tablet'  => $divider_height_tablet,
				'phone'   => $divider_height_phone,
			);

			tm_pb_generate_responsive_css(
				$divider_height_values,
				$divider_module_class,
				'height',
				$function_name
			);
		}

		$divider_css_style = '';

		if ( '' !== $divider_color && 'on' === $divider ) {
			$divider_css_style .= sprintf( ' background-color: %s;',
				esc_attr( $divider_color )
			);

			if ( '' !== $divider_css_style ) {
				TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '#tm_builder_outer_content ' . $divider_module_class,
					'declaration' => ltrim( $divider_css_style )
				) );
			}
		}
	}

	function enqueue_assets() {
		wp_enqueue_style( 'tm-builder-swiper' );
		wp_enqueue_script( 'tm-builder-swiper' );
	}

	function pre_shortcode_content() {
		global $tm_pb_brands_showcase;

		$tm_pb_brands_showcase = array(
			'template'       => $this->shortcode_atts['template'],
			'columns'        => $this->shortcode_atts['columns'],
			'columns_laptop' => $this->shortcode_atts['columns_laptop'],
			'columns_tablet' => $this->shortcode_atts['columns_tablet'],
			'columns_phone'  => $this->shortcode_atts['columns_phone'],
		);
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$template = $this->shortcode_atts['template'];
		$carousel_settings = null;

		if ( 'carousel' === $template ) {
			$carousel_settings = htmlentities( json_encode( array(
				'autoplay'           => $this->shortcode_atts['autoplay'],
				'navigateButton'     => $this->shortcode_atts['navigate_button'],
				'pagination'         => $this->shortcode_atts['navigate_pagination'],
				'slidesPerView'      => $this->shortcode_atts['slides_per_view'],
				'centeredSlides'     => $this->shortcode_atts['centered_slides'],
				'spaceBetweenSlides' => apply_filters( 'tm_pb_module_carousel_space', 10 ),
			) ) );
		}

		$this->shortcode_atts = array_merge( $this->shortcode_atts, array(
			'module_class' => TM_Builder_Element::add_module_order_class(
				$this->_var( 'module_class' ),
				$function_name
			) . ' tm_pb_bg_layout_light',

			'divider_hide_on_mobile' => 'on' === $this->_var( 'divider_hide_on_mobile' ) ?
				' ' . self::HIDE_ON_MOBILE : '',

			'template' => sprintf( 'brands-showcase/%s/brands-showcase-item.php', $template ),
			'carousel_settings' => $carousel_settings
		) );

		$this->set_vars( array(
			'super_title',
			'title',
			'sub_title',
			'divider',
			'admin_label',
			'module_id',
			'module_class',
			'divider_color',
			'divider_height',
			'divider_height_laptop',
			'divider_height_tablet',
			'divider_height_phone',
			'divider_style',
			'divider_width',
			'divider_hide_on_mobile',
			'template',
			'carousel_settings',
			'columns',
			'columns_laptop',
			'columns_tablet',
			'columns_phone',
			'autoplay',
			'navigate_button',
			'pagination',
			'slides_per_view',
			'centered_slides',
		) );

		$this->init_divider_styles( $this->shortcode_atts['module_class'], $function_name );
		$this->shortcode_content = trim( strip_tags( $this->shortcode_content, '<div></div><a></a><img><img/><span></span>' ) );
		$output = $this->get_template_part( sprintf( 'brands-showcase/%s/brands-showcase.php', $template ) );
		return $output;
	}

	public function prepare_brand( $brand = array() ) {

		if ( isset( $brand['brand_logo'] ) && ! empty( $brand['brand_logo'] ) ) {
			$brand['brand_logo'] = apply_filters( 'tm_pb_module_brands_brand_logo', $brand['brand_logo'] );
		}

		return $brand;
	}
}

new Tm_Builder_Module_Brands_Showcase;
