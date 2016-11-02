<?php
/*
 * Plugin Name: Power Builder
 * Plugin URI:
 * Description: A drag and drop page builder for any WordPress theme.
 * Version: 1.3.0
 * Author: TemplateMonster
 * Author URI: http://templatemonster.com/
 * License: GPLv2 or later
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

defined( 'TM_BUILDER_VERSION' ) or define( 'TM_BUILDER_VERSION', '1.3.0' );
defined( 'TM_BUILDER_DIR' ) or define( 'TM_BUILDER_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
defined( 'TM_BUILDER_URI' ) or define( 'TM_BUILDER_URI', plugins_url( '', __FILE__ ) );

/**
 * Tm_Builder_Plugin class
 *
 * @package Tm_Builder
 */
class Tm_Builder_Plugin {

	/**
	 * If instance created, this flag is true.
	 *
	 * @var boolean Prevents from multiple instances of `Tm_Builder_Plugin`.
	 */
	private static $_instantiated;

	/**
	 * Shared options
	 * @var stdClass Plugin private options
	 */
	public $options;

	/**
	 * Tm_Builder_Plugin constructor
	 */
	public function __construct() {

		// Allow only one instance of the class
		if ( true === self::$_instantiated ) {
			wp_die( sprintf(
				esc_html__( '%s is a singleton class and you cannot create a second instance.', 'tm_builder' ),
				get_class( $this )
			) );
		} else {
			self::$_instantiated = true;
		}

		/* @TODO Remove this */
		if ( ( defined( 'TM_BUILDER_THEME' ) && TM_BUILDER_THEME ) || function_exists( 'tm_divi_fonts_url' ) ) {
			return; // Disable the plugin, if the theme comes with the Builder
		}

		defined( 'TM_BUILDER_ACTIVE' ) or define( 'TM_BUILDER_ACTIVE', true );
		defined( 'TM_BUILDER_LAYOUT_POST_TYPE' ) or define( 'TM_BUILDER_LAYOUT_POST_TYPE', 'tm_pb_layout' );

		load_theme_textdomain( 'tm_builder', TM_BUILDER_DIR . '/framework/languages/' );
		load_plugin_textdomain( 'tm_builder_plugin', false, TM_BUILDER_DIR . '/languages/' );

		require TM_BUILDER_DIR . '/functions.php';
		require TM_BUILDER_DIR . '/framework/framework.php';

		$this->options = new stdClass();

		$this->options->pagename = 'tm_builder_options';
		$this->options->plugin_class_name = 'tm_builder';
		$this->options->save_button_text = esc_html__( 'Save', 'tm_builder' );
		$this->options->top_level_page = 'toplevel_page';
		$this->options->protocol = $this->getProtocol();
		$this->options->config = get_option( 'tm_builder_options' ) || array();

		add_action( 'admin_menu', array( $this, 'admin_menu' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'wp_ajax_tm_builder_save_settings', array( $this, 'builder_save_settings' ) );

		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'the_content', array( $this, 'add_builder_content_wrapper' ) );

		add_filter( 'tm_builder_inner_content_class', array( $this, 'add_builder_inner_content_class' ) );
	}

	/**
	 * Get protocol string
	 * @return string `http` or `https` string
	 */
	public static function getProtocol() {
		return is_ssl() ? 'https' : 'http';
	}

