<?php

/**
 * Returns array of allowed media breakpoints for option
 */
function tm_pb_media_breakpoints() {
	return apply_filters( 'tm_pb_media_breakpoints', array(
		'phone'   => esc_html__( 'Phone', 'tm_builder' ),
		'tablet'  => esc_html__( 'Tablet', 'tm_builder' ),
		'laptop'  => esc_html__( 'Laptop', 'tm_builder' ),
		'desktop' => esc_html__( 'Desktop', 'tm_builder' ),
	) );
}

/**
 * Returns array of allowed media breakpoints valuews for processing
 */
function tm_pb_media_breakpoint_values() {
	return apply_filters( 'tm_pb_media_breakpoint_values', array(
		'phone'   => 'max_width_767',
		'tablet'  => '768_980',
		'laptop'  => '981_1440',
		'desktop' => 'min_width_1441',
	) );
}

/**
 * Returns URL to google maps API
 */
function tm_get_maps_api_url() {

	// Set fixed protocol for preview URL to prevent cross origin issue.
	// Set google maps API domain and protocol.
	$scheme = 'http://';
	$domain = 'maps';

	if ( is_ssl() ) {
		$scheme = 'https://';
		$domain = 'maps-api-ssl';
	}

	$base     = sprintf( '%1$s%2$s.google.com/maps/api/js', $scheme, $domain );
	$settings = get_option( 'tm_builder_settings' );
	$key      = Cherry_Toolkit::get_arg( $settings, 'api_key', false );

	$google_maps_url = add_query_arg(
		array(
			'v'   => 3,
			'key' => esc_attr( $key ),
		),
		esc_url( $base )
	);

	return $google_maps_url;
}

/**
 * Fix '&' encoding bug
 */
add_filter( 'script_loader_tag', 'tm_pb_fix_map_url', 10, 3 );
function tm_pb_fix_map_url( $tag, $handle, $src ) {

	if ( 'google-maps-api' !== $handle ) {
		return $tag;
	}

	return str_replace( array( '#038;', '&&' ), array( '&', '&' ), $tag );
}

// exclude predefined layouts from import
function tm_remove_predefined_layouts_from_import( $posts ) {
	$processed_posts = $posts;

	if ( isset( $posts ) && is_array( $posts ) ) {
		$processed_posts = array();

		foreach ( $posts as $post ) {
			if ( isset( $post['postmeta'] ) && is_array( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					if ( '_tm_pb_predefined_layout' === $meta['key'] && 'on' === $meta['value'] )
						continue 2;
				}
			}

			$processed_posts[] = $post;
		}
	}

	return $processed_posts;
}
add_filter( 'wp_import_posts', 'tm_remove_predefined_layouts_from_import', 5 );

// set the layout_type taxonomy to "layout" for layouts imported from old version of Divi.
function tm_update_old_layouts_taxonomy( $posts ) {
	$processed_posts = $posts;

	if ( isset( $posts ) && is_array( $posts ) ) {
		$processed_posts = array();

		foreach ( $posts as $post ) {
			$update_built_for_post_type = false;

			if ( 'tm_pb_layout' === $post['post_type'] ) {
				if ( ! isset( $post['terms'] ) ) {
					$post['terms'][] = array(
						'name'   => 'layout',
						'slug'   => 'layout',
						'domain' => 'layout_type'
					);
					$post['terms'][] = array(
						'name'   => 'not_global',
						'slug'   => 'not_global',
						'domain' => 'scope'
					);
				}

				$update_built_for_post_type = true;

				// check whether _tm_pb_built_for_post_type custom field exists
				if ( ! empty( $post['postmeta'] ) ) {
					foreach ( $post['postmeta'] as $index => $value ) {
						if ( '_tm_pb_built_for_post_type' === $value['key'] ) {
							$update_built_for_post_type = false;
						}
					}
				}
			}

			// set _tm_pb_built_for_post_type value to 'page' if not exists
			if ( $update_built_for_post_type ) {
				$post['postmeta'][] = array(
					'key'   => '_tm_pb_built_for_post_type',
					'value' => 'page',
				);
			}

			$processed_posts[] = $post;
		}
	}

	return $processed_posts;
}
add_filter( 'wp_import_posts', 'tm_update_old_layouts_taxonomy', 10 );

// add custom filters for posts in the Divi Library
if ( ! function_exists( 'tm_pb_add_layout_filters' ) ) :
function tm_pb_add_layout_filters() {
	if ( isset( $_GET['post_type'] ) && 'tm_pb_layout' === $_GET['post_type'] ) {
		$layout_categories = get_terms( 'layout_category' );
		$filter_category = array();
		$filter_category[''] = esc_html__( 'All Categories', 'tm_builder' );

		if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
			foreach( $layout_categories as $category ) {
				$filter_category[$category->slug] = $category->name;
			}
		}

		$filter_layout_type = array(
			''        => esc_html__( 'All Layouts', 'tm_builder' ),
			'module'  => esc_html__( 'Modules', 'tm_builder' ),
			'row'     => esc_html__( 'Rows', 'tm_builder' ),
			'section' => esc_html__( 'Sections', 'tm_builder' ),
			'layout'  => esc_html__( 'Layouts', 'tm_builder' ),
		);

		$filter_scope = array(
			''           => esc_html__( 'Global/not Global', 'tm_builder' ),
			'global'     => esc_html__( 'Global', 'tm_builder' ),
			'not_global' => esc_html__( 'not Global', 'tm_builder' )
		);
		?>

		<select name="layout_type">
		<?php
			$selected = isset( $_GET['layout_type'] ) ? $_GET['layout_type'] : '';
			foreach ( $filter_layout_type as $value => $label ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					$value == $selected ? ' selected="selected"' : '',
					esc_html( $label )
				);
			} ?>
		</select>

		<select name="scope">
		<?php
			$selected = isset( $_GET['scope'] ) ? $_GET['scope'] : '';
			foreach ( $filter_scope as $value => $label ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					$value == $selected ? ' selected="selected"' : '',
					esc_html( $label )
				);
			} ?>
		</select>

		<select name="layout_category">
		<?php
			$selected = isset( $_GET['layout_category'] ) ? $_GET['layout_category'] : '';
			foreach ( $filter_category as $value => $label ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					$value == $selected ? ' selected="selected"' : '',
					esc_html( $label )
				);
			} ?>
		</select>
	<?php
	}
}
endif;
add_action( 'restrict_manage_posts', 'tm_pb_add_layout_filters' );

// Add "Export Divi Layouts" button to the Divi Library page
if ( ! function_exists( 'tm_pb_load_export_section' ) ) :
function tm_pb_load_export_section(){
	$current_screen = get_current_screen();

	if ( 'edit-tm_pb_layout' === $current_screen->id ) {
		// display wp error screen if library is disabled for current user
		if ( ! tm_pb_is_allowed( 'divi_library' ) || ! tm_pb_is_allowed( 'add_library' ) || ! tm_pb_is_allowed( 'save_library' ) ) {
			wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'tm_builder' ) );
		}

		add_action( 'all_admin_notices', 'tm_pb_export_layouts_interface' );
	}
}
endif;
add_action( 'load-edit.php', 'tm_pb_load_export_section' );

// Check whether the library editor page should be displayed or not
function tm_pb_check_library_permissions(){
	$current_screen = get_current_screen();

	if ( 'tm_pb_layout' === $current_screen->id && ( ! tm_pb_is_allowed( 'divi_library' ) || ! tm_pb_is_allowed( 'save_library' ) ) ) {
		// display wp error screen if library is disabled for current user
		wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'tm_builder' ) );
	}
}
add_action( 'load-post.php', 'tm_pb_check_library_permissions' );

// exclude premade layouts from the list of all templates in the library.
if ( ! function_exists( 'exclude_premade_layouts_library' ) ) :
function exclude_premade_layouts_library( $query ) {
	global $pagenow;
	$current_post_type = get_query_var( 'post_type' );

	if ( is_admin() && 'edit.php' === $pagenow && $current_post_type && 'tm_pb_layout' === $current_post_type ) {
		$meta_query = array(
			array(
				'key'     => '_tm_pb_predefined_layout',
				'value'   => 'on',
				'compare' => 'NOT EXISTS',
			),
		);

		$used_built_for_post_types = tm_pb_get_used_built_for_post_types();
		if ( isset( $_GET['built_for'] ) && count( $used_built_for_post_types ) > 1 ) {
			$built_for_post_type = sanitize_text_field( $_GET['built_for'] );
			// get array of all standard post types if built_for is one of them
			$built_for_post_type_processed = in_array( $built_for_post_type, tm_pb_get_standard_post_types() ) ? tm_pb_get_standard_post_types() : $built_for_post_type;

			if ( in_array( $built_for_post_type, $used_built_for_post_types ) ) {
				$meta_query[] = array(
					'key'     => '_tm_pb_built_for_post_type',
					'value'   => $built_for_post_type_processed,
					'compare' => 'IN',
				);
			}
		}

		$query->set( 'meta_query', $meta_query );
	}

	return $query;
}
endif;
add_action( 'pre_get_posts', 'exclude_premade_layouts_library' );

if ( ! function_exists( 'tm_pb_is_pagebuilder_used' ) ) :
function tm_pb_is_pagebuilder_used( $page_id ) {
	return ( 'on' === get_post_meta( $page_id, '_tm_pb_use_builder', true ) );
}
endif;

if ( ! function_exists( 'tm_pb_get_font_icon_symbols' ) ) :
function tm_pb_get_font_icon_symbols() {
	return tm_builder_icons_gateway()->get_font_icon_symbols();
}
endif;

if ( ! function_exists( 'tm_pb_get_font_icon_list' ) ) :
function tm_pb_get_font_icon_list() {
	$output = is_customize_preview() ? tm_pb_get_font_icon_list_items() : '<%= window.tm_builder.font_icon_list_template() %>';

	$output = sprintf( '<ul class="tm_font_icon">%1$s</ul>', $output );

	return $output;
}
endif;

if ( ! function_exists( 'tm_pb_get_font_icon_list_items' ) ) :
function tm_pb_get_font_icon_list_items() {
	$output = '';

	$symbols = tm_pb_get_font_icon_symbols();

	foreach ( $symbols as $symbol => $base ) {
		$output .= sprintf(
			'<li data-icon="%1$s" data-show="&#x%1$s;" class="%2$s"></li>',
			esc_attr( $symbol ), esc_attr( $base )
		);
	}

	return $output;
}
endif;

if ( ! function_exists( 'tm_pb_font_icon_list' ) ) :
function tm_pb_font_icon_list() {
	echo tm_pb_get_font_icon_list();
}
endif;

if ( ! function_exists( 'tm_pb_get_font_down_icon_symbols' ) ) :
function tm_pb_get_font_down_icon_symbols() {
	$symbols = array( 'f0dd', 'f149', 'f0ed', 'f0a7', 'f107', 'f01a', 'f0ab', 'f063', 'f150', 'f13a', 'f078', 'f175', 'f150' );

	return $symbols;
}
endif;

if ( ! function_exists( 'tm_pb_get_font_down_icon_list' ) ) :
function tm_pb_get_font_down_icon_list() {
	$output = is_customize_preview() ? tm_pb_get_font_down_icon_list_items() : '<%= window.tm_builder.font_down_icon_list_template() %>';

	$output = sprintf( '<ul class="tm_font_icon">%1$s</ul>', $output );

	return $output;
}
endif;

if ( ! function_exists( 'tm_pb_get_font_down_icon_list_items' ) ) :
function tm_pb_get_font_down_icon_list_items() {
	$output = '';

	$symbols = tm_pb_get_font_down_icon_symbols();

	foreach ( $symbols as $symbol ) {
		$output .= sprintf( '<li data-icon="%1$s" data-show="&#x%1$s;"></li>', esc_attr( $symbol ) );
	}

	return $output;
}
endif;

if ( ! function_exists( 'tm_pb_font_down_icon_list' ) ) :
function tm_pb_font_down_icon_list() {
	echo tm_pb_get_font_down_icon_list();
}
endif;

function tm_pb_get_font_social_icon_list() {
	$output = is_customize_preview() ? tm_builder_icons_gateway()->get_social_icon_list_items() : '<%= window.tm_builder.font_social_icon_list_template() %>';

	$output = sprintf( '<ul class="tm_font_icon">%1$s</ul>', $output );

	return $output;
}

/**
 * Processes font icon value for use on front-end
 *
 * @param string $font_icon        Font Icon ( exact value or in %%index_number%% format ).
 * @param string $symbols_function Optional. Name of the function that gets an array of font icon values.
 *                                 tm_pb_get_font_icon_symbols function is used by default.
 * @return string $font_icon       Font Icon value
 */
if ( ! function_exists( 'tm_pb_process_font_icon' ) ) :
function tm_pb_process_font_icon( $font_icon, $symbols_function = 'default' ) {
	tm_builder_icons_gateway()->set_icon_family( $font_icon );
	return esc_attr( '&#x' . $font_icon . ';' );
}
endif;

/**
 * Save currently processed icon font-family into cache
 *
 * @param  string $icon      Processed icon.
 * @param  array  $icons_set All icons set.
 */
function tm_builder_set_icon_family( $icon = null, $icons_set = array() ) {

	if ( ! $icon ) {
		return;
	}

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
 * Gets currently processed icon from cache.
 *
 * @return string|bool
 */
function tm_builder_get_icon_family() {

	$family = wp_cache_get( 'tm_builder_processed_icon' );

	if ( $family ) {
		wp_cache_delete( 'tm_builder_processed_icon' );
		return $family;
	}

	return false;
}

if ( ! function_exists( 'tm_builder_accent_color' ) ) :
function tm_builder_accent_color( $default_color = '#7EBEC5' ) {
	$accent_color = tm_get_option( 'regular_accent_color_1', $default_color );
	return apply_filters( 'tm_builder_accent_color', $accent_color );
}
endif;

if ( ! function_exists( 'tm_builder_secondary_color' ) ) :
function tm_builder_secondary_color( $default_color = '#7EBEC5' ) {
	$accent_color = tm_get_option( 'regular_accent_color_2', $default_color );
	return apply_filters( 'tm_builder_secondary_color', $accent_color );
}
endif;

if ( ! function_exists( 'tm_builder_get_text_orientation_options' ) ) :
function tm_builder_get_text_orientation_options() {
	$text_orientation_options = array(
		'left'      => esc_html__( 'Left', 'tm_builder' ),
		'center'    => esc_html__( 'Center', 'tm_builder' ),
		'right'     => esc_html__( 'Right', 'tm_builder' ),
		'justified' => esc_html__( 'Justified', 'tm_builder' ),
	);

	if ( is_rtl() ) {
		$text_orientation_options = array(
			'right'  => esc_html__( 'Right', 'tm_builder' ),
			'center' => esc_html__( 'Center', 'tm_builder' ),
		);
	}

	return apply_filters( 'tm_builder_text_orientation_options', $text_orientation_options );
}
endif;

if ( ! function_exists( 'tm_builder_get_gallery_settings' ) ) :
function tm_builder_get_gallery_settings() {
	$output = sprintf(
		'<input type="button" class="button button-upload tm-pb-gallery-button" value="%1$s" />',
		esc_attr__( 'Update Gallery', 'tm_builder' )
	);

	return $output;
}
endif;

if ( ! function_exists( 'tm_builder_get_nav_menus_options' ) ) :
function tm_builder_get_nav_menus_options() {
	$nav_menus_options = array( 'none' => esc_html__( 'Select a menu', 'tm_builder' ) );

	$nav_menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
	foreach ( (array) $nav_menus as $_nav_menu ) {
		$nav_menus_options[ $_nav_menu->term_id ] = $_nav_menu->name;
	}

	return apply_filters( 'tm_builder_nav_menus_options', $nav_menus_options );
}
endif;

if ( ! function_exists( 'tm_builder_generate_center_map_setting' ) ) :
function tm_builder_generate_center_map_setting() {
	return '<div id="tm_pb_map_center_map" class="tm-pb-map tm_pb_map_center_map"></div>';
}
endif;

if ( ! function_exists( 'tm_builder_generate_pin_zoom_level_input' ) ) :
function tm_builder_generate_pin_zoom_level_input() {
	return '<input class="tm_pb_zoom_level" type="hidden" value="18" />';
}
endif;

if ( ! function_exists( 'tm_builder_include_categories_option' ) ) :
function tm_builder_include_categories_option( $args = array() ) {
	$defaults = apply_filters( 'tm_builder_include_categories_defaults', array (
		'use_terms'  => true,
		'term_name'  => 'project_category',
		'input_name' => 'tm_pb_include_categories',
	) );

	$args = wp_parse_args( $args, $defaults );

	$name = $args['input_name'];

	$output = "\t" . "<% var " . $name . "_temp = typeof " . $name . " !== 'undefined' ? " . $name . ".split( ',' ) : []; %>" . "\n";

	if ( $args['use_terms'] ) {
		$cats_array = get_terms( $args['term_name'] );
	} else {
		$cats_array = get_categories( apply_filters( 'tm_builder_get_categories_args', 'hide_empty=0' ) );
	}

	if ( empty( $cats_array ) ) {
		$output = '<p>' . esc_html__( "You currently don't have any projects assigned to a category.", 'tm_builder' ) . '</p>';
	}

	foreach ( $cats_array as $category ) {
		$contains = sprintf(
			'<%%= _.contains( %2$s_temp, "%1$s" ) ? checked="checked" : "" %%>',
			esc_html( $category->term_id ),
			$name
		);

		$output .= sprintf(
			'%5$s<label><input type="checkbox" name="%4$s" value="%1$s"%3$s> %2$s</label><br/>',
			esc_attr( $category->term_id ),
			esc_html( $category->name ),
			$contains,
			$name,
			"\n\t\t\t\t\t"
		);
	}

	$output = '<div id="' . $name . '">' . $output . '</div>';

	return apply_filters( 'tm_builder_include_categories_option_html', $output, $args );
}
endif;

if ( ! function_exists( 'tm_builder_include_categories_shop_option' ) ) :
function tm_builder_include_categories_shop_option( $args = array() ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$defaults = apply_filters( 'tm_builder_include_categories_shop_defaults', array (
		'use_terms' => true,
		'term_name' => 'product_category',
	) );

	$args = wp_parse_args( $args, $defaults );

	$output = "\t" . "<% var tm_pb_include_categories_shop_temp = typeof tm_pb_include_categories !== 'undefined' ? tm_pb_include_categories.split( ',' ) : []; %>" . "\n";

	$cats_array = $args['use_terms'] ? get_terms( $args['term_name'] ) : get_categories( apply_filters( 'tm_builder_get_categories_shop_args', 'hide_empty=0' ) );

	foreach ( $cats_array as $category ) {
		$contains = sprintf(
			'<%%= _.contains( tm_pb_include_categories_shop_temp, "%1$s" ) ? checked="checked" : "" %%>',
			esc_html( $category->slug )
		);

		$output .= sprintf(
			'%4$s<label><input type="checkbox" name="tm_pb_include_categories" value="%1$s"%3$s> %2$s</label><br/>',
			esc_attr( $category->slug ),
			esc_html( $category->name ),
			$contains,
			"\n\t\t\t\t\t"
		);
	}

	return apply_filters( 'tm_builder_include_categories_option_html', $output );
}
endif;

if ( ! function_exists( 'tm_pb_extract_items' ) ) :
function tm_pb_extract_items( $content ) {
	$output = $first_character = '';
	$lines = explode( "\n", str_replace( array( '<p>', '</p>', '<br />' ), '', $content ) );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '&#8211;' === substr( $line, 0, 7 ) ) {
			$line = '-' . substr( $line, 7 );
		}
		if ( '' === $line ) {
			continue;
		}
		$first_character = $line[0];
		if ( in_array( $first_character, array( '-', '+' ) ) ) {
			$line = trim( substr( $line, 1 ) );
		}
		$output .= sprintf( '[tm_pb_pricing_item available="%2$s"]%1$s[/tm_pb_pricing_item]',
			$line,
			( '-' === $first_character ? 'off' : 'on' )
		);
	}
	return do_shortcode( $output );
}
endif;

if ( ! function_exists( 'tm_builder_process_range_value' ) ) :
function tm_builder_process_range_value( $range, $option_type = '' ) {
	$range = trim( $range );
	$range_digit = floatval( $range );
	$range_string = str_replace( $range_digit, '', (string) $range );

	if ( '' === $range_string ) {
		$range_string = 'line_height' === $option_type && 3 >= $range_digit ? 'em' : 'px';
	}

	$result = $range_digit . $range_string;

	return apply_filters( 'tm_builder_processed_range_value', $result, $range, $range_string );
}
endif;

if ( ! function_exists( 'tm_builder_get_border_styles' ) ) :
function tm_builder_get_border_styles() {
	$styles = array(
		'solid'  => esc_html__( 'Solid', 'tm_builder' ),
		'dotted' => esc_html__( 'Dotted', 'tm_builder' ),
		'dashed' => esc_html__( 'Dashed', 'tm_builder' ),
		'double' => esc_html__( 'Double', 'tm_builder' ),
		'groove' => esc_html__( 'Groove', 'tm_builder' ),
		'ridge'  => esc_html__( 'Ridge', 'tm_builder' ),
		'inset'  => esc_html__( 'Inset', 'tm_builder' ),
		'outset' => esc_html__( 'Outset', 'tm_builder' ),
	);

	return apply_filters( 'tm_builder_border_styles', $styles );
}
endif;

if ( ! function_exists( 'tm_builder_get_websafe_fonts' ) ) :
function tm_builder_get_websafe_fonts() {
	$websafe_fonts = array(
		'Georgia' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'cyrillic,greek,latin',
			'type'			=> 'serif',
		),
		'Times New Roman' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'arabic,cyrillic,greek,hebrew,latin',
			'type'			=> 'serif',
		),
		'Arial' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'arabic,cyrillic,greek,hebrew,latin',
			'type'			=> 'sans-serif',
		),
		'Trebuchet' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'cyrillic,latin',
			'type'			=> 'sans-serif',
			'add_ms_version'=> true,
		),
		'Verdana' => array(
			'styles' 		=> '300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
			'character_set' => 'cyrillic,latin',
			'type'			=> 'sans-serif',
		),
	);

	$_websafe_fonts = array();

	foreach ( $websafe_fonts as $font_name => $settings ) {
		$settings['standard'] = true;

		$_websafe_fonts[ $font_name ] = $settings;
	}

	$websafe_fonts = $_websafe_fonts;

	return apply_filters( 'tm_websafe_fonts', $websafe_fonts );
}
endif;

if ( ! function_exists( 'tm_builder_get_google_fonts' ) ) :
function tm_builder_get_google_fonts() {

	if ( ! class_exists( 'TM_Dashboard_Fonts' ) ) {
		require_once( TM_BUILDER_DIR . '/dashboard/includes/google-fonts.php' );
	}

	$fonts = new Tm_Dashboard_Fonts();
	return $fonts->tm_get_google_fonts();
}
endif;

if ( ! function_exists( 'tm_builder_get_fonts' ) ) :
function tm_builder_get_fonts( $settings = array() ) {
	$defaults = array(
		'prepend_standard_fonts' => true,
	);

	$settings = wp_parse_args( $settings, $defaults );

	$fonts = $settings['prepend_standard_fonts']
		? array_merge( tm_builder_get_websafe_fonts(), tm_builder_get_google_fonts() )
		: array_merge( tm_builder_get_google_fonts(), tm_builder_get_websafe_fonts() );

	return $fonts;
}
endif;

if ( ! function_exists( 'tm_builder_font_options' ) ) :
function tm_builder_font_options() {
	$options         = array();

	$default_options = array( 'default' => array(
		'name' => esc_html__( 'Default', 'tm_builder' ),
	) );
	$fonts           = array_merge( $default_options, tm_builder_get_fonts() );

	foreach ( $fonts as $font_name => $font_settings ) {
		$options[ $font_name ] = 'default' !== $font_name ? $font_name : $font_settings['name'];
	}

	return $options;
}
endif;

