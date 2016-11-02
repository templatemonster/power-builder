<?php
class Tm_Builder_Module_Blog extends Tm_Builder_Module {

	private $function_name = null;
	public $classes = array(
		'base' => array(),
		'item' => array( 'tm_pb_post' ),
	);

	function init() {
		$this->name = esc_html__( 'Blog', 'tm_builder' );
		$this->slug = 'tm_pb_blog';
		$this->icon = 'f181';

		$this->whitelisted_fields = array(
			'blog_layout',
			'posts_number',
			'columns',
			'columns_laptop',
			'columns_tablet',
			'columns_phone',
			'include_categories',
			'meta_date',
			'show_thumbnail',
			'image_size',
			'show_content',
			'show_more',
			'show_author',
			'show_date',
			'show_categories',
			'show_comments',
			'show_pagination',
			'excerpt',
			'offset_number',
			'admin_label',
			'module_id',
			'module_class',
			'masonry_tile_background_color',
			'use_overlay',
			'overlay_icon_color',
			'hover_overlay_color',
			'hover_icon',
		);

		$this->fields_defaults = array(
			'blog_layout'     => array( 'list' ),
			'posts_number'    => array( 10, 'add_default_setting' ),
			'meta_date'       => array( 'M j, Y', 'add_default_setting' ),
			'columns'         => array( 4 ),
			'columns_laptop'  => array( 4 ),
			'columns_tablet'  => array( 2 ),
			'columns_phone'   => array( 1 ),
			'show_thumbnail'  => array( 'on' ),
			'image_size'      => array( 'post-thumbnail' ),
			'show_content'    => array( 'off' ),
			'show_more'       => array( 'off' ),
			'show_author'     => array( 'on' ),
			'show_date'       => array( 'on' ),
			'show_categories' => array( 'on' ),
			'show_comments'   => array( 'off' ),
			'show_pagination' => array( 'on' ),
			'offset_number'   => array( 0, 'only_default_setting' ),
			'use_overlay'     => array( 'off' ),
			'excerpt'         => array( 55 ),
		);

		$this->main_css_element = '%%order_class%% .tm_pb_post';
		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2",
						'important' => 'all',
					),
				),
				'meta' => array(
					'label'    => esc_html__( 'Meta', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_post_meta",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
			'border' => array(),
		);
		$this->custom_css_options = array(
			'post_item' => array(
				'label'    => esc_html__( 'Post item', 'tm_builder' ),
				'selector' => '.tm_pb_post',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'tm_builder' ),
				'selector' => '.tm_pb_post h2',
			),
			'post_meta' => array(
				'label'    => esc_html__( 'Post Meta', 'tm_builder' ),
				'selector' => '.tm_pb_post .post-meta',
			),
			'pagenavi' => array(
				'label'    => esc_html__( 'Pagenavi', 'tm_builder' ),
				'selector' => '.wp_pagenavi',
			),
			'featured_image' => array(
				'label'    => esc_html__( 'Featured Image', 'tm_builder' ),
				'selector' => '.tm_pb_image_container',
			),
			'read_more' => array(
				'label'    => esc_html__( 'Read More Button', 'tm_builder' ),
				'selector' => '.tm_pb_post .more-link',
			),
		);
	}

	function get_fields() {

		$options = apply_filters( 'tm_builder_blog_module_options', array(
			'blog_layout' => array(
				'list'    => esc_html__( 'List', 'tm_builder' ),
				'grid'    => esc_html__( 'Grid', 'tm_builder' ),
				'masonry' => esc_html__( 'Masonry', 'tm_builder' ),
			),
			'columns' => array(
				'min'  => '1',
				'max'  => '6',
				'step' => '1',
			),
		) );

		$fields = array(
			'blog_layout' => array(
				'label'             => esc_html__( 'Layout', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => $options['blog_layout'],
				'affects'           => array(
					'#tm_pb_background_layout',
					'#tm_pb_masonry_tile_background_color',
					'#tm_pb_columns',
				),
				'description'        => esc_html__( 'Toggle between the various blog layout types.', 'tm_builder' ),
			),
			'posts_number' => array(
				'label'             => esc_html__( 'Posts Number', 'tm_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Choose how much posts you would like to display per page.', 'tm_builder' ),
			),
			'columns' => array(
				'label'           => esc_html__( 'Columns', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '4',
				'range_settings' => array(
					'min'  => $options['columns']['min'],
					'max'  => $options['columns']['max'],
					'step' => $options['columns']['step'],
				),
				'depends_show_if_not' => 'list',
				'mobile_options'      => true,
				'mobile_global'       => true,
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Include Categories', 'tm_builder' ),
				'renderer'         => 'tm_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'description'      => esc_html__( 'Choose which categories you would like to include in the feed.', 'tm_builder' ),
			),
			'meta_date' => array(
				'label'             => esc_html__( 'Meta Date Format', 'tm_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'If you would like to adjust the date format, input the appropriate PHP date format here.', 'tm_builder' ),
			),
			'show_thumbnail' => array(
				'label'             => esc_html__( 'Show Featured Image', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_image_size',
				),
				'description'        => esc_html__( 'This will turn thumbnails on and off.', 'tm_builder' ),
			),
			'image_size' => array(
				'label'             => esc_html__( 'Featured Image Size', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => tm_builder_tools()->get_image_sizes(),
				'depends_show_if'   => 'on',
				'description'        => esc_html__( 'Select featured thumbnail size.', 'tm_builder' ),
			),
			'show_content' => array(
				'label'             => esc_html__( 'Content', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'excerpt' => esc_html__( 'Show Excerpt', 'tm_builder' ),
					'content' => esc_html__( 'Show Content', 'tm_builder' ),
					'none'    => esc_html__( 'Hide Content', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_show_more',
					'#tm_pb_excerpt',
				),
				'description'        => esc_html__( 'Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'tm_builder' ),
			),
			'excerpt' => array(
				'label'           => esc_html__( 'Excerpt length', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '55',
				'range_settings'  => array(
					'min'  => 1,
					'max'  => 150,
					'step' => 1,
				),
				'depends_show_if' => 'excerpt',
			),
			'show_more' => array(
				'label'               => esc_html__( 'Read More Button', 'tm_builder' ),
				'type'                => 'yes_no_button',
				'option_category'     => 'configuration',
				'options'             => array(
					'off' => esc_html__( 'Off', 'tm_builder' ),
					'on'  => esc_html__( 'On', 'tm_builder' ),
				),
				'depends_show_if_not' => 'content',
				'description'         => esc_html__( 'Here you can define whether to show "read more" link after the excerpts or not.', 'tm_builder' ),
			),
			'show_author' => array(
				'label'             => esc_html__( 'Show Author', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Turn on or off the author link.', 'tm_builder' ),
			),
			'show_date' => array(
				'label'             => esc_html__( 'Show Date', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Turn the date on or off.', 'tm_builder' ),
			),
			'show_categories' => array(
				'label'             => esc_html__( 'Show Categories', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Turn the category links on or off.', 'tm_builder' ),
			),
			'show_comments' => array(
				'label'             => esc_html__( 'Show Comment Count', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Turn comment count on and off.', 'tm_builder' ),
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Pagination', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Turn pagination on and off.', 'tm_builder' ),
			),
			'offset_number' => array(
				'label'           => esc_html__( 'Offset Number', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Choose how many posts you would like to offset by', 'tm_builder' ),
			),
			'use_overlay' => array(
				'label'             => esc_html__( 'Featured Image Overlay', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Off', 'tm_builder' ),
					'on'  => esc_html__( 'On', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_overlay_icon_color',
					'#tm_pb_hover_overlay_color',
					'#tm_pb_hover_icon',
				),
				'description'       => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the featured image of a post.', 'tm_builder' ),
			),
			'overlay_icon_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'tm_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay icon', 'tm_builder' ),
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'tm_builder' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'tm_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'tm-pb-font-icon' ),
				'renderer'            => 'tm_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'depends_show_if'     => 'on',
				'description'         => esc_html__( 'Here you can define a custom icon for the overlay', 'tm_builder' ),
			),
			'masonry_tile_background_color' => array(
				'label'               => esc_html__( 'Grid Tile Background Color', 'tm_builder' ),
				'type'                => 'color-alpha',
				'custom_color'        => true,
				'tab_slug'            => 'advanced',
				'depends_show_if_not' => 'list',
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

	/**
	 * Aggregates all blog-related styles definitions
	 */
	public function set_styles() {

		$styles = array(
			'masonry_tile_background_color' => array(
				'selector' => '%%order_class%%.tm_pb_blog_grid .tm_pb_post',
				'format'   => 'background-color: %1$s;',
			),
			'overlay_icon_color' => array(
				'selector' => '%%order_class%% .tm_overlay:before',
				'format'   => 'color: %1$s !important;',
			),
			'hover_overlay_color' => array(
				'selector' => '%%order_class%% .tm_overlay',
				'format'   => 'background-color: %1$s;',
			),
		);

		foreach ( $styles as $var => $data ) {

			$style = $this->_var( $var );

			if ( ! empty( $style ) ) {

				TM_Builder_Element::set_style( $this->function_name, array(
					'selector'    => $data['selector'],
					'declaration' => sprintf(
						$data['format'],
						esc_html( $style )
					),
				) );

			}
		}

		$cols = $this->get_cols();

		$queries = array(
			'desktop' => 'xl_up',
			'laptop'  => 'lg',
			'tablet'  => 'md',
			'phone'   => 'sm_down',
		);

		foreach ( $cols as $device => $data ) {

			$style = array(
				'selector'    => '%%order_class%% .tm_pb_blog_masonry_wrapper[data-columns]::before',
				'declaration' => sprintf( 'content: \'%1$s .%2$s\';', $data['cols'], $data['class'] ),
			);

			if ( false !== $query = TM_Builder_Element::get_media_query( $queries[ $device ] ) ) {
				$style['media_query'] = $query;
			}

			TM_Builder_Element::set_style( $this->function_name, $style );
		}

	}

	/**
	 * Get column class.
	 *
	 * @return string
	 */
	public function get_cols() {

		$data_map = array(
			'desktop' => 'columns',
			'laptop'  => 'columns_laptop',
			'tablet'  => 'columns_tablet',
			'phone'   => 'columns_phone',
		);

		$namespace = array(
			'desktop' => 'xl',
			'laptop'  => 'lg',
			'tablet'  => 'md',
			'phone'   => 'sm',
		);

		$result = array();

		foreach ( $data_map as $device => $var ) {
			$col = intval( $this->_var( $var ) );

			if ( ! $col ) {
				$col = 4;
			}

			$result[ $device ] = array(
				'cols'      => $col,
				'class'     => sprintf( 'col-%2$s-%1$s', round( 12/$col ), $namespace[ $device ] ),
			);

		}

		return $result;

	}

	/**
	 * Returns arguments for posts query
	 *
	 * @return array
	 */
	public function get_query_args() {

		global $paged;

		$args = array( 'posts_per_page' => (int) $this->_var( 'posts_number' ) );

		$tm_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );

		if ( is_front_page() ) {
			$paged = $tm_paged;
		}

		if ( '' !== $this->_var( 'include_categories' ) ) {
			$args['cat'] = $this->_var( 'include_categories' );
		}

		if ( ! is_search() ) {
			$args['paged'] = $tm_paged;
		}

		$offset_number = $this->_var( 'offset_number' );

		if ( '' !== $offset_number && ! empty( $offset_number ) ) {
			/**
			 * Offset + pagination don't play well. Manual offset calculation required
			 * @see: https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			if ( $paged > 1 ) {
				$args['offset'] = ( ( $tm_paged - 1 ) * intval( $this->_var( 'posts_number' ) ) ) + intval( $offset_number );
			} else {
				$args['offset'] = intval( $offset_number );
			}
		}

		if ( is_single() && ! isset( $args['post__not_in'] ) ) {
			$args['post__not_in'] = array( get_the_ID() );
		}

		return $args;
	}

	/**
	 * Prepare posts loop data
	 */
	public function setup_loop() {

		$this->_var( 'posts' )->the_post();
		$this->_var( 'post_format', tm_pb_post_format() );

		if ( 'on' === $this->_var( 'show_thumbnail' ) ) {
			$this->_var( 'thumb', get_the_post_thumbnail(
				get_the_id(),
				$this->_var( 'image_size' ),
				array( 'alt' => get_the_title() )
			) );
		}

		if ( ( ! $this->_var( 'thumb' ) || 'off' === $this->_var( 'show_thumbnail' ) )
			&& ! in_array( $this->_var( 'post_format' ), array( 'video', 'gallery' ) ) ) {
			$this->classes['item'][] = 'tm_pb_no_thumb';
		}

		if ( in_array( $this->_var( 'post_format' ), array( 'audio', 'quote', 'link' ) ) ) {
			$this->_var( 'format_media', false );
		}

	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$this->set_vars(
			array(
				'module_id',
				'module_class',
				'blog_layout',
				'posts_number',
				'columns',
				'columns_laptop',
				'columns_tablet',
				'columns_phone',
				'include_categories',
				'meta_date',
				'show_thumbnail',
				'image_size',
				'show_content',
				'show_author',
				'show_date',
				'show_categories',
				'show_comments',
				'show_pagination',
				'show_more',
				'offset_number',
				'masonry_tile_background_color',
				'overlay_icon_color',
				'hover_overlay_color',
				'hover_icon',
				'use_overlay',
				'excerpt',
			)
		);

		$this->classes = array(
			'base' => array(),
			'item' => array( 'tm_pb_post' ),
		);

		$this->function_name = $function_name;

		global $paged;

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class');

		$this->set_styles();

		$this->_var( 'item_overlay', $this->get_overlay() );

		if ( 'list' !== $this->_var( 'blog_layout' ) ) {
			$background_layout = 'light';
		}

		if ( 'masonry' === $this->_var( 'blog_layout' ) ) {
			wp_enqueue_script( 'salvattore' );
		}

		$args = $this->get_query_args();

		$this->_var( 'posts', new WP_Query( $args ) );

		// Main Query fix for pagination
		global $wp_query;
		$temp_query = $wp_query;
		$wp_query   = null;
		$wp_query   = $this->_var( 'posts' );

		$posts = $this->get_template_part( 'blog.php' );

		// Reset Query fix for pagination
		wp_reset_query();
		$wp_query = null;
		$wp_query = $temp_query;

		$this->classes['base'][] = 'tm_pb_module';
		$this->classes['base'][] = 'tm_pb_bg_layout_light';
		$this->classes['base'][] = 'tm_pb_posts';
		$this->classes['base'][] = 'clearfix';
		$this->classes['base'][] = sprintf( 'layout-%s', $this->_var( 'blog_layout' ) );

		$output = $this->wrap_module( $posts, $this->classes['base'], $function_name );

		return $output;
	}

	/**
	 * Get overlay HTML markup
	 * @return string
	 */
	public function get_overlay() {

		if ( 'on' !== $this->_var( 'use_overlay' ) ) {
			return '';
		}

		$icon        = tm_pb_process_font_icon( $this->_var( 'hover_icon' ) );
		$icon_family = tm_builder_get_icon_family();

		$data_icon = ( '' !== $this->_var( 'hover_icon' ) ) ? sprintf( ' data-icon="%1$s"', $icon ) : '';

		if ( $icon_family ) {
			TM_Builder_Element::set_style( $this->function_name, array(
				'selector'    => '%%order_class%% .tm_overlay.tm_pb_inline_icon:before',
				'declaration' => sprintf(
					'font-family: "%1$s" !important;',
					esc_attr( $icon_family )
				),
			) );
		}

		$this->classes['item'][] = 'tm_pb_has_overlay';

		return sprintf(
			'<span class="tm_overlay%1$s"%2$s></span>',
			( '' !== $this->_var( 'hover_icon' ) ? ' tm_pb_inline_icon' : '' ),
			$data_icon
		);

	}

	/**
	 * Returns posts pagination
	 *
	 * @return string
	 */
	public function get_pagination() {

		if ( 'on' !== $this->_var( 'show_pagination' ) || is_search() ) {
			return '';
		}

		$result = $this->get_template_part( 'blog/pagination.php' );

		return $result;
	}

	/**
	 * Get post content depending from shortcode atts
	 *
	 * @return string
	 */
	public function get_post_content() {

		$post_content = get_the_content();
		$format       = '<div class="tm_pb_post_content">%s</div>';

		// do not display the content if it contains Blog, Post Slider, Fullwidth Post Slider, or Portfolio modules to avoid infinite loops
		if ( ! has_shortcode( $post_content, 'tm_pb_blog' )
			&& ! has_shortcode( $post_content, 'tm_pb_portfolio' )
			&& ! has_shortcode( $post_content, 'tm_pb_post_slider' )
			&& ! has_shortcode( $post_content, 'tm_pb_fullwidth_post_slider' )
		) {

			switch ( $this->_var( 'show_content' ) ) {
				case 'content':

					global $more;

					// page builder doesn't support more tag, so display the_content() in case of post made with page builder
					if ( tm_pb_is_pagebuilder_used( get_the_ID() ) ) {
						$more = 1;
						return sprintf( $format, get_the_content() );
					} else {
						$more = null;
						return sprintf( $format, get_the_content( esc_html__( 'read more...', 'tm_builder' ) ) );
					}

					break;

				case 'excerpt':

					if ( ! $this->_var( 'excerpt' ) ) {
						$this->_var( 'excerpt', 55 );
					}

					if ( has_excerpt() ) {
						return sprintf( $format, get_the_excerpt() );
					} else {
						return sprintf( $format, tm_pb_truncate_post( $this->_var( 'excerpt' ), false ) );
					}

					break;
			}

		} else if ( has_excerpt() ) {
			return sprintf( $format, get_the_excerpt() );
		}

	}

	/**
	 * Returns read more button HTML.
	 *
	 * @return string
	 */
	public function get_more_button() {
		if ( 'content' === $this->_var( 'show_content' ) || 'on' !== $this->_var( 'show_more' ) ) {
			return '';
		}

		return $this->get_template_part( 'blog/more.php' );
	}

	/**
	 * Returns open blog listing wrapper
	 *
	 * @return string
	 */
	public function open_posts_list() {
		return sprintf(
			'<div class="tm_pb_blog_%1$s_wrapper %2$s" data-columns>',
			esc_attr( $this->_var( 'blog_layout' ) ),
			'list' !== $this->_var( 'blog_layout' ) ? 'row' : ''
		);
	}

	/**
	 * Returns close blog listing wrapper
	 *
	 * @return string
	 */
	public function close_posts_list() {
		return '</div>';
	}

	/**
	 * Returns HTML for opening div with layout classes for grid layout.
	 *
	 * @return string
	 */
	public function open_grid_col() {

		if ( 'grid' !== $this->_var( 'blog_layout' ) ) {
			return;
		}

		$cols  = $this->get_cols();
		$class = '';

		foreach ( $cols as $col ) {
			$class .= ' ' . $col['class'];
		}

		return sprintf( '<div class="%s">', trim( $class ) );
	}

	/**
	 * Returns HTML for closing div with layout classes for grid layout.
	 *
	 * @return string
	 */
	public function close_grid_col() {

		if ( 'grid' !== $this->_var( 'blog_layout' ) ) {
			return;
		}

		return '</div>';
	}

	/**
	 * Check if metadata is visible
	 *
	 * @return boolean
	 */
	public function is_meta_visible() {
		return ( 'on' === $this->_var( 'show_author' )
			|| 'on' === $this->_var( 'show_date' )
			|| 'on' === $this->_var( 'show_categories' )
			|| 'on' === $this->_var( 'show_comments' ) );
	}

}

new Tm_Builder_Module_Blog;