	/**
	 * If builder used, wrap the post content and return it.
	 *
	 * @param string $content Post content.
	 * @return string If builder used, returns wrapped post content, otherwise default post content.
	 */
	public function add_builder_content_wrapper( $content ) {

		// No builder used in the post, return default content
		if ( ! tm_pb_is_pagebuilder_used( get_the_ID() ) ) {
			return $content;
		}

		// Builder layout should only be used in singular template
		if ( ! is_singular() ) {

			// get_the_excerpt() for excerpt retrieval causes infinite loop; thus we're using excerpt from global $post variable
			global $post;

			$read_more = sprintf(
				' <a href="%1$s" title="%2$s" class="more-link">%3$s</a>',
				esc_url( get_permalink() ),
				sprintf( esc_attr__( 'Read more on %1$s', 'tm_builder' ), esc_html( get_the_title() ) ),
				esc_html__( 'read more', 'tm_builder' )
			);

			// Use post excerpt if there's any. If there is no excerpt defined,
			// Generate from post content by stripping all shortcode first
			if ( ! empty( $post->post_excerpt ) ) {
				return wpautop( $post->post_excerpt . $read_more );
			} else {
				$shortcodeless_content = preg_replace( '/\[[^\]]+\]/', '', $content );
				return wpautop( tm_wp_trim_words( $shortcodeless_content, 270, $read_more ) );
			}
		}

		$outer_classes = implode( ' ', apply_filters( 'tm_builder_outer_content_class', array(
			'tm_builder_outer_content'
		) ) );

		$inner_classes = implode( ' ', apply_filters( 'tm_builder_inner_content_class', array(
			'tm_builder_inner_content'
		) ) );

		return sprintf(
			'<div class="%1$s" id="%2$s">
				<div class="%3$s">
					%4$s
				</div>
			</div>',
			esc_attr( $outer_classes ),
			esc_attr( apply_filters( 'tm_builder_outer_content_id', 'tm_builder_outer_content' ) ),
			esc_attr( $inner_classes ),
			$content
		);

		return $content;
	}

	/**
	 * Modify `<body>` classes
	 * @param  array $classes Body classes array.
	 * @return array
	 */
	public function body_class( $classes ) {
		$classes[] = 'tm_pb_builder';

		return $classes;
	}

	/**
	 * Modify inner content classes
	 * @param array $classes Inner content classes
	 */
	public function add_builder_inner_content_class( $classes ) {
		$classes[] = 'tm_pb_gutters3';

		return $classes;
	}

	/**
	 * `admin_init` hook
	 */
	public function admin_init() {
		require_once( TM_BUILDER_DIR . '/dashboard/includes/options.php' );

		if ( isset( $all_sections ) ) {
			$this->options->sections = $all_sections;
		}

		if ( isset( $assigned_options ) ) {
			$this->options->assigned = $assigned_options;
		}

		add_action( 'plugins_loaded', array( $this, 'add_class_localization' ) );
		add_action( 'wp_ajax_tm_dashboard_generate_warning', array( $this, 'generate_modal_warning' ) );
		add_action( 'wp_ajax_tm_dashboard_execute_live_search', array( $this, 'execute_live_search' ) );

		add_action( 'admin_init', array( $this, 'set_post_types' ), 99 );
		add_action( 'admin_init', array( $this, 'process_settings_export' ) );
		add_action( 'admin_init', array( $this, 'process_settings_import' ) );
	}