if ( ! function_exists( 'tm_builder_get_font_options_items' ) ) :
function tm_builder_get_font_options_items() {
	$output = '';
	$font_options = tm_builder_font_options();

	foreach ( $font_options as $key => $value ) {
		$output .= sprintf(
			'<option value="%1$s">%2$s</option>',
			esc_attr( $key ),
			esc_html( $value )
		);
	}

	return $output;
}
endif;

if ( ! function_exists( 'tm_builder_get_websafe_font_stack' ) ) :
function tm_builder_get_websafe_font_stack( $type = 'sans-serif' ) {
	$font_stack = '';

	switch ( $type ) {
		case 'sans-serif':
			$font_stack = 'Helvetica, Arial, Lucida, sans-serif';
			break;
		case 'serif':
			$font_stack = 'Georgia, "Times New Roman", serif';
			break;
		case 'cursive':
			$font_stack = 'cursive';
			break;
	}

	return $font_stack;
}
endif;

if ( ! function_exists( 'tm_builder_get_font_family' ) ) :
function tm_builder_get_font_family( $font_name, $use_important = false ) {
	$fonts = tm_builder_get_fonts();

	$font_style = $font_weight = '';

	$font_name_ms = isset( $fonts[ $font_name ] ) && isset( $fonts[ $font_name ]['add_ms_version'] ) ? "'{$font_name} MS', " : "";

	if ( isset( $fonts[ $font_name ]['parent_font'] ) ){
		$font_style = $fonts[ $font_name ]['styles'];
		$font_name = $fonts[ $font_name ]['parent_font'];
	}

	if ( '' !== $font_style ) {
		$font_weight = sprintf( ' font-weight: %1$s;', esc_html( $font_style ) );
	}

	$style = sprintf( 'font-family: \'%1$s\', %5$s%2$s%3$s;%4$s',
		esc_html( $font_name ),
		isset( $fonts[ $font_name ] ) ? tm_builder_get_websafe_font_stack( $fonts[ $font_name ]['type'] ) : "",
		( $use_important ? ' !important' : '' ),
		$font_weight,
		$font_name_ms
	);

	return $style;
}
endif;

if ( ! function_exists( 'tm_builder_set_element_font' ) ) :
function tm_builder_set_element_font( $font, $use_important = false, $default = false ) {
	$style = '';

	if ( '' === $font ) {
		return $style;
	}

	$font_values = explode( '|', $font );
	$default = ! $default ? "||||" : $default;
	$font_values_default = explode( '|', $default );

	if ( ! empty( $font_values ) ) {
		$font_values       = array_map( 'trim', $font_values );
		$font_name         = $font_values[0];
		$is_font_bold      = 'on' === $font_values[1] ? true : false;
		$is_font_italic    = 'on' === $font_values[2] ? true : false;
		$is_font_uppercase = 'on' === $font_values[3] ? true : false;
		$is_font_underline = 'on' === $font_values[4] ? true : false;

		$font_name_default         = $font_values_default[0];
		$is_font_bold_default      = 'on' === $font_values_default[1] ? true : false;
		$is_font_italic_default    = 'on' === $font_values_default[2] ? true : false;
		$is_font_uppercase_default = 'on' === $font_values_default[3] ? true : false;
		$is_font_underline_default = 'on' === $font_values_default[4] ? true : false;

		if ( '' !== $font_name && $font_name_default !== $font_name ) {
			tm_builder_enqueue_font( $font_name );

			$style .= tm_builder_get_font_family( $font_name, $use_important ) . ' ';
		}

		$style .= tm_builder_set_element_font_style( 'font-weight', $is_font_bold_default, $is_font_bold, 'normal', 'bold', $use_important );

		$style .= tm_builder_set_element_font_style( 'font-style', $is_font_italic_default, $is_font_italic, 'none', 'italic', $use_important );

		$style .= tm_builder_set_element_font_style( 'text-transform', $is_font_uppercase_default, $is_font_uppercase, 'none', 'uppercase', $use_important );

		$style .= tm_builder_set_element_font_style( 'text-decoration', $is_font_underline_default, $is_font_underline, 'none', 'underline', $use_important );

		$style = rtrim( $style );
	}

	return $style;
}
endif;

if ( ! function_exists( 'tm_builder_set_element_font_style' ) ) :
function tm_builder_set_element_font_style( $property, $default, $value, $property_default, $property_value, $use_important ) {
	$style = "";

	if ( $value && ! $default ) {
		$style = sprintf(
			'%1$s: %2$s%3$s; ',
			esc_html( $property ),
			$property_value,
			( $use_important ? ' !important' : '' )
		);
	} elseif ( ! $value && $default ) {
		$style = sprintf(
			'%1$s: %2$s%3$s; ',
			esc_html( $property ),
			$property_default,
			( $use_important ? ' !important' : '' )
		);
	}

	return $style;
}
endif;

if ( ! function_exists( 'tm_builder_get_element_style_css' ) ) :
function tm_builder_get_element_style_css( $value, $property = 'margin', $use_important = false ) {
	$style = '';

	$values = explode( '|', $value );

	if ( ! empty( $values ) ) {
		$element_style = '';
		$i = 0;
		$values = array_map( 'trim', $values );
		$positions = array(
			'top',
			'right',
			'bottom',
			'left',
		);

		foreach ( $values as $element_style_value ) {
			if ( '' !== $element_style_value ) {
				$element_style .= sprintf(
					'%3$s-%1$s: %2$s%4$s; ',
					esc_attr( $positions[ $i ] ),
					esc_attr( tm_builder_process_range_value( $element_style_value ) ),
					esc_attr( $property ),
					( $use_important ? ' !important' : '' )
				);
			}

			$i++;
		}

		$style .= rtrim( $element_style );
	}

	return $style;
}
endif;

if ( ! function_exists( 'tm_builder_enqueue_font' ) ) :
function tm_builder_enqueue_font( $font_name ) {
	$fonts = tm_builder_get_fonts();
	$websafe_fonts = tm_builder_get_websafe_fonts();
	$protocol = is_ssl() ? 'https' : 'http';

	// Skip enqueueing if font name is not found. Possibly happen if support for particular font need to be dropped
	if ( ! array_key_exists( $font_name, $fonts ) ) {
		return;
	}

	// Skip enqueueing for websafe fonts
	if ( array_key_exists( $font_name, $websafe_fonts ) ) {
		return;
	}

	if ( isset( $fonts[ $font_name ]['parent_font'] ) ){
		$font_name = $fonts[ $font_name ]['parent_font'];
	}
	$font_character_set = $fonts[ $font_name ]['character_set'];

	$query_args = array(
		'family' => sprintf( '%s:%s',
			str_replace( ' ', '+', $font_name ),
			apply_filters( 'tm_builder_set_styles', $fonts[ $font_name ]['styles'], $font_name )
		),
		'subset' => apply_filters( 'tm_builder_set_character_set', $font_character_set, $font_name ),
	);

	$font_name_slug = sprintf(
		'tm-gf-%1$s',
		strtolower( str_replace( ' ', '-', $font_name ) )
	);

	wp_enqueue_style( $font_name_slug, esc_url( add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ) ), array(), null );
}
endif;

function tm_pb_maybe_add_advanced_styles() {
	$style = TM_Builder_Element::get_style();

	if ( $style ) {
		printf(
			'<style type="text/css" id="tm-builder-advanced-style">
				%1$s
			</style>',
			$style
		);
	}
}
add_action( 'wp_footer', 'tm_pb_maybe_add_advanced_styles' );

if ( ! function_exists( 'tm_pb_video_oembed_data_parse' ) ) :
function tm_pb_video_oembed_data_parse( $return, $data, $url ) {
	if ( isset( $data->thumbnail_url ) ) {
		return esc_url( str_replace( array('https://', 'http://'), '//', $data->thumbnail_url ), array('http') );
	} else {
		return false;
	}
}
endif;

if ( ! function_exists( 'tm_pb_check_oembed_provider' ) ) {
function tm_pb_check_oembed_provider( $url ) {
	require_once( ABSPATH . WPINC . '/class-oembed.php' );
	$oembed = _wp_oembed_get_object();
	return $oembed->get_provider( esc_url( $url ), array( 'discover' => false ) );
}
}

if ( ! function_exists( 'tm_pb_set_video_oembed_thumbnail_resolution' ) ) :
function tm_pb_set_video_oembed_thumbnail_resolution( $image_src, $resolution = 'default' ) {
	// Replace YouTube video thumbnails to high resolution.
	if ( 'high' === $resolution && false !== strpos( $image_src,  'hqdefault.jpg' ) ) {
		return str_replace( 'hqdefault.jpg', 'maxresdefault.jpg', $image_src );
	}

	return $image_src;
}
endif;

function tm_pb_video_get_oembed_thumbnail() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$video_url = esc_url( $_POST['tm_video_url'] );
	if ( false !== wp_oembed_get( $video_url ) ) {
		// Get image thumbnail
		add_filter( 'oembed_dataparse', 'tm_pb_video_oembed_data_parse', 10, 3 );
		// Save thumbnail
		$image_src = wp_oembed_get( $video_url );
		// Set back to normal
		remove_filter( 'oembed_dataparse', 'tm_pb_video_oembed_data_parse', 10, 3 );
		if ( '' === $image_src ) {
			die( -1 );
		}
		echo esc_url( $image_src );
	} else {
		die( -1 );
	}
	die();
}
add_action( 'wp_ajax_tm_pb_video_get_oembed_thumbnail', 'tm_pb_video_get_oembed_thumbnail' );

function tm_builder_widgets_init(){
	$tm_pb_widgets = get_theme_mod( 'tm_pb_widgets' );

	if ( $tm_pb_widgets['areas'] ) {
		foreach ( $tm_pb_widgets['areas'] as $id => $name ) {
			register_sidebar( array(
				'name' => sanitize_text_field( $name ),
				'id' => sanitize_text_field( $id ),
				'before_widget' => '<div id="%1$s" class="tm_pb_widget %2$s">',
				'after_widget' => '</div> <!-- end .tm_pb_widget -->',
				'before_title' => '<h4 class="widgettitle">',
				'after_title' => '</h4>',
			) );
		}
	}
}
add_action( 'widgets_init', 'tm_builder_widgets_init' );

if ( ! function_exists( 'tm_builder_get_widget_areas' ) ) :
function tm_builder_get_widget_areas() {
	global $wp_registered_sidebars;
	$tm_pb_widgets = get_theme_mod( 'tm_pb_widgets' );

	$output = '<select name="tm_pb_area" id="tm_pb_area">';

	foreach ( $wp_registered_sidebars as $id => $options ) {
		$selected = sprintf(
			'<%%= typeof( tm_pb_area ) !== "undefined" && "%1$s" === tm_pb_area ?  " selected=\'selected\'" : "" %%>',
			esc_html( $id )
		);

		$output .= sprintf(
			'<option value="%1$s"%2$s>%3$s</option>',
			esc_attr( $id ),
			$selected,
			esc_html( $options['name'] )
		);
	}

	$output .= '</select>';

	return $output;
}
endif;

if ( ! function_exists( 'tm_pb_export_layouts_interface' ) ) :
function tm_pb_export_layouts_interface() {
	if ( ! current_user_can( 'export' ) )
		wp_die( esc_html__( 'You do not have sufficient permissions to export the content of this site.', 'tm_builder' ) );

?>
	<div class="tm_pb_export_section">
		<h2 id="tm_page_title"><?php esc_html_e( 'Export Builder Layouts', 'tm_builder' ); ?></h2>
		<p><?php esc_html_e( 'When you click the button below WordPress will create an XML file for you to save to your computer.', 'tm_builder' ); ?></p>
		<p><?php esc_html_e( 'This format, which we call WordPress eXtended RSS or WXR, will contain all layouts you created using the Page Builder.', 'tm_builder' ); ?></p>
		<p><?php esc_html_e( 'Once you&#8217;ve saved the download file, you can use the Import function in another WordPress installation to import all layouts from this site.', 'tm_builder' ); ?></p>
		<p><?php esc_html_e( 'Select Templates you want to export:', 'tm_builder' ); ?></p>

		<form action="<?php echo esc_url( admin_url( 'export.php' ) ); ?>" method="get" id="tm-pb-export-layouts">
			<input type="hidden" name="download" value="true" />
			<input type="hidden" name="content" value="<?php echo esc_attr( TM_BUILDER_LAYOUT_POST_TYPE ); ?>" />

		<?php
			$all_template_types = array(
				'layout'  => esc_html__( 'Layouts', 'tm_builder' ),
				'section' => esc_html__( 'Sections', 'tm_builder' ),
				'row'     => esc_html__( 'Rows', 'tm_builder' ),
				'module'  => esc_html__( 'Modules', 'tm_builder' )
			);

			foreach( $all_template_types as $template_type => $template_name ) {
				$term = get_term_by( 'name', $template_type, 'layout_type', OBJECT );

				if ( ! $term ) {
					continue;
				}

				printf(
					'<label>
						<input type="checkbox" name="tm_pb_template_%1$s" value="%2$s" checked="checked" />
						%3$s
					</label>
					<br/><br/>',
					esc_attr( $template_type ),
					esc_attr( $term->term_id ),
					esc_html( $template_name )
				);
			}

			submit_button( esc_html__( 'Download Export File', 'tm_builder' ) );
		?>
		</form>
	</div>
	<div class="tm_export_section_link_wrap">
		<a href="#" id="tm_show_export_section"><?php esc_html_e( 'Export Layouts', 'tm_builder' ); ?></a>
	</div>
	<div class="clearfix"></div>
	<div class="tm_manage_library_cats">
		<a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=layout_category' ) ); ?>" id="tm_load_category_page"><?php esc_html_e( 'Manage Categories', 'tm_builder' ); ?></a>
	</div>
	<?php tm_pb_settings_form(); ?>
<?php }
endif;

/**
 * Add builder settings form
 */
function tm_pb_settings_form() {

	tm_pb_process_settings_form();

	$settings      = get_option( 'tm_builder_settings' );
	$api_key       = Cherry_Toolkit::get_arg( $settings, 'api_key', false );
	$default_types = array( 'post' => 'true', 'page' => 'true' );
	$saved_types   = Cherry_Toolkit::get_arg( $settings, 'allowed_post_types', $default_types );

	tm_builder_core()->interface_builder()->register_form(
		array(
			'tm_builder_settings' => array(
				'type'   => 'form',
				'action' => '',
			),
		)
	);

	tm_builder_core()->interface_builder()->register_section(
		array(
			'tm_builder' => array(
				'type'   => 'section',
				'parent' => 'tm_builder_settings',
				'title'  => esc_html__( 'Additional settings', 'tm_builder' ),
			),
		)
	);

	tm_builder_core()->interface_builder()->register_settings(
		array(
			'id'     => 'tm_pb_settings',
			'parent' => 'tm_builder',
		)
	);

	tm_builder_core()->interface_builder()->register_control(
		array(
			'type'        => 'text',
			'id'          => 'api_key',
			'name'        => 'api_key',
			'parent'      => 'tm_pb_settings',
			'title'       => esc_html__( 'Google maps API key', 'tm_builder' ),
			'description' => sprintf(
				esc_html__( 'Create own API key here %s', 'tm_builder' ),
				make_clickable( 'https://developers.google.com/maps/documentation/javascript/get-api-key' )
			),
			'value'       => $api_key,
			'placeholder' => esc_html__( 'Google maps API key', 'tm_builder' ),
		)
	);

	$post_types     = get_post_types( array( 'public' => true ), 'objects' );
	$post_types_opt = array();
	foreach ( $post_types as $type => $object ) {
		$post_types_opt[ $type ] = $object->label;
	}

	if ( isset( $post_types_opt['attachment'] ) ) {
		unset( $post_types_opt['attachment'] );
	}

	tm_builder_core()->interface_builder()->register_control(
		array(
			'type'    => 'checkbox',
			'id'      => 'allowed_post_types',
			'name'    => 'allowed_post_types',
			'title'   => esc_html__( 'Allowed post types', 'tm_builder' ),
			'parent'  => 'tm_pb_settings',
			'value'   => $saved_types,
			'options' => $post_types_opt,
		)
	);

	tm_builder_core()->interface_builder()->register_html(
		array(
			'form_html' => array(
				'type'   => 'html',
				'parent' => 'tm_pb_settings',
				'class'  => 'cherry-control form-button',
				'html'   => '<div class="custom-button save-button">
					<input type="hidden" name="tm_pb_process_settings" value="1">
					<button type="submit" class="button button-primary">' . esc_html__( 'Save', 'tm_builder' ) . '</button>
					</div>',
			),
		)
	);

	tm_builder_core()->interface_builder()->render();

}

/**
 * Process settings form
 *
 * @return void
 */
function tm_pb_process_settings_form() {

	if ( ! isset( $_POST['tm_pb_process_settings'] ) ) {
		return;
	}

	$options = get_option( 'tm_builder_settings' );

	if ( ! $options ) {
		$options = array();
	}

	$available_options = array(
		'api_key'            => 'esc_attr',
		'allowed_post_types' => 'array_filter',
	);

	foreach ( $available_options as $option => $sanitize_callback ) {
		if ( ! empty( $_POST[ $option ] ) && is_callable( $sanitize_callback ) ) {
			$options[ $option ] = call_user_func( $sanitize_callback, $_POST[ $option ] );
		} elseif ( ! empty( $_POST[ $option ] ) ) {
			$options[ $option ] = $_POST[ $option ];
		} else {
			$options[ $option ] = false;
		}
	}

	update_option( 'tm_builder_settings', $options );

}

add_action( 'export_wp', 'tm_pb_edit_export_query' );
function tm_pb_edit_export_query() {
	add_filter( 'query', 'tm_pb_edit_export_query_filter' );
}

function tm_pb_edit_export_query_filter( $query ) {
	// Apply filter only once
	remove_filter( 'query', 'tm_pb_edit_export_query_filter') ;

	global $wpdb;

	$content = ! empty( $_GET['content'] ) ? $_GET['content'] : '';

	if ( TM_BUILDER_LAYOUT_POST_TYPE !== $content ) {
		return $query;
	}

	$sql = '';
	$i = 0;
	$possible_types = array(
		'layout',
		'section',
		'row',
		'module',
		'fullwidth_section',
		'specialty_section',
		'fullwidth_module',
	);

	foreach ( $possible_types as $template_type ) {
		$selected_type = 'tm_pb_template_' . $template_type;

		if ( isset( $_GET[ $selected_type ] ) ) {
			if ( 0 === $i ) {
				$sql = " AND ( {$wpdb->term_relationships}.term_taxonomy_id = %d";
			} else {
				$sql .= " OR {$wpdb->term_relationships}.term_taxonomy_id = %d";
			}

			$sql_args[] = (int) $_GET[ $selected_type ];

			$i++;
		}
	}

	if ( '' !== $sql ) {
		$sql  .= ' )';

		$sql = sprintf(
			'SELECT ID FROM %4$s
			 INNER JOIN %3$s ON ( %4$s.ID = %3$s.object_id )
			 WHERE %4$s.post_type = "%1$s"
			 AND %4$s.post_status != "auto-draft"
			 %2$s', TM_BUILDER_LAYOUT_POST_TYPE,
			$sql,
			$wpdb->term_relationships,
			$wpdb->posts
		);

		$query = $wpdb->prepare( $sql, $sql_args );
	}

	return $query;
}

function tm_pb_setup_theme(){
	add_action( 'add_meta_boxes', 'tm_pb_add_custom_box', 5 );
}
add_action( 'init', 'tm_pb_setup_theme', 11 );

function tm_builder_set_post_type( $post_type = '' ) {
	global $tm_builder_post_type, $post;

	$tm_builder_post_type = ! empty( $post_type ) ? $post_type : $post->post_type;
}

function tm_builder_get_builder_post_types() {

	$settings    = get_option( 'tm_builder_settings' );
	$saved_types = Cherry_Toolkit::get_arg( $settings, 'allowed_post_types', array() );
	$post_types  = array();

	if ( ! $settings ) {
		$post_types = array(
			'page',
			'post',
		);
	} else {
		foreach ( $saved_types as $post_type => $value ) {
			if ( true === filter_var( $value, FILTER_VALIDATE_BOOLEAN ) ) {
				$post_types[] = $post_type;
			}
		}
	}

	$post_types = array_merge( array( 'tm_pb_layout' ), $post_types );

	return apply_filters( 'tm_builder_post_types', $post_types );
}

function tm_pb_metabox_settings_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( ! isset( $_POST['tm_pb_settings_nonce'] ) || ! wp_verify_nonce( $_POST['tm_pb_settings_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	if ( isset( $_POST['tm_pb_use_builder'] ) ) {
		update_post_meta( $post_id, '_tm_pb_use_builder', sanitize_text_field( $_POST['tm_pb_use_builder'] ) );
	} else {
		delete_post_meta( $post_id, '_tm_pb_use_builder' );
	}

	if ( isset( $_POST['tm_pb_old_content'] ) ) {
		update_post_meta( $post_id, '_tm_pb_old_content', $_POST['tm_pb_old_content'] );
	} else {
		delete_post_meta( $post_id, '_tm_pb_old_content' );
	}
}
add_action( 'save_post', 'tm_pb_metabox_settings_save_details', 10, 2 );

function _tm_pb_sanitize_code_module_content_regex( $matches ) {
	$sanitized_content = wp_kses_post( htmlspecialchars_decode( $matches[1] ) );
	$sanitized_shortcode = str_replace( $matches[1], $sanitized_content, $matches[0] );
	return $sanitized_shortcode;
}

function tm_pb_builder_post_content_capability_check( $content) {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		$content = preg_replace_callback('/\[tm_pb_code .*\](.*)\[\/tm-pb_code\]/mis', '_tm_pb_sanitize_code_module_content_regex', $content );
		$content = preg_replace_callback('/\[tm_pb_fullwidth_code .*\](.*)\[\/tm-pb_fullwidth_code\]/mis', '_tm_pb_sanitize_code_module_content_regex', $content );
	}

	return $content;
}
add_filter( 'content_save_pre', 'tm_pb_builder_post_content_capability_check' );

function tm_pb_before_main_editor( $post ) {
	if ( ! in_array( $post->post_type, tm_builder_get_builder_post_types() ) ) return;

	$_tm_builder_use_builder = get_post_meta( $post->ID, '_tm_pb_use_builder', true );
	$is_builder_used = 'on' === $_tm_builder_use_builder ? true : false;

	$builder_always_enabled = apply_filters('tm_builder_always_enabled', false, $post->post_type, $post );
	if ( $builder_always_enabled || 'tm_pb_layout' === $post->post_type ) {
		$is_builder_used = true;
		$_tm_builder_use_builder = 'on';
	}

	// Add button only if current user is allowed to use it otherwise display placeholder with all required data
	if ( tm_pb_is_allowed( 'divi_builder_control' ) ) {
		printf( '<div class="tm_pb_toggle_builder_wrapper%5$s"><a href="#" id="tm_pb_toggle_builder" data-builder="%2$s" data-editor="%3$s" class="button button-primary button-large%5$s%6$s">%1$s</a></div><div id="tm_pb_main_editor_wrap"%4$s>',
			( $is_builder_used ? esc_html__( 'Use Default Editor', 'tm_builder' ) : esc_html__( 'Use Power builder', 'tm_builder' ) ),
			esc_html__( 'Use Power builder', 'tm_builder' ),
			esc_html__( 'Use Default Editor', 'tm_builder' ),
			( $is_builder_used ? ' class="tm_pb_hidden"' : '' ),
			( $is_builder_used ? ' tm_pb_builder_is_used' : '' ),
			( $builder_always_enabled ? ' tm_pb_hidden' : '' )
		);
	} else {
		printf( '<div class="tm_pb_toggle_builder_wrapper%2$s"></div><div id="tm_pb_main_editor_wrap"%1$s>',
			( $is_builder_used ? ' class="tm_pb_hidden"' : '' ),
			( $is_builder_used ? ' tm_pb_builder_is_used' : '' )
		);
	}

	?>
	<p class="tm_pb_page_settings" style="display: none;">
		<?php wp_nonce_field( basename( __FILE__ ), 'tm_pb_settings_nonce' ); ?>
		<input type="hidden" id="tm_pb_use_builder" name="tm_pb_use_builder" value="<?php echo esc_attr( $_tm_builder_use_builder ); ?>" />
		<textarea id="tm_pb_old_content" name="tm_pb_old_content"><?php echo esc_attr( get_post_meta( $post->ID, '_tm_pb_old_content', true ) ); ?></textarea>
	</p>
	<?php
}
add_action( 'edit_form_after_title', 'tm_pb_before_main_editor' );

