<?php

/**
 * Class Tm_Builder_Module
 */
class Tm_Builder_Module extends Tm_Builder_Element {

	/**
	 * Module name
	 *
	 * @var string Module name.
	 */
	public $name;

	/**
	 * Module icon name
	 *
	 * @var string Module icon name, should be defined within the icon-font
	 *      			 or icons (images) collection.
	 */
	public $icon;

	/**
	 * Module slug
	 *
	 * @var string Module slug.
	 */
	public $slug;

	/**
	 * Module main class name
	 *
	 * @var string CSS class name(s).
	 */
	public $main_css_element;

	/**
	 * @var array
	 */
	public $whitelisted_fields = array();

	/**
	 * Module options defaults
	 *
	 * @var array Defaults for each module option.
	 */
	public $fields_defaults = array();

	/**
	 * Advanced module settings page title
	 *
	 * @var string Advanced settings page title.
	 */
	public $advanced_setting_title_text;

	/**
	 * Module settings page title
	 *
	 * @var string Settings page title.
	 */
	public $settings_text;

	/**
	 * Advanced module options
	 *
	 * @var array Advanced module options, which are shown
	 *      		  on advanced settings page.
	 */
	public $advanced_options = array();

	/**
	 * Module custom CSS options
	 *
	 * @var array Custom CSS options.
	 */
	public $custom_css_options = array();

	/**
	 * Shortcode attributes.
	 *
	 * @var array
	 */
	public $shortcode_atts = array();

	/**
	 * Get module template. Can be overriden from theme
	 *
	 * @author TemplateMonster
	 * @param  string $name Template name.
	 * @return void
	 */
	public function get_template_part( $name = null, $alt = null ) {

		$template = $this->locate_template( $name, $alt );

		if ( ! $template ) {
			return;
		}

		ob_start();
		include $template;

		return ob_get_clean();
	}

	/**
	 * Get path to module template. Can be overriden from theme/
	 *
	 * @author TemplateMonster
	 * @param  string $name Template name.
	 * @param  string $alt  Template name to search in theme if first is not found.
	 * @return string
	 */
	public function locate_template( $name = null, $alt = null ) {

		$templates = array( 'builder/templates/' . $name );

		if ( null !== $alt ) {
			$templates[] = 'builder/templates/' . $alt;
		}

		$template = locate_template( $templates );

		if ( ! $template ) {
			$template = tm_builder_modules_loader()->modules_dir() . '/templates/' . $name;
		}

		if ( ! file_exists( $template ) && null !== $alt ) {
			$template = tm_builder_modules_loader()->modules_dir() . '/templates/' . $alt;
		}

		if ( file_exists( $template ) ) {
			return $template;
		}
	}

	/**
	 * Wrap each module into block with spicific ID and classes
	 *
	 * @author TemplateMonster
	 * @param  string $content       Current module content.
	 * @param  array  $classes       Additional module classes.
	 * @param  string $function_name
	 * @return string
	 */
	public function wrap_module( $content = null, $classes = array(), $function_name, $atts = array() ) {

		// Explode the string by space, so we can accept `$classes` with values:
		// 'tm_pb_module_1 tm_pb_module_2 tm_pb_module_3'
		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}

		$classes[] = 'tm_pb_module';

