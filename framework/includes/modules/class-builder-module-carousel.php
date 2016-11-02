<?php
class Tm_Builder_Module_Carousel extends Tm_Builder_Module {

	protected $settings = array(
		'terms_type',
		'category_name',
		'tag',
		'post_format',
		'include',
		'posts_per_page',
		'post_offset',
		'super_title',
		'title',
		'subtitle',
		'title_delimiter',
		'title_length',
		'excerpt_length',
		'more',
		'more_text',
		'meta_date',
		'disabled_on',
		'admin_label',
		'image',
		'button_show_all',
		'button_show_all_text',
		'button_show_all_link',
		'image_size',

		/*  Advanced  */
		'autoplay',
		'navigate_button',
		'pagination',
		'first_item_center',
		'counter_item_view',
		'orientation',

		/*  Custom CSS  */
		'width',
		'height',
		'module_id',
		'module_class',
	);

	public function init() {
		$this->name					= esc_html__( 'Carousel', 'tm_builder' );
		$this->icon					= 'f03e';
		$this->slug					= 'tm_pb_swiper';
		$this->main_css_element		= '%%order_class%%.tm_pb_swiper';

		$this->whitelisted_fields	= $this->settings;

		$this->fields_defaults		= array(
			'terms_type'			=> array( 'category_name' ),
			'posts_per_page'		=> array( '10' ),
			'post_offset'			=> array( '0' ),
			'super_title'			=> array( '' ),
			'title'					=> array( '' ),
			'subtitle'				=> array( '' ),
			'title_delimiter'		=> array( 'on' ),
			'title_length'			=> array( '5' ),
			'excerpt_length'		=> array( '5' ),
			'more'					=> array( 'on' ),
			'more_text'				=> array( 'more' ),
			'navigate_button'		=> array( 'on' ),
			'pagination'			=> array( 'on' ),
			'first_item_center'		=> array( 'off' ),
			'counter_item_view'		=> array( '3' ),
			'width'					=> array( '100%' ),
			'height'				=> array( '3em' ),
			'image'					=> array( 'on' ),
			'button_show_all'		=> array( 'off' ),
			'button_show_all_text'	=> array( 'Show All' ),
			'button_show_all_link'	=> array( '#' ),
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 9 );
	}