function tm_pb_after_main_editor( $post ) {
	if ( ! in_array( $post->post_type, tm_builder_get_builder_post_types() ) ) return;
	echo '</div> <!-- #tm_pb_main_editor_wrap -->';
}
add_action( 'edit_form_after_editor', 'tm_pb_after_main_editor' );

function tm_pb_admin_scripts_styles( $hook ) {
	global $typenow;

	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) return;

	/*
	 * Load the builder javascript and css files for custom post types
	 * custom post types can be added using tm_builder_post_types filter
	*/

	$post_types = tm_builder_get_builder_post_types();

	if ( isset( $typenow ) && in_array( $typenow, $post_types ) ){
		tm_pb_add_builder_page_js_css();
	}
}
add_action( 'admin_enqueue_scripts', 'tm_pb_admin_scripts_styles', 10, 1 );

function tm_pb_fix_builder_shortcodes( $content ) {
	// if the builder is used for the page, get rid of random p tags
	if ( is_singular() && 'on' === get_post_meta( get_the_ID(), '_tm_pb_use_builder', true ) ) {
		$content = tm_pb_fix_shortcodes( $content );
	}

	return $content;
}
add_filter( 'the_content', 'tm_pb_fix_builder_shortcodes' );

function tm_pb_current_user_can_lock() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$permission = tm_pb_is_allowed( 'lock_module' );
	$permission = json_encode( $permission );

	die( $permission );
}
add_action( 'wp_ajax_tm_pb_current_user_can_lock', 'tm_pb_current_user_can_lock' );


function tm_pb_show_all_layouts_built_for_post_type( $post_type ) {
	$similar_post_types = array(
		'post',
		'page',
		'project',
	);

	if ( in_array( $post_type, $similar_post_types ) ) {
		return $similar_post_types;
	}

	return $post_type;
}
add_filter( 'tm_pb_show_all_layouts_built_for_post_type', 'tm_pb_show_all_layouts_built_for_post_type' );

function tm_pb_show_all_layouts() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	printf( '
		<label for="tm_pb_load_layout_replace">
			<input name="tm_pb_load_layout_replace" type="checkbox" id="tm_pb_load_layout_replace" %2$s/>
			%1$s
		</label>',
		esc_html__( 'Replace the existing content with loaded layout', 'tm_builder' ),
		checked( get_theme_mod( 'tm_pb_replace_content', 'on' ), 'on', false )
	);

	$post_type = ! empty( $_POST['tm_layouts_built_for_post_type'] ) ? sanitize_text_field( $_POST['tm_layouts_built_for_post_type'] ) : 'post';
	$layouts_type = ! empty( $_POST['tm_load_layouts_type'] ) ? sanitize_text_field( $_POST['tm_load_layouts_type'] ) : 'predefined';

	$predefined_operator = 'predefined' === $layouts_type ? 'EXISTS' : 'NOT EXISTS';

	$post_type = apply_filters( 'tm_pb_show_all_layouts_built_for_post_type', $post_type, $layouts_type );

	$query_args = array(
		'meta_query'      => array(
			'relation' => 'AND',
			array(
				'key'     => '_tm_pb_predefined_layout',
				'value'   => 'on',
				'compare' => $predefined_operator,
			),
			array(
				'key'     => '_tm_pb_built_for_post_type',
				'value'   => $post_type,
				'compare' => 'IN',
			),
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    => array( 'section', 'row', 'module', 'fullwidth_section', 'specialty_section', 'fullwidth_module' ),
				'operator' => 'NOT IN',
			),
		),
		'post_type'       => TM_BUILDER_LAYOUT_POST_TYPE,
		'posts_per_page'  => '-1',
	);

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) :

		echo '<ul class="tm-pb-all-modules tm-pb-load-layouts">';

		while ( $query->have_posts() ) : $query->the_post();

			printf( '<li class="tm_pb_text" data-layout_id="%2$s">%1$s<span class="tm_pb_layout_buttons"><a href="#" class="button-primary tm_pb_layout_button_load">%3$s</a>%4$s</span></li>',
				esc_html( get_the_title() ),
				esc_attr( get_the_ID() ),
				esc_html__( 'Load', 'tm_builder' ),
				'predefined' !== $layouts_type ?
					sprintf( '<a href="#" class="button tm_pb_layout_button_delete">%1$s</a>',
						esc_html__( 'Delete', 'tm_builder' )
					)
					: ''
			);

		endwhile;

		echo '</ul>';
	endif;

	wp_reset_postdata();

	die();
}
add_action( 'wp_ajax_tm_pb_show_all_layouts', 'tm_pb_show_all_layouts' );


function tm_pb_get_saved_templates() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$templates_data = array();

	$layout_type = ! empty( $_POST['tm_layout_type'] ) ? sanitize_text_field( $_POST['tm_layout_type'] ) : 'layout';
	$module_width = ! empty( $_POST['tm_module_width'] ) && 'module' === $layout_type ? sanitize_text_field( $_POST['tm_module_width'] ) : '';
	$additional_condition = '' !== $module_width ?
		array(
				'taxonomy' => 'module_width',
				'field'    => 'slug',
				'terms'    =>  $module_width,
			) : '';
	$is_global = ! empty( $_POST['tm_is_global'] ) ? sanitize_text_field( $_POST['tm_is_global'] ) : 'false';
	$global_operator = 'global' === $is_global ? 'IN' : 'NOT IN';

	$meta_query = array();
	$specialty_query = ! empty( $_POST['tm_specialty_columns'] ) && 'row' === $layout_type ? sanitize_text_field( $_POST['tm_specialty_columns'] ) : '0';

	if ( '0' !== $specialty_query ) {
		$columns_val = '3' === $specialty_query ? array( '4_4', '1_2,1_2', '1_3,1_3,1_3' ) : array( '4_4', '1_2,1_2' );
		$meta_query[] = array(
			'key'     => '_tm_pb_row_layout',
			'value'   => $columns_val,
			'compare' => 'IN',
		);
	}

	$post_type = ! empty( $_POST['tm_post_type'] ) ? sanitize_text_field( $_POST['tm_post_type'] ) : 'post';
	$post_type = apply_filters( 'tm_pb_show_all_layouts_built_for_post_type', $post_type, $layout_type );
	$meta_query[] = array(
		'key'     => '_tm_pb_built_for_post_type',
		'value'   => $post_type,
		'compare' => 'IN',
	);


	$query = new WP_Query( array(
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    =>  $layout_type,
			),
			array(
				'taxonomy' => 'scope',
				'field'    => 'slug',
				'terms'    => array( 'global' ),
				'operator' => $global_operator,
			),
			$additional_condition,
		),
		'post_type'       => TM_BUILDER_LAYOUT_POST_TYPE,
		'posts_per_page'  => '-1',
		'meta_query'      => $meta_query,
	) );

	wp_reset_postdata();

	if ( ! empty ( $query->posts ) ) {
		foreach( $query->posts as $single_post ) {

			if ( 'module' === $layout_type ) {
				$module_type = get_post_meta( $single_post->ID, '_tm_pb_module_type', true );
				$module_icon = get_post_meta( $single_post->ID, '_tm_pb_module_icon', true );
			} else {
				$module_type = '';
				$module_icon = '';
			}

			// add only modules allowed for current user
			if ( '' === $module_type || tm_pb_is_allowed( $module_type ) ) {
				$categories = wp_get_post_terms( $single_post->ID, 'layout_category' );
				$categories_processed = array();

				if ( ! empty( $categories ) ) {
					foreach( $categories as $category_data ) {
						$categories_processed[] = esc_html( $category_data->slug );
					}
				}

				$templates_data[] = array(
					'ID'          => $single_post->ID,
					'title'       => esc_html( $single_post->post_title ),
					'shortcode'   => $single_post->post_content,
					'is_global'   => $is_global,
					'layout_type' => $layout_type,
					'module_icon' => $module_icon,
					'module_type' => $module_type,
					'categories'  => $categories_processed,
				);
			}
		}
	}
	if ( empty( $templates_data ) ) {
		$templates_data = array( 'error' => esc_html__( 'You have not saved any items to your library yet. Once an item has been saved to your library, it will appear here for easy use.', 'tm_builder' ) );
	}

	$json_templates = json_encode( $templates_data );

	die( $json_templates );
}
add_action( 'wp_ajax_tm_pb_get_saved_templates', 'tm_pb_get_saved_templates' );

function tm_pb_add_template_meta() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = ! empty( $_POST['tm_post_id'] ) ? sanitize_text_field( $_POST['tm_post_id'] ) : '';
	$value = ! empty( $_POST['tm_meta_value'] ) ? sanitize_text_field( $_POST['tm_meta_value'] ) : '';
	$custom_field = ! empty( $_POST['tm_custom_field'] ) ? sanitize_text_field( $_POST['tm_custom_field'] ) : '';

	if ( '' !== $post_id ){
		update_post_meta( $post_id, $custom_field, $value );
	}
}
add_action( 'wp_ajax_tm_pb_add_template_meta', 'tm_pb_add_template_meta' );

// generate the html for "Add new template" Modal in Library
if ( ! function_exists( 'tm_pb_generate_new_layout_modal' ) ) {
	function tm_pb_generate_new_layout_modal() {
		$template_type_option_output = '';
		$template_module_tabs_option_output = '';
		$template_global_option_output = '';
		$layout_cat_option_output = '';

		$template_type_options = apply_filters( 'tm_pb_new_layout_template_types', array(
			'module'            => esc_html__( 'Module', 'tm_builder' ),
			'fullwidth_module'  => esc_html__( 'Fullwidth Module', 'tm_builder' ),
			'row'               => esc_html__( 'Row', 'tm_builder' ),
			'section'           => esc_html__( 'Section', 'tm_builder' ),
			'fullwidth_section' => esc_html__( 'Fullwidth Section', 'tm_builder' ),
			'specialty_section' => esc_html__( 'Specialty Section', 'tm_builder' ),
			'layout'            => esc_html__( 'Layout', 'tm_builder' ),
		) );

		$template_module_tabs_options = apply_filters( 'tm_pb_new_layout_module_tabs', array(
			'general'  => esc_html__( 'Include General Settings', 'tm_builder' ),
			'advanced' => esc_html__( 'Include Advanced Design Settings', 'tm_builder' ),
			'css'      => esc_html__( 'Include Custom CSS', 'tm_builder' ),
		) );

		// construct output for the template type option
		if ( ! empty( $template_type_options ) ) {
			$template_type_option_output = sprintf(
				'<br><label>%1$s:</label>
				<select id="new_template_type">',
				esc_html__( 'Template Type', 'tm_builder' )
			);

			foreach( $template_type_options as $option_id => $option_name ) {
				$template_type_option_output .= sprintf(
					'<option value="%1$s">%2$s</option>',
					esc_attr( $option_id ),
					esc_html( $option_name )
				);
			}

			$template_type_option_output .= '</select>';
		}

		// construct output for the module tabs option
		if ( ! empty( $template_module_tabs_options ) ) {
			$template_module_tabs_option_output = '<br><div class="tm_module_tabs_options">';

			foreach( $template_module_tabs_options as $option_id => $option_name ) {
				$template_module_tabs_option_output .= sprintf(
					'<label>%1$s<input type="checkbox" value="%2$s" id="tm_pb_template_general" checked /></label>',
					esc_html( $option_name ),
					esc_attr( $option_id )
				);
			}

			$template_module_tabs_option_output .= '</div>';
		}

		$template_global_option_output = apply_filters( 'tm_pb_new_layout_global_option', sprintf(
			'<br><label>%1$s<input type="checkbox" value="global" id="tm_pb_template_global"></label>',
			esc_html__( 'Global', 'tm_builder' )
		) );

		// construct output for the layout category option
		$layout_cat_option_output .= sprintf(
			'<br><label>%1$s</label>',
			esc_html__( 'Select category(ies) for new template or type a new name ( optional )', 'tm_builder' )
		);

		$layout_categories = apply_filters( 'tm_pb_new_layout_cats_array', get_terms( 'layout_category', array( 'hide_empty' => false ) ) );
		if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
			$layout_cat_option_output .= '<div class="layout_cats_container">';

			foreach( $layout_categories as $category ) {
				$layout_cat_option_output .= sprintf(
					'<label>%1$s<input type="checkbox" value="%2$s"/></label>',
					esc_html( $category->name ),
					esc_attr( $category->term_id )
				);
			}

			$layout_cat_option_output .= '</div>';
		}

		$layout_cat_option_output .= '<input type="text" value="" id="tm_pb_new_cat_name" class="regular-text">';

		$output = sprintf(
			'<div class="tm_pb_modal_overlay tm_modal_on_top tm_pb_new_template_modal">
				<div class="tm_pb_prompt_modal">
					<h2>%1$s</h2>
					<div class="tm_pb_prompt_modal_inside">
						<label>%2$s:</label>
							<input type="text" value="" id="tm_pb_new_template_name" class="regular-text">
							%7$s
							%3$s
							%4$s
							%5$s
							%6$s
							%8$s
							<input id="tm_builder_layout_built_for_post_type" type="hidden" value="page">
					</div>
					<a href="#" class="tm_pb_prompt_dont_proceed tm-pb-modal-close"></a>
					<div class="tm_pb_prompt_buttons">
						<span class="spinner"></span>
						<input type="submit" class="tm_pb_create_template button-primary tm_pb_prompt_proceed">
					</div>
				</div>
			</div>',
			esc_html__( 'New Template Settings', 'tm_builder' ),
			esc_html__( 'Template Name', 'tm_builder' ),
			$template_type_option_output,
			$template_module_tabs_option_output,
			$template_global_option_output, //#5
			$layout_cat_option_output,
			apply_filters( 'tm_pb_new_layout_before_options', '' ),
			apply_filters( 'tm_pb_new_layout_after_options', '' )
		);

		return apply_filters( 'tm_pb_new_layout_modal_output', $output );
	}
}

if ( ! function_exists( 'tm_pb_add_new_layout' ) ) {
	function tm_pb_add_new_layout() {
		if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die( -1 );

		if ( ! current_user_can( 'edit_posts' ) ) {
			die( -1 );
		}

		$fields_data = isset( $_POST['tm_layout_options'] ) ? $_POST['tm_layout_options'] : '';

		if ( '' === $fields_data ) {
			die();
		}

		$fields_data_json = str_replace( '\\', '',  $fields_data );
		$fields_data_array = json_decode( $fields_data_json, true );
		$processed_data_array = array();

		// prepare array with fields data in convenient format
		if ( ! empty( $fields_data_array ) ) {
			foreach ( $fields_data_array as $index => $field_data ) {
				$processed_data_array[ $field_data['field_id'] ] = $field_data['field_val'];
			}
		}

		$processed_data_array = apply_filters( 'tm_pb_new_layout_data_from_form', $processed_data_array, $fields_data_array );

		if ( empty( $processed_data_array ) ) {
			die();
		}

		$args = array(
			'layout_type'          => ! empty( $processed_data_array['new_template_type'] ) ? sanitize_text_field( $processed_data_array['new_template_type'] ) : 'layout',
			'layout_selected_cats' => ! empty( $processed_data_array['selected_cats'] ) ? sanitize_text_field( $processed_data_array['selected_cats'] ) : '',
			'built_for_post_type'  => ! empty( $processed_data_array['tm_builder_layout_built_for_post_type'] ) ? sanitize_text_field( $processed_data_array['tm_builder_layout_built_for_post_type'] ) : 'page',
			'layout_new_cat'       => ! empty( $processed_data_array['tm_pb_new_cat_name'] ) ? sanitize_text_field( $processed_data_array['tm_pb_new_cat_name'] ) : '',
			'columns_layout'       => ! empty( $processed_data_array['tm_columns_layout'] ) ? sanitize_text_field( $processed_data_array['tm_columns_layout'] ) : '0',
			'module_type'          => ! empty( $processed_data_array['tm_module_type'] ) ? sanitize_text_field( $processed_data_array['tm_module_type'] ) : 'tm_pb_unknown',
			'layout_scope'         => ! empty( $processed_data_array['tm_pb_template_global'] ) ? sanitize_text_field( $processed_data_array['tm_pb_template_global'] ) : 'not_global',
			'module_width'         => 'regular',
			'layout_content'       => ! empty( $processed_data_array['template_shortcode'] ) ? $processed_data_array['template_shortcode'] : '',
			'layout_name'          => ! empty( $processed_data_array['tm_pb_new_template_name'] ) ? sanitize_text_field( $processed_data_array['tm_pb_new_template_name'] ) : '',
		);

		// construct the initial shortcode for new layout
		switch ( $args['layout_type'] ) {
			case 'row' :
				$args['layout_content'] = '[tm_pb_row template_type="row"][/tm-pb_row]';
				break;
			case 'section' :
				$args['layout_content'] = '[tm_pb_section template_type="section"][tm_pb_row][/tm-pb_row][/tm-pb_section]';
				break;
			case 'module' :
				$args['layout_content'] = sprintf( '[tm_pb_module_placeholder selected_tabs="%1$s"]', ! empty( $processed_data_array['selected_tabs'] ) ? $processed_data_array['selected_tabs'] : 'all' );
				break;
			case 'fullwidth_module' :
				$args['layout_content'] = sprintf( '[tm_pb_fullwidth_module_placeholder selected_tabs="%1$s"]', ! empty( $processed_data_array['selected_tabs'] ) ? $processed_data_array['selected_tabs'] : 'all' );
				$args['module_width'] = 'fullwidth';
				$args['layout_type'] = 'module';
				break;
			case 'fullwidth_section' :
				$args['layout_content'] = '[tm_pb_section template_type="section" fullwidth="on"][/tm-pb_section]';
				$args['layout_type'] = 'section';
				break;
			case 'specialty_section' :
				$args['layout_content'] = '[tm_pb_section template_type="section" specialty="on" skip_module="true" specialty_placeholder="true"][/tm-pb_section]';
				$args['layout_type'] = 'section';
				break;
		}

		$new_layout_meta = tm_pb_submit_layout( apply_filters( 'tm_pb_new_layout_args', $args ) );
		die( $new_layout_meta );
	}
}
add_action( 'wp_ajax_tm_pb_add_new_layout', 'tm_pb_add_new_layout' );

if ( ! function_exists( 'tm_pb_submit_layout' ) ) {
	function tm_pb_submit_layout( $args ) {
		if ( empty( $args ) ) {
			return;
		}

		$layout_cats_processed = array();

		if ( '' !== $args['layout_selected_cats'] ) {
			$layout_cats_array = explode( ',', $args['layout_selected_cats'] );
			$layout_cats_processed = array_map( 'intval', $layout_cats_array );
		}

		$meta = array();

		if ( 'row' === $args['layout_type'] && '0' !== $args['columns_layout'] ) {
			$meta = array_merge( $meta, array( '_tm_pb_row_layout' => $args['columns_layout'] ) );
		}

		if ( 'module' === $args['layout_type'] ) {
			$meta = array_merge(
				$meta,
				array(
					'_tm_pb_module_type' => $args['module_type'],
					'_tm_pb_module_icon' => $args['module_icon'],
				)
			);
		}

		//tm-layouts_built_for_post_type
		$meta = array_merge( $meta, array( '_tm_pb_built_for_post_type' => $args['built_for_post_type'] ) );

		$tax_input = array(
			'scope'           => $args['layout_scope'],
			'layout_type'     => $args['layout_type'],
			'module_width'    => $args['module_width'],
			'layout_category' => $layout_cats_processed,
		);

		$new_layout_id = tm_pb_create_layout( $args['layout_name'], $args['layout_content'], $meta, $tax_input, $args['layout_new_cat'] );
		$new_post_data['post_id'] = $new_layout_id;

		$new_post_data['edit_link'] = htmlspecialchars_decode( get_edit_post_link( $new_layout_id ) );
		$json_post_data = json_encode( $new_post_data );

		return $json_post_data;
	}
}

function tm_pb_save_layout() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	if ( empty( $_POST['tm_layout_name'] ) ) {
		die();
	}

	$args = array(
		'layout_type'          => isset( $_POST['tm_layout_type'] ) ? sanitize_text_field( $_POST['tm_layout_type'] ) : 'layout',
		'layout_selected_cats' => isset( $_POST['tm_layout_cats'] ) ? sanitize_text_field( $_POST['tm_layout_cats'] ) : '',
		'built_for_post_type'  => isset( $_POST['tm_post_type'] ) ? sanitize_text_field( $_POST['tm_post_type'] ) : 'page',
		'layout_new_cat'       => isset( $_POST['tm_layout_new_cat'] ) ? sanitize_text_field( $_POST['tm_layout_new_cat'] ) : '',
		'columns_layout'       => isset( $_POST['tm_columns_layout'] ) ? sanitize_text_field( $_POST['tm_columns_layout'] ) : '0',
		'module_type'          => isset( $_POST['tm_module_type'] ) ? sanitize_text_field( $_POST['tm_module_type'] ) : 'tm_pb_unknown',
		'module_icon'          => isset( $_POST['tm_module_icon'] ) ? esc_attr( $_POST['tm_module_icon'] ) : '',
		'layout_scope'         => isset( $_POST['tm_layout_scope'] ) ? sanitize_text_field( $_POST['tm_layout_scope'] ) : 'not_global',
		'module_width'         => isset( $_POST['tm_module_width'] ) ? sanitize_text_field( $_POST['tm_module_width'] ) : 'regular',
		'layout_content'       => isset( $_POST['tm_layout_content'] ) ? $_POST['tm_layout_content'] : '',
		'layout_name'          => isset( $_POST['tm_layout_name'] ) ? sanitize_text_field( $_POST['tm_layout_name'] ) : '',
	);

	$new_layout_meta = tm_pb_submit_layout( $args );
	die( $new_layout_meta );
}
add_action( 'wp_ajax_tm_pb_save_layout', 'tm_pb_save_layout' );

function tm_pb_get_global_module() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = isset( $_POST['tm_global_id'] ) ? $_POST['tm_global_id'] : '';

	if ( '' !== $post_id ) {
		$query = new WP_Query( array(
			'p'         => (int) $post_id,
			'post_type' => TM_BUILDER_LAYOUT_POST_TYPE
		) );

		wp_reset_postdata();

		if ( !empty( $query->post ) ) {
			$global_shortcode['shortcode'] = $query->post->post_content;
		}
	}

	if ( empty( $global_shortcode ) ) {
		$global_shortcode['error'] = 'nothing';
	}

	$json_post_data = json_encode( $global_shortcode );

	die( $json_post_data );
}
add_action( 'wp_ajax_tm_pb_get_global_module', 'tm_pb_get_global_module' );

