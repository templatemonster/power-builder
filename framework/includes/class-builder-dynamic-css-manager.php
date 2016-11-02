<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Builder_Dynamic_CSS_Manager' ) ) {

	/**
	 * Define TM_Builder_Dynamic_CSS_Manager class
	 */
	class TM_Builder_Dynamic_CSS_Manager {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Check if builder CSS already added
		 * @var boolean
		 */
		private $added = false;

		/**
		 * Constructor for the class
		 */
		function __construct() {
			add_action( 'cherry_dynamic_css_include_custom_files', array( $this, 'add_files' ), 10, 2 );
		}

		/**
		 * Add builder CSS files
		 * @param array  $args Arguments array.
		 * @param object $core Core object.
		 */
		public function add_files( $args, $core ) {

			if ( true === $this->added ) {
				return;
			}

			$handle = opendir( $this->plugin_modules_path() );

			if ( ! $handle ) {
				return false;
			}

			while ( ( false !== $file = readdir( $handle ) ) ) {

				if ( in_array( $file, array( '.', '..', 'templates' ) ) ) {
					continue;
				}

				$file = str_replace( array( 'class-builder-module-', '.php' ), array( '', '.css' ), $file );

				$path = $this->locate_file( $file );

				if ( $path ) {
					include $path;
				}
			}

			closedir( $handle );

		}

		/**
		 * Locate dynamic CSS file.
		 *
		 * @param  string $file File name.
		 * @return void
		 */
		public function locate_file( $file ) {

			$path = false;

			if ( file_exists( get_stylesheet_directory() . $this->theme_dynamic_path() . $file ) ) {
				$path = get_stylesheet_directory() . $this->theme_dynamic_path() . $file;
			}

			if ( ! $path && file_exists( get_template_directory() . $this->theme_dynamic_path() . $file ) ) {
				$path = get_template_directory() . $this->theme_dynamic_path() . $file;
			}

			if ( ! $path && file_exists( $this->plugin_dynamic_path() . $file ) ) {
				$path = $this->plugin_dynamic_path() . $file;
			}

			return $path;

		}

		/**
		 * Returns theme dynamic CSS path
		 *
		 * @return string
		 */
		public function theme_dynamic_path() {
			return apply_filters(
				'tm_builder_theme_dynamic_css_path',
				'/assets/css/dynamic/builder/'
			);
		}

		/**
		 * Return plugin dynamic CSS path
		 *
		 * @return string
		 */
		public function plugin_dynamic_path() {
			return TM_BUILDER_DIR . 'framework/assets/css/dynamic/';
		}

		/**
		 * Return plugin modules path
		 *
		 * @return string
		 */
		public function plugin_modules_path() {
			return TM_BUILDER_DIR . 'framework/includes/modules/';
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
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
 * Returns instance of TM_Builder_Dynamic_CSS_Manager
 *
 * @return object
 */
function tm_builder_dynamic_css_manager() {
	return TM_Builder_Dynamic_CSS_Manager::get_instance();
}

tm_builder_dynamic_css_manager();
