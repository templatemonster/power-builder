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

if ( ! class_exists( 'TM_Builder_Core' ) ) {

	/**
	 * Define TM_Builder_Core class
	 */
	class TM_Builder_Core {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Holder for core instance
		 *
		 * @var Cherry_Core
		 */
		private $core = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {
			// Load the installer core.
			add_action( 'after_setup_theme', require( trailingslashit( __DIR__ ) . 'cherry-framework/setup.php' ), 0 );
			add_action( 'after_setup_theme', array( $this, 'init_core' ), 1 );
			add_action( 'after_setup_theme', array( 'Cherry_Core', 'load_all_modules' ), 2 );

			// Initialization of modules.
			add_action( 'after_setup_theme', array( $this, 'init_modules' ), 3 );
		}

		/**
		 * Returns current core instance.
		 *
		 * @return Cherry_Core
		 */
		public function get_core() {

			if ( ! $this->core ) {
				wp_die(
					__( 'Doing it wrong. get_core() function called before core was initalized', 'tm_builder' ),
					__( 'Core called to early', 'tm_builder' )
				);
			}

			return $this->core;

		}

		/**
		 * Returns instance of utility module
		 *
		 * @return Cherry_Utility
		 */
		public function utility() {
			$core    = $this->get_core();
			$utility = $core->modules['cherry-utility'];
			return $utility->utility;
		}

		/**
		 * Initalize new core instance
		 *
		 * @return Cherry_Core
		 */
		public function init_core() {

			/**
			 * Fires before loads the core theme functions.
			 *
			 * @since 1.0.0
			 */
			do_action( 'tm_builder_core_before' );

			global $chery_core_version;

			if ( null !== $this->core ) {
				return $this->core;
			}

			if ( 0 < sizeof( $chery_core_version ) ) {
				$core_paths = array_values( $chery_core_version );
				require_once( $core_paths[0] );
			}else{
				wp_die( __( 'Class Cherry_Core not found', 'tm_builder' ) );
			}

			$this->core = new Cherry_Core( array(
				'base_dir' => TM_BUILDER_DIR . 'cherry-framework',
				'base_url' => TM_BUILDER_URI . 'cherry-framework',
				'modules'  => array(
					'cherry-js-core' => array(
						'autoload' => true,
					),
					'cherry-ui-elements' => array(
						'autoload' => false,
					),
					'cherry-term-meta' => array(
						'autoload' => false,
					),
					'cherry-utility' => array(
						'autoload' => true,
						'args'     => array(
							'meta_key' => array(
								'term_thumb' => 'cherry_terms_thumbnails'
							),
						)
					),
					'cherry-customizer' => array(
						'autoload' => false,
					),
					'cherry-interface-builder' => array(
						'autoload' => false,
					),
				),
			) );

			return $this->core;

		}

		/**
		 * Init reauired modules
		 *
		 * @return void
		 */
		public function init_modules() {

			$this->get_core()->init_module( 'cherry-term-meta', array(
				'tax'      => 'category',
				'priority' => 10,
				'fields'   => array(
					'cherry_terms_thumbnails' => array(
						'type'               => 'media',
						'value'              => '',
						'multi_upload'       => false,
						'library_type'       => 'image',
						'upload_button_text' => esc_html__( 'Set thumbnail', 'tm_builder' ),
						'label'              => esc_html__( 'Category thumbnail', 'tm_builder' ),
					),
				),
			) );

			$this->get_core()->init_module( 'cherry-term-meta', array(
				'tax'      => 'post_tag',
				'priority' => 10,
				'fields'   => array(
					'cherry_terms_thumbnails' => array(
						'type'               => 'media',
						'value'              => '',
						'multi_upload'       => false,
						'library_type'       => 'image',
						'upload_button_text' => esc_html__( 'Set thumbnail', 'tm_builder' ),
						'label'              => esc_html__( 'Tag thumbnail', 'tm_builder' ),
					),
				),
			) );

			if ( is_admin() ) {
				$this->get_core()->init_module( 'cherry-interface-builder', array() );
			}

		}

		/**
		 * Returns interface builder module instance
		 *
		 * @return object
		 */
		public function interface_builder() {
			return $this->get_core()->modules['cherry-interface-builder'];
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
 * Returns instance of TM_Builder_Core
 *
 * @return object
 */
function tm_builder_core() {
	return TM_Builder_Core::get_instance();
}

tm_builder_core();