function tm_pb_update_layout() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = isset( $_POST['tm_template_post_id'] ) ? $_POST['tm_template_post_id'] : '';
	$new_content = isset( $_POST['tm_layout_content'] ) ? tm_pb_builder_post_content_capability_check( $_POST['tm_layout_content'] ) : '';

	if ( '' !== $post_id ) {
		$update = array(
			'ID'           => $post_id,
			'post_content' => $new_content,
		);

		wp_update_post( $update );
	}

	die();
}
add_action( 'wp_ajax_tm_pb_update_layout', 'tm_pb_update_layout' );

function tm_pb_load_layout() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$layout_id = (int) $_POST['tm_layout_id'];

	if ( '' === $layout_id ) die( -1 );

	$replace_content = isset( $_POST['tm_replace_content'] ) && 'on' === $_POST['tm_replace_content'] ? 'on' : 'off';

	set_theme_mod( 'tm_pb_replace_content', $replace_content );

	$layout = get_post( $layout_id );

	if ( $layout )
		echo $layout->post_content;

	die();
}
add_action( 'wp_ajax_tm_pb_load_layout', 'tm_pb_load_layout' );

function tm_pb_delete_layout() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_others_posts' ) ) {
		die( -1 );
	}

	$layout_id = (int) $_POST['tm_layout_id'];

	if ( '' === $layout_id ) die( -1 );

	wp_delete_post( $layout_id );

	die();
}
add_action( 'wp_ajax_tm_pb_delete_layout', 'tm_pb_delete_layout' );

function tm_pb_get_backbone_templates() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_type = sanitize_text_field( $_POST['tm_post_type'] );
	$start_from = isset( $_POST['tm_templates_start_from'] ) ? sanitize_text_field( $_POST['tm_templates_start_from'] ) : 0;
	$amount = TM_BUILDER_AJAX_TEMPLATES_AMOUNT;

	// get the portion of templates
	$result = json_encode( TM_Builder_Element::output_templates( $post_type, $start_from, $amount ) );

	die( $result );
}
add_action( 'wp_ajax_tm_pb_get_backbone_templates', 'tm_pb_get_backbone_templates' );

if ( ! function_exists( 'tm_pb_create_layout' ) ) :
function tm_pb_create_layout( $name, $content, $meta = array(), $tax_input = array(), $new_category = '' ) {
	$layout = array(
		'post_title'   => sanitize_text_field( $name ),
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => TM_BUILDER_LAYOUT_POST_TYPE,
	);

	$layout_id = wp_insert_post( $layout );

	if ( !empty( $meta ) ) {
		foreach ( $meta as $meta_key => $meta_value ) {
			add_post_meta( $layout_id, $meta_key, sanitize_text_field( $meta_value ) );
		}
	}
	if ( '' !== $new_category ) {
		$new_term_id = wp_insert_term( $new_category, 'layout_category' );
		$tax_input['layout_category'][] = (int) $new_term_id['term_id'];
	}

	if ( ! empty( $tax_input ) ) {
		foreach( $tax_input as $taxonomy => $terms ) {
			wp_set_post_terms( $layout_id, $terms, $taxonomy );
		}
	}

	return $layout_id;
}
endif;

/**
 * Get layout type of given post ID
 * @return string|bool
 */
if ( ! function_exists( 'tm_pb_get_layout_type' ) ) :
function tm_pb_get_layout_type( $post_id ) {
	// Get taxonomies
	$layout_type_data = wp_get_post_terms( $post_id, 'layout_type' );

	if ( empty( $layout_type_data ) ) {
		return false;
	}

	// Pluck name out of taxonomies
	$layout_type_array = wp_list_pluck( $layout_type_data, 'name' );

	// Logically, a layout only have one layout type.
	$layout_type = implode( "|", $layout_type_array );

	return $layout_type;
}
endif;

if ( ! function_exists( 'tm_pb_enqueue_app_js' ) ) {
	/**
	 * Enqueue app.js and it's dependencies
	 */
	function tm_pb_enqueue_app_js() {

		// Application sources
		$sources = array(
			'tm-pb-module-model'                                => '/models/tm-pagebuilder-module.js',
			'tm-pb-saved-template-model'                        => '/models/tm-pagebuilder-saved-template.js',
			'tm-pb-history-model'                               => '/models/tm-pagebuilder-history.js',
			'tm-pb-layout-model'                                => '/models/tm-pagebuilder-layout.js',
			'tm-pb-modules-collection'                          => '/collections/tm-pagebuilder-modules.js',
			'tm-pb-saved-templates-collection'                  => '/collections/tm-pagebuilder-saved-templates.js',
			'tm-pb-histories-collection'                        => '/collections/tm-pagebuilder-histories.js',
			'tm-pb-templates-view'                              => '/views/tm-pagebuilder-templates-view.js',
			'tm-pb-single-template-view'                        => '/views/tm-pagebuilder-single-template-view.js',
			'tm-pb-templates-modal'                             => '/views/tm-pagebuilder-templates-modal.js',
			'tm-pb-section-view'                                => '/views/tm-pagebuilder-section-view.js',
			'tm-pb-row-view'                                    => '/views/tm-pagebuilder-row-view.js',
			'tm-pb-modal-view'                                  => '/views/tm-pagebuilder-modal-view.js',
			'tm-pb-column-view'                                 => '/views/tm-pagebuilder-column-view.js',
			'tm-pb-column-settings-view'                        => '/views/tm-pagebuilder-column-settings-view.js',
			'tm-pb-save-layout-settings-view'                   => '/views/tm-pagebuilder-save-layout-settings-view.js',
			'tm-pb-modules-view'                                => '/views/tm-pagebuilder-modules-view.js',
			'tm-pb-module-settings-view'                        => '/views/tm-pagebuilder-module-settings-view.js',
			'tm-pb-advanced-module-settings-view'               => '/views/tm-pagebuilder-advanced-module-settings-view.js',
			'tm-pb-advanced-module-setting-view'                => '/views/tm-pagebuilder-advanced-module-setting-view.js',
			'tm-pb-advanced-module-setting-title-view'          => '/views/tm-pagebuilder-advanced-module-setting-title-view.js',
			'tm-pb-advanced-module-setting-edit-view-container' => '/views/tm-pagebuilder-advanced-module-setting-edit-view-container.js',
			'tm-pb-advanced-module-setting-edit-view'           => '/views/tm-pagebuilder-advanced-module-setting-edit-view.js',
			'tm-pb-block-module-view'                           => '/views/tm-pagebuilder-block-module-view.js',
			'tm-pb-right-click-options-view'                    => '/views/tm-pagebuilder-right-click-options-view.js',
			'tm-pb-visualize-histories-view'                    => '/views/tm-pagebuilder-visualize-histories-view.js',
			'tm-pb-app-view'                                    => '/views/tm-pagebuilder-app-view.js',
		);

		wp_enqueue_script(
			'tm-pb-main',
			TM_BUILDER_URI . '/framework/admin/assets/js/app/tm-pagebuilder.js',
			array(),
			TM_BUILDER_VERSION,
			true
		);

		// Enqueue app files
		foreach( $sources as $name => $src ) {
			wp_enqueue_script(
				$name,
				TM_BUILDER_URI . '/framework/admin/assets/js/app' . $src,
				array(),
				TM_BUILDER_VERSION,
				true
			);
		}

		wp_enqueue_script(
			'tm_pb_admin_js',
			TM_BUILDER_URI . '/framework/admin/assets/js/app/app.js',
			array(
				'jquery',
				'jquery-ui-core',
				'underscore',
				'backbone',
				'tm-pb-main'
			),
			TM_BUILDER_VERSION,
			true
		);

	}
}

if ( ! function_exists( 'tm_pb_add_builder_page_js_css' ) ) {
	/**
	 * wp enueue assets
	 * @TODO provide more descriptive description
	 */
	function tm_pb_add_builder_page_js_css(){
		global $typenow, $post;

		if ( tm_is_yoast_seo_plugin_active() ) {
			// save the original content of $post variable
			$post_original = $post;
			// get the content for yoast
			$post_content_processed = do_shortcode( $post->post_content );
			// set the $post to the original content to make sure it wasn't changed by do_shortcode()
			$post = $post_original;
		}

		wp_enqueue_style( 'font-awesome', TM_BUILDER_URI . '/framework/assets/css/font-awesome.min.css', array(), '4.6.1' );
		wp_enqueue_style( 'tm_pb_admin_css', TM_BUILDER_URI .'/framework/admin/assets/css/style.css', array(), TM_BUILDER_VERSION );
		wp_enqueue_style( 'tm_pb_admin_date_css', TM_BUILDER_URI . '/framework/admin/assets/css/libs/jquery-ui-1.10.4.custom.css', array(), TM_BUILDER_VERSION );

		// we need some post data when editing saved templates.
		if ( 'tm_pb_layout' === $typenow ) {
			$template_scope = wp_get_object_terms( get_the_ID(), 'scope' );
			$is_global_template = ! empty( $template_scope[0] ) ? $template_scope[0]->slug : 'regular';
			$post_id = get_the_ID();

			// Check whether it's a Global item's page and display wp error if Global items disabled for current user
			if ( ! tm_pb_is_allowed( 'edit_global_library' ) && 'global' === $is_global_template ) {
				wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'tm_builder' ) );
			}

			$built_for_post_type = get_post_meta( get_the_ID(), '_tm_pb_built_for_post_type', true );

			if ( '' === $built_for_post_type ) {
				$built_for_post_type = 'page';
			}

			$post_type = apply_filters( 'tm_pb_built_for_post_type', $built_for_post_type, get_the_ID() );
		} else {
			$is_global_template = '';
			$post_id = '';
			$post_type = $typenow;
		}

		// we need this data to create the filter when adding saved modules
		$layout_categories = get_terms( 'layout_category' );
		$layout_cat_data = array();
		$layout_cat_data_json = '';

		if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
			foreach( $layout_categories as $category ) {
				$layout_cat_data[] = array(
					'slug' => $category->slug,
					'name' => $category->name,
				);
			}
		}

		if ( ! empty( $layout_cat_data ) ) {
			$layout_cat_data_json = json_encode( $layout_cat_data );
		}

		$preview_url = esc_url( home_url( '/' ) );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'underscore' );
		wp_enqueue_script( 'backbone' );

		wp_enqueue_script( 'google-maps-api', tm_get_maps_api_url(), array(), '3', true );

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script(
			'wp-color-picker-alpha',
			TM_BUILDER_URI . '/framework/admin/assets/js/libs/wp-color-picker-alpha.min.js',
			array(
				'jquery',
				'wp-color-picker'
			),
			TM_BUILDER_VERSION,
			true
		);

		wp_enqueue_script(
			'tm_pb_admin_date_js',
			TM_BUILDER_URI . '/framework/admin/assets/js/libs/jquery-ui-1.10.4.custom.min.js',
			array(
				'jquery'
			),
			TM_BUILDER_VERSION,
			true
		);

		wp_enqueue_script(
			'tm_pb_admin_date_addon_js',
			TM_BUILDER_URI . '/framework/admin/assets/js/libs/jquery-ui-timepicker-addon.js',
			array(
				'tm_pb_admin_date_js'
			),
			TM_BUILDER_VERSION,
			true
		);

		wp_enqueue_script(
			'validation',
			TM_BUILDER_URI . '/framework/admin/assets/js/libs/jquery.validate.js',
			array(
				'jquery'
			),
			TM_BUILDER_VERSION,
			true
		);

		wp_enqueue_script(
			'minicolors',
			TM_BUILDER_URI . '/framework/admin/assets/js/libs/jquery.minicolors.js',
			array(
				'jquery'
			),
			TM_BUILDER_VERSION,
			true
		);

		tm_pb_enqueue_app_js();

		/* @TODO code refactoring required */
		wp_localize_script( 'tm-pb-main', 'tm_pb_options', apply_filters( 'tm_pb_options_builder', array(
			'debug'                                    => defined( 'WP_DEBUG' ) ? WP_DEBUG : false,
			'ajaxurl'                                  => admin_url( 'admin-ajax.php' ),
			'home_url'                                 => home_url(),
			'preview_url'                              => add_query_arg( 'tm_pb_preview', 'true', $preview_url ),
			'tm_admin_load_nonce'                      => wp_create_nonce( 'tm_admin_load_nonce' ),
			'images_uri'                               => TM_BUILDER_URI .'/images',
			'post_type'                                => $post_type,
			'tm_builder_module_parent_shortcodes'      => TM_Builder_Element::get_parent_shortcodes( $post_type ),
			'tm_builder_module_child_shortcodes'       => TM_Builder_Element::get_child_shortcodes( $post_type ),
			'tm_builder_module_raw_content_shortcodes' => TM_Builder_Element::get_raw_content_shortcodes( $post_type ),
			'tm_builder_modules'                       => TM_Builder_Element::get_modules_js_array( $post_type ),
			'tm_builder_modules_count'                 => TM_Builder_Element::get_modules_count( $post_type ),
			'tm_builder_modules_with_children'         => TM_Builder_Element::get_shortcodes_with_children( $post_type ),
			'tm_builder_templates_amount'              => TM_BUILDER_AJAX_TEMPLATES_AMOUNT,
			'default_initial_column_type'              => apply_filters( 'tm_builder_default_initial_column_type', '4_4' ),
			'default_initial_text_module'              => apply_filters( 'tm_builder_default_initial_text_module', 'tm_pb_text' ),
			'section_only_row_dragged_away'            => esc_html__( 'The section should have at least one row.', 'tm_builder' ),
			'fullwidth_module_dragged_away'            => esc_html__( 'Fullwidth module can\'t be used outside of the Fullwidth Section.', 'tm_builder' ),
			'stop_dropping_3_col_row'                  => esc_html__( '3 column row can\'t be used in this column.', 'tm_builder' ),
			'preview_image'                            => esc_html__( 'Preview', 'tm_builder' ),
			'empty_admin_label'                        => esc_html__( 'Module', 'tm_builder' ),
			'video_module_image_error'                 => esc_html__( 'Still images cannot be generated from this video service and/or this video format', 'tm_builder' ),
			'geocode_error'                            => esc_html__( 'Geocode was not successful for the following reason', 'tm_builder' ),
			'geocode_error_2'                          => esc_html__( 'Geocoder failed due to', 'tm_builder' ),
			'no_results'                               => esc_html__( 'No results found', 'tm_builder' ),
			'all_tab_options_hidden'                   => esc_html__( 'No available options for this configuration.', 'tm_builder' ),
			'update_global_module'                     => esc_html__( 'You\'re about to update global module. This change will be applied to all pages where you use this module. Press OK if you want to update this module', 'tm_builder' ),
			'global_row_alert'                         => esc_html__( 'You cannot add global rows into global sections', 'tm_builder' ),
			'global_module_alert'                      => esc_html__( 'You cannot add global modules into global sections or rows', 'tm_builder' ),
			'all_cat_text'                             => esc_html__( 'All Categories', 'tm_builder' ),
			'is_global_template'                       => $is_global_template,
			'template_post_id'                         => $post_id,
			'layout_categories'                        => $layout_cat_data_json,
			'map_pin_address_error'                    => esc_html__( 'Map Pin Address cannot be empty', 'tm_builder' ),
			'map_pin_address_invalid'                  => esc_html__( 'Invalid Pin and address data. Please try again.', 'tm_builder' ),
			'locked_section_permission_alert'          => esc_html__( 'You do not have permission to unlock this section.', 'tm_builder' ),
			'locked_row_permission_alert'              => esc_html__( 'You do not have permission to unlock this row.', 'tm_builder' ),
			'locked_module_permission_alert'           => esc_html__( 'You do not have permission to unlock this module.', 'tm_builder' ),
			'locked_item_permission_alert'             => esc_html__( 'You do not have permission to perform this task.', 'tm_builder' ),
			'localstorage_unavailability_alert'        => esc_html__( 'Unable to perform copy/paste process due to inavailability of localStorage feature in your browser. Please use latest modern browser (Chrome, Firefox, or Safari) to perform copy/paste process', 'tm_builder' ),
			'product_version'                          => TM_BUILDER_VERSION,
			'modules_count'                            => tm_builder_modules_loader()->modules_count(),
			'verb'          => array(
				'did'       => esc_html__( 'Did', 'tm_builder' ),
				'added'     => esc_html__( 'Added', 'tm_builder' ),
				'edited'    => esc_html__( 'Edited', 'tm_builder' ),
				'removed'   => esc_html__( 'Removed', 'tm_builder' ),
				'moved'     => esc_html__( 'Moved', 'tm_builder' ),
				'expanded'  => esc_html__( 'Expanded', 'tm_builder' ),
				'collapsed' => esc_html__( 'Collapsed', 'tm_builder' ),
				'locked'    => esc_html__( 'Locked', 'tm_builder' ),
				'unlocked'  => esc_html__( 'Unlocked', 'tm_builder' ),
				'cloned'    => esc_html__( 'Cloned', 'tm_builder' ),
				'cleared'   => esc_html__( 'Cleared', 'tm_builder' ),
				'enabled'   => esc_html__( 'Enabled', 'tm_builder' ),
				'disabled'  => esc_html__( 'Disabled', 'tm_builder' ),
				'copied'    => esc_html__( 'Copied', 'tm_builder' ),
				'renamed'   => esc_html__( 'Renamed', 'tm_builder' ),
				'loaded'    => esc_html__( 'Loaded', 'tm_builder' ),
			),
			'noun'                  => array(
				'section'           => esc_html__( 'Section', 'tm_builder' ),
				'saved_section'     => esc_html__( 'Saved Section', 'tm_builder' ),
				'fullwidth_section' => esc_html__( 'Fullwidth Section', 'tm_builder' ),
				'specialty_section' => esc_html__( 'Specialty Section', 'tm_builder' ),
				'column'            => esc_html__( 'Column', 'tm_builder' ),
				'row'               => esc_html__( 'Row', 'tm_builder' ),
				'saved_row'         => esc_html__( 'Saved Row', 'tm_builder' ),
				'module'            => esc_html__( 'Module', 'tm_builder' ),
				'saved_module'      => esc_html__( 'Saved Module', 'tm_builder' ),
				'page'              => esc_html__( 'Page', 'tm_builder' ),
				'layout'            => esc_html__( 'Layout', 'tm_builder' ),
			),
			'addition' => array(
				'phone' => esc_html__( 'on Phone', 'tm_builder' ),
				'tablet' => esc_html__( 'on Tablet', 'tm_builder' ),
				'desktop' => esc_html__( 'on Desktop', 'tm_builder' ),
			),
			'invalid_color'    => esc_html__( 'Invalid Color', 'tm_builder' ),
			'tm_pb_preview_nonce' => wp_create_nonce( 'tm_pb_preview_nonce' ),
			'is_divi_library'  => 'tm_pb_layout' === $typenow ? 1 : 0,
			'layout_type'      => 'tm_pb_layout' === $typenow ? tm_pb_get_layout_type( get_the_ID() ) : 0,
			'is_plugin_used'   => tm_is_builder_plugin_active(),
			'yoast_content'    => tm_is_yoast_seo_plugin_active() ? $post_content_processed : '',
			'standart_section_name' => esc_html__( 'Standart Section', 'tm_builder' ),
			'fullwidth_section_name' => esc_html__( 'Fullwidth Section', 'tm_builder' ),
			'specialty_section_name' => esc_html__( 'Specialty Section', 'tm_builder' ),
		) ) );

		wp_enqueue_style( 'tm_pb_admin_css', TM_BUILDER_URI .'/framework/admin/assets/css/style.css', array(), TM_BUILDER_VERSION );
		wp_enqueue_style( 'tm_pb_admin_date_css', TM_BUILDER_URI . '/framework/admin/assets/css/libs/jquery-ui-1.10.4.custom.css', array(), TM_BUILDER_VERSION );
	}

}

function tm_pb_add_custom_box() {
	$post_types = tm_builder_get_builder_post_types();

	foreach ( $post_types as $post_type ){
		add_meta_box( TM_BUILDER_LAYOUT_POST_TYPE, esc_html__( 'Power builder', 'tm_builder' ), 'tm_pb_pagebuilder_meta_box', $post_type, 'normal', 'high' );
	}
}

if ( ! function_exists( 'tm_pb_get_the_author_posts_link' ) ) :
function tm_pb_get_the_author_posts_link(){
	global $authordata, $post;

	// Fallback for preview
	if ( empty( $authordata ) && isset( $post->post_author ) ) {
		$authordata = get_userdata( $post->post_author );
	}

	// If $authordata is empty, don't continue
	if ( empty( $authordata ) ) {
		return;
	}

	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
		esc_attr( sprintf( __( 'Posts by %s', 'tm_builder' ), get_the_author() ) ),
		get_the_author()
	);
	return apply_filters( 'the_author_posts_link', $link );
}
endif;

if ( ! function_exists( 'tm_pb_get_comments_popup_link' ) ) :
function tm_pb_get_comments_popup_link( $zero = false, $one = false, $more = false ){
	$id = get_the_ID();
	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() ) return;

	if ( $number > 1 )
		$output = str_replace( '%', number_format_i18n( $number ), ( false === $more ) ? __( '% Comments', $themename ) : $more );
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? __( 'No Comments', 'tm_builder' ) : $zero;
	else // must be one
		$output = ( false === $one ) ? __( '1 Comment', 'tm_builder' ) : $one;

	return '<span class="comments-number">' . '<a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters( 'comments_number', esc_html( $output ), esc_html( $number ) ) . '</a>' . '</span>';
}
endif;

