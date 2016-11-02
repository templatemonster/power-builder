<?php

function tm_builder_load_global_functions_script() {
	wp_enqueue_script( 'tm-builder-modules-global-functions-script', TM_BUILDER_URI . '/framework/assets/js/frontend-builder-global-functions.js', array( 'jquery' ), TM_BUILDER_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'tm_builder_load_global_functions_script', 7 );

function tm_builder_load_modules_styles() {

	$frontend_scripts = array(
		'google-maps-api' => array(
			tm_get_maps_api_url(),
			array(),
		),
		'divi-fitvids' => array(
			TM_BUILDER_URI . '/framework/assets/js/libs/jquery.fitvids.js',
			array( 'jquery' ),
		),
		'waypoints' => array(
			TM_BUILDER_URI . '/framework/assets/js/libs/waypoints.min.js',
			array( 'jquery' ),
		),
		'magnific-popup' => array(
			TM_BUILDER_URI . '/framework/assets/js/libs/jquery.magnific-popup.js',
			array( 'jquery' ),
		),
		'tm-jquery-touch-mobile' => array(
			TM_BUILDER_URI . '/framework/assets/js/libs/jquery.mobile.custom.min.js',
			array( 'jquery', ),
		),
		'tm-builder-frontend-closest-descendent' => array(
			TM_BUILDER_URI . '/framework/assets/js/libs/jquery.closest-descendent.js',
			array( 'jquery', ),
		),
		'tm-builder-frontend-reverse' => array(
			TM_BUILDER_URI . '/framework/assets/js/libs/jquery.reverse.js',
			array( 'jquery', ),
		),
		'tm-builder-frontend-simple-carousel' => array(
			TM_BUILDER_URI . '/framework/assets/js/jquery.tm-pb-simple-carousel.js',
			array( 'jquery', ),
		),
		'tm-builder-frontend-simple-slider' => array(
			TM_BUILDER_URI . '/framework/assets/js/jquery.tm-pb-simple-slider.js',
			array( 'jquery', ),
		),
		'tm-builder-frontend-easy-pie-chart' => array(
			TM_BUILDER_URI . '/framework/assets/js/libs/jquery.easypiechart.js',
			array( 'jquery', ),
		),
		'tm-builder-frontend-tm-hash' => array(
			TM_BUILDER_URI . '/framework/assets/js/tm-hash.js',
			array( 'jquery', ),
		),
		'tm-builder-modules-script' => array(
			TM_BUILDER_URI . '/framework/assets/js/scripts.js',
			array(
				'jquery',
				'tm-jquery-touch-mobile',
				'tm-builder-frontend-closest-descendent',
				'tm-builder-frontend-reverse',
				'tm-builder-frontend-simple-carousel',
				'tm-builder-frontend-simple-slider',
				'tm-builder-frontend-tm-hash'
			),
		),
		'tm-builder-swiper' => array(
			TM_BUILDER_URI . '/framework/assets/js/swiper.jquery.min.js',
			array( 'jquery', ),
		),
	);

	wp_register_script( 'hashchange', TM_BUILDER_URI . '/framework/assets/js/libs/jquery.hashchange.js', array( 'jquery' ), TM_BUILDER_VERSION, true );
	wp_register_script( 'salvattore', TM_BUILDER_URI . '/framework/assets/js/libs/salvattore.min.js', array(), TM_BUILDER_VERSION, true );
	wp_register_script( 'easypiechart', TM_BUILDER_URI . '/framework/assets/js/libs/jquery.easypiechart.js', array( 'jquery' ), TM_BUILDER_VERSION, true );
	wp_register_script( 'tm-builder-swiper', TM_BUILDER_URI . '/framework/assets/js/swiper.jquery.min.js', array( 'jquery' ), TM_BUILDER_VERSION, true );

	if ( tm_is_builder_plugin_active() ) {
		wp_enqueue_style( 'tm-builder-swiper', TM_BUILDER_URI . '/framework/assets/css/swiper.min.css', array(), TM_BUILDER_VERSION );

		$frontend_scripts['fittext'] = array( TM_BUILDER_URI . '/framework/assets/js/libs/jquery.fittext.js', array( 'jquery' ) );
	}

	// Load main styles CSS file only if the Builder plugin is active
	if ( tm_is_builder_plugin_active() ) {
			$styles = apply_filters( 'tm_builder_front_styles',
				array(
					'tm-builder-modules-grid' => array(
						'src' => TM_BUILDER_URI . '/framework/assets/css/grid.css',
						'ver' => TM_BUILDER_VERSION,
					),
					'tm-builder-modules-style' => array(
						'src' => TM_BUILDER_URI . '/framework/assets/css/style.css',
						'ver' => TM_BUILDER_VERSION,
					),
					'magnific-popup' => array(
						'src' => TM_BUILDER_URI . '/framework/assets/css/magnific-popup.css',
						'ver' => TM_BUILDER_VERSION,
					),
					'font-awesome' => array(
						'src' => TM_BUILDER_URI . '/framework/assets/css/font-awesome.min.css',
						'ver' => '4.6.1',
					),
				)
			);

			foreach ( $styles as $handle => $data ) {
				$data = array_merge(
					array(
						'src'   => '',
						'deps'  => '',
						'ver'   => '',
						'media' => 'all',
					),
					$data
				);

				wp_enqueue_style( $handle, $data['src'], $data['deps'], $data['ver'], $data['media'] );
			}
	}


	foreach( $frontend_scripts as $handle => $opts ) {
		wp_enqueue_script(
			$handle,
			esc_url( $opts[0] ),
			$opts[1],
			TM_BUILDER_VERSION,
			true
		);
	}

	wp_localize_script( 'tm-builder-modules-script', 'tm_pb_custom', array(
		'ajaxurl'                => admin_url( 'admin-ajax.php' ),
		'images_uri'             => get_template_directory_uri() . '/images',
		'builder_images_uri'     => TM_BUILDER_URI . '/framework/assets/images',
		'tm_frontend_nonce'      => wp_create_nonce( 'tm_frontend_nonce' ),
		'subscription_failed'    => esc_html__( 'Please, check the fields below to make sure you entered the correct information.', 'tm_builder' ),
		'fill_message'           => esc_html__( 'Please, fill in the following fields:', 'tm_builder' ),
		'contact_error_message'  => esc_html__( 'Please, fix the following errors:', 'tm_builder' ),
		'invalid'                => esc_html__( 'Invalid email', 'tm_builder' ),
		'captcha'                => esc_html__( 'Captcha', 'tm_builder' ),
		'prev'                   => esc_html__( 'Prev', 'tm_builder' ),
		'previous'               => esc_html__( 'Previous', 'tm_builder' ),
		'next'                   => esc_html__( 'Next', 'tm_builder' ),
		'wrong_captcha'          => esc_html__( 'You entered the wrong number in captcha.', 'tm_builder' ),
		'is_builder_plugin_used' => tm_is_builder_plugin_active(),
		'is_divi_theme_used'     => function_exists( 'tm_divi_fonts_url' ),
		'widget_search_selector' => apply_filters( 'tm_pb_widget_search_selector', '.widget_search' ),
	) );

}
add_action( 'wp_enqueue_scripts', 'tm_builder_load_modules_styles', 11 );

if ( ! function_exists( 'tm_builder_add_main_elements' ) ) {
	function tm_builder_add_main_elements() {
		// Load base class
		require( TM_BUILDER_DIR . '/framework/includes/class-builder-structure-element.php' );

		// Load dynamic CSS manager
		require( TM_BUILDER_DIR . '/framework/includes/class-builder-dynamic-css-manager.php' );

		// Load icons gateway
		require( TM_BUILDER_DIR . '/framework/includes/class-builder-icons-gateway.php' );

		// Load structures
		require( TM_BUILDER_DIR . '/framework/includes/structure/class-builder-section.php' );
		require( TM_BUILDER_DIR . '/framework/includes/structure/class-builder-row.php' );
		require( TM_BUILDER_DIR . '/framework/includes/structure/class-builder-row-inner.php' );
		require( TM_BUILDER_DIR . '/framework/includes/structure/class-builder-column.php' );

		// Load tools class
		require( TM_BUILDER_DIR . '/framework/includes/class-builder-tools.php' );

		// Load modules-loader
		require( TM_BUILDER_DIR . '/framework/includes/class-modules-loader.php' );

		do_action( 'tm_builder_ready' );
	}
}

if ( ! function_exists( 'tm_builder_should_load_framework' ) ) :
function tm_builder_should_load_framework() {
	global $pagenow;

	$is_admin = is_admin();
	$action_hook = $is_admin ? 'wp_loaded' : 'wp';
	$required_admin_pages = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'customize.php', 'edit-tags.php', 'admin-ajax.php', 'export.php' ); // list of admin pages where we need to load builder files
	$specific_filter_pages = array( 'edit.php', 'admin.php', 'edit-tags.php' ); // list of admin pages where we need more specific filtering

	$is_edit_library_page = 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'tm_pb_layout' === $_GET['post_type'];
	$is_role_editor_page = 'admin.php' === $pagenow && isset( $_GET['page'] ) && apply_filters( 'tm_divi_role_editor_page', 'tm_divi_role_editor' ) === $_GET['page'];
	$is_import_page = 'admin.php' === $pagenow && isset( $_GET['import'] ) && 'wordpress' === $_GET['import']; // Page Builder files should be loaded on import page as well to register the tm_pb_layout post type properly
	$is_edit_layout_category_page = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'layout_category' === $_GET['taxonomy'];

	if ( ! $is_admin || ( $is_admin && in_array( $pagenow, $required_admin_pages ) && ( ! in_array( $pagenow, $specific_filter_pages ) || $is_edit_library_page || $is_role_editor_page || $is_edit_layout_category_page || $is_import_page ) ) ) {
		return true;
	} else {
		return false;
	}

}
endif;

