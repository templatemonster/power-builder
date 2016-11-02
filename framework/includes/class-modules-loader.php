<?php
/**
 * Modules loader
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Builder_Modules_Loader' ) ) {

	/**
	 * Define  class
	 */
	class TM_Builder_Modules_Loader {

		/**
		 * Default modules directory path
		 * @var string
		 */
		private $modules_dir;

		/**
		 * Holder for supported modules list
		 * @var array|bool
		 */
		private $modules_support = false;

		/**
		 * Active modules list
		 * @var array
		 */
		public $modules_list = array();

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			$this->modules_dir = TM_BUILDER_DIR . 'framework/includes/modules';
			$this->theme_dir   = apply_filters( 'tm_pb_theme_modules_dir', 'builder/modules/' );

			/**
			 * Filter supported modules list
			 *
			 * @var array
			 */
			$this->modules_support = apply_filters(
				'tm_builder_modules_support',
				get_theme_support( 'tm-builder-modules' )
			);

			/**
			 * Filter deprecated modules list
			 *
			 * @var array
			 */
			$this->deprecated_modules = apply_filters(
				'tm_builder_deprecated_modules',
				array(
					'Tm_Builder_Module_Fullwidth_Header',
					'Tm_Builder_Module_Fullwidth_Map',
					'Tm_Builder_Module_Fullwidth_Menu',
					'Tm_Builder_Module_Fullwidth_Slider',
				)
			);

			$this->load_modules();
		}

		/**
		 * Load all modules
		 *
		 * @return void
		 */
		public function load_modules() {

			$handle = opendir( $this->modules_dir );

			if ( ! $handle ) {
				return false;
			}

			while ( ( false !== $file = readdir( $handle ) ) ) {

				if ( ! is_file( $this->join_path( $this->modules_dir, $file ) ) ) {
					continue;
				}

				$class_name = $this->get_class_name( $file );

				$path = locate_template( array( $this->theme_dir . $file ) );
				if ( ! $path ) {
					$path = $this->join_path( $this->modules_dir, $file );
				}

				if ( ! class_exists( $class_name ) && $this->is_module_support( $class_name ) ) {
					include_once $path;
					$this->modules_list[ $class_name ] = $path;
				}
			}

			/**
			 * Load your own modules on this hook and add it into $this->modules_list property.
			 *
			 * @param object $this Loader class instance.
			 */
			do_action( 'tm_builder_load_user_modules', $this );

			closedir( $handle );

		}

		/**
		 * Returns active modules count
		 *
		 * @return string
		 */
		public function modules_count() {
			return count( $this->modules_list );
		}

		/**
		 * Adds module to $this->modules_list.
		 *
		 * @param  string $class_name Module class name.
		 * @param  string $path       Module path.
		 * @return void
		 */
		public function add_module( $class_name, $path ) {

			if ( ! isset( $this->modules_list[ $class_name ] ) ) {
				$this->modules_list[ $class_name ] = $path;
			}

		}

		/**
		 * Check if cureent module supported by theme.
		 * Also returns true if theme support for builder not defined in theme.
		 *
		 * @param  string $class Module class name.
		 * @return boolean
		 */
		public function is_module_support( $class ) {

			if ( is_array( $this->deprecated_modules ) && in_array( $class, $this->deprecated_modules ) ) {
				return false;
			}

			if ( empty( $this->modules_support ) ) {
				return true;
			}

			return in_array( $class, $this->modules_support[0] );

		}

		/**
		 * Get module class name from module file name
		 *
		 * @param  string $file Filnemae.
		 * @return void
		 */
		public function get_class_name( $file ) {
			$file = str_replace( array( '-', 'class', '.php' ), array( ' ', 'tm', '' ), $file );
			return str_replace( ' ', '_', ucwords( $file ) );
		}

		/**
		 * Join multiple paths with a `DIRECTORY_SEPARATOR`
		 * @param  string $a Path string
		 * @param  string $b Path string
		 * @return string    Resulting path string
		 */
		public function join_path( $a, $b ) {
			return join( DIRECTORY_SEPARATOR, func_get_args() );
		}

		/**
		 * Returns private $this->modules_dir propery
		 * @return string
		 */
		public function modules_dir() {
			return $this->modules_dir;
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
 * Returns instance of TM_Builder_Modules_Loader
 *
 * @return object
 */
function tm_builder_modules_loader() {
	return TM_Builder_Modules_Loader::get_instance();
}

tm_builder_modules_loader();