if ( ! function_exists( 'tm_pb_postinfo_meta' ) ) :
function tm_pb_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
	$postinfo_meta = '';

	if ( in_array( 'author', $postinfo ) )
		$postinfo_meta .= ' ' . esc_html__( 'by', 'tm_builder' ) . ' <span class="author vcard">' . tm_pb_get_the_author_posts_link() . '</span>';

	if ( in_array( 'date', $postinfo ) ) {
		if ( in_array( 'author', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= '<span class="published">' . esc_html( get_the_time( wp_unslash( $date_format ) ) ) . '</span>';
	}

	if ( in_array( 'categories', $postinfo ) ){
		if ( in_array( 'author', $postinfo ) || in_array( 'date', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= get_the_category_list(', ');
	}

	if ( in_array( 'comments', $postinfo ) ){
		if ( in_array( 'author', $postinfo ) || in_array( 'date', $postinfo ) || in_array( 'categories', $postinfo ) ) $postinfo_meta .= ' | ';
		$postinfo_meta .= tm_pb_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );
	}

	return $postinfo_meta;
}
endif;


if ( ! function_exists( 'tm_pb_fix_shortcodes' ) ){
	function tm_pb_fix_shortcodes( $content, $decode_entities = false ){
		if ( $decode_entities ) {
			$content = tm_builder_replace_code_content_entities( $content );
			$content = TM_Builder_Element::convert_smart_quotes_and_amp( $content );
			$content = html_entity_decode( $content, ENT_QUOTES );
		}

		$replace_tags_from_to = array (
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']',
			"<br />\n[" => '[',
		);

		return strtr( $content, $replace_tags_from_to );
	}
}

if ( ! function_exists( 'tm_pb_load_global_module' ) ) {
	function tm_pb_load_global_module( $global_id, $row_type = '' ) {
		$global_shortcode = '';

		if ( '' !== $global_id ) {
			$query = new WP_Query( array(
				'p'         => (int) $global_id,
				'post_type' => TM_BUILDER_LAYOUT_POST_TYPE
			) );

			wp_reset_postdata();
			if ( ! empty( $query->post ) ) {
				$global_shortcode = $query->post->post_content;

				if ( '' !== $row_type && 'tm_pb_row_inner' === $row_type ) {
					$global_shortcode = str_replace( 'tm_pb_row', 'tm_pb_row_inner', $global_shortcode );
				}
			}
		}

		return $global_shortcode;
	}
}

if ( ! function_exists( 'tm_pb_extract_shortcode_content' ) ) {
	function tm_pb_extract_shortcode_content( $content, $shortcode_name ) {

		$start = strpos( $content, ']' ) + 1;
		$end = strrpos( $content, '[/' . $shortcode_name );

		if ( false !== $end ) {
			$content = substr( $content, $start, $end - $start );
		} else {
			$content = (bool) false;
		}

		return $content;
	}
}

function tm_builder_get_columns_layout() {
	$layout_columns =
		'<% if ( typeof tm_pb_specialty !== \'undefined\' && tm_pb_specialty === \'on\' ) { %>
			<li data-layout="1_2,1_2" data-specialty="1,0" data-specialty_columns="2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2 tm_pb_variations tm_pb_2_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
				</div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2 tm_pb_specialty_column"></div>
			</li>

			<li data-layout="1_2,1_2" data-specialty="0,1" data-specialty_columns="2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2 tm_pb_specialty_column"></div>

				<div class="tm_pb_layout_column tm_pb_column_layout_1_2 tm_pb_variations tm_pb_2_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
				</div>
			</li>

			<li data-layout="1_4,3_4" data-specialty="0,1" data-specialty_columns="3">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4 tm_pb_specialty_column"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_3_4 tm_pb_variations tm_pb_3_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_3"></div>
						<div class="tm_pb_variation tm_pb_variation_1_3"></div>
						<div class="tm_pb_variation tm_pb_variation_1_3"></div>
					</div>
				</div>
			</li>

			<li data-layout="3_4,1_4" data-specialty="1,0" data-specialty_columns="3">
				<div class="tm_pb_layout_column tm_pb_column_layout_3_4 tm_pb_variations tm_pb_3_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_3"></div>
						<div class="tm_pb_variation tm_pb_variation_1_3"></div>
						<div class="tm_pb_variation tm_pb_variation_1_3"></div>
					</div>
				</div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4 tm_pb_specialty_column"></div>
			</li>

			<li data-layout="1_4,1_2,1_4" data-specialty="0,1,0" data-specialty_columns="2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4 tm_pb_specialty_column"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2 tm_pb_variations tm_pb_2_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
				</div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4 tm_pb_specialty_column"></div>
			</li>

			<li data-layout="1_2,1_4,1_4" data-specialty="1,0,0" data-specialty_columns="2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2 tm_pb_variations tm_pb_2_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
				</div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4 tm_pb_specialty_column"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4 tm_pb_specialty_column"></div>
			</li>

			<li data-layout="1_4,1_4,1_2" data-specialty="0,0,1" data-specialty_columns="2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4 tm_pb_specialty_column"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4 tm_pb_specialty_column"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2 tm_pb_variations tm_pb_2_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
				</div>
			</li>

			<li data-layout="1_3,2_3" data-specialty="0,1" data-specialty_columns="2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_3 tm_pb_specialty_column"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_2_3 tm_pb_variations tm_pb_2_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
				</div>
			</li>

			<li data-layout="2_3,1_3" data-specialty="1,0" data-specialty_columns="2">
				<div class="tm_pb_layout_column tm_pb_column_layout_2_3 tm_pb_variations tm_pb_2_variations">
					<div class="tm_pb_variation tm_pb_variation_full"></div>
					<div class="tm_pb_variation_row">
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
						<div class="tm_pb_variation tm_pb_variation_1_2"></div>
					</div>
				</div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_3 tm_pb_specialty_column"></div>
			</li>
		<% } else if ( typeof view !== \'undefined\' && typeof view.model.attributes.specialty_columns !== \'undefined\' ) { %>
			<li data-layout="4_4">
				<div class="tm_pb_layout_column tm_pb_column_layout_fullwidth"></div>
			</li>
			<li data-layout="1_2,1_2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2"></div>
			</li>

			<% if ( view.model.attributes.specialty_columns === 3 ) { %>
				<li data-layout="1_3,1_3,1_3">
					<div class="tm_pb_layout_column tm_pb_column_layout_1_3"></div>
					<div class="tm_pb_layout_column tm_pb_column_layout_1_3"></div>
					<div class="tm_pb_layout_column tm_pb_column_layout_1_3"></div>
				</li>
			<% } %>
		<% } else { %>
			<li data-layout="4_4">
				<div class="tm_pb_layout_column tm_pb_column_layout_fullwidth"></div>
			</li>
			<li data-layout="1_2,1_2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2 34"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2"></div>
			</li>
			<li data-layout="1_3,1_3,1_3">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_3"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_3"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_3"></div>
			</li>
			<li data-layout="1_4,1_4,1_4,1_4">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="2_3,1_3">
				<div class="tm_pb_layout_column tm_pb_column_layout_2_3"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_3"></div>
			</li>
			<li data-layout="1_3,2_3">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_3"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_2_3"></div>
			</li>
			<li data-layout="1_4,3_4">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_3_4"></div>
			</li>
			<li data-layout="3_4,1_4">
				<div class="tm_pb_layout_column tm_pb_column_layout_3_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="1_2,1_4,1_4">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="1_4,1_4,1_2">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2"></div>
			</li>
			<li data-layout="1_4,1_2,1_4">
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_2"></div>
				<div class="tm_pb_layout_column tm_pb_column_layout_1_4"></div>
			</li>
	<%
		}
	%>';

	return apply_filters( 'tm_builder_layout_columns', $layout_columns );
}


function tm_pb_pagebuilder_meta_box() {
	global $typenow;

	do_action( 'tm_pb_before_page_builder' );

	echo '<div id="tm_pb_hidden_editor">';
	wp_editor(
		'',
		'tm_pb_content_new',
		array(
			'media_buttons' => true,
			'tinymce' => array(
				'wp_autoresize_on' => true
			)
		)
	);
	echo '</div>';

	printf(
		'<div id="tm_pb_main_container" class="post-type-%1$s%2$s"></div>',
		esc_attr( $typenow ),
		! tm_pb_is_allowed( 'move_module' ) ? ' tm-pb-disable-sort' : ''
	);
	$rename_module_menu = sprintf(
		'<%% if ( this.hasOption( "rename" ) ) { %%>
			<li><a class="tm-pb-right-click-rename" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Rename', 'tm_builder' )
	);
	$copy_module_menu = sprintf(
		'<%% if ( this.hasOption( "copy" ) ) { %%>
			<li><a class="tm-pb-right-click-copy" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Copy', 'tm_builder' )
	);
	$paste_after_menu = sprintf(
		'<%% if ( this.hasOption( "paste-after" ) ) { %%>
			<li><a class="tm-pb-right-click-paste-after" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste After', 'tm_builder' )
	);
	$paste_menu_item = sprintf(
		'<%% if ( this.hasOption( "paste-column" ) ) { %%>
			<li><a class="tm-pb-right-click-paste-column" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste', 'tm_builder' )
	);
	$paste_app_menu_item = sprintf(
		'<%% if ( this.hasOption( "paste-app" ) ) { %%>
			<li><a class="tm-pb-right-click-paste-app" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste', 'tm_builder' )
	);
	$save_to_lib_menu = sprintf(
		'<%% if ( this.hasOption( "save-to-library") ) { %%>
			<li><a class="tm-pb-right-click-save-to-library" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Save to Library', 'tm_builder' )
	);
	$lock_unlock_menu = sprintf(
		'<%% if ( this.hasOption( "lock" ) ) { %%>
			<li><a class="tm-pb-right-click-lock" href="#"><span class="unlock">%1$s</span><span class="lock">%2$s</span></a></li>
		<%% } %%>',
		esc_html__( 'Unlock', 'tm_builder' ),
		esc_html__( 'Lock', 'tm_builder' )
	);
	$enable_disable_menu = sprintf(
		'<%% if ( this.hasOption( "disable" ) ) { %%>
			<li><a class="tm-pb-right-click-disable" href="#"><span class="enable">%1$s</span><span class="disable">%2$s</span></a>
				<span class="tm_pb_disable_on_options"><span class="tm_pb_disable_on_option tm_pb_disable_on_phone"></span><span class="tm_pb_disable_on_option tm_pb_disable_on_tablet"></span><span class="tm_pb_disable_on_option tm_pb_disable_on_desktop"></span></span>
			</li>
		<%% } %%>',
		esc_html__( 'Enable', 'tm_builder' ),
		esc_html__( 'Disable', 'tm_builder' )
	);
	// Right click options Template
	printf(
		'<script type="text/template" id="tm-builder-right-click-controls-template">
		<ul class="options">
			<%% if ( "module" !== this.options.model.attributes.type || _.contains( %13$s, this.options.model.attributes.module_type ) ) { %%>
				%1$s

				%8$s

				<%% if ( this.hasOption( "undo" ) ) { %%>
				<li><a class="tm-pb-right-click-undo" href="#">%9$s</a></li>
				<%% } %%>

				<%% if ( this.hasOption( "redo" ) ) { %%>
				<li><a class="tm-pb-right-click-redo" href="#">%10$s</a></li>
				<%% } %%>

				%2$s

				%3$s

				<%% if ( this.hasOption( "collapse" ) ) { %%>
				<li><a class="tm-pb-right-click-collapse" href="#"><span class="expand">%4$s</span><span class="collapse">%5$s</span></a></li>
				<%% } %%>

				%6$s

				%7$s

				%12$s

				%11$s

			<%% } %%>

			<%% if ( this.hasOption( "preview" ) ) { %%>
			<li><a class="tm-pb-right-click-preview" href="#">%14$s</a></li>
			<%% } %%>
		</ul>
		</script>',
		tm_pb_is_allowed( 'edit_module' ) && ( tm_pb_is_allowed( 'general_settings' ) || tm_pb_is_allowed( 'advanced_settings' ) || tm_pb_is_allowed( 'custom_css_settings' ) ) ? $rename_module_menu : '',
		tm_pb_is_allowed( 'disable_module' ) ? $enable_disable_menu : '',
		tm_pb_is_allowed( 'lock_module' ) ? $lock_unlock_menu : '',
		esc_html__( 'Expand', 'tm_builder' ),
		esc_html__( 'Collapse', 'tm_builder' ), //#5
		tm_pb_is_allowed( 'add_module' ) ? $copy_module_menu : '',
		tm_pb_is_allowed( 'add_module' ) ? $paste_after_menu : '',
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'save_library' ) ? $save_to_lib_menu : '',
		esc_html__( 'Undo', 'tm_builder' ),
		esc_html__( 'Redo', 'tm_builder' ), //#10
		tm_pb_is_allowed( 'add_module' ) ? $paste_menu_item : '',
		tm_pb_is_allowed( 'add_module' ) ? $paste_app_menu_item : '',
		tm_pb_allowed_modules_list(),
		esc_html__( 'Settings', 'tm_builder' )
	);

	// "Rename Module Admin Label" Modal Window Template
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-rename_admin_label">
			<div class="tm_pb_prompt_modal">
				<a href="#" class="tm_pb_prompt_dont_proceed tm-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="tm_pb_prompt_buttons">
					<input type="submit" class="tm_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'tm_builder' ),
		esc_attr__( 'Save', 'tm_builder' )
	);

	// "Rename Module Admin Label" Modal Content Template
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-rename_admin_label-text">
			<h3>%1$s</h3>
			<p>%2$s</p>

			<input type="text" value="" id="tm_pb_new_admin_label" class="regular-text" />
		</script>',
		esc_html__( 'Rename', 'tm_builder' ),
		esc_html__( 'Enter a new name for this module', 'tm_builder' )
	);

	$save_to_lib_button = sprintf(
		'<a href="#" class="tm-pb-layout-buttons tm-pb-layout-buttons-save" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Save to Library', 'tm_builder' ),
		esc_html__( 'Save to Library', 'tm_builder' )
	);
	$load_from_lib_button = sprintf(
		'<a href="#" class="tm-pb-layout-buttons tm-pb-layout-buttons-load" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Load From Library', 'tm_builder' ),
		esc_html__( 'Load From Library', 'tm_builder' )
	);
	$clear_layout_button = sprintf(
		'<a href="#" class="tm-pb-layout-buttons tm-pb-layout-buttons-clear" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Clear Layout', 'tm_builder' ),
		esc_html__( 'Clear Layout', 'tm_builder' )
	);
	// App Template
	printf(
		'<script type="text/template" id="tm-builder-app-template">
			<img src="%10$s" class="tm_builder_logo" alt="" width="42" height="42">
			<div id="tm_pb_layout_controls">

				%1$s

				%2$s

				%3$s

				<a href="#" class="tm-pb-layout-buttons tm-pb-layout-buttons-history" title="%8$s">
					<span class="icon"></span><span class="label">%9$s</span>
				</a>

				<a href="#" class="tm-pb-layout-buttons tm-pb-layout-buttons-redo" title="%4$s">
					<span class="icon"></span><span class="label">%5$s</span>
				</a>

				<a href="#" class="tm-pb-layout-buttons tm-pb-layout-buttons-undo" title="%6$s">
					<span class="icon"></span><span class="label">%7$s</span>
				</a>
			</div>
			<div id="tm-pb-histories-visualizer-overlay"></div>
			<ol id="tm-pb-histories-visualizer"></ol>
		</script>',
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'save_library' ) ? $save_to_lib_button : '',
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'load_layout' ) && tm_pb_is_allowed( 'add_library' ) && tm_pb_is_allowed( 'add_module' ) ? $load_from_lib_button : '',
		tm_pb_is_allowed( 'add_module' ) ? $clear_layout_button : '',
		esc_attr__( 'Redo', 'tm_builder' ),
		esc_html__( 'Redo', 'tm_builder' ),
		esc_attr__( 'Undo', 'tm_builder' ),
		esc_html__( 'Undo', 'tm_builder' ),
		esc_attr__( 'See History', 'tm_builder' ),
		esc_html__( 'See History', 'tm_builder' ),
		TM_BUILDER_URI . '/assets/images/power-logo.png'
	);

	$section_settings_button = sprintf(
		'<%% if ( ( typeof tm_pb_template_type === \'undefined\' || \'section\' === tm_pb_template_type || \'\' === tm_pb_template_type )%3$s ) { %%>
			<a href="#" class="tm-pb-settings tm-pb-settings-section" title="%1$s"><span>%2$s</span></a>
		<%% } %%>',
		esc_attr__( 'Settings', 'tm_builder' ),
		esc_html__( 'Settings', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? ' && typeof tm_pb_global_module === "undefined"' : '' // do not display settings on global sections if not allowed for current user
	);
	$section_clone_button = sprintf(
		'<a href="#" class="tm-pb-clone tm-pb-clone-section" title="%1$s"><span>%2$s</span></a>',
		esc_attr__( 'Clone Section', 'tm_builder' ),
		esc_html__( 'Clone Section', 'tm_builder' )
	);
	$section_remove_button = sprintf(
		'<a href="#" class="tm-pb-remove tm-pb-remove-section" title="%1$s"><span>%2$s</span></a>',
		esc_attr__( 'Delete Section', 'tm_builder' ),
		esc_html__( 'Delete Section', 'tm_builder' )
	);
	$section_unlock_button = sprintf(
		'<a href="#" class="tm-pb-unlock" title="%1$s"><span>%2$s</span></a>',
		esc_attr__( 'Unlock Section', 'tm_builder' ),
		esc_html__( 'Unlock Section', 'tm_builder' )
	);
	// Section Template
	$settings_controls = sprintf(
		'<div class="tm-pb-controls">
			<span></span>
			%1$s

			<%% if ( typeof tm_pb_template_type === \'undefined\' || ( \'section\' !== tm_pb_template_type && \'row\' !== tm_pb_template_type && \'module\' !== tm_pb_template_type ) ) { %%>
				%2$s
				%3$s
			<%% } %%>

			<a href="#" class="tm-pb-expand" title="%4$s"><span>%5$s</span></a>
			%6$s
		</div>',
		tm_pb_is_allowed( 'edit_module' ) && ( tm_pb_is_allowed( 'general_settings' ) || tm_pb_is_allowed( 'advanced_settings' ) || tm_pb_is_allowed( 'custom_css_settings' ) ) ? $section_settings_button : '',
		tm_pb_is_allowed( 'add_module' ) ? $section_clone_button : '',
		tm_pb_is_allowed( 'add_module' ) ? $section_remove_button : '',
		esc_attr__( 'Expand Section', 'tm_builder' ),
		esc_html__( 'Expand Section', 'tm_builder' ),
		tm_pb_is_allowed( 'lock_module' ) ? $section_unlock_button : ''
	);

	$add_from_lib_section = sprintf(
		'<span class="tm-pb-section-add-saved">%1$s</span>',
		esc_html__( 'Add From Library', 'tm_builder' )
	);

	$add_standard_section_button = sprintf(
		'<span class="tm-pb-section-add-main">%1$s</span>',
		esc_html__( 'Standard Section', 'tm_builder' )
	);
	$add_standard_section_button = apply_filters( 'tm_builder_add_main_section_button', $add_standard_section_button );

	$add_fullwidth_section_button = sprintf(
		'<span class="tm-pb-section-add-fullwidth">%1$s</span>',
		esc_html__( 'Fullwidth Section', 'tm_builder' )
	);
	$add_fullwidth_section_button = apply_filters( 'tm_builder_add_fullwidth_section_button', $add_fullwidth_section_button );

	$add_specialty_section_button = sprintf(
		'<span class="tm-pb-section-add-specialty">%1$s</span>',
		esc_html__( 'Specialty Section', 'tm_builder' )
	);
	$add_specialty_section_button = apply_filters( 'tm_builder_add_specialty_section_button', $add_specialty_section_button );

	/*$settings_add_controls = sprintf(
		'<%% if ( typeof tm_pb_template_type === \'undefined\' || ( \'section\' !== tm_pb_template_type && \'row\' !== tm_pb_template_type && \'module\' !== tm_pb_template_type ) ) { %%>
			<div href="#" class="tm-pb-section-add">
				%1$s
				%2$s
				%3$s
				%4$s
			</div>
		<%% } %%>',
		$add_standard_section_button,
		$add_fullwidth_section_button,
		$add_specialty_section_button,
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'add_library' ) ? $add_from_lib_section : ''
	);*/

	$settings_add_controls = sprintf(
		'<%% if ( typeof tm_pb_template_type === \'undefined\' || ( \'section\' !== tm_pb_template_type && \'row\' !== tm_pb_template_type && \'module\' !== tm_pb_template_type ) ) { %%>
			<div href="#" class="tm-pb-section-add">
				%1$s
				%2$s
				%3$s
			</div>
		<%% } %%>',
		$add_standard_section_button,
		$add_specialty_section_button,
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'add_library' ) ? $add_from_lib_section : ''
	);

	printf(
		'<script type="text/template" id="tm-builder-section-template">
			<div class="tm-pb-right-click-trigger-overlay"></div>
			%1$s
			<div class="tm-pb-section-content tm-pb-data-cid%3$s%4$s" data-cid="<%%= cid %%>" data-skip="<%%= typeof( tm_pb_skip_module ) === \'undefined\' ? \'false\' : \'true\' %%>">
			</div>
			%2$s
			<div class="tm-pb-locked-overlay tm-pb-locked-overlay-section"></div>
			<span class="tm-pb-section-title"><%%= admin_label.replace( /%%22/g, "&quot;" ) %%></span>
		</script>',
		apply_filters( 'tm_builder_section_settings_controls', $settings_controls ),
		tm_pb_is_allowed( 'add_module' ) ? apply_filters( 'tm_builder_section_add_controls', $settings_add_controls ) : '',
		! tm_pb_is_allowed( 'move_module' ) ? ' tm-pb-disable-sort' : '',
		! tm_pb_is_allowed( 'edit_global_library' )
			? sprintf( '<%%= typeof tm_pb_global_module !== \'undefined\' ? \' tm-pb-disable-sort\' : \'\' %%>' )
			: ''
	);

	$row_settings_button = sprintf(
		'<%% if ( ( typeof tm_pb_template_type === \'undefined\' || tm_pb_template_type !== \'module\' )%3$s ) { %%>
			<a href="#" class="tm-pb-settings tm-pb-settings-row" title="%1$s"><span>%2$s</span></a>
		<%% } %%>',
		esc_attr__( 'Settings', 'tm_builder' ),
		esc_html__( 'Settings', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? ' && ( typeof tm_pb_global_module === "undefined" || "" === tm_pb_global_module ) && ( typeof tm_pb_global_parent === "undefined" || "" === tm_pb_global_parent )' : '' // do not display settings button on global rows if not allowed for current user
	);
	$row_clone_button = sprintf(
		'%3$s
			<a href="#" class="tm-pb-clone tm-pb-clone-row" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Clone Row', 'tm_builder' ),
		esc_html__( 'Clone Row', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? '<% if ( typeof tm_pb_global_parent === "undefined" || "" === tm_pb_global_parent ) { %>' : '', // do not display clone button on rows within global sections if not allowed for current user
		! tm_pb_is_allowed( 'edit_global_library' ) ? '<% } %>' : ''
	);
	$row_remove_button = sprintf(
		'%3$s
			<a href="#" class="tm-pb-remove tm-pb-remove-row" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Delete Row', 'tm_builder' ),
		esc_html__( 'Delete Row', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? '<% if ( typeof tm_pb_global_parent === "undefined" || "" === tm_pb_global_parent ) { %>' : '', // do not display clone button on rows within global sections if not allowed for current user
		! tm_pb_is_allowed( 'edit_global_library' ) ? '<% } %>' : ''
	);
	$row_change_structure_button = sprintf(
		'%3$s
			<a href="#" class="tm-pb-change-structure" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Change Structure', 'tm_builder' ),
		esc_html__( 'Change Structure', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? '<% if ( ( typeof tm_pb_global_module === "undefined" || "" === tm_pb_global_module ) && ( typeof tm_pb_global_parent === "undefined" || "" === tm_pb_global_parent ) ) { %>' : '', // do not display change structure button on global rows if not allowed for current user
		! tm_pb_is_allowed( 'edit_global_library' ) ? '<% } %>' : ''
	);
	$row_unlock_button = sprintf(
		'<a href="#" class="tm-pb-unlock" title="%1$s"><span>%2$s</span></a>',
		esc_attr__( 'Unlock Row', 'tm_builder' ),
		esc_html__( 'Unlock Row', 'tm_builder' )
	);
	// Row Template
	$settings = sprintf(
		'<div class="tm-pb-controls">
			%1$s
		<%% if ( typeof tm_pb_template_type === \'undefined\' || \'section\' === tm_pb_template_type ) { %%>
			%2$s
		<%% }

		if ( typeof tm_pb_template_type === \'undefined\' || tm_pb_template_type !== \'module\' ) { %%>
			%4$s
		<%% }

		if ( typeof tm_pb_template_type === \'undefined\' || \'section\' === tm_pb_template_type ) { %%>
			%3$s
		<%% } %%>

		<a href="#" class="tm-pb-expand" title="%5$s"><span>%6$s</span></a>
		%7$s
		</div>',
		tm_pb_is_allowed( 'edit_module' ) && ( tm_pb_is_allowed( 'general_settings' ) || tm_pb_is_allowed( 'advanced_settings' ) || tm_pb_is_allowed( 'custom_css_settings' ) ) ? $row_settings_button : '',
		tm_pb_is_allowed( 'add_module' ) ? $row_clone_button : '',
		tm_pb_is_allowed( 'add_module' ) ? $row_remove_button : '',
		tm_pb_is_allowed( 'edit_module' ) && ( tm_pb_is_allowed( 'general_settings' ) || tm_pb_is_allowed( 'advanced_settings' ) || tm_pb_is_allowed( 'custom_css_settings' ) ) ? $row_change_structure_button : '',
		esc_attr__( 'Expand Row', 'tm_builder' ),
		esc_html__( 'Expand Row', 'tm_builder' ),
		tm_pb_is_allowed( 'lock_module' ) ? $row_unlock_button : ''
	);

	$row_class = sprintf(
		'class="tm-pb-row-content tm-pb-data-cid%1$s%2$s <%%= typeof tm_pb_template_type !== \'undefined\' && \'module\' === tm_pb_template_type ? \' tm_pb_hide_insert\' : \'\' %%>"',
		! tm_pb_is_allowed( 'move_module' ) ? ' tm-pb-disable-sort' : '',
		! tm_pb_is_allowed( 'edit_global_library' )
			? sprintf( '<%%= typeof tm_pb_global_parent !== \'undefined\' || typeof tm_pb_global_module !== \'undefined\' ? \' tm-pb-disable-sort\' : \'\' %%>' )
			: ''
	);

	$data_skip = 'data-skip="<%= typeof( tm_pb_skip_module ) === \'undefined\' ? \'false\' : \'true\' %>"';

	$add_row_button = sprintf(
		'<%% if ( ( typeof tm_pb_template_type === \'undefined\' || \'section\' === tm_pb_template_type )%2$s ) { %%>
			<a href="#" class="tm-pb-row-add">
				<span>%1$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Add Row', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? ' && typeof tm_pb_global_parent === "undefined"' : '' // do not display add row buton on global sections if not allowed for current user
	);

	$insert_column_button = sprintf(
		'<a href="#" class="tm-pb-insert-column">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Insert Column(s)', 'tm_builder' )
	);

	printf(
		'<script type="text/template" id="tm-builder-row-template">
			<div class="tm-pb-right-click-trigger-overlay"></div>
			%1$s
			<div data-cid="<%%= cid %%>" %2$s %3$s>
				<div class="tm-pb-row-container"></div>
				%4$s
			</div>
			%5$s
			<div class="tm-pb-locked-overlay tm-pb-locked-overlay-row"></div>
			<span class="tm-pb-row-title"><%%= admin_label.replace( /%%22/g, "&quot;" ) %%></span>
		</script>',
		apply_filters( 'tm_builder_row_settings_controls', $settings ),
		$row_class,
		$data_skip,
		tm_pb_is_allowed( 'add_module' ) ? $insert_column_button : '',
		tm_pb_is_allowed( 'add_module' ) ? $add_row_button : ''
	);


	// Module Block Template
	$clone_button = sprintf(
		'<%% if ( ( typeof tm_pb_template_type === \'undefined\' || tm_pb_template_type !== \'module\' )%3$s && _.contains(%4$s, module_type) ) { %%>
			<a href="#" class="tm-pb-clone tm-pb-clone-module" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Clone Module', 'tm_builder' ),
		esc_html__( 'Clone Module', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? ' &&  ( typeof tm_pb_global_parent === "undefined" || "" === tm_pb_global_parent )' : '',
		tm_pb_allowed_modules_list()
	);
	$remove_button = sprintf(
		'<%% if ( ( typeof tm_pb_template_type === \'undefined\' || tm_pb_template_type !== \'module\' )%3$s && _.contains(%4$s, module_type) ) { %%>
			<a href="#" class="tm-pb-remove tm-pb-remove-module" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Remove Module', 'tm_builder' ),
		esc_html__( 'Remove Module', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? ' &&  ( typeof tm_pb_global_parent === "undefined" || "" === tm_pb_global_parent )' : '',
		tm_pb_allowed_modules_list()
	);
	$unlock_button = sprintf(
		'<%% if ( typeof tm_pb_template_type === \'undefined\' || tm_pb_template_type !== \'module\' ) { %%>
			<a href="#" class="tm-pb-unlock" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Unlock Module', 'tm_builder' ),
		esc_attr__( 'Unlock Module', 'tm_builder' )
	);
	$settings_button = sprintf(
		'<%% if (%3$s _.contains( %4$s, module_type ) ) { %%>
			<a href="#" class="tm-pb-settings" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Module Settings', 'tm_builder' ),
		esc_html__( 'Module Settings', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? ' ( typeof tm_pb_global_parent === "undefined" || "" === tm_pb_global_parent ) && ( typeof tm_pb_global_module === "undefined" || "" === tm_pb_global_module ) &&' : '',
		tm_pb_allowed_modules_list()
	);

	printf(
		'<script type="text/template" id="tm-builder-block-module-template">
			%1$s
			%2$s
			%3$s
			%4$s
			<span class="tm-pb-module-title"><%%= admin_label.replace( /%%22/g, "&quot;" ) %%></span>
		</script>',
		tm_pb_is_allowed( 'edit_module' ) && ( tm_pb_is_allowed( 'general_settings' ) || tm_pb_is_allowed( 'advanced_settings' ) || tm_pb_is_allowed( 'custom_css_settings' ) ) ? $settings_button : '',
		tm_pb_is_allowed( 'add_module' ) ? $clone_button : '',
		tm_pb_is_allowed( 'add_module' ) ? $remove_button : '',
		tm_pb_is_allowed( 'lock_module' ) ? $unlock_button : ''
	);


	// Modal Template
	$save_exit_button = sprintf(
		'<a href="#" class="tm-pb-modal-save button button-primary">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Save & Exit', 'tm_builder' )
	);

	$save_template_button = sprintf(
		'<%% if ( typeof tm_pb_template_type === \'undefined\' || \'\' === tm_pb_template_type ) { %%>
			<a href="#" class="tm-pb-modal-save-template button">
				<span>%1$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Save & Add To Library', 'tm_builder' )
	);

	$can_edit_or_has_modal_view_tab = tm_pb_is_allowed( 'edit_module' ) && ( tm_pb_is_allowed( 'general_settings' ) || tm_pb_is_allowed( 'advanced_settings' ) || tm_pb_is_allowed( 'custom_css_settings' ) );

	printf(
		'<script type="text/template" id="tm-builder-modal-template">
			<div class="tm-pb-modal-container%5$s">

				<a href="#" class="tm-pb-modal-close">
					<span>%1$s</span>
				</a>

			<%% if ( ! ( typeof open_view !== \'undefined\' && open_view === \'column_specialty_settings\' ) && typeof type !== \'undefined\' && ( type === \'module\' || type === \'section\' || type === \'row_inner\' || ( type === \'row\' && typeof open_view === \'undefined\' ) ) ) { %%>
				<div class="tm-pb-modal-bottom-container%4$s">
					%3$s
					%2$s
				</div>
			<%% } %%>

			</div>
		</script>',
		esc_html__( 'Cancel', 'tm_builder' ),
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'save_library' ) ? $save_template_button : '',
		$can_edit_or_has_modal_view_tab ? $save_exit_button : '',
		! tm_pb_is_allowed( 'divi_library' ) || ! tm_pb_is_allowed( 'save_library' ) ? ' tm_pb_single_button' : '',
		$can_edit_or_has_modal_view_tab ? '' : ' tm_pb_no_editing'
	);


	// Column Settings Template
	$columns_number =
		'<% if ( view.model.attributes.specialty_columns === 3 ) { %>
			3
		<% } else { %>
			2
		<% } %>';
	$data_specialty_columns = sprintf(
		'<%% if ( typeof view !== \'undefined\' && typeof view.model.attributes.specialty_columns !== \'undefined\' ) { %%>
			data-specialty_columns="%1$s"
		<%% } %%>',
		$columns_number
	);

	$saved_row_tab = sprintf(
		'<li class="tm-pb-saved-module" data-open_tab="tm-pb-saved-modules-tab">
			<a href="#">%1$s</a>
		</li>',
		esc_html__( 'Add From Library', 'tm_builder' )
	);
	$saved_row_container = '<% if ( ( typeof change_structure === \'undefined\' || \'true\' !== change_structure ) && ( typeof tm_pb_specialty === \'undefined\' || tm_pb_specialty !== \'on\' ) ) { %>
								<div class="tm-pb-main-settings tm-pb-main-settings-full tm-pb-saved-modules-tab"></div>
							<% } %>';
	printf(
		'<script type="text/template" id="tm-builder-column-settings-template">

			<h3 class="tm-pb-settings-heading" data-current_row="<%%= cid %%>">%1$s</h3>

		<%% if ( ( typeof change_structure === \'undefined\' || \'true\' !== change_structure ) && ( typeof tm_pb_specialty === \'undefined\' || tm_pb_specialty !== \'on\' ) ) { %%>
			<ul class="tm-pb-options-tabs-links tm-pb-saved-modules-switcher" %2$s>
				<li class="tm-pb-saved-module tm-pb-options-tabs-links-active" data-open_tab="tm-pb-new-modules-tab" data-content_loaded="true">
					<a href="#">%3$s</a>
				</li>
				%4$s
			</ul>
		<%% } %%>

			<div class="tm-pb-main-settings tm-pb-main-settings-full tm-pb-new-modules-tab active-container">
				<ul class="tm-pb-column-layouts">
					%5$s
				</ul>
			</div>

			%6$s

		</script>',
		esc_html__( 'Insert Columns Preset', 'tm_builder' ),
		$data_specialty_columns,
		esc_html__( 'New Row', 'tm_builder' ),
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'add_library' ) ? $saved_row_tab : '',
		tm_builder_get_columns_layout(),
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'add_library' ) ? $saved_row_container : ''
	);

	// "Add Module" Template
	$fullwidth_class =
		'<% if ( typeof module.fullwidth_only !== \'undefined\' && module.fullwidth_only === \'on\' ) { %> tm_pb_fullwidth_only_module<% } %>';
	$saved_modules_tab = sprintf(
		'<li class="tm-pb-saved-module" data-open_tab="tm-pb-saved-modules-tab">
			<a href="#">%1$s</a>
		</li>',
		esc_html__( 'Add From Library', 'tm_builder' )
	);
	$saved_modules_container = '<div class="tm-pb-main-settings tm-pb-main-settings-full tm-pb-saved-modules-tab"></div>';
	printf(
		'<script type="text/template" id="tm-builder-modules-template">
			<h3 class="tm-pb-settings-heading">%1$s</h3>

			<ul class="tm-pb-options-tabs-links tm-pb-saved-modules-switcher">
				<li class="tm-pb-new-module tm-pb-options-tabs-links-active" data-open_tab="tm-pb-all-modules-tab">
					<a href="#">%2$s</a>
				</li>

				%3$s
			</ul>

			<div class="tm-pb-main-settings tm-pb-main-settings-full tm-pb-all-modules-tab active-container">
				<ul class="tm-pb-all-modules">
				<%% _.each(modules, function(module) { %%>
					<%% if ( "tm_pb_row" !== module.label && "tm_pb_section" !== module.label && "tm_pb_column" !== module.label && "tm_pb_row_inner" !== module.label && _.contains(%6$s, module.label ) ) { %%>
						<li class="<%%= module.label %%>%4$s" data-icon="&#x<%%= module.icon %%>;">
							<span class="tm_module_title"><%%= module.title %%></span>
						</li>
					<%% } %%>
				<%% }); %%>
				</ul>
			</div>

			%5$s
		</script>',
		esc_html__( 'Insert Module', 'tm_builder' ),
		esc_html__( 'New Module', 'tm_builder' ),
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'add_library' ) ? $saved_modules_tab : '',
		$fullwidth_class,
		tm_pb_is_allowed( 'divi_library' ) && tm_pb_is_allowed( 'add_library' ) ? $saved_modules_container : '',
		tm_pb_allowed_modules_list()
	);


	// Load Layout Template
	printf(
		'<script type="text/template" id="tm-builder-load_layout-template">
			<h3 class="tm-pb-settings-heading">%1$s</h3>

		<%% if ( typeof display_switcher !== \'undefined\' && display_switcher === \'on\' ) { %%>
			<ul class="tm-pb-options-tabs-links tm-pb-saved-modules-switcher">
				<li class="tm-pb-new-module tm-pb-options-tabs-links-active" data-open_tab="tm-pb-all-modules-tab">
					<a href="#">%2$s</a>
				</li>
				<li class="tm-pb-saved-module" data-open_tab="tm-pb-saved-modules-tab">
					<a href="#">%3$s</a>
				</li>
			</ul>
		<%% } %%>

		<%% if ( typeof display_switcher !== \'undefined\' && display_switcher === \'on\' ) { %%>
			<div class="tm-pb-main-settings tm-pb-main-settings-full tm-pb-all-modules-tab active-container"></div>
			<div class="tm-pb-main-settings tm-pb-main-settings-full tm-pb-saved-modules-tab" style="display: none;"></div>
		<%% } else { %%>
			<div class="tm-pb-main-settings tm-pb-main-settings-full tm-pb-saved-modules-tab active-container"></div>
		<%% } %%>
		</script>',
		esc_html__( 'Load Layout', 'tm_builder' ),
		esc_html__( 'Predefined Layouts', 'tm_builder' ),
		esc_html__( 'Add From Library', 'tm_builder' )
	);

	$insert_module_button = sprintf(
		'%2$s
		<a href="#" class="tm-pb-insert-module<%%= typeof tm_pb_template_type === \'undefined\' || \'module\' !== tm_pb_template_type ? \'\' : \' tm_pb_hidden_button\' %%>">
			<span>%1$s</span>
		</a>
		%3$s',
		esc_html__( 'Insert Module(s)', 'tm_builder' ),
		! tm_pb_is_allowed( 'edit_global_library' ) ? '<% if ( typeof tm_pb_global_parent === "undefined" ) { %>' : '',
		! tm_pb_is_allowed( 'edit_global_library' ) ? '<% } %>' : ''
	);
	// Column Template
	printf(
		'<script type="text/template" id="tm-builder-column-template">
			%1$s
		</script>',
		tm_pb_is_allowed( 'add_module' ) ? $insert_module_button : ''
	);


	// Advanced Settings Buttons Module
	printf(
		'<script type="text/template" id="tm-builder-advanced-setting">
			<a href="#" class="tm-pb-advanced-setting-remove">
				<span>%1$s</span>
			</a>

			<a href="#" class="tm-pb-advanced-setting-options">
				<span>%2$s</span>
			</a>

			<a href="#" class="tm-pb-clone tm-pb-advanced-setting-clone">
				<span>%3$s</span>
			</a>
		</script>',
		esc_html__( 'Delete', 'tm_builder' ),
		esc_html__( 'Settings', 'tm_builder' ),
		esc_html__( 'Clone Module', 'tm_builder' )
	);

	// Advanced Settings Modal Buttons Template
	printf(
		'<script type="text/template" id="tm-builder-advanced-setting-edit">
			<div class="tm-pb-modal-container">
				<a href="#" class="tm-pb-modal-close">
					<span>%1$s</span>
				</a>

				<div class="tm-pb-modal-bottom-container">
					<a href="#" class="tm-pb-modal-save">
						<span>%2$s</span>
					</a>
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'tm_builder' ),
		esc_html__( 'Save', 'tm_builder' )
	);


	// "Deactivate Builder" Modal Message Template
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-deactivate_builder-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<p>%3$s</p>
		</script>',
		esc_html__( 'Disable Builder', 'tm_builder' ),
		esc_html__( 'All content created in the Builder will be lost. Previous content will be restored.', 'tm_builder' ),
		esc_html__( 'Do you wish to proceed?', 'tm_builder' )
	);


	// "Clear Layout" Modal Window Template
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-clear_layout-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<p>%3$s</p>
		</script>',
		esc_html__( 'Clear Layout', 'tm_builder' ),
		esc_html__( 'All of your current page content will be lost.', 'tm_builder' ),
		esc_html__( 'Do you wish to proceed?', 'tm_builder' )
	);


	// "Reset Advanced Settings" Modal Template
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-reset_advanced_settings-text">
			<p>%1$s</p>
			<p>%2$s</p>
		</script>',
		esc_html__( 'All advanced module settings in will be lost.', 'tm_builder' ),
		esc_html__( 'Do you wish to proceed?', 'tm_builder' )
	);


	// "Save Layout" Modal Window Template
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-save_layout">
			<div class="tm_pb_prompt_modal">
				<a href="#" class="tm_pb_prompt_dont_proceed tm-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="tm_pb_prompt_buttons">
					<input type="submit" class="tm_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Cancel', 'tm_builder' ),
		esc_html__( 'Save', 'tm_builder' )
	);


	// "Save Layout" Modal Content Template
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-save_layout-text">
			<h3>%1$s</h3>
			<p>%2$s</p>

			<label>%3$s</label>
			<input type="text" value="" id="tm_pb_new_layout_name" class="regular-text" />
		</script>',
		esc_html__( 'Save To Library', 'tm_builder' ),
		esc_html__( 'Save your current page to the Library for later use.', 'tm_builder' ),
		esc_html__( 'Layout Name:', 'tm_builder' )
	);


	// "Save Template" Modal Window Layout
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-save_template">
			<div class="tm_pb_prompt_modal tm_pb_prompt_modal_save_library">
				<div class="tm_pb_prompt_buttons">
					<input type="submit" class="tm_pb_prompt_proceed" value="%1$s" />
				</div>
			</div>
		</script>',
		esc_attr__( 'Save And Add To Library', 'tm_builder' )
	);


	// "Save Template" Content Layout
	$layout_categories = get_terms( 'layout_category', array( 'hide_empty' => false ) );
	$categories_output = sprintf( '<div class="tm-pb-option"><label>%1$s</label>',
		esc_html__( 'Add To Categories:', 'tm_builder' )
	);

	if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
		$categories_output .= '<div class="tm-pb-option-container layout_cats_container">';
		foreach( $layout_categories as $category ) {
			$categories_output .= sprintf( '<label>%1$s<input type="checkbox" value="%2$s"/></label>',
				esc_html( $category->name ),
				esc_attr( $category->term_id )
			);
		}
		$categories_output .= '</div></div>';
	}

	$categories_output .= sprintf( '
		<div class="tm-pb-option">
			<label>%1$s:</label>
			<div class="tm-pb-option-container">
				<input type="text" value="" id="tm_pb_new_cat_name" class="regular-text" />
			</div>
		</div>',
		esc_html__( 'Create New Category', 'tm_builder' )
	);

	$general_checkbox = sprintf(
		'<label>
			%1$s <input type="checkbox" value="general" id="tm_pb_template_general" checked />
		</label>',
		esc_html__( 'Include General settings', 'tm_builder' )
	);
	$advanced_checkbox = sprintf(
		'<label>
			%1$s <input type="checkbox" value="advanced" id="tm_pb_template_advanced" checked />
		</label>',
		esc_html__( 'Include Advanced Design settings', 'tm_builder' )
	);
	$css_checkbox = sprintf(
		'<label>
			%1$s <input type="checkbox" value="css" id="tm_pb_template_css" checked />
		</label>',
		esc_html__( 'Include Custom CSS', 'tm_builder' )
	);

	printf(
		'<script type="text/template" id="tm-builder-prompt-modal-save_template-text">
			<div class="tm-pb-main-settings">

				<div class="tm-pb-option">
					<label>%2$s:</label>

					<div class="tm-pb-option-container">
						<input type="text" value="" id="tm_pb_new_template_name" class="regular-text" />
					</div>
				</div>

			<%% if ( \'module\' === module_type ) { %%>
				<div class="tm-pb-option">
					<label>%3$s:</label>

					<div class="tm-pb-option-container tm_pb_select_module_tabs">
						%4$s

						%5$s

						%6$s
						<p class="tm_pb_error_message_save_template" style="display: none;">
							%7$s
						</p>
					</div>
				</div>
			<%% } %%>

			<%% if ( \'global\' !== is_global && \'global\' !== is_global_child ) { %%>
				<div class="tm-pb-option">
					<label>%8$s</label>

					<div class="tm-pb-option-container">
						<label>
							%9$s <input type="checkbox" value="" id="tm_pb_template_global" />
						</label>
					</div>
				</div>
			<%% } %%>

				%10$s
			</div>
		</script>',
		esc_html__( 'Here you can save the current item and add it to your Library for later use as well.', 'tm_builder' ),
		esc_html__( 'Template Name', 'tm_builder' ),
		esc_html__( 'Selective Sync', 'tm_builder' ),
		tm_pb_is_allowed( 'general_settings' ) ? $general_checkbox : '',
		tm_pb_is_allowed( 'advanced_settings' ) ? $advanced_checkbox : '',
		tm_pb_is_allowed( 'custom_css_settings' ) ? $css_checkbox : '',
		esc_html__( 'Please select at least 1 tab to save', 'tm_builder' ),
		esc_html__( 'Save as Global:', 'tm_builder' ),
		esc_html__( 'Make this a global item', 'tm_builder' ),
		$categories_output
	);


	// Prompt Modal Window Template
	printf(
		'<script type="text/template" id="tm-builder-prompt-modal">
			<div class="tm_pb_prompt_modal">
				<a href="#" class="tm_pb_prompt_dont_proceed tm-pb-modal-close">
					<span>%1$s<span>
				</a>

				<div class="tm_pb_prompt_buttons">
					<a href="#" class="tm_pb_prompt_proceed">%2$s</a>
				</div>
			</div>
		</script>',
		esc_html__( 'No', 'tm_builder' ),
		esc_html__( 'Yes', 'tm_builder' )
	);


	// "Add Specialty Section" Button Template
	printf(
		'<script type="text/template" id="tm-builder-add-specialty-section-button">
			<a href="#" class="tm-pb-section-add-specialty tm-pb-add-specialty-template" data-is_template="true">%1$s</a>
		</script>',
		esc_html__( 'Add Specialty Section', 'tm_builder' )
	);


	// Saved Entry Template
	echo
		'<script type="text/template" id="tm-builder-saved-entry">
			<a class="tm_pb_saved_entry_item"><%= title %></a>
		</script>';


	// Font Icons Template
	printf(
		'<script type="text/template" id="tm-builder-google-fonts-options-items">
			%1$s
		</script>',
		tm_builder_get_font_options_items()
	);


	// Font Icons Template
	printf(
		'<script type="text/template" id="tm-builder-font-icon-list-items">
			%1$s
		</script>',
		tm_pb_get_font_icon_list_items()
	);

	// Histories Visualizer Item Template
	printf(
		'<script type="text/template" id="tm-builder-histories-visualizer-item-template">
			<li id="tm-pb-history-<%%= this.options.get( "timestamp" ) %%>" class="<%%= this.options.get( "current_active_history" ) ? "active" : "undo"  %%>" data-timestamp="<%%= this.options.get( "timestamp" )  %%>">
				<span class="datetime"><%%= this.options.get( "datetime" )  %%></span>
				<span class="verb"> <%%= this.getVerb()  %%></span>
				<span class="noun"> <%%= this.getNoun()  %%></span>
				<%% if ( typeof this.getAddition === "function" && "" !== this.getAddition() ) { %%>
					<span class="addition"> <%%= this.getAddition() %%></span>
				<%% } %%>
			</li>
		</script>'
	);

	// Font Down Icons Template
	printf(
		'<script type="text/template" id="tm-builder-font-down-icon-list-items">
			%1$s
		</script>',
		tm_pb_get_font_down_icon_list_items()
	);

	// Font social icons template
	printf(
		'<script type="text/template" id="tm-builder-font-down-icon-list-items">
			%1$s
		</script>',
		tm_pb_get_font_down_icon_list_items()
	);

	printf(
		'<script type="text/template" id="tm-builder-preview-icons-template">
			<ul class="tm-pb-preview-screensize-switcher">
				<li><a href="#" class="tm-pb-preview-mobile" data-width="375"><span class="label">%1$s</span></a></li>
				<li><a href="#" class="tm-pb-preview-tablet" data-width="768"><span class="label">%2$s</span></a></li>
				<li><a href="#" class="tm-pb-preview-desktop active"><span class="label">%3$s</span></a></li>
			</ul>
		</script>',
		esc_html__( 'Mobile', 'tm_builder' ),
		esc_html__( 'Tablet', 'tm_builder' ),
		esc_html__( 'Desktop', 'tm_builder' )
	);

	printf(
		'<script type="text/template" id="tm-builder-options-tabs-links-template">
			<ul class="tm-pb-options-tabs-links">
				<%% _.each(this.tm_builder_template_options.tabs.options, function(tab, index) { %%>
					<li class="tm_pb_options_tab_<%%= tab.slug %%><%%= \'1\' === index ? \' tm-pb-options-tabs-links-active\' : \'\' %%>">
						<a href="#"><%%= tab.label %%></a>
					</li>
				<%% }); %%>
			</ul>
		</script>'
	);

	printf(
		'<script type="text/template" id="tm-builder-mobile-options-tabs-template">
			<div class="tm_pb_mobile_settings_tabs">
				<a href="#" class="tm_pb_mobile_settings_tab tm_pb_mobile_settings_active_tab" data-settings_tab="desktop">
					%1$s
				</a>
				<a href="#" class="tm_pb_mobile_settings_tab" data-settings_tab="laptop">
					%2$s
				</a>
				<a href="#" class="tm_pb_mobile_settings_tab" data-settings_tab="tablet">
					%3$s
				</a>
				<a href="#" class="tm_pb_mobile_settings_tab" data-settings_tab="phone">
					%4$s
				</a>
			</div>
		</script>',
		esc_html__( 'Desktop', 'tm_builder' ),
		esc_html__( 'Laptop', 'tm_builder' ),
		esc_html__( 'Tablet', 'tm_builder' ),
		esc_html__( 'Phone', 'tm_builder' )
	);

	printf(
		'<script type="text/template" id="tm-builder-padding-inputs-template">
			<label>
				<%%= this.tm_builder_template_options.padding.options.label %%>
				<input type="text" class="tm_custom_margin tm_custom_margin_<%%= this.tm_builder_template_options.padding.options.side %%><%%= this.tm_builder_template_options.padding.options.class %%><%%= \'need_mobile\' === this.tm_builder_template_options.padding.options.need_mobile ? \' tm_pb_setting_mobile tm_pb_setting_mobile_desktop tm_pb_setting_mobile_active\' : \'\' %%>"<%%= \'need_mobile\' === this.tm_builder_template_options.padding.options.need_mobile ? \' data-device="desktop"\' : \'\' %%> />
				<%% if ( \'need_mobile\' === this.tm_builder_template_options.padding.options.need_mobile ) { %%>
					<input type="text" class="tm_custom_margin tm_pb_setting_mobile tm_pb_setting_mobile_laptop tm_custom_margin_<%%= this.tm_builder_template_options.padding.options.side %%><%%= this.tm_builder_template_options.padding.options.class %%>" data-device="laptop" />
					<input type="text" class="tm_custom_margin tm_pb_setting_mobile tm_pb_setting_mobile_tablet tm_custom_margin_<%%= this.tm_builder_template_options.padding.options.side %%><%%= this.tm_builder_template_options.padding.options.class %%>" data-device="tablet" />
					<input type="text" class="tm_custom_margin tm_pb_setting_mobile tm_pb_setting_mobile_phone tm_custom_margin_<%%= this.tm_builder_template_options.padding.options.side %%><%%= this.tm_builder_template_options.padding.options.class %%>" data-device="phone" />
				<%% } %%>
			</label>
		</script>'
	);

	printf(
		'<script type="text/template" id="tm-builder-yes-no-button-template">
			<div class="tm_pb_yes_no_button tm_pb_off_state">
				<span class="tm_pb_value_text tm_pb_on_value"><%%= this.tm_builder_template_options.yes_no_button.options.on %%></span>
				<span class="tm_pb_button_slider"></span>
				<span class="tm_pb_value_text tm_pb_off_value"><%%= this.tm_builder_template_options.yes_no_button.options.off %%></span>
			</div>
		</script>'
	);

	printf(
		'<script type="text/template" id="tm-builder-font-buttons-option-template">
			<%% _.each(this.tm_builder_template_options.font_buttons.options, function(font_button) { %%>
				<div class="tm_builder_<%%= font_button %%>_font tm_builder_font_style mce-widget">
					<button type="button">

					</button>
				</div>
			<%% }); %%>
		</script>'
	);

	do_action( 'tm_pb_after_page_builder' );
}

