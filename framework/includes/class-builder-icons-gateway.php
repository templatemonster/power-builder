<?php
/**
 * 3rd party fonts icons manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Builder_Icons_Gateway' ) ) {

	/**
	 * Define TM_Builder_Icons_Gateway class
	 */
	class TM_Builder_Icons_Gateway {

		/**
		 * Custom font icons array
		 * @var array
		 */
		public $font_icons = array();

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Temporary holder for processed icons
		 *
		 * @var array
		 */
		private $temp_icons = array();

		/**
		 * all fonn icons set
		 *
		 * @var array
		 */
		private $icon_symbols = array();

		/**
		 * Social icons list
		 *
		 * @var string
		 */
		private $social_icons = null;

		/**
		 * Social icons codes
		 * @var array
		 */
		private $social_icons_symbols = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			add_action( 'tm_pb_after_page_builder', array( $this, 'print_social_js_template' ) );

			/**
			 * Grab custom font icons from theme and 3rd party plugins
			 * Icons must be passed in format $handle => $path_to_css_file
			 */
			$this->font_icons = apply_filters( 'tm_builder_custom_font_icons', array() );

			if ( empty( $this->font_icons ) ) {
				return;
			}

			// Register custom fonts
			add_action( 'wp_enqueue_scripts', array( $this, 'register_fonts' ), 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_fonts' ), 1 );
			// Add icons to picker
			add_filter( 'tm_pb_font_icon_symbols', array( $this, 'prepare_icon_picker' ) );
			// Enqueue styles on backend
			add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
			// Enqueue styles on frontend
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public' ) );

		}

		/**
		 * Register custom fonts CSS files
		 *
		 * @since  4.0.0
		 */
		function register_fonts() {

			if ( ! is_array( $this->font_icons ) ) {
				return;
			}

			foreach ( $this->font_icons as $handle => $data ) {
				wp_register_style( $handle, $data['src'] );
			}

		}

		/**
		 * Replace URL with path in src
		 *
		 * @param  string $url
		 * @return string $path
		 */
		public function prepare_path( $url ) {

			$url = remove_query_arg( array( 'rev', 'ver', 'v' ), $url );

			return str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $url );
		}

		/**
		 * Enqueue custom font icons in admin (to show its in icon picker)
		 *
		 * @param  array  $styles Page hook name.
		 */
		public function enqueue_admin( $hook ) {

			if ( 'post.php' !== $hook ) {
				return;
			}

			foreach ( $this->font_icons as $handle => $data ) {
				wp_enqueue_style( $handle );
			}
		}

		/**
		 * Enqueue custom font icons in admin (to show its in icon picker)
		 */
		public function enqueue_public() {

			foreach ( $this->font_icons as $handle => $data ) {
				wp_enqueue_style( $handle );
			}

		}

		/**
		 * Prepare icons for icon picker
		 *
		 * @param  array  $icons existing icons
		 */
		public function prepare_icon_picker( $icons ) {

			$user_icons = array();

			foreach ( $this->font_icons as $handle => $data ) {
				$path = $this->prepare_path( $data['src'] );

				if ( ! file_exists( $path ) ) {
					continue;
				}

				$result = file_get_contents( $path );
				preg_match_all( '/content\:\s?[\'\"].?([a-zA-Z0-9]+)[\'\"]/', $result, $matches );

				if ( is_array( $matches ) && ! empty( $matches[1] ) ) {
					$this->temp_icons = array();
					array_walk( $matches[1], array( $this, 'format_matches' ), $data['base'] );
					$user_icons = array_merge( $user_icons, $this->temp_icons );
				}

			}

			if ( ! empty( $user_icons ) ) {
				return array_merge( $icons, $user_icons );
			} else {
				return $icons;
			}

		}

		/**
		 * Returns list of social icons
		 *
		 * @return string
		 */
		public function get_social_icon_list_items() {

			if ( null == $this->social_icons ) {

				$icons = $this->get_all_icons();

				foreach ( $icons['icons'] as $icon ) {

					if ( ! in_array( 'Brand Icons', $icon['categories'] ) ) {
						continue;
					}

					$this->social_icons_symbols[ $icon['unicode'] ] = 'font-awesome';
					$this->social_icons .= sprintf(
						'<li data-icon="%1$s" data-show="&#x%1$s;"></li>',
						esc_attr( $icon['unicode'] )
					);
				}

			}

			return $this->social_icons;

		}

		/**
		 * Get social icons unicode symbols
		 *
		 * @return array
		 */
		public function get_social_icons_symbols() {

			if ( empty( $this->social_icons_symbols ) ) {

				$icons = $this->get_all_icons();

				foreach ( $icons['icons'] as $icon ) {

					if ( ! in_array( 'Brand Icons', $icon['categories'] ) ) {
						continue;
					}

					$this->social_icons_symbols[ $icon['unicode'] ] = 'font-awesome';
				}
			}

			return $this->social_icons_symbols;

		}

		/**
		 * Return full FontAwesome icons list.
		 *
		 * @return array
		 */
		public function get_all_icons() {

			$font_awesome_style_path = TM_BUILDER_DIR . 'framework/admin/assets/fonts/font-awesome.json';

			if ( file_exists( $font_awesome_style_path ) ) {
				$font_awesome_list = file_get_contents( $font_awesome_style_path );
			}

			return json_decode( $font_awesome_list, true );
		}

		/**
		 * Format natches array into icons set required.
		 *
		 * @param  string &$item Array value.
		 * @param  int    &$key  Array key.
		 * @param  string $base  Base class name.
		 */
		public function format_matches( &$item, &$key, $base ) {
			$this->temp_icons[ $item ] = str_replace( ' ', '-', $base );
		}

		/**
		 * Print JavaScript template for social icons dropdown.
		 *
		 * @return void
		 */
		public function print_social_js_template() {
			printf(
				'<script type="text/template" id="tm-builder-font-social-icon-list-items">
					%1$s
				</script>',
				$this->get_social_icon_list_items()
			);
		}

		/**
		 * Get all font icon symbols list
		 * @return array
		 */
		public function get_font_icon_symbols() {

			if ( empty( $this->icon_symbols ) ) {
				$this->icon_symbols = array();
				$icons              = $this->get_all_icons();

				if ( is_array( $icons ) && ! empty( $icons ) ) {
					foreach ( $icons['icons'] as $key => $icon ) {
						$this->icon_symbols[ $icon['unicode'] ] = 'font-awesome';
					}
				}

				$this->icon_symbols = apply_filters( 'tm_pb_font_icon_symbols', $this->icon_symbols );
			}

			return $this->icon_symbols;
		}

		/**
		 * Setup cuurent icon font family
		 *
		 * @param  string $icon Processed icon.
		 * @return void
		 */
		public function set_icon_family( $icon ) {

			$icons_set = $this->get_font_icon_symbols();

			if ( ! isset( $icons_set[ $icon ] ) ) {
				return;
			}

			if ( 'font-awesome' !== $icons_set[ $icon ] ) {
				wp_cache_set( 'tm_builder_processed_icon', $icons_set[ $icon ] );
			} else {
				wp_cache_delete( 'tm_builder_processed_icon' );
			}

		}

		/**
		 * Returns the instance.
		 *
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

	}

}

/**
 * Returns instance of TM_Builder_Icons_Gateway
 *
 * @return object
 */
function tm_builder_icons_gateway() {
	return TM_Builder_Icons_Gateway::get_instance();
}

tm_builder_icons_gateway();
