<?php
class Tm_Builder_Module_Posts extends Tm_Builder_Module {

	public $posts_query;

	function init() {

		$this->name = esc_html__( 'Posts', 'tm_builder' );
		$this->slug = 'tm_pb_posts';
		$this->icon = 'f009';

		$this->whitelisted_fields = array(
			'terms_type',
			'categories',
			'post_tag',
			'post_format',
			'post_id',
			'posts_per_page',
			'post_offset',
			'super_title',
			'title',
			'subtitle',
			'title_delimiter',
			'more',
			'more_text',
			'ajax_more',
			'more_url',
			'meta_data',
			'post_layout',
			'excerpt',
			'columns',
			'columns_laptop',
			'columns_tablet',
			'columns_phone',
			'image_size',
			'use_space',
			'use_rows_space',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'title_delimiter' => array( 'off' ),
			'more'            => array( 'off' ),
			'ajax_more'       => array( 'on' ),
			'columns'         => array( 4 ),
			'columns_laptop'  => array( 4 ),
			'columns_tablet'  => array( 2 ),
			'columns_phone'   => array( 1 ),
			'posts_per_page'  => array( 3 ),
			'meta_data'       => array( 'on' ),
			'excerpt'         => array( 25 ),
			'post_layout'     => array( 'layout-1' ),
			'use_space'       => array( 'on' ),
			'use_rows_space'  => array( 'on' ),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_posts';
		$this->advanced_options = array(
			'fonts' => array(
				'supertitle' => array(
					'label'     => esc_html__( 'Supertitle', 'tm_builder' ),
					'font_size' => array(
						'default' => '20px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css'       => array(
						'main' => "{$this->main_css_element} .tm-posts_supertitle",
					),
				),
				'title' => array(
					'label'    => esc_html__( 'Title', 'tm_builder' ),
					'font_size' => array(
						'default' => '24px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm-posts_title",
					),
				),
				'subtitle' => array(
					'label'    => esc_html__( 'Subtitle', 'tm_builder' ),
					'font_size' => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm-posts_subtitle",
					),
				),
				'item_title' => array(
					'label'    => esc_html__( 'Item Title', 'tm_builder' ),
					'font_size' => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm-posts_item_title",
					),
				),
				'item_meta' => array(
					'label'    => esc_html__( 'Item Meta', 'tm_builder' ),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm-posts_item_meta a",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.2em',
					),
					'css'      => array(
						'line_height' => "{$this->main_css_element} .tm-posts_item_excerpt",
					),
				),
			),
			'background' => array(
				'use_background_color' => false,
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'tm_builder' ),
					'css' => array(
						'main' => "{$this->main_css_element} .tm-posts_button_wrap .btn",
					),
				),
			),
		);

		add_action( 'wp_ajax_tm_pb_load_more', array( $this, 'process_load_more' ) );
		add_action( 'wp_ajax_nopriv_tm_pb_load_more', array( $this, 'process_load_more' ) );

	}

	function get_fields() {
		$fields = array(
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
			),
			'terms_type' => array(
				'label'					=> esc_html__( 'Choose taxonomy type', 'tm_builder' ),
				'type'					=> 'select',
				'option_category'		=> 'basic_option',
				'options'				=> array(
					'categories'		=> esc_html__( 'Categories', 'tm_builder' ),
					'post_tag'			=> esc_html__( 'Tag', 'tm_builder' ),
					'post_format'		=> esc_html__( 'Post Format', 'tm_builder' ),
					'post_id'			=> esc_html__( 'Post id', 'tm_builder' )
				),
				'affects'			=> array(
					'#tm_pb_categories',
					'#tm_pb_post_tag',
					'#tm_pb_post_format',
					'#tm_pb_post_id',
					'#tm_pb_posts_per_page',
					'#tm_pb_post_offset',
				),
				'description'			=> esc_html__( 'Choose taxonomy type', 'tm_builder' ),
			),
			'categories' => array(
				'label'					=> esc_html__( 'Include categories', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'depends_show_if'		=> 'categories',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms'  => false,
					'input_name' => 'tm_pb_categories',
				),
				'description'			=> esc_html__( 'Choose which categories you would like to include.', 'tm_builder' ),
			),
			'post_tag' => array(
				'label'					=> esc_html__( 'Include tags', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'depends_show_if'		=> 'post_tag',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms'  => true,
					'term_name'  => 'post_tag',
					'input_name' => 'tm_pb_post_tag',
				),
				'description'		=> esc_html__( 'Choose which categories you would like to include.', 'tm_builder' ),
			),
			'post_format' => array(
				'label'					=> esc_html__( 'Include post format', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'depends_show_if'		=> 'post_format',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms'  => true,
					'term_name'  => 'post_format',
					'input_name' => 'tm_pb_post_format',
				),
				'description'			=> esc_html__( 'Choose which post format you would like to include.', 'tm_builder' ),
			),
			'post_id' => array(
				'label'           => esc_html__( 'Include post id', 'tm_builder' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'depends_show_if' => 'post_id',
				'description'     => esc_html__( 'Enter post id you would like to include. The separator gap. Example: 256 472 23 6', 'tm_builder' ),
			),
			'posts_per_page' => array(
				'label'               => esc_html__( 'Posts count ( Set 0 to show all ) ', 'tm_builder' ),
				'option_category'     => 'basic_option',
				'type'                => 'range',
				'range_settings'      => array(
					'min'  => 0,
					'max'  => 30,
					'step' => 1,
				),
				'default'             => '3',
				'depends_show_if_not' => 'post_id',
			),
			'post_offset' => array(
				'label'               => esc_html__( 'Offset post', 'tm_builder' ),
				'option_category'     => 'basic_option',
				'type'                => 'range',
				'default'             => '0',
				'depends_show_if_not' => 'post_id',
			),
			'super_title' => array(
				'label'           => esc_html__( 'Super Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
			),
			'subtitle' => array(
				'label'           => esc_html__( 'Sub Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
			),
			'title_delimiter' => array(
				'label'           => esc_html__( 'Display title delimiter', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'more' => array(
				'label'           => esc_html__( 'Display more button', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'affects'         => array(
					'#tm_pb_more_text',
					'#tm_pb_ajax_more',
				),
			),
			'more_text' => array(
				'label'           => esc_html__( 'More button text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'on',
			),
			'ajax_more' => array(
				'label'   => esc_html__( 'AJAX load more', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'affects'         => array(
					'#tm_pb_more_url',
				),
			),
			'more_url' => array(
				'label'           => esc_html__( 'More button URL', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'off',
			),
			'excerpt' => array(
				'label'           => esc_html__( 'Excerpt length', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '25',
				'range_settings' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
				'description'     => esc_html__( 'Set words number in excerpt (set 0 to hide excerpt)', 'tm_builder' ),
			),
			'image_size' => array(
				'label'           => esc_html__( 'Featured Image Size', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => tm_builder_tools()->get_image_sizes(),
				'description'     => esc_html__( 'Select featured thumbnail size.', 'tm_builder' ),
			),
			'meta_data' => array(
				'label'   => esc_html__( 'Display post meta data', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'post_layout' => array(
				'label'   => esc_html__( 'Layout', 'tm_builder' ),
				'type'    => 'select',
				'options' => array(
					'layout-1' => esc_html__( 'Layout 1', 'tm_builder' ),
					'layout-2' => esc_html__( 'Layout 2', 'tm_builder' ),
					'layout-3' => esc_html__( 'Layout 3', 'tm_builder' ),
				),
				'option_category' => 'configuration',
			),
			'use_space' => array(
				'label'             => esc_html__( 'Use gutter between columns', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'use_rows_space' => array(
				'label'             => esc_html__( 'Use gutter between rows', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
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

		$var_list = array(
			'terms_type',
			'categories',
			'post_tag',
			'post_format',
			'post_id',
			'posts_per_page',
			'post_offset',
			'super_title',
			'title',
			'subtitle',
			'title_delimiter',
			'more',
			'more_text',
			'ajax_more',
			'more_url',
			'meta_data',
			'post_layout',
			'excerpt',
			'columns',
			'columns_laptop',
			'columns_tablet',
			'columns_phone',
			'image_size',
			'use_space',
			'use_rows_space',
			'button_icon',
			'custom_button',
		);

		$this->set_vars( $var_list );

		if ( '' === $this->_var( 'button_icon' ) ) {
			$this->_var( 'button_icon', 'f18e' );
		}

		$this->_var( 'icon', esc_attr( tm_pb_process_font_icon( $this->_var( 'button_icon' ) ) ) );

		$icon_family = tm_builder_get_icon_family();

		if ( $icon_family ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_custom_button_icon:after',
				'declaration' => sprintf(
					'font-family: "%1$s" !important;',
					esc_attr( $icon_family )
				),
			) );
		}

		$this->posts_query = tm_builder_tools()->build_module_query( $this );

		$atts = array();

		if ( 'on' === $this->_var( 'ajax_more' ) ) {

			$data_atts = array();

			foreach ( $var_list as $var ) {
				$data_atts[ $var ] = $this->_var( $var );
			}

			$atts['data-atts']  = json_encode( $data_atts );
			$atts['data-page']  = 1;
			$atts['data-pages'] = $this->posts_query->max_num_pages;
		}

		$content = $this->get_template_part( 'post/posts.php' );
		$classes = array();
		$output  = $this->wrap_module( $content, $classes, $function_name, $atts );

		wp_reset_postdata();
		wp_reset_query();

		return $output;
	}

	function process_load_more() {

		if ( empty( $_REQUEST['atts'] ) || empty( $_REQUEST['page'] ) ) {
			die();
		}

		$atts = $_REQUEST['atts'];

		if ( ! is_array( $atts ) ) {
			die();
		}

		foreach ( $atts as $att => $value ) {
			$this->_var( $att, esc_attr( $value ) );
		}

		$paged = intval( $_REQUEST['page'] );
		$paged = $paged + 1;

		$this->_var( 'paged', $paged );

		$this->posts_query = tm_builder_tools()->build_module_query( $this );

		$content = '';

		if ( $this->posts_query->have_posts() ) {

			while ( $this->posts_query->have_posts() ) {
				$this->posts_query->the_post();
				$content .= $this->get_template_part( $this->get_layout_template() );
			}

		}

		wp_send_json_success( array(
			'result' => $content,
			'atts'   => $atts,
			'page'   => $paged,
		) );

	}

	/**
	 * Get layout template name
	 *
	 * @return string
	 */
	public function get_layout_template() {

		$layout = $this->_var( 'post_layout' );

		if ( ! $layout ) {
			$layout = 'layout-1';
		}

		$template = sprintf( 'post/item-posts-%s.php', esc_attr( $layout ) );

		return $template;

	}

}

new Tm_Builder_Module_Posts;