/**
 * Modify builder editor's TinyMCE configuration
 *
 * @return array
 */
function tm_pb_content_new_mce_config( $mceInit, $editor_id ) {
	if ( 'tm_pb_content_new' === $editor_id && isset( $mceInit['toolbar1'] ) ) {
		// Get toolbar as array
		$toolbar1 = explode(',', $mceInit['toolbar1'] );

		// Look for read more (wp_more)'s array' key
		$wp_more_key = array_search( 'wp_more', $toolbar1 );

		if ( $wp_more_key ) {
			unset( $toolbar1[ $wp_more_key ] );
		}

		// Update toolbar1 configuration
		$mceInit['toolbar1'] = implode(',', $toolbar1 );
	}

	return $mceInit;
}
add_filter( 'tiny_mce_before_init', 'tm_pb_content_new_mce_config', 10, 2 );

/**
 * Get post format with filterable output
 *
 * @todo once WordPress provide filter for get_post_format() output, this function can be retired
 * @see get_post_format()
 *
 * @return mixed string|bool string of post format or false for default
 */
function tm_pb_post_format() {

	$format = get_post_format();

	if ( ! $format ) {
		$format = 'standard';
	}

	return apply_filters( 'tm_pb_post_format', $format, get_the_ID() );
}

/**
 * Return post format into false when using pagebuilder
 *
 * @return mixed string|bool string of post format or false for default
 */
