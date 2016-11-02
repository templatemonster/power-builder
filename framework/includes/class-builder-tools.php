<?php
/**
 * Class with builder service tools
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Builder_Tools' ) ) {

	/**
	 * Define  class
	 */
	class TM_Builder_Tools {

		/**
		 * Holder for cached values
		 *
		 * @var array
		 */
		private $cache = array();

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Try set up query arguments by standard query-related options
		 *
		 * @param  object $module     current module instance.
		 * @param  array  $extra_args Additional arguments.
		 * @return object
		 */
		public function build_module_query( $module, $extra_args = array() ) {

			$vars = array(
				'posts_per_page' => 'posts_per_page',
				'post_offset'    => 'offset',
			);

			$taxonomies = array(
				'categories'     => array(
					'taxonomy' => 'category',
					'field'    => 'id',
				),
				'post_tag'       => array(
					'taxonomy' => 'post_tag',
					'field'    => 'id',
				),
				'post_format'    => array(
					'taxonomy' => 'post_format',
					'field'    => 'id',
				),
			);

			$query_args = array(
				'ignore_sticky_posts' => true,
				'post_status'         => 'publish',
			);

			$paged = $module->_var( 'paged' );

			if ( $paged ) {
				$query_args['paged'] = $paged;
			}

			foreach ( $vars as $module_var => $query_var ) {

				if ( ! $module->_var( $module_var ) ) {
					continue;
				}

				$query_args[ $query_var ] = $module->_var( $module_var );

			}

			if ( 'post_id' === $module->_var( 'terms_type' ) && '' !== $module->_var( 'post_id' ) ) {
				$query_args['post__in'] = explode( ' ', $module->_var( 'post_id' ) );
			}

			foreach ( $taxonomies as $module_var => $tax ) {

				if ( ! $module->_var( $module_var ) ) {
					continue;
				}

				if ( $module_var !== $module->_var( 'terms_type' ) ) {
					continue;
				}

				$terms = explode( ',', $module->_var( $module_var ) );

				if ( empty( $terms ) ) {
					continue;
				}

				$query_args['tax_query'] = array(
					array_merge(
						$tax,
						array( 'terms' => $terms, )
					)
				);
			}

			if ( ! empty( $extra_args ) ) {
				$query_args = array_merge( $query_args, $extra_args );
			}

			if ( empty( $query_args ) ) {
				return $query_args;
			}

			return new WP_Query( $query_args );

		}

		/**
		 * Get column class.
		 *
		 * @param  object $module current module instance.
		 * @return array
		 */
		public function get_cols( $module ) {

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
				$col = intval( $module->_var( $var ) );

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
		 * Returns column classes string
		 *
		 * @param  object $module current module instance.
		 * @return string
		 */
		public function get_col_classes( $module ) {

			$cols  = $this->get_cols( $module );
			$class = 'tm_pb_column';

			foreach ( $cols as $col ) {
				$class .= ' ' . $col['class'];
			}

			return ltrim( $class );
		}

		/**
		 * Returns row classes string.
		 *
		 * @param  object $module current module instance.
		 * @return string
		 */
		public function get_row_classes( $module ) {

			$classes = 'row';

			if ( 'off' === $module->_var( 'use_space' ) ) {
				$classes .= ' tm_pb_col_padding_reset';
			}

			if ( 'off' === $module->_var( 'use_rows_space' ) ) {
				$classes .= ' tm_pb_row_padding_reset';
			}

			$classes .= ' tm-posts_' . $module->_var( 'post_layout' );

			return $classes;
		}

		/**
		 * Replace URL-related macros with real URLs
		 *
		 * @since  1.1.0
		 * @param  string $url URL to parse.
		 * @return string
		 */
		public function render_url( $url ) {

			$macros = apply_filters( 'tm_pb_url_macros', array(
				'%%home_url%%' => home_url( '/' ),
				'%%theme_url%%' => trailingslashit( get_stylesheet_directory_uri() ),
			) );

			return esc_url( str_replace( array_keys( $macros ), array_values( $macros ), $url ) );

		}

		/**
		 * Get registered image sizes into array for select option
		 *
		 * @return array
		 */
		public function get_image_sizes() {

			global $_wp_additional_image_sizes;

			$sizes  = get_intermediate_image_sizes();
			$result = array();

			foreach ( $sizes as $size ) {
				if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
					$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
				} else {
					$result[ $size ] = sprintf(
						'%1$s (%2$sx%3$s)',
						ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
						$_wp_additional_image_sizes[ $size ]['width'],
						$_wp_additional_image_sizes[ $size ]['height']
					);
				}
			}

			return $result;
		}

		/**
		 * Parse child modules html and retrieve the array
		 * @param  string $shortcode_content Default shortcode content.
		 * @return array                     Parsed children
		 */
		public function parse_children( $shortcode_content ) {
			$children = array();

			// Due to some strange html code, this regexp required for
			// removing everything inside the `div` body.
			// Then convert everything into an array.
			$shortcode_content = preg_replace( '/\>(\n|\t|\w|\s|&nbsp;|)+\<\/div\>/', '', $shortcode_content );
			$shortcode_content = explode( '<div', $shortcode_content );

			foreach( $shortcode_content as $child ) {
				if ( ! empty( $child ) ) {
					// Parse each item as a collection of shortcode attributes
					$children[] = shortcode_parse_atts( $child );
				}
			}

			return $children;
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
 * Returns instance of TM_Builder_Tools
 *
 * @return object
 */
function tm_builder_tools() {
	return TM_Builder_Tools::get_instance();
}