	/**
	 * Generates modal warning window for internal messages. Works via php or via Ajax
	 * Ok_link could be a link to particular tab in dashboard, external link or empty
	 *
	 * @param string $message Modal message text.
	 * @param string $ok_link Ok button link.
	 * @param boolean $hide_close Enable/disable modal close button.
	 * @param string $ok_text Ok button text.
	 * @param string $custom_button_text If not empty, will add a custom button with a text.
	 * @param string $custom_button_link If `$custom_button_text` not empty. Custom button link.
	 * @param string $custom_button_class If `$custom_button_text` not empty. Custom button CSS class/classes.
	 *
	 * @return string|void If it's not `$ajax_request`, will return the HTML, otherwise, will print the generated HTML.
	 */
	public function generate_modal_warning( $message = '', $ok_link = '#', $hide_close = false, $ok_text = '', $custom_button_text = '', $custom_button_link = '#', $custom_button_class = '' ) {
		$ajax_request = isset( $_POST[ 'message' ] ) ? true : false;

		if ( true === $ajax_request ){
			if ( ! wp_verify_nonce( $_POST['generate_warning_nonce'] , 'generate_warning' ) ) {
				die( -1 );
			}
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$message = isset( $_POST[ 'message' ] ) ? stripslashes( $_POST[ 'message' ] ) : sanitize_text_field( $message );
		$ok_link = isset( $_POST[ 'ok_link' ] ) ? $_POST[ 'ok_link' ] : $ok_link;
		$hide_close = isset( $_POST[ 'hide_close' ] ) ? (bool) $_POST[ 'hide_close' ] : (bool) $hide_close;
		$ok_text = isset( $_POST[ 'ok_text' ] ) ? $_POST[ 'ok_text' ] : $ok_text;
		$custom_button_text = isset( $_POST[ 'custom_button_text' ] ) ? $_POST[ 'custom_button_text' ] : $custom_button_text;
		$custom_button_link = isset( $_POST[ 'custom_button_link' ] ) ? $_POST[ 'custom_button_link' ] : $custom_button_link;
		$custom_button_class = isset( $_POST[ 'custom_button_class' ] ) ? $_POST[ 'custom_button_class' ] : $custom_button_class;

		$result = sprintf(
			'<div class="tm_dashboard_networks_modal tm_dashboard_warning">
				<div class="tm_dashboard_inner_container">
					<div class="tm_dashboard_modal_header">%4$s</div>
					<div class="dashboard_icons_container">
						%1$s
					</div>
					<div class="tm_dashboard_modal_footer"><a href="%3$s" class="tm_dashboard_ok tm_dashboard_warning_button%6$s">%2$s</a>%5$s</div>
				</div>
			</div>',
			wp_kses_post( $message ),
			'' == $ok_text ? esc_html__( 'Ok', 'tm_dashboard' ) : $ok_text,
			esc_url( $ok_link ),
			false === $hide_close ? '<span class="tm_dashboard_close"></span>' : '',
			'' != $custom_button_text ?
				sprintf(
					'<a href="%1$s" class="tm_dashboard_custom_btn tm_dashboard_warning_button%3$s">%2$s</a>',
					esc_url( $custom_button_link ),
					esc_html( $custom_button_text ),
					'' !== $custom_button_class
						? ' ' . esc_attr( $custom_button_class )
						: ''
				)
				: '',
			'' !== $custom_button_text ? ' tm_dashboard_2_btns' : ''
		);

		if ( $ajax_request ){
			echo $result;
			die;
		} else {
			return $result;
		}
	}

	/**
	 * Load Google fonts class
	 *
	 * @return TM_Dashboard_Fonts
	 */
	public static function load_fonts_class() {
		if ( ! class_exists( 'TM_Dashboard_Fonts' ) ) {
			require_once( TM_BUILDER_DIR . '/dashboard/includes/google-fonts.php' );
		}

		return new Tm_Dashboard_Fonts();
	}

	/**
	 * Handles ajax request for save_settings button
	 * @return string
	 */
	public function builder_save_settings() {
		if ( ! wp_verify_nonce( $_POST['save_settings_nonce'], 'save_settings' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$options = $_POST['options'];
		$option_sub_title = isset( $_POST['options_sub_title'] ) ? $_POST['options_sub_title'] : '';
		$error_message = $this->process_and_update_options( $options, $option_sub_title );
		die( $error_message );
	}

	/**
	 * Get builder options
	 * @return array
	 */
	public function get_builder_options() {
		return $this->options->config;
	}

	/**
	 * Add menu items into admin menu
	 */
	public function admin_menu() {

		if ( ! empty( $_GET['post_type'] ) && TM_BUILDER_LAYOUT_POST_TYPE == $_GET['post_type'] ){
			$icon = TM_BUILDER_URI . '/assets/images/power-logo-tiny.png';
		} else {
			$icon = TM_BUILDER_URI . '/assets/images/power-logo-tiny-active.png';
		}

		add_menu_page(
			esc_html__( 'Power', 'tm_builder' ),
			esc_html__( 'Power', 'tm_builder' ),
			'manage_options',
			sprintf( 'edit.php?post_type=%s', TM_BUILDER_LAYOUT_POST_TYPE ),
			'',
			$icon
		);
	}

	/**
	 * Enqueue scripts for all admin pages
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		wp_enqueue_style( 'tm-builder-css', TM_BUILDER_URI . '/assets/css/admin.css', array(), TM_BUILDER_VERSION );
		wp_enqueue_script( 'tm-builder-js', TM_BUILDER_URI . '/assets/js/admin.js', array( 'jquery' ), TM_BUILDER_VERSION, true );

		wp_localize_script( 'tm-builder-js', 'builder_settings', array(
			'tm_builder_nonce'           => wp_create_nonce( 'tm_builder_nonce' ),
			'ajaxurl'                    => admin_url( 'admin-ajax.php', $this->getProtocol() ),
			'authorize_text'             => esc_html__( 'Authorize', 'tm_builder_plugin' ),
			'reauthorize_text'           => esc_html__( 'Re-Authorize', 'tm_builder_plugin' ),
			'save_settings'              => wp_create_nonce( 'save_settings' ),
		) );

		//wp_enqueue_script( 'tm-dashboard-mce-js', TM_BUILDER_DIR . '/dashboard/js/tinymce/js/tinymce/tinymce.min.js', array( 'jquery' ), TM_BUILDER_VERSION, true );
		//wp_enqueue_style( 'tm-dashboard-css', TM_BUILDER_DIR . '/dashboard/css/tm-dashboard.css', array(), TM_BUILDER_VERSION );
		//wp_enqueue_script( 'tm-dashboard-js', TM_BUILDER_DIR . '/dashboard/js/tm-dashboard.js', array( 'jquery' ), TM_BUILDER_VERSION, true );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();

		wp_localize_script( 'tm-dashboard-js', 'dashboardSettings', array(
			'dashboard_nonce'  => wp_create_nonce( 'dashboard_nonce' ),
			'search_nonce'     => wp_create_nonce( 'search_nonce' ),
			'ajaxurl'          => admin_url( 'admin-ajax.php', $this->options->protocol ),
			'save_settings'    => wp_create_nonce( 'save_settings' ),
			'generate_warning' => wp_create_nonce( 'generate_warning' ),
			'plugin_class'     => $this->options->plugin_class_name,
		) );
	}

	/**
	 * Generates the array of post types and categories registered in WordPress
	 * @return void
	 */
	public function set_post_types() {
		$default_post_types = array( 'post', 'page' );
		$theme_name = wp_get_theme();
		$final_categories = array();

		$custom_post_types = get_post_types( array(
			'public'   => true,
			'_builtin' => false,
		) );

		if ( ( $key = array_search( 'wysijap', $custom_post_types ) ) !== false) {
			unset( $custom_post_types[$key] );
		}

		$this->dashboard_post_types = array_merge( $default_post_types, $custom_post_types );

		$categories = get_categories( array(
			'hide_empty' => 0,
		) );

		foreach ( $categories as $key => $value ) {
			$final_categories[$value->term_id] = $value->name;
		}

		$this->dashboard_categories['post'] = $final_categories;

		foreach ( $this->dashboard_post_types as $post_type ) {
			$taxonomy_name = '';
			$cats_array = array();

			switch ( $post_type ) {

				case 'project' :
					$taxonomy_name = 'project_category';

					break;

				case 'product' :
					$taxonomy_name = 'product_cat';

					break;

				case 'listing' :
					if ( 'Explorable' === $theme_name ) {
						$taxonomy_name = 'listing_type';
					} else {
						$taxonomy_name = 'listing_category';
					}

					break;

				case 'event' :
						$taxonomy_name = 'event_category';

					break;

				case 'gallery' :
					$taxonomy_name = 'gallery_category';

					break;

			}

			if ( '' !== $taxonomy_name && taxonomy_exists( $taxonomy_name ) ) {
				$cats_array = get_categories( 'taxonomy=' . $taxonomy_name . '&hide_empty=0' );
				if ( ! empty( $cats_array ) ) {
					$cats_array_final = array();

					foreach( $cats_array as $single_cat ) {
						$cats_array_final[$single_cat->cat_ID] = $single_cat->cat_name;
					}

					$this->dashboard_categories[$post_type] = $cats_array_final;
				}
			}
		}
	}

	/**
	 * Generates the output for the hint in dashboard options
	 *
	 * @param string $text Hint text.
	 * @param boolean $escape Escape the text or leave as it is?
	 *
	 * @return string
	 */
	public function generate_hint( $text, $escape ) {
		$output = sprintf(
			'<span class="tm_dashboard_more_info tm_dashboard_icon">
				<span class="tm_dashboard_more_text">%1$s</span>
			</span>',
			true === $escape ? esc_html( $text ) : $text
		);

		return $output;
	}

	/**
	 * Handles ajax request for save_settings button
	 *
	 * @param array $options Options.
	 *
	 * @return string
	 */
	public function dashboard_save_settings( $options = array() ) {
		if ( ! wp_verify_nonce( $_POST['save_settings_nonce'], 'save_settings' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$options = $_POST['options'];
		$option_sub_title = isset( $_POST['options_sub_title'] ) ? $_POST['options_sub_title'] : '';
		$error_message = $this->process_and_update_options( $options, $option_sub_title );
		die( $error_message );
	}

	/**
	 * Handles options array and import options into DataBase.
	 * $sub_array variable toggles between 2 option formats:
	 *	1) false -  [option_1, option_2, ... , option_n]
	 * 	2) true -  key_1[option_1, option_2, ... , option_n], key_2[option_1, option_2, ... , option_n], ... , key_n[option_1, option_2, ... , option_n]
	 *
	 *	@return string
	 */
	public function prepare_import_settings( $options = array(), $sub_array = false ) {
		//if options stored in sub_arrays, then we need to go through each sub_array and save the data for each of them
		if ( true === $sub_array ) {
			foreach ( $options as $subtitle => $values ) {
				$error_message = $this->process_and_update_options( $values, $subtitle );
			}
		} else {
			 $error_message = $this->process_and_update_options( $options );
		}

		return $error_message;
	}

	/**
	 *
	 * supposed to check whether network is authorized or not
	 * verdict should be overriden from plugin using 'tm_<plugin_name>_authorization_verdict' filter
	 * FALSE will be returned by default
	 *
	 * @return bool
	 */
	public function api_is_network_authorized( $network ) {
		$is_authorized = apply_filters( 'tm_builder_authorization_verdict', false, $network );

		return (bool) $is_authorized;
	}

	/**
	 *
	 * Executes live search through the posts/pages and returns the output to jQuery
	 *
	 * @return string
	 */
	public function execute_live_search() {
		if ( ! wp_verify_nonce( $_POST['dashboard_search'] , 'search_nonce' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$search_string = ! empty( $_POST['dashboard_live_search'] ) ? sanitize_text_field( $_POST['dashboard_live_search'] ) : '';
		$page          = ! empty( $_POST['dashboard_page'] ) ? sanitize_text_field( $_POST['dashboard_page'] ) : 1;
		$post_type     = ! empty( $_POST['dashboard_post_type'] ) ? sanitize_text_field( $_POST['dashboard_post_type'] ) : 'any';
		$full_content  = ! empty( $_POST['dashboard_full_content'] ) ? sanitize_text_field( $_POST['dashboard_full_content'] ) : 'true';

		$args['s']       = $search_string;
		$args['pagenum'] = $page;

		$results = $this->posts_query( $args, $post_type );
		if ( 'true' === $full_content ) {
			$output = '<ul class="tm_dashboard_search_results">';
		} else {
			$output = '';
		}

		if ( empty( $results ) ) {
			if ( 'true' === $full_content ) {
				$output .= sprintf(
					'<li class="tm_dashboard_no_res">%1$s</li>',
					esc_html__( 'No results found', 'bloom' )
				);
			}
		} else {
			foreach( $results as $single_post ) {
				$output .= sprintf(
					'<li data-post_id="%2$s">[%3$s] - %1$s</li>',
					esc_html( $single_post['title'] ),
					esc_attr( $single_post['id'] ),
					esc_html( $single_post['post_type'] )
				);
			}
		}

		if ( 'true' === $full_content ) {
			$output .= '</ul>';
		}

		die( $output );
	}

	/**
	 *
	 * Retrieves the posts from WP based on search criteria. Used for live posts search.
	 * This function is based on the internal WP function "wp_link_query" from /wp-includes/class-wp-editor.php
	 *
	 * @return array
	 */
	public function posts_query( $args = array(), $include_post_type = '' ) {
		if ( 'only_pages' === $include_post_type ) {
			$pt_names = array( 'page' );
		} elseif ( 'any' === $include_post_type || 'only_posts' === $include_post_type ) {
			$dashboard_post_types = ! empty( $this->dashboard_post_types ) ? $this->dashboard_post_types : array();
			$pt_names = array_values( $dashboard_post_types );

			if ( 'only_posts' === $include_post_type ) {
				unset( $pt_names[1] );
			}
		} else {
			$pt_names = $include_post_type;
		}

		$query = array(
			'post_type'              => $pt_names,
			'suppress_filters'       => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status'            => 'publish',
			'posts_per_page'         => 20,
		);

		$args['pagenum'] = isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;

		if ( isset( $args['s'] ) ) {
			$query['s'] = $args['s'];
		}

		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

		$get_posts = new WP_Query;
		$posts = $get_posts->query( $query );
		if ( ! $get_posts->post_count ) {
			return false;
		}

		$results = array();
		foreach ( $posts as $post ) {
			$results[] = array(
				'id'        => (int) $post->ID,
				'title'     => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'post_type' => sanitize_text_field( $post->post_type ),
			);
		}

		wp_reset_postdata();

		return $results;
	}

	/**
	 * Processes and saves options array into Database
	 * $option_sub_title variable toggles between 2 option formats:
	 *	1) '' -  [option_1, option_2, ... , option_n]
	 * 	2) '<subtitle>' -  <subtitle>[option_1, option_2, ... , option_n]
	 *
	 * Supports 'tm_<plugin_name>_after_save_options' hook
	 *
	 * @return string
	 */
	function process_and_update_options( $options, $option_sub_title = '' ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		$this->dashboard_options = $this->get_options_array();
		$dashboard_options = $this->dashboard_options;
		$dashboard_sections = $this->dashboard_sections;
		$dashboard_options_assigned = $this->assigned_options;

		$error_message = '';
		$dashboard_options_temp = array();
		if ( ! is_array( $options ) ) {
			$processed_array = str_replace( array( '%5B', '%5D' ), array( '[', ']' ), $options );
			parse_str( $processed_array, $output );
			$array_prefix = true;
		} else {
			$output = $options;
			$array_prefix = false;
		}

		if ( isset( $dashboard_sections ) ) {
			foreach ( $dashboard_sections as $key => $value ) {
				$current_section = sanitize_text_field( $key );
				if ( isset( $value[ 'contents' ] ) ) {
					foreach( $value[ 'contents' ] as $key => $value ) {
						$options_prefix = sanitize_text_field( $current_section . '_' . $key );
						$options_array = $dashboard_options_assigned[$current_section . '_' . $key . '_options'];
						if ( isset( $options_array ) ) {
							foreach( $options_array as $option ) {
								$current_option_name = '';

								if ( isset( $option[ 'name' ] ) ) {
									if ( '' !== $option_sub_title ) {
										$current_option_name = $option[ 'name' ];
									} else {
										$current_option_name = $options_prefix . '_' . $option[ 'name' ];
									}
								}

								$current_option_name = sanitize_text_field( $current_option_name );

								//determine where the value is stored and set appropriate value as current
								if ( true === $array_prefix ) {
									$current_option_value = isset( $output['tm_dashboard'][ $current_option_name ] ) ? $output['tm_dashboard'][ $current_option_name ] : false;
								} else {
									$current_option_value = isset( $output[ $current_option_name ] ) ? $output[ $current_option_name ] : false;
								}
								if ( isset( $option[ 'validation_type' ] ) ) {
									switch( $option[ 'validation_type' ] ) {
										case 'simple_array' :
											$dashboard_options_temp[ $current_option_name ] = ! empty( $current_option_value )
												? array_map( 'sanitize_text_field', $current_option_value )
												: array();
										break;

										case 'simple_text':
											$dashboard_options_temp[ $current_option_name ] = ! empty( $current_option_value )
												? sanitize_text_field( stripslashes( $current_option_value ) )
												: '';

												if ( function_exists ( 'icl_register_string' ) && isset( $option[ 'is_wpml_string' ] ) ) {
													$wpml_option_name = '' !== $option_sub_title
														? $current_option_name . '_' . $option_sub_title
														: $option_sub_title;
													icl_register_string( 'tm_builder', $wpml_option_name, sanitize_text_field( $current_option_value ) );
												}
										break;

										case 'boolean' :
											$dashboard_options_temp[ $current_option_name ] = ! empty( $current_option_value )
												? in_array( $current_option_value, array( '1', false ) )
													? sanitize_text_field( $current_option_value )
													: false
												: false;
										break;

										case 'number' :
											$dashboard_options_temp[ $current_option_name ] = intval( stripslashes( ! empty( $current_option_value )
													? absint( $current_option_value )
													: ''
											) );
										break;

										case 'complex_array' :
											if ( isset( $current_option_name ) && '' != $current_option_name ) {
												if ( ! empty( $current_option_value ) && is_array( $current_option_value ) ) {
													foreach ( $current_option_value as $key => $value ) {
														foreach ( $value as $_key => $_value ) {
															$value[ $_key ] = sanitize_text_field( $_value );
														}

														$current_option_value[ $key ] = $value;
													}

													$dashboard_options_temp[ $current_option_name ] = $current_option_value;
												}
											}
										break;

										case 'url' :
											if ( isset( $current_option_name ) && '' != $current_option_name ) {
												$dashboard_options_temp[ $current_option_name ] = ! empty( $current_option_value )
													? esc_url_raw( stripslashes( $current_option_value ) )
													: '';
											}
										break;

										case 'html' :
											if ( isset( $current_option_name ) && '' != $current_option_name ) {
												$dashboard_options_temp[ $current_option_name ] = ! empty( $current_option_value )
													? stripslashes( esc_html( $current_option_value ) )
													: '';

												if ( function_exists ( 'icl_register_string' ) && isset( $option[ 'is_wpml_string' ] ) ) {
													$wpml_option_name = '' !== $option_sub_title
														? $current_option_name . '_' . $option_sub_title
														: $option_sub_title;
													icl_register_string( 'tm_builder', $wpml_option_name, esc_html( $current_option_value ) );
												}
											}
										break;
									} // end switch
								}

								do_action( 'tm_builder_after_save_options', $dashboard_options_temp, $current_option_name, $option, $output );
							} // end foreach( $options_array as $option )
						} //if ( isset( $options_array ) )
					} // end foreach( $value[ 'contents' ] as $key => $value )
				} // end if ( isset( $value[ 'contents' ] ) )
			} // end foreach ( $dashboard_sections as $key => $value )
		} //end if ( isset( $dashboard_sections ) )

		if ( '' !== $option_sub_title ) {
			$final_array[$option_sub_title] = $dashboard_options_temp;
		} else {
			$final_array = $dashboard_options_temp;
		}
		self::update_option( $final_array );

		if ( ! empty( $final_array[ 'sharing_locations_manage_locations' ] ) && empty( $final_array[ 'sharing_networks_networks_sorting' ] ) ) {
			$error_message = $this->generate_modal_warning( esc_html__( 'Please select social networks in "Social Sharing / Networks" settings', 'tm_dashboard' ), '#tab_tm_social_tab_content_sharing_networks' );
		}

		return $error_message;
	}

	/**
	 * Removes unneeded options from the export file. Array of options can be modified using 'tm_<plugin_name>_export_exclude' filter.
	 * @return array
	 */
	public function remove_site_specific_fields( $settings ) {
		$remove_options = apply_filters( 'tm_builder_export_exclude', array(
			'access_tokens',
			'db_version',
		) );

		foreach ( $remove_options as $option ) {
			if ( isset( $settings[$option] ) ) {
				unset( $settings[$option] );
			}
		}

		return $settings;
	}

	/**
	 * Process settings export
	 *
	 * @return void
	 */
	public function process_settings_export() {
		if( empty( $_POST[ 'tm_dashboard_action' ] ) || 'export_settings' !== $_POST[ 'tm_dashboard_action' ] ) {
			return;
		}

		if( ! wp_verify_nonce( $_POST[ 'tm_dashboard_export_nonce' ], 'tm_dashboard_export_nonce' ) ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$dashboard_options = $this->dashboard_options;

		ignore_user_abort( true );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=tm_builder-settings-export-' . date( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		echo json_encode( $this->remove_site_specific_fields( $dashboard_options ) );
		exit;
	}


	/**
	 * Processes .json file with settings and import settings into the database.
	 * Supports settings in 2 formats:
	 * 	1) [option_1, option_2, ... , option_n]
	 * 	2) key_1[option_1, option_2, ... , option_n], key_2[option_1, option_2, ... , option_n], ... , key_n[option_1, option_2, ... , option_n]
	 * Works with 1 format by default, format can be changed using 'tm_<plugin_name>_import_sub_array' filter. Set to TRUE to enable 2 format.
	 * Import array can be modified before importing data using 'tm_<plugin_name>_import_array' filter
	 */
	public function process_settings_import() {
		if( empty( $_POST[ 'tm_dashboard_action' ] ) || 'import_settings' !== $_POST[ 'tm_dashboard_action' ] ) {
			return;
		}

		if( ! wp_verify_nonce( $_POST[ 'tm_dashboard_import_nonce' ], 'tm_dashboard_import_nonce' ) ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$end_array = explode( '.', $_FILES[ 'import_file' ][ 'name' ] );
		$extension = end( $end_array );
		$import_file = $_FILES[ 'import_file' ][ 'tmp_name' ];

		if ( empty( $import_file ) ) {
			echo $this->generate_modal_warning( esc_html__( 'Please select .json file for import', 'tm_dashboard' ) );
			return;
		}

		if ( $extension !== 'json' ) {
			echo $this->generate_modal_warning( esc_html__( 'Please provide valid .json file', 'tm_dashboard' ) );
			return;
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$dashboard_settings = (array) json_decode( file_get_contents( $import_file ), true );
		$sub_array = apply_filters( 'tm_builder_import_sub_array', false );

		$error_message = $this->prepare_import_settings( apply_filters( 'tm_builder_import_array', $dashboard_settings ), $sub_array );

		if ( ! empty( $error_message ) ) {
			echo $this->generate_modal_warning( $error_message );
		} else {
			$options_page = 'toplevel_page' === $this->top_level_page ? 'admin' : $this->top_level_page;
			echo $this->generate_modal_warning( esc_html__( 'Options imported successfully.', 'tm_dashboard' ), admin_url( $options_page . '.php?page=' . $this->_options_pagename ), true );
		}
	}

	/**
	 * Update options
	 * @param  array $options Options array.
	 */
	public function update_option( $options ) {
		//we need to update current version of options, not cached version
		update_option( 'tm_builder_options', array_merge( $this->options->config, $options ) );
	}

	/**
	 * Removes option from the database based on the $option_key
	 *
	 * @param string|int $option_key A key of option that should be removed.
	 */
	function remove_option( $option_key ) {
		//we need to remove options from the current version of options, not cached version
		if ( isset( $this->options->config[ $option_key ] ) ) {
			unset( $this->options->config[ $option_key ] );
			update_option( 'tm_builder_options', $this->options->config );
		}
	}

}

if ( ! function_exists( 'tm_builder_init_plugin' ) ) {
	/**
	 * Initialize plugin hook
	 */
	function tm_builder_init_plugin() {
		new Tm_Builder_Plugin();
	}
}

if ( ! function_exists( 'tm_builder_init_core' ) ) {
	/**
	 * Initalize Cherry core
	 */
	function tm_builder_init_core() {
		require TM_BUILDER_DIR . 'tm-builder-core.php';
		add_action( 'after_setup_theme', require( TM_BUILDER_DIR . 'cherry-framework/setup.php' ), 0 );
	}

	tm_builder_init_core();
}

add_action( 'init', 'tm_builder_init_plugin' );