function tm_pb_post_format_in_pagebuilder( $post_format, $post_id ) {

	if ( tm_pb_is_pagebuilder_used( $post_id ) ) {
		return false;
	}

	return $post_format;
}
add_filter( 'tm_pb_post_format', 'tm_pb_post_format_in_pagebuilder', 10, 2 );

/*
 * Is Builder plugin active?
 *
 * @return bool  True - if the plugin is active
 */
if ( ! function_exists( 'tm_is_builder_plugin_active' ) ) :
function tm_is_builder_plugin_active() {
	return defined( 'TM_BUILDER_ACTIVE' );
}
endif;

if ( ! function_exists( 'tm_pb_get_audio_player' ) ) :
function tm_pb_get_audio_player() {
	$output = sprintf(
		'<div class="tm_audio_container">
			%1$s
		</div> <!-- .tm_audio_container -->',
		do_shortcode( '[audio]' )
	);

	return $output;
}
endif;

/*
 * Displays post audio, quote and link post formats content
 */
if ( ! function_exists( 'tm_divi_post_format_content' ) ) :
function tm_divi_post_format_content() {
	$post_format = tm_pb_post_format();

	$text_color_class = tm_divi_get_post_text_color();

	$inline_style = tm_divi_get_post_bg_inline_style();

	switch ( $post_format ) {
		case 'audio' :
			printf(
				'<div class="tm_audio_content%4$s"%5$s>
					<h2><a href="%3$s">%1$s</a></h2>
					%2$s
				</div> <!-- .tm_audio_content -->',
				esc_html( get_the_title() ),
				tm_pb_get_audio_player(),
				esc_url( get_permalink() ),
				esc_attr( $text_color_class ),
				$inline_style
			);

			break;
		case 'quote' :
			printf(
				'<div class="tm_quote_content%4$s"%5$s>
					%1$s
					<a href="%2$s" class="tm_quote_main_link">%3$s</a>
				</div> <!-- .tm_quote_content -->',
				tm_get_blockquote_in_content(),
				esc_url( get_permalink() ),
				esc_html__( 'Read more', 'tm_builder' ),
				esc_attr( $text_color_class ),
				$inline_style
			);

			break;
		case 'link' :
			printf(
				'<div class="tm_link_content%5$s"%6$s>
					<h2><a href="%2$s">%1$s</a></h2>
					<a href="%3$s" class="tm_link_main_url">%4$s</a>
				</div> <!-- .tm_link_content -->',
				esc_html( get_the_title() ),
				esc_url( get_permalink() ),
				esc_url( tm_get_link_url() ),
				esc_html( tm_get_link_url() ),
				esc_attr( $text_color_class ),
				$inline_style
			);

			break;
	}
}
endif;

/**
 * Extract and return the first blockquote from content.
 */
if ( ! function_exists( 'tm_get_blockquote_in_content' ) ) :
function tm_get_blockquote_in_content() {

	global $more;

	$more_default = $more;
	$more         = 1;

	$content = apply_filters( 'the_content', get_the_content() );
	$more    = $more_default;

	if ( preg_match( '/<blockquote>(.+?)<\/blockquote>/is', $content, $matches ) ) {
		return $matches[0];
	} else {
		return false;
	}
}
endif;