		return $this->wrap_clean(
			$content,
			$classes,
			$function_name,
			$atts
		);
	}

	/**
	 * Wrap each 3rd party module into block with spicific ID and classes
	 *
	 * @author TemplateMonster
	 * @param  string $content       Current module content.
	 * @param  array  $classes       Additional module classes.
	 * @param  string $function_name
	 * @return string
	 */
	public function wrap_clean( $content = null, $classes = array(), $function_name, $atts = array() ) {

		$module_class = false;
		$module_id = false;

		// Explode the string by space, so we can accept `$classes` with values:
		// 'tm_pb_module_1 tm_pb_module_2 tm_pb_module_3'
		if ( ! is_array( $classes ) ) {
			$classes = explode( ' ', $classes );
		}

		if ( ! empty( $this->shortcode_atts['module_class'] ) ) {
			$module_class = esc_attr( $this->shortcode_atts['module_class'] );
		} elseif ( false !== $this->_var( 'module_class' ) ) {
			$module_class = $this->_var( 'module_class' );
		}

		if ( ! empty( $this->shortcode_atts['module_id'] ) ) {
			$module_id = esc_attr( $this->shortcode_atts['module_id'] );
		} elseif ( false !== $this->_var( 'module_id' ) ) {
			$module_id = $this->_var( 'module_id' );
		}

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );
		$classes      = array_merge( array(
				$this->slug,
				$module_class,
			),
			$classes
		);

		return sprintf(
			'<div%1$s class="%2$s"%5$s>%3$s</div><!-- .%4$s -->',
			! empty( $module_id ) ? ' id="' . esc_attr( $module_id ) . '"' : '',
			$this->parse_classes( $classes ),
			$content,
			$this->slug,
			$this->prepare_atts( $atts )
		);
	}

	/**
	 * Glues html attributes array into string
	 *
	 * @param  array $atts           Attributes array.
	 * @param  bool  $advanced_mode  In advanced mode the function will check each value of the attribute.
	 *                               If value is empty, attribute will not be glued.
	 *                               Also `switcher`-like values supported by providing an array value, where
	 *                               the first element will be `switcher` on/off value and the second element
	 *                               will be the value inserted on a positive 'on' `switcher` value.
	 * @return string
	 */
	public function prepare_atts( $atts = array(), $advanced_mode = false ) {
		$result = '';

		if ( ! empty( $atts ) ) {
			foreach ( $atts as $key => $value ) {
				if ( $advanced_mode ) {
					if ( is_array( $value ) &&
						 0 < sizeof( $value ) ) {
						$value = 'on' === $value[0] ? isset( $value[1] ) ? $value[1] : '' : '';
					}

					if ( empty( $value ) ) {
						continue;
					}
				}

				$result .= sprintf( ' %1$s=\'%2$s\'', $key, $value );
			}
		}

		return $result;
	}

	/**
	 * Glues array of classes into string
	 *
	 * @param  array $classes Classes array.
	 * @return string
	 */
	public function parse_classes( $classes ) {
		return str_replace( '  ', ' ', implode( ' ', $classes ) );
	}

	/**
	 * Define varibales set in $this->var property.
	 *
	 * @author TemplateMonster
	 * @param  array    $properties Variables array
	 * @return boolean              True, if no errors occured
	 */
	public function set_vars( $properties = array() ) {

		if ( ! is_array( $properties ) ) {
			return false;
		}

		/**
		 * Filter module variables array
		 *
		 * @param array  $properties Default variables array.
		 * @param object $this       Current module object instance.
		 */
		$properties = apply_filters( 'tm_builder_module_vars', $properties, $this );

		foreach ( $properties as $key ) {
			$value = false;

			if ( isset( $this->shortcode_atts[ $key ] ) ) {
				$value = $this->shortcode_atts[ $key ];
			}

			$this->_var( $key, $value );
		}

		return true;
	}

	/**
	 * Get or set variable value
	 *
	 * @author TemplateMonster
	 * @param  string $key     Variable name.
	 * @param  mixed  [$value] Variable value (optional), if passed - var will be set instead of get.
	 * @return mixed
	 */
	public function _var( $key = null, $value = null ) {

		$args = func_get_args();

		if ( ! $key ) {
			return false;
		}

		if ( 1 === sizeof( $args ) ) {
			if ( isset( $this->_properties[ $key ] ) ) {
				return $this->_properties[ $key ];
			}
		} else {
			$this->_properties[ $key ] = $value;
		}

		return false;
	}

	/**
	 * If passed var is not empty - print it with passed format.
	 *
	 * @param  string $var    Variable to print.
	 * @param  string $format Output format.
	 */
	public function html( $var = null, $format = '%s' ) {
		if ( ! empty( $var ) ) {
			return sprintf( $format, $var );
		}
	}

	/**
	 * Check if variable defined and switcher is enabled - prints formatted HTML
	 * @param  string $var    Variable name.
	 * @param  string $output Output HTML.
	 * @return string
	 */
	public function esc_switcher( $var = null, $output = null ) {

		if ( ! $var ) {
			return;
		}

		$val = $this->_var( $var );

		if ( ! $val || 'off' === $val ) {
			return;
		}

		return $output;

	}

	/**
	 * Set self-defined vars array
	 *
	 * @param  array $vars Varables array.
	 * @return void
	 */
	public function test_vars( $vars ) {

		foreach ( $vars as $var => $value ) {
			$this->_var( $var, $value );
		}

	}

}