	public function get_fields() {
		return array(
			/*  General Settings  */
			'terms_type' => array(
				'label'					=> esc_html__( 'Choose taxonomy type', 'tm_builder' ),
				'type'					=> 'select',
				'option_category'		=> 'basic_option',
				'options'				=> array(
					'category_name'			=> esc_html__( 'Categories', 'tm_builder' ),
					'tag'					=> esc_html__( 'Tag', 'tm_builder' ),
					'post_format'			=> esc_html__( 'Post Format', 'tm_builder' ),
					'include'				=> esc_html__( 'Post id', 'tm_builder' )
				),
				'affects'				=> array(
					'#tm_pb_category_name',
					'#tm_pb_tag',
					'#tm_pb_post_format',
					'#tm_pb_include',
					'#tm_pb_posts_per_page',
					'#tm_pb_post_offset',
				),
				'description'			=> esc_html__( 'Choose taxonomy type', 'tm_builder' ),
			),
			'category_name' => array(
				'label'					=> esc_html__( 'Include categories', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'depends_show_if'		=> 'category_name',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms' => false,
					'input_name' => 'tm_pb_category_name',
				),
				'description'			=> esc_html__( 'Choose which categories you would like to include in the carousel.', 'tm_builder' ),
			),
			'tag' => array(
				'label'					=> esc_html__( 'Include tags', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'depends_show_if'		=> 'tag',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms'  => true,
					'term_name'  => 'post_tag',
					'input_name' => 'tm_pb_tag',
				),
				'description'		=> esc_html__( 'Choose which categories you would like to include in the carousel.', 'tm_builder' ),
			),
			'post_format' => array(
				'label'					=> esc_html__( 'Include post format', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'depends_show_if'		=> 'post_format',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms'	=> true,
					'term_name'	=> 'post_format',
					'input_name' => 'tm_pb_post_format',
				),
				'description'			=> esc_html__( 'Choose which post format you would like to include in the carousel.', 'tm_builder' ),
			),
			'include' => array(
				'label'					=> esc_html__( 'Include post id', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'type'					=> 'text',
				'depends_show_if'		=> 'include',
				'description'			=> esc_html__( 'Enter post id you would like to include in the carousel. The separator gap. Example: 256 472 23 6', 'tm_builder' ),
			),
			'posts_per_page' => array(
				'label'					=> esc_html__( 'Posts count ( Set 0 to show all ) ', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'type'					=> 'range',
				'default'				=> '12',
				'depends_show_if_not'	=> 'include',
			),
			'post_offset' => array(
				'label'					=> esc_html__( 'Offset post', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'type'					=> 'range',
				'default'				=> '0',
				'depends_show_if_not'	=> 'include',
			),
			'super_title' => array(
				'label'					=> esc_html__( 'Super Title', 'tm_builder' ),
				'type'					=> 'text',
				'option_category'		=> 'configuration',
				'default'				=> $this->fields_defaults['super_title'][0],
			),
			'title' => array(
				'label'					=> esc_html__( 'Title', 'tm_builder' ),
				'type'					=> 'text',
				'option_category'		=> 'configuration',
				'default'				=> $this->fields_defaults['title'][0],
			),
			'subtitle' => array(
				'label'					=> esc_html__( 'Sub Title', 'tm_builder' ),
				'type'					=> 'text',
				'option_category'		=> 'configuration',
				'default'				=> $this->fields_defaults['subtitle'][0],
			),
			'title_delimiter' => array(
				'label'					=> esc_html__( 'Display title delimiter', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
					'off'		=> esc_html__( 'No', 'tm_builder' ),
				),
				'affects'				=> array(
					'#tm_pb_background_color',
				),
			),
			'title_length' => array(
				'label'					=> esc_html__( 'Title words length ( Set 0 to hide title. )', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'type'					=> 'range',
				'default'				=> '5',
			),
			'excerpt_length' => array(
				'label'					=> esc_html__( 'Excerpt words length ( Set 0 to hide excerpt. )', 'tm_builder' ),
				'option_category'		=> 'basic_option',
				'type'					=> 'range',
				'default'				=> '5',
			),
			'image' => array(
				'label'					=> esc_html__( 'Display post image', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
					'off'		=> esc_html__( 'No', 'tm_builder' ),
				),
			),
			'more' => array(
				'label'					=> esc_html__( 'Display more button', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
					'off'		=> esc_html__( 'No', 'tm_builder' ),
				),
				'affects'				=> array(
					'#tm_pb_more_text',
				),
			),
			'more_text' => array(
				'label'					=> esc_html__( 'More button text', 'tm_builder' ),
				'type'					=> 'text',
				'option_category'		=> 'configuration',
				'depends_show_if'		=> 'on',
				'default'				=> $this->fields_defaults['more_text'][0],

			),
			'button_show_all' => array(
				'label'					=> esc_html__( 'Display "Show all" button', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'off'		=> esc_html__( 'No', 'tm_builder' ),
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'				=> array(
					'#tm_pb_button_show_all_text',
					'#tm_pb_button_show_all_link',
				),
			),
			'button_show_all_text' => array(
				'label'           => esc_html__( 'Button text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'on',
				'default'				=> $this->fields_defaults['button_show_all_text'][0],
			),
			'button_show_all_link' => array(
				'label'           => esc_html__( 'Button link', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'on',
				'default'				=> $this->fields_defaults['button_show_all_link'][0],
			),
			'meta_date' => array(
				'label'					=> esc_html__( 'Display post meta data', 'tm_builder' ),
				'type'					=> 'multiple_checkboxes',
				'options'				=> array(
					'date'			=> esc_html__( 'Date', '__tm' ),
					'author'		=> esc_html__( 'Author', '__tm' ),
					'comment_count'	=> esc_html__( 'Comment count', '__tm' ),
					'category'		=> esc_html__( 'Category', '__tm' ),
					'post_tag'		=> esc_html__( 'Tag', '__tm' ),
				),
				'option_category' => 'configuration',
			),
			'image_size' => array(
				'label'           => esc_html__( 'Featured Image Size', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => tm_builder_tools()->get_image_sizes(),
				'description'     => esc_html__( 'Select featured thumbnail size.', 'tm_builder' ),
			),
			'disabled_on' => array(
				'label'					=> esc_html__( 'Disable on', 'tm_builder' ),
				'type'					=> 'multiple_checkboxes',
				'options'				=> tm_pb_media_breakpoints(),
				'additional_att'		=> 'disable_on',
				'option_category'		=> 'configuration',
				'description'			=> esc_html__( 'This will disable the module on selected devices', 'tm_builder' ),
			),
			'admin_label' => array(
				'label'					=> esc_html__( 'Admin Label', 'tm_builder' ),
				'type'					=> 'text',
				'option_category'		=> 'configuration',
				'description'			=> esc_html__( 'This will change the label of the module in the builder for easy identification.', 'tm_builder' ),
			),

			/*  Advanced  */
			'autoplay' => array(
				'label'					=> esc_html__( 'Autoplay', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'off'		=> esc_html__( 'No', 'tm_builder' ),
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'				=> 'advanced',
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
			),
			'pagination' => array(
				'label'					=> esc_html__( 'Display pagination buttons', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
					'off'		=> esc_html__( 'No', 'tm_builder' ),
				),
				'tab_slug'				=> 'advanced',
			),
			'first_item_center' => array(
				'label'					=> esc_html__( 'Display first item in center', 'tm_builder' ),
				'type'					=> 'yes_no_button',
				'option_category'		=> 'configuration',
				'options'				=> array(
					'off'		=> esc_html__( 'No', 'tm_builder' ),
					'on'		=> esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'				=> 'advanced',
			),
			'counter_item_view' => array(
				'label'					=> esc_html__( 'Multi Row slides layout', 'tm_builder' ),
				'option_category'		=> 'configuration',
				'type'					=> 'range',
				'default'				=> '3',
				'tab_slug'				=> 'advanced',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '6',
					'step' => '1',
				),
			),

			/*  Custom CSS  */
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
	}

	public function get_carousel_slides(){
		$terms_type		= $this->_var( 'terms_type' );
		$post_taxonomy	= $this->_var( $terms_type );

	/*	if ( ! $post_taxonomy ) {
			return '';
		}*/

		$posts_per_page = ( $this->_var('posts_per_page') === '0' ) ? -1 : $this->_var('posts_per_page') ;
		$post_args = array(
			'post_type'			=> 'post',
			'offset'			=> $this->_var('post_offset'),
			'posts_per_page'	=> $posts_per_page,
		);

		$post_args[ $terms_type ] = ( 'include' === $terms_type ) ? $post_taxonomy : $this->get_terms_slug( $terms_type, $post_taxonomy );
		$posts = get_posts( $post_args );

		if ( empty( $posts ) ) {
			return '';
		}

		global $post;

		$slides = '';
		$title_length = $this->_var( 'title_length' );
		$excerpt_length = $this->_var( 'excerpt_length' );
		$more_button_visible = 'on' === $this->_var( 'more' ) ? true : false ;
		$image_visible = 'on' === $this->_var( 'image' ) ? true : false ;
		$meta_date = explode( '|', $this->_var( 'meta_date' ) );
		$date_visible = isset( $meta_date[0] ) && $meta_date[0] && 'on' === $meta_date[0] ? true : false ;
		$author_visible = isset( $meta_date[1] ) && $meta_date[1] && 'on' === $meta_date[1] ? true : false ;
		$comment_count_visible = isset( $meta_date[2] ) && $meta_date[02] && 'on' === $meta_date[2] ? true : false ;
		$category_visible = isset( $meta_date[3] ) && $meta_date[3] && 'on' === $meta_date[3] ? true : false ;
		$post_tag_visible = isset( $meta_date[4] ) && $meta_date[4] && 'on' === $meta_date[4] ? true : false ;

		foreach ( $posts as $post ) {
			setup_postdata( $post );

			$image = tm_builder_core()->utility()->media->get_image(
				apply_filters( 'tm_pb_module_carousel_img_settings',
					array(
						'visible'		=> $image_visible,
						'size'			=> $this->_var( 'image_size' ),
						'mobile_size'	=> '__tm-thumb-s',
						'class'			=> 'post-thumbnail__link',
						'html'			=> '<div class="post-thumbnail"><a href="%1$s" %2$s><img class="post-thumbnail__img" src="%3$s" alt="%4$s" %5$s></a></div>',
					)
				)
			);
			$this->_var( 'image' , $image );

			$title_visible = ( '0' === $title_length ) ? false : true ;
			$post_title = tm_builder_core()->utility()->attributes->get_title(
				apply_filters( 'tm_pb_module_carousel_title_settings',
					array(
						'visible'		=> $title_visible,
						'length'		=> $title_length,
						'html'			=> '<h6 %1$s><a href="%2$s" %3$s>%4$s</a></h6>',
					)
				)
			);
			$this->_var( 'post_title' , $post_title );

			$excerpt_visible = ( '0' === $excerpt_length ) ? false : true ;
			$excerpt = tm_builder_core()->utility()->attributes->get_content(
				apply_filters( 'tm_pb_module_carousel_content_settings',
					array(
						'visible'		=> $excerpt_visible,
						'length'		=> $excerpt_length,
						'content_type'	=> 'post_excerpt',
					)
				)
			);
			$this->_var( 'excerpt' , $excerpt );

			$permalink = tm_builder_core()->utility()->attributes->get_post_permalink();
			$this->_var( 'permalink' , $permalink );

			$date = tm_builder_core()->utility()->meta_data->get_date(
				apply_filters( 'tm_pb_module_carousel_date_settings',
					array(
						'visible'		=> $date_visible,
						'html'			=> '<div class="post-meta post-date">%1$s<a href="%2$s" %3$s %4$s><time datetime="%5$s">%6$s%7$s</time></a></div>',
						'class'			=> 'post__date-link',
					)
				)
			);
			$this->_var( 'date' , $date );

			$count = tm_builder_core()->utility()->meta_data->get_comment_count(
				apply_filters( 'tm_pb_module_carousel_comment_count_settings',
					array(
						'visible'		=> $comment_count_visible,
						'html'			=> '<div class="post-meta post-comments">%1$s<a href="%2$s" %3$s %4$s>%5$s%6$s</a></div>',
						'class'			=> 'post__comments-link',
						'sufix'			=> _n_noop( '%s comment', '%s comments', '__tm' ),
					)
				)
			);
			$this->_var( 'count' , $count );

			$author = tm_builder_core()->utility()->meta_data->get_author(
				apply_filters( 'tm_pb_module_carousel_author_settings',
					array(
						'visible'		=> $author_visible,
						'prefix'		=> esc_html__( 'Posted by ', 'tm_builder' ),
						'html'			=> '<div class="post-meta posted-by">%1$s<a href="%2$s" %3$s %4$s rel="author">%5$s%6$s</a></div>',
						'class'			=> 'posted-by__author',
					)
				)
			);
			$this->_var( 'author' , $author );

			$category = tm_builder_core()->utility()->meta_data->get_terms(
				apply_filters( 'tm_pb_module_carousel_category_settings',
					array(
						'delimiter'		=> ', ',
						'type'			=> 'category',
						'visible'		=> $category_visible,
						'before'		=> '<div class="post-meta post__cats">',
						'after'			=> '</div>',
					)
				)
			);
			$this->_var( 'category' , $category );

			$tag = tm_builder_core()->utility()->meta_data->get_terms(
				apply_filters( 'tm_pb_module_carousel_tag_settings',
					array(
						'delimiter'		=> ', ',
						'type'			=> 'post_tag',
						'visible'		=> $post_tag_visible,
						'before'		=> '<div class="post-meta post__tags">',
						'after'			=> '</div>',
					)
				)
			);
			$this->_var( 'tag' , $tag );

			$more_button = tm_builder_core()->utility()->attributes->get_button(
				apply_filters( 'tm_pb_module_carousel_more_button_settings',
					array(
						'visible'		=> $more_button_visible,
						'text'			=> $this->_var( 'more_text' ),
						'vlass'			=> 'post-meta',
					)
				)
			);
			$this->_var( 'more_button' , $more_button );

			$slides .= $this->get_template_part( 'carousel/carousel-slide.php' );
		}

		wp_reset_postdata();
		wp_reset_query();

		return $slides;
	}

	private function get_terms_slug( $terms_type, $post_taxonomy ) {
		$term_args = array( 'include' => $post_taxonomy );

		switch ($terms_type) {
			case 'category_name':
				$term_args['taxonomy'] = 'category';
			break;

			case 'tag':
				$term_args['taxonomy'] = 'post_tag';
			break;

			default:
				$term_args['taxonomy'] = 'post_format';
			break;
		}

		$terms = get_terms( $term_args );
		$terms_count = count( $terms );
		$terms_slugs = '';

		foreach ($terms as $key => $value) {
			$terms_slugs .= $value->slug . ',';
		}

		return $terms_slugs;
	}

	public function shortcode_callback( $atts, $content = null, $function_name ) {
		$this->set_vars( $this->settings );
		$data_settings = json_encode(
			array(
				'height'			=> $this->_var('height'),
				'autoplay'			=> $this->_var('autoplay'),
				'navigateButton'	=> $this->_var('navigate_button'),
				'pagination'		=> $this->_var('pagination'),
				'slidesPerView'		=> $this->_var('counter_item_view'),
				'centeredSlides'	=> $this->_var('first_item_center'),
				'spaceBetweenSlides'=> apply_filters( 'tm_pb_module_carousel_space', 10 ),
			)
		);

		$args = array( 'data-settings' => htmlentities( $data_settings ) );

		$delimiter = ( 'on' === $this->_var( 'title_delimiter' ) ) ? '<span class="title-delimiter"></span>' : '' ;
		$this->_var( 'delimiter' , $delimiter  );

		$button_show_all_text = $this->_var( 'button_show_all_text' );
		$button_show_all_link = $this->_var( 'button_show_all_link' );
		$show_all = ( 'on' === $this->_var( 'button_show_all' ) && $button_show_all_text && $button_show_all_link ) ? '<a href="' . tm_builder_tools()->render_url( $button_show_all_link ) . '" class="btn"><span class="btn__text">' . $button_show_all_text . '</span></a>' : '' ;
		$this->_var( 'show_all' , $show_all );

		$slides = $this->get_carousel_slides();

		$this->_var( 'slides' , $slides );

		$content = $this->get_template_part( 'carousel/carousel.php' );
		$classes = array();
		$output  = $this->wrap_module( $content, $classes, $function_name, $args );

		return $output;
	}

	public function enqueue_assets() {
		wp_enqueue_style( 'tm-builder-swiper' );
		wp_enqueue_script( 'tm-builder-swiper' );
	}

}
new Tm_Builder_Module_Carousel;