if ( ! function_exists( 'tm_get_link_url' ) ) :
function tm_get_link_url() {
	if ( '' !== ( $link_url = get_post_meta( get_the_ID(), '_format_link_url', true ) ) ) {
		return $link_url;
	}

	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
endif;

if ( ! function_exists( 'tm_get_first_video' ) ) :
function tm_get_first_video() {
	$first_video  = '';
	$video_width  = (int) apply_filters( 'tm_blog_video_width', 1080 );
	$video_height = (int) apply_filters( 'tm_blog_video_height', 630 );

	preg_match_all( '|^\s*https?://[^\s"]+\s*$|im', get_the_content(), $urls );

	foreach ( $urls[0] as $url ) {

		$oembed = wp_oembed_get( esc_url( $url ) );

		if ( !$oembed ) {
			continue;
		}

		$first_video = $oembed;
		$first_video = preg_replace( '/<embed /', '<embed wmode="transparent" ', $first_video );
		$first_video = preg_replace( '/<\/object>/','<param name="wmode" value="transparent" /></object>', $first_video );
		$first_video = preg_replace( "/width=\"[0-9]*\"/", "width={$video_width}", $first_video );
		$first_video = preg_replace( "/height=\"[0-9]*\"/", "height={$video_height}", $first_video );

		break;
	}

	if ( '' === $first_video && has_shortcode( get_the_content(), 'video' )  ) {
		$regex = get_shortcode_regex();
		preg_match( "/{$regex}/s", get_the_content(), $match );

		$first_video = preg_replace( "/width=\"[0-9]*\"/", "width=\"{$video_width}\"", $match[0] );
		$first_video = preg_replace( "/height=\"[0-9]*\"/", "height=\"{$video_height}\"", $first_video );

		add_filter( 'the_content', 'tm_delete_post_video' );

		$first_video = do_shortcode( tm_pb_fix_shortcodes( $first_video ) );
	}

	return ( '' !== $first_video ) ? $first_video : false;
}
endif;

if ( ! function_exists( 'tm_delete_post_video' ) ) :
/*
 * Removes the first video shortcode from content on single pages since it is displayed
 * at the top of the page. This will also remove the video shortcode url from archive pages content
 */
function tm_delete_post_video( $content ) {
	if ( has_post_format( 'video' ) ) :
		$regex = get_shortcode_regex();
		preg_match_all( "/{$regex}/s", $content, $matches );

		// $matches[2] holds an array of shortcodes names in the post
		foreach ( $matches[2] as $key => $shortcode_match ) {
			if ( 'video' === $shortcode_match ) {
				$content = str_replace( $matches[0][$key], '', $content );
				if ( is_single() && is_main_query() ) {
					break;
				}
			}
		}
	endif;

	return $content;
}
endif;

/**
 * Fix JetPack post excerpt shortcode issue.
 */
function tm_jetpack_post_excerpt( $results ) {
    foreach ( $results as $key => $post ) {
        if ( isset( $post['excerpt'] ) ) {
        	// Remove ET shortcodes from JetPack excerpt.
            $results[$key]['excerpt'] = preg_replace( '#\[tm_pb(.*)\]#', '', $post['excerpt'] );
        }
    }
    return $results;
}
add_filter( 'jetpack_relatedposts_returned_results', 'tm_jetpack_post_excerpt' );

/**
 * Adds a Divi gallery type when the Jetpack plugin is enabled
 */
function tm_jetpack_gallery_type( $types ) {
	$types['divi'] = 'Builder';
	return $types;
}
add_filter( 'jetpack_gallery_types', 'tm_jetpack_gallery_type' );

if ( ! function_exists( 'tm_get_gallery_attachments' ) ) :
/**
 * Fetch the gallery attachments
 */
function tm_get_gallery_attachments( $attr ) {
	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( ! $attr['orderby'] ) {
			unset( $attr['orderby'] );
		}
	}
	$html5 = current_theme_supports( 'html5', 'gallery' );
	$atts = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => get_the_ID() ? get_the_ID() : 0,
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => '',
	), $attr, 'gallery' );

	$id = intval( $atts['id'] );
	if ( 'RAND' == $atts['order'] ) {
		$atts['orderby'] = 'none';
	}
	if ( ! empty( $atts['include'] ) ) {
		$_attachments = get_posts( array(
			'include'        => $atts['include'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[ $val->ID ] = $_attachments[ $key ];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$attachments = get_children( array(
			'post_parent'    => $id,
			'exclude'        => $atts['exclude'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );
	} else {
		$attachments = get_children( array(
			'post_parent'    => $id,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );
	}

	return $attachments;
}
endif;

/**
 * Generate the HTML for custom gallery layouts
 */
function tm_gallery_layout( $val, $attr ) {
	// check to see if the gallery output is already rewritten
	if ( ! empty( $val ) ) {
		return $val;
	}

	if ( tm_is_builder_plugin_active() ) {
		return $val;
	}

	if ( ! apply_filters( 'tm_gallery_layout_enable', false ) ) {
		return $val;
	}

	$output = '';

	if ( ! is_singular() && ! tm_pb_is_pagebuilder_used( get_the_ID() ) ) {
		$attachments = tm_get_gallery_attachments( $attr );
		$gallery_output = '';
		foreach ( $attachments as $attachment ) {
			$attachment_image = wp_get_attachment_url( $attachment->ID, 'tm-pb-post-main-image-fullwidth' );
			$gallery_output .= sprintf(
				'<div class="tm_pb_slide" style="background: url(%1$s);"></div>',
				esc_attr( $attachment_image )
			);
		}
		$output = sprintf(
			'<div class="tm_pb_slider tm_pb_slider_fullwidth_off tm_pb_gallery_post_type">
				<div class="tm_pb_slides">
					%1$s
				</div>
			</div>',
			$gallery_output
		);

	} else {
		if ( ! isset( $attr['type'] ) || ! in_array( $attr['type'], array( 'rectangular', 'square', 'circle', 'rectangle' ) ) ) {
			$attachments = tm_get_gallery_attachments( $attr );
			$gallery_output = '';
			foreach ( $attachments as $attachment ) {
				$gallery_output .= sprintf(
					'<li class="tm_gallery_item">
						<a href="%1$s" title="%3$s">
							<span class="tm_portfolio_image">
								%2$s
								<span class="tm_overlay"></span>
							</span>
						</a>
						%4$s
					</li>',
					esc_url( wp_get_attachment_url( $attachment->ID, 'full' ) ),
					wp_get_attachment_image( $attachment->ID, 'tm-pb-portfolio-image' ),
					esc_attr( $attachment->post_title ),
					! empty( $attachment->post_excerpt )
						? sprintf( '<p class="tm_pb_gallery_caption">%1$s</p>', esc_html( $attachment->post_excerpt ) )
						: ''
				);
			}
			$output = sprintf(
				'<ul class="tm_post_gallery clearfix">
					%1$s
				</ul>',
				$gallery_output
			);
		}
	}
	return $output;
}
add_filter( 'post_gallery', 'tm_gallery_layout', 1000, 2 );

if ( ! function_exists( 'tm_pb_gallery_images' ) ) :
function tm_pb_gallery_images( $force_gallery_layout = '' ) {
	if ( 'slider' === $force_gallery_layout ) {
		$attachments = get_post_gallery( get_the_ID(), false );
		$gallery_output = '';
		$output = '';
		$images_array = ! empty( $attachments['ids'] ) ? explode( ',', $attachments['ids'] ) : array();

		if ( empty ( $images_array ) ) {
			return $output;
		}

		foreach ( $images_array as $attachment ) {
			$image_src = wp_get_attachment_url( $attachment, 'tm-pb-post-main-image-fullwidth' );
			$gallery_output .= sprintf(
				'<div class="tm_pb_slide" style="background: url(%1$s);"></div>',
				esc_url( $image_src )
			);
		}
		printf(
			'<div class="tm_pb_slider tm_pb_slider_fullwidth_off tm_pb_gallery_post_type">
				<div class="tm_pb_slides">
					%1$s
				</div>
			</div>',
			$gallery_output
		);
	} else {
		add_filter( 'tm_gallery_layout_enable', 'tm_gallery_layout_turn_on' );
		printf( do_shortcode( '%1$s' ), get_post_gallery() );
		remove_filter( 'tm_gallery_layout_enable', 'tm_gallery_layout_turn_on' );
	}
}
endif;

/**
 * Used to always use divi gallery on tm_pb_gallery_images
 */
function tm_gallery_layout_turn_on() {
	return true;
}

/*
 * Remove Elegant Builder plugin filter, that activates visual mode on each page load in WP-Admin
 */
function tm_pb_remove_lb_plugin_force_editor_mode() {
	remove_filter( 'wp_default_editor', 'tm_force_tmce_editor' );
}
add_action( 'admin_init', 'tm_pb_remove_lb_plugin_force_editor_mode' );

/**
 *
 * Generates array of all Role options
 *
 */
function tm_pb_all_role_options() {
	// get all the modules and build array of capabilities for them
	$all_modules_array = TM_Builder_Element::get_modules_array();
	$module_capabilies = array();

	foreach ( $all_modules_array as $module => $module_details ) {
		if ( ! in_array( $module_details['label'], array( 'tm_pb_section', 'tm_pb_row', 'tm_pb_row_inner', 'tm_pb_column' ) ) ) {
			$module_capabilies[ $module_details['label'] ] = array(
				'name'    => sanitize_text_field( $module_details['title'] ),
				'default' => 'on',
			);
		}
	}

	// we need to display some options only when theme activated
	$theme_only_options = ! tm_is_builder_plugin_active()
		? array(
			'theme_customizer' => array(
				'name'           => esc_html__( 'Theme Customizer', 'tm_builder' ),
				'default'        => 'on',
				'applicability'  => array( 'administrator' ),
			),
			'module_customizer' => array(
				'name'           => esc_html__( 'Module Customizer', 'tm_builder' ),
				'default'        => 'on',
				'applicability'  => array( 'administrator' ),
			),
			'page_options' => array(
				'name'    => esc_html__( 'Page Options', 'tm_builder' ),
				'default' => 'on',
			),
		)
		: array();

	$all_role_options = array(
		'general_capabilities' => array(
			'section_title' => '',
			'options'       => array(
				'theme_options' => array(
					'name'           => tm_is_builder_plugin_active() ? esc_html__( 'Plugin Options', 'tm_builder' ) : esc_html__( 'Theme Options', 'tm_builder' ),
					'default'        => 'on',
					'applicability'  => array( 'administrator' ),
				),
				'divi_library' => array(
					'name'    => esc_html__( 'Library', 'tm_builder' ),
					'default' => 'on',
				),
			),
		),
		'builder_capabilities' => array(
			'section_title' => esc_html__( 'Builder Interface', 'tm_builder'),
			'options'       => array(
				'add_module' => array(
					'name'    => esc_html__( 'Add/Delete Item', 'tm_builder' ),
					'default' => 'on',
				),
				'edit_module' => array(
					'name'    => esc_html__( 'Edit Item', 'tm_builder' ),
					'default' => 'on',
				),
				'move_module' => array(
					'name'    => esc_html__( 'Move Item', 'tm_builder' ),
					'default' => 'on',
				),
				'disable_module' => array(
					'name'    => esc_html__( 'Disable Item', 'tm_builder' ),
					'default' => 'on',
				),
				'lock_module' => array(
					'name'    => esc_html__( 'Lock Item', 'tm_builder' ),
					'default' => 'on',
				),
				'divi_builder_control' => array(
					'name'    => esc_html__( 'Toggle Builder', 'tm_builder' ),
					'default' => 'on',
				),
				'load_layout' => array(
					'name'    => esc_html__( 'Load Layout', 'tm_builder' ),
					'default' => 'on',
				),
			),
		),
		'library_capabilities' => array(
			'section_title' => esc_html__( 'Library Settings', 'tm_builder' ),
			'options'       => array(
				'save_library' => array(
					'name'    => esc_html__( 'Save To Library', 'tm_builder' ),
					'default' => 'on',
				),
				'add_library' => array(
					'name'    => esc_html__( 'Add From Library', 'tm_builder' ),
					'default' => 'on',
				),
				'edit_global_library' => array(
					'name'    => esc_html__( 'Edit Global Items', 'tm_builder' ),
					'default' => 'on',
				),
			),
		),
		'module_tabs' => array(
			'section_title' => esc_html__( 'Settings Tabs', 'tm_builder' ),
			'options'       => array(
				'general_settings' => array(
					'name'    => esc_html__( 'General Settings', 'tm_builder' ),
					'default' => 'on',
				),
				'advanced_settings' => array(
					'name'    => esc_html__( 'Advanced Settings', 'tm_builder' ),
					'default' => 'on',
				),
				'custom_css_settings' => array(
					'name'    => esc_html__( 'Custom CSS', 'tm_builder' ),
					'default' => 'on',
				),
			),
		),
		'general_module_capabilities' => array(
			'section_title' => esc_html__( 'Settings Types', 'tm_builder' ),
			'options'       => array(
				'edit_colors' => array(
					'name'    => esc_html__( 'Edit Colors', 'tm_builder' ),
					'default' => 'on',
				),
				'edit_content' => array(
					'name'    => esc_html__( 'Edit Content', 'tm_builder' ),
					'default' => 'on',
				),
				'edit_fonts' => array(
					'name'    => esc_html__( 'Edit Fonts', 'tm_builder' ),
					'default' => 'on',
				),
				'edit_buttons' => array(
					'name'    => esc_html__( 'Edit Buttons', 'tm_builder' ),
					'default' => 'on',
				),
				'edit_layout' => array(
					'name'    => esc_html__( 'Edit Layout', 'tm_builder' ),
					'default' => 'on',
				),
				'edit_configuration' => array(
					'name'    => esc_html__( 'Edit Configuration', 'tm_builder' ),
					'default' => 'on',
				),
			),
		),
		'module_capabilies' => array(
			'section_title' => esc_html__( 'Module Use', 'tm_builder' ),
			'options'       => $module_capabilies,
		),
	);

	$all_role_options['general_capabilities']['options'] = array_merge( $all_role_options['general_capabilities']['options'], $theme_only_options );

	return $all_role_options;
}

/**
 *
 * Prints the admin page for Role Editor
 *
 */
function tm_pb_display_role_editor() {
	$all_role_options = tm_pb_all_role_options();
	$option_tabs = '';
	$menu_tabs = '';

	// get all roles registered in current WP
	if ( ! function_exists( 'get_editable_roles' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/user.php' );
	}

	$all_roles = get_editable_roles();
	$builder_roles_array = array();

	if ( ! empty( $all_roles ) ) {
		foreach( $all_roles as $role => $role_data ) {
			// add roles with edit_posts capability into $builder_roles_array
			if ( ! empty( $role_data['capabilities']['edit_posts'] ) && 1 === (int) $role_data['capabilities']['edit_posts'] ) {
				$builder_roles_array[ $role ] = $role_data['name'];
			}
		}
	}

	// fill the builder roles array with default roles if it's empty
	if ( empty( $builder_roles_array ) ) {
		$builder_roles_array = array(
			'administrator' => esc_html__( 'Administrator', 'tm_builder' ),
			'editor'        => esc_html__( 'Editor', 'tm_builder' ),
			'author'        => esc_html__( 'Author', 'tm_builder' ),
			'contributor'   => esc_html__( 'Contributor', 'tm_builder' ),
		);
	}

	foreach( $builder_roles_array as $role => $role_title ) {
		$option_tabs .= tm_pb_generate_roles_tab( $all_role_options, $role );

		$menu_tabs .= sprintf(
			'<a href="#" class="tm-pb-layout-buttons%4$s" data-open_tab="tm_pb_role-%3$s_options" title="%1$s">
				<span>%2$s</span>
			</a>',
			esc_attr( $role_title ),
			esc_html( $role_title ),
			esc_attr( $role ),
			'administrator' === $role ? ' tm_pb_roles_active_menu' : ''
		);
	}

	printf(
		'<div class="tm_pb_roles_main_container">
			<a href="#" id="tm_pb_save_roles" class="button button-primary button-large">%3$s</a>
			<h3 class="tm_pb_roles_title"><span>%2$s</span></h3>
			<div id="tm_pb_main_container" class="post-type-page">
				<div id="tm_pb_layout_controls">
					%1$s
					<a href="#" class="tm-pb-layout-buttons tm-pb-layout-buttons-reset" title="Reset all settings">
						<span class="icon"></span><span class="label">Reset</span>
					</a>
				</div>
			</div>
			<div class="tm_pb_roles_container_all">
				%4$s
			</div>
		</div>',
		$menu_tabs,
		esc_html__( 'Role Editor', 'tm_builder' ),
		esc_html__( 'Save Roles', 'tm_builder' ),
		$option_tabs
	);
}

/**
 *
 * Generates the options tab for specified role.
 *
 * @return string
 */
function tm_pb_generate_roles_tab( $all_role_options, $role ) {
	$form_sections = '';

	// generate all sections of the form for current role.
	if ( ! empty( $all_role_options ) ) {
		foreach( $all_role_options as $capability_id => $capability_options ) {
			$form_sections .= sprintf(
				'<div class="tm_pb_roles_section_container">
					%1$s
					<div class="tm_pb_roles_options_internal">
						%2$s
					</div>
				</div>',
				! empty( $capability_options['section_title'] )
					? sprintf( '<h4 class="tm_pb_roles_divider">%1$s <span class="tm_pb_toggle_all"></span></h4>', esc_html( $capability_options['section_title'] ) )
					: '',
				tm_pb_generate_capabilities_output( $capability_options['options'], $role )
			);
		}
	}

	$output = sprintf(
		'<div class="tm_pb_roles_options_container tm_pb_role-%2$s_options%3$s">
			<p class="tm_pb_roles_notice">%1$s</p>
			<form id="tm_pb_%2$s_role" data-role_id="%2$s">
				%4$s
			</form>
		</div>',
		esc_html__( 'Using the Role Editor, you can limit the types of actions that can be taken by WordPress users of different roles. This is a great way to limit the functionality available to your customers or guest authors to ensure that they only have the necessary options available to them.', 'tm_builder' ),
		esc_attr( $role ),
		'administrator' === $role ? ' active-container' : '',
		$form_sections // #4
	);

	return $output;
}

/**
 *
 * Generates the enable/disable buttons list based on provided capabilities array and role
 *
 * @return string
 */
function tm_pb_generate_capabilities_output( $cap_array, $role ) {
	$output = '';
	$saved_capabilities = get_option( 'tm_pb_role_settings', array() );

	if ( ! empty( $cap_array ) ) {
		foreach ( $cap_array as $capability => $capability_details ) {
			if ( empty( $capability_details['applicability'] ) || ( ! empty( $capability_details['applicability'] ) && in_array( $role, $capability_details['applicability'] ) ) ) {
				$output .= sprintf(
					'<div class="tm_pb_capability_option">
						<span class="tm_pb_capability_title">%4$s</span>
						<div class="tm_pb_yes_no_button_wrapper">
							<div class="tm_pb_yes_no_button tm_pb_on_state">
								<span class="tm_pb_value_text tm_pb_on_value">%1$s</span>
								<span class="tm_pb_button_slider"></span>
								<span class="tm_pb_value_text tm_pb_off_value">%2$s</span>
							</div>
							<select name="%3$s" id="%3$s" class="tm-pb-main-setting regular-text">
								<option value="on" %5$s>Yes</option>
								<option value="off" %6$s>No</option>
							</select>
						</div>
					</div>',
					esc_html__( 'Enable', 'tm_builder' ),
					esc_html__( 'Disable', 'tm_builder' ),
					esc_attr( $capability ),
					esc_html( $capability_details['name'] ),
					! empty( $saved_capabilities[$role][$capability] ) ? selected( 'on', $saved_capabilities[$role][$capability], false ) : selected( 'on', $capability_details['default'], false ),
					! empty( $saved_capabilities[$role][$capability] ) ? selected( 'off', $saved_capabilities[$role][$capability], false ) : selected( 'off', $capability_details['default'], false )
				);
			}
		}
	}

	return $output;
}

/**
 *
 * Loads scripts and styles for Role Editor Admin page
 *
 */
function tm_pb_load_roles_admin( $hook ) {
	// load scripts only on role editor page

	if ( apply_filters( 'tm_pb_load_roles_admin_hook', 'divi_page_tm_divi_role_editor' ) !== $hook ) {
		return;
	}

	wp_enqueue_style( 'builder-roles-editor-styles', TM_BUILDER_URI . '/framework/admin/assets/css/roles-style.css' );
	wp_enqueue_script( 'builder-roles-editor-scripts', TM_BUILDER_URI . '/framework/admin/assets/js/roles-admin.js', array( 'jquery' ), TM_BUILDER_VERSION, true );
	wp_localize_script( 'builder-roles-editor-scripts', 'tm_pb_roles_options', array(
		'ajaxurl'        => admin_url( 'admin-ajax.php' ),
		'tm_roles_nonce' => wp_create_nonce( 'tm_roles_nonce' ),
		'modal_title'    => esc_html__( 'Reset Roles', 'tm_builder' ),
		'modal_message'  => esc_html__( 'All of your current role settings will be set to defaults. Do you wish to proceed?', 'tm_builder' ),
		'modal_yes'      => esc_html__( 'Yes', 'tm_builder' ),
		'modal_no'       => esc_html__( 'no', 'tm_builder' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'tm_pb_load_roles_admin' );

/**
 * Saves the Role Settings into WP database
 * @return void
 */
function tm_pb_save_role_settings() {
	if ( ! wp_verify_nonce( $_POST['tm_pb_save_roles_nonce'] , 'tm_roles_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	// handle received data and convert json string to array
	$data_json = str_replace( '\\', '' ,  $_POST['tm_pb_options_all'] );
	$data = json_decode( $data_json, true );
	$processed_options = array();

	// convert settings string for each role into array and save it into tm_pb_role_settings option
	if ( ! empty( $data ) ) {
		foreach( $data as $role => $settings ) {
			parse_str( $data[ $role ], $processed_options[ $role ] );
		}
	}

	update_option( 'tm_pb_role_settings', $processed_options );

	die();
}
add_action( 'wp_ajax_tm_pb_save_role_settings', 'tm_pb_save_role_settings' );

/**
 * Check whether the specified capability allowed for current user
 * @return bool
 */
function tm_pb_is_allowed( $capability, $role = '' ) {
	$saved_capabilities = tm_pb_get_role_settings();
	$role = '' === $role ? tm_pb_get_current_user_role() : $role;

	if ( ! empty( $saved_capabilities[ $role ][ $capability ] ) ) {
		$verdict = 'off' === $saved_capabilities[ $role ][ $capability ] ? false : true;
	} else {
		return true;
	}

	return $verdict;
}

/**
 * Generates the array of allowed modules in jQuery Array format
 * @return string
 */
function tm_pb_allowed_modules_list( $role = '' ) {
	global $typenow;

	$saved_capabilities = tm_pb_get_role_settings();
	$role = '' === $role ? tm_pb_get_current_user_role() : $role;

	$all_modules_array = TM_Builder_Element::get_modules_array( $typenow );

	$saved_modules_capabilities = isset( $saved_capabilities[ $role ] ) ? $saved_capabilities[ $role ] : array();

	$alowed_modules = "[";
	foreach ( $all_modules_array as $module => $module_details ) {
		if ( ! in_array( $module_details['label'], array( 'tm_pb_section', 'tm_pb_row', 'tm_pb_row_inner', 'tm_pb_column' ) ) ) {
			// Add module into the list if it's not saved or if it's saved not with "off" state
			if ( ! isset( $saved_modules_capabilities[ $module_details['label'] ] ) || ( isset( $saved_modules_capabilities[ $module_details['label'] ] ) && 'off' !== $saved_modules_capabilities[ $module_details['label'] ] ) ) {
				$alowed_modules .= "'" . $module_details['label'] . "',";
			}
		}
	}

	$alowed_modules .= "]";

	return $alowed_modules;
}

/**
 * Determines the current user role
 * @return string
 */
function tm_pb_get_current_user_role() {
	$current_user = wp_get_current_user();
	$user_roles = $current_user->roles;

	$role = ! empty( $user_roles ) ? $user_roles[0] : '';

	return $role;
}

/**
 * Gets the array of role settings
 * @return string
 */
function tm_pb_get_role_settings() {
	global $tm_pb_role_settings;

	// if we don't have saved global variable, then get the value from WPDB
	$tm_pb_role_settings = isset( $tm_pb_role_settings ) ? $tm_pb_role_settings : get_option( 'tm_pb_role_settings', array() );

	return $tm_pb_role_settings;
}

if ( ! function_exists( 'tm_divi_get_post_text_color' ) ) {
	function tm_divi_get_post_text_color() {
		$text_color_class = '';

		$post_format = tm_pb_post_format();

		if ( in_array( $post_format, array( 'audio', 'link', 'quote' ) ) ) {
			$text_color_class = ( $text_color = get_post_meta( get_the_ID(), '_tm_post_bg_layout', true ) ) ? $text_color : 'light';
			$text_color_class = ' tm_pb_text_color_' . $text_color_class;
		}

		return $text_color_class;
	}
}

if ( ! function_exists( 'tm_divi_get_post_bg_inline_style' ) ) {
	function tm_divi_get_post_bg_inline_style() {
		$inline_style = '';

		$post_id = get_the_ID();

		$post_use_bg_color = get_post_meta( $post_id, '_tm_post_use_bg_color', true )
			? true
			: false;
		$post_bg_color  = ( $bg_color = get_post_meta( $post_id, '_tm_post_bg_color', true ) ) && '' !== $bg_color
			? $bg_color
			: '#ffffff';

		if ( $post_use_bg_color ) {
			$inline_style = sprintf( ' style="background-color: %1$s;"', esc_html( $post_bg_color ) );
		}

		return $inline_style;
	}
}

/**
 * Register rewrite rule and tag for preview page
 * @return void
 */
function tm_pb_register_preview_endpoint() {
	add_rewrite_tag( '%tm_pb_preview%', 'true' );
}
add_action( 'init', 'tm_pb_register_preview_endpoint', 11 );

/**
 * Flush rewrite rules to fix the issue "preg_match" issue with 2.5
 * @return void
 */
function tm_pb_maybe_flush_rewrite_rules() {
	$setting_name = 'tm_builder_flush_rewrite_rules';

	if ( get_option( $setting_name ) ) {
		return;
	}

	flush_rewrite_rules();

	update_option( $setting_name, 'done' );
}
add_action( 'init', 'tm_pb_maybe_flush_rewrite_rules', 9 );

/*
 * do_shortcode() replaces square brackers with html entities,
 * convert them back to make sure js code works ok
 */
if ( ! function_exists( 'tm_builder_replace_code_content_entities' ) ) :
function tm_builder_replace_code_content_entities( $content ) {
	$content = str_replace( '&#091;', '[', $content );
	$content = str_replace( '&#093;', ']', $content );

	return $content;
}
endif;

// adjust the number of all layouts displayed on library page to exclude predefined layouts
function tm_pb_fix_count_library_items( $counts ) {
	// do nothing if get_current_screen function doesn't exists at this point to avoid php errors in some plugins.
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $counts;
	}

	$current_screen = get_current_screen();

	if ( isset( $current_screen->id ) && 'edit-tm_pb_layout' === $current_screen->id && isset( $counts->publish ) ) {
		// perform query to get all the not predefined layouts
		$query = new WP_Query( array(
			'meta_query'      => array(
				array(
					'key'     => '_tm_pb_predefined_layout',
					'value'   => 'on',
					'compare' => 'NOT EXISTS',
				),
			),
			'post_type'       => TM_BUILDER_LAYOUT_POST_TYPE,
			'posts_per_page'  => '-1',
		) );

		// set the $counts->publish = amount of non predefined layouts
		$counts->publish = isset( $query->post_count ) ? (int) $query->post_count : 0;
	}

	return $counts;
}
add_filter( 'wp_count_posts', 'tm_pb_fix_count_library_items' );

function tm_pb_generate_mobile_options_tabs() {
	$mobile_settings_tabs = '<%= window.tm_builder.mobile_tabs_output() %>';

	return $mobile_settings_tabs;
}

// Generates the css code for responsive options.
// Uses array of values for each device as input parameter and css_selector with property to apply the css
function tm_pb_generate_responsive_css( $values_array, $css_selector, $css_property, $function_name, $additional_css = '' ) {
	if ( ! empty( $values_array ) ) {
		foreach( $values_array as $device => $current_value ) {
			if ( '' === $current_value ) {
				continue;
			}

			$declaration = '';

			// value can be provided as a string or array in following format - array( 'property_1' => 'value_1', 'property_2' => 'property_2', ... , 'property_n' => 'value_n' )
			if ( is_array( $current_value ) && ! empty( $current_value ) ) {
				foreach( $current_value as $this_property => $this_value ) {
					if ( '' === $this_value ) {
						continue;
					}

					$declaration .= sprintf(
						'%1$s: %2$s%3$s',
						$this_property,
						esc_html( tm_builder_process_range_value( $this_value ) ),
						'' !== $additional_css ? $additional_css : ';'
					);
				}
			} else {
				$declaration = sprintf(
					'%1$s: %2$s%3$s',
					$css_property,
					esc_html( tm_builder_process_range_value( $current_value ) ),
					'' !== $additional_css ? $additional_css : ';'
				);
			}

			if ( '' === $declaration ) {
				continue;
			}

			$style = array(
				'selector'    => $css_selector,
				'declaration' => $declaration,
			);

			if ( 'desktop' !== $device ) {
				switch ( $device ) {
					case 'tablet':
						$current_media_query = 'max_width_980';
						break;

					case 'laptop':
						$current_media_query = '981_1440';
						break;

					default:
						$current_media_query = 'max_width_767';
						break;
				}

				$style['media_query'] = TM_Builder_Element::get_media_query( $current_media_query );
			}
			TM_Builder_Element::set_style( $function_name, $style );
		}
	}
}

function tm_pb_custom_search( $query = false ) {
	if ( is_admin() || ! is_a( $query, 'WP_Query' ) || ! $query->is_search ) {
		return;
	}

	if ( isset( $_GET['tm_pb_searchform_submit'] ) ) {
		$postTypes = array();
		if ( ! isset($_GET['tm_pb_include_posts'] ) && ! isset( $_GET['tm_pb_include_pages'] ) ) $postTypes = array( 'post' );
		if ( isset( $_GET['tm_pb_include_pages'] ) ) $postTypes = array( 'page' );
		if ( isset( $_GET['tm_pb_include_posts'] ) ) $postTypes[] = 'post';
		$query->set( 'post_type', $postTypes );

		if ( ! empty( $_GET['tm_pb_search_cat'] ) ) {
			$categories_array = explode( ',', $_GET['tm_pb_search_cat'] );
			$query->set( 'category__not_in', $categories_array );
		}

		if ( isset( $_GET['tm-posts-count'] ) ) {
			$query->set( 'posts_per_page', (int) $_GET['tm-posts-count'] );
		}
	}
}
add_action( 'pre_get_posts', 'tm_pb_custom_search' );

if ( ! function_exists( 'tm_custom_comments_display' ) ) :
function tm_custom_comments_display( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
			<div class="comment_avatar">
				<?php echo get_avatar( $comment, $size = '80' ); ?>
			</div>

			<div class="comment_postinfo">
				<?php printf( '<span class="fn">%s</span>', get_comment_author_link() ); ?>
				<span class="comment_date">
				<?php
					/* translators: 1: date, 2: time */
					printf( esc_html__( 'on %1$s at %2$s', 'tm_builder' ), get_comment_date(), get_comment_time() );
				?>
				</span>
				<?php edit_comment_link( esc_html__( '(Edit)', 'tm_builder' ), ' ' ); ?>
			<?php
				$tm_comment_reply_link = get_comment_reply_link( array_merge( $args, array(
					'reply_text' => esc_html__( 'Reply', 'tm_builder' ),
					'depth'      => (int) $depth,
					'max_depth'  => (int) $args['max_depth'],
				) ) );
			?>
			</div> <!-- .comment_postinfo -->

			<div class="comment_area">
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<em class="moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'tm_builder' ) ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-content clearfix">
				<?php
					comment_text();
					if ( $tm_comment_reply_link ) echo '<span class="reply-container">' . $tm_comment_reply_link . '</span>';
				?>
				</div> <!-- end comment-content-->
			</div> <!-- end comment_area-->
		</article> <!-- .comment-body -->
<?php }
endif;

function tm_pb_execute_content_shortcodes() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$unprocessed_data = $_POST['tm_pb_unprocessed_data'];

	echo do_shortcode( $unprocessed_data );
}
add_action( 'wp_ajax_tm_pb_execute_content_shortcodes', 'tm_pb_execute_content_shortcodes' );

/* Exclude library related taxonomies from Yoast SEO Sitemap */
function tm_wpseo_sitemap_exclude_taxonomy( $value, $taxonomy ) {
	$excluded = array( 'scope', 'module_width', 'layout_type', 'layout_category', 'layout' );

	if ( in_array( $taxonomy, $excluded ) ) {
		return true;
	}

	return false;
}
add_filter( 'wpseo_sitemap_exclude_taxonomy', 'tm_wpseo_sitemap_exclude_taxonomy', 10, 2 );

/**
 * Is Yoast SEO plugin active?
 *
 * @return bool  True - if the plugin is active
 */
if ( ! function_exists( 'tm_is_yoast_seo_plugin_active' ) ) :
function tm_is_yoast_seo_plugin_active() {
	return class_exists( 'WPSEO_Options' );
}
endif;

/**
 * Display the Notice about cache once the theme was udpated
 */
function tm_pb_maybe_display_cache_notice() {
	$ignore_notice_option = get_option( 'tm_pb_cache_notice', array() );
	$ignore_this_notice = empty( $ignore_notice_option[ TM_BUILDER_VERSION ] ) ? 'show' : $ignore_notice_option[ TM_BUILDER_VERSION ];
	$screen = get_current_screen();

	// check whether any cache plugin installed and get its page link
	$plugin_page = tm_pb_detect_cache_plugins();

	if ( current_user_can( 'manage_options' ) && 'post' === $screen->base && 'ignore' !== $ignore_this_notice && false !== $plugin_page ) {
		$hide_button = sprintf(
			' <br> <a class="tm_pb_hide_cache_notice" href="%3$s">%2$s</a> <a class="tm_pb_hide_cache_notice" href="#">%1$s</a>',
			esc_html__( 'Hide Notice', 'tm_builder' ),
			esc_html__( 'Clear Cache', 'tm_builder' ),
			esc_url( $plugin_page )
		);

		$notice_text = tm_get_safe_localization( __( 'The Builder has been updated, but you are currently using a caching plugin. Please clear your plugin cache <strong>and</strong> clear your browser cache (in that order) to make sure you are loading the updated builder files. Loading cached files may cause the builder to malfunction.', 'tm_builder' ) );

		printf( '<div class="update-nag tm-pb-update-nag"><p>%1$s%2$s</p></div>', $notice_text, $hide_button );
	}
}
add_action( 'admin_notices', 'tm_pb_maybe_display_cache_notice' );

/**
 * Update tm_pb_cache_notice option to indicate that Cache Notice was closed for current version of theme
 */
function tm_pb_hide_cache_notice() {
	if ( ! wp_verify_nonce( $_POST['tm_admin_load_nonce'], 'tm_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	update_option(
		'tm_pb_cache_notice',
		array( TM_BUILDER_VERSION => 'ignore',
		)
	);
}
add_action( 'wp_ajax_tm_pb_hide_cache_notice', 'tm_pb_hide_cache_notice' );

/**
 * Detect the activated cache plugins and return the link to plugin options and return its page link or false
 * @return string or bool
 */
function tm_pb_detect_cache_plugins() {
	if ( function_exists( 'edd_w3edge_w3tc_activate_license' ) ) {
		return 'admin.php?page=w3tc_pgcache';
	}

	if ( function_exists( 'wpsupercache_activate' ) ) {
		return 'options-general.php?page=wpsupercache';
	}

	if ( class_exists( 'HyperCache' ) ) {
		return 'options-general.php?page=hyper-cache%2Foptions.php';
	}

	if ( class_exists( '\zencache\plugin' ) ) {
		return 'admin.php?page=zencache';
	}

	if ( class_exists( 'WpFastestCache' ) ) {
		return 'admin.php?page=WpFastestCacheOptions';
	}

	if ( '1' === get_option( 'wordfenceActivated' ) ) {
		return 'admin.php?page=WordfenceSitePerf';
	}

	if ( function_exists( 'cachify_autoload' ) ) {
		return 'options-general.php?page=cachify';
	}

	if ( class_exists( 'FlexiCache' ) ) {
		return 'options-general.php?page=flexicache';
	}

	if ( function_exists( 'rocket_init' ) ) {
		return 'options-general.php?page=wprocket';
	}

	if ( function_exists( 'cloudflare_init' ) ) {
		return 'options-general.php?page=cloudflare';
	}

	return false;
}

/**
 * Get image by id
 * @param  string $image_url Image url string
 * @return int            Attachment id
 */
function tm_get_image_id( $image_url ) {

	global $wpdb;

	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );

	if ( isset( $attachment[0] ) ) {
		return $attachment[0];
	} else {
		return false;
	}
}

/**
 * Get map style
 *
 * @return [type] [description]
 */
function get_google_map_styles() {
	$map_style_array = array();

	$plugin_path = TM_BUILDER_DIR . '/framework/assets/googlemap/';
	$theme_path  = get_stylesheet_directory() . '/assets/googlemap/';

	if ( file_exists( $plugin_path ) && is_dir( $plugin_path ) ) {
		$plugin_map_styles = scandir( $plugin_path );
		$plugin_map_styles = array_diff( $plugin_map_styles, array( '.', '..', 'index.php' ) );
	}

	if ( file_exists( $theme_path ) && is_dir( $theme_path ) ) {
		$theme_map_styles = scandir( $theme_path );
		$theme_map_styles = array_diff( $theme_map_styles, array( '.', '..', 'index.php' ) );
		$map_style_array  = array_merge( $theme_map_styles, $plugin_map_styles );
	} else {
		$map_style_array = $plugin_map_styles;
	}

	foreach ( $map_style_array as $key => $value) {
		$result_array[ str_replace( '.json', '', $value ) ] = $value;
	}

	return $result_array;
}

/**
 * Get map style json
 *
 * @param  string $map_style Map style string
 * @return string
 */
function get_map_style_json( $map_style ){
	$theme_path  = get_stylesheet_directory() . '/assets/googlemap/';
	$plugin_path = TM_BUILDER_DIR . '/framework/assets/googlemap/';

	$map_style_path = $theme_path . $map_style . '.json';

	if ( file_exists( $map_style_path ) ) {
		$style = file_get_contents( $map_style_path );

		return $style;
	}

	$map_style_path = $plugin_path . $map_style . '.json';

	if ( file_exists( $map_style_path ) ) {
		$style = file_get_contents( $map_style_path );

		return $style;
	}

	return '';
}