if ( ! function_exists( 'tm_builder_load_framework' ) ) {
	/**
	 * Load framework parts
	 * @return boolean If `tm_builder_should_load_framework` returns true, result is also true
	 */
	function tm_builder_load_framework() {

		require( TM_BUILDER_DIR . '/framework/functions.php' );

		// load builder files on front-end and on specific admin pages only.
		if ( tm_builder_should_load_framework() ) {

			require( TM_BUILDER_DIR . '/framework/layouts.php' );
			require( TM_BUILDER_DIR . '/framework/includes/class-builder-element.php' );

			define( 'TM_BUILDER_AJAX_TEMPLATES_AMOUNT', apply_filters( 'tm_pb_templates_loading_amount', 15 ) );
			add_action( 'init', array( 'TM_Builder_Element', 'set_media_queries' ), 11 );

			require( TM_BUILDER_DIR . '/framework/includes/class-builder-module.php' );
			require( TM_BUILDER_DIR . '/framework/includes/class-global-settings.php' );

			do_action( 'tm_builder_framework_loaded' );

			$action_hook = 'wp';
			if ( is_admin() ) {
				$action_hook = 'wp_loaded';
			}

			add_action( $action_hook, 'tm_builder_init_global_settings' );
			add_action( $action_hook, 'tm_builder_add_main_elements' );

			return true;
		}

		return false;
	}
}

tm_builder_load_framework();
