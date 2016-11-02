<?php
function tm_builder_register_layouts(){
	$labels = array(
		'name'               => esc_html_x( 'Layouts', 'Layout type general name', 'tm_builder' ),
		'singular_name'      => esc_html_x( 'Layout', 'Layout type singular name', 'tm_builder' ),
		'add_new'            => esc_html_x( 'Add New', 'Layout item', 'tm_builder' ),
		'add_new_item'       => esc_html__( 'Add New Layout', 'tm_builder' ),
		'edit_item'          => esc_html__( 'Edit Layout', 'tm_builder' ),
		'new_item'           => esc_html__( 'New Layout', 'tm_builder' ),
		'all_items'          => esc_html__( 'All Layouts', 'tm_builder' ),
		'view_item'          => esc_html__( 'View Layout', 'tm_builder' ),
		'search_items'       => esc_html__( 'Search Layouts', 'tm_builder' ),
		'not_found'          => esc_html__( 'Nothing found', 'tm_builder' ),
		'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'tm_builder' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'publicly_queryable' => false,
		'can_export'         => true,
		'query_var'          => false,
		'has_archive'        => false,
		'capability_type'    => 'post',
		'map_meta_cap'       => true,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'revisions' ),
	);

	if ( ! defined( 'TM_BUILDER_LAYOUT_POST_TYPE' ) ) {
		define( 'TM_BUILDER_LAYOUT_POST_TYPE', 'tm_pb_layout' );
	}

	register_post_type( TM_BUILDER_LAYOUT_POST_TYPE, apply_filters( 'tm_pb_layout_args', $args ) );

	$labels = array(
		'name'              => esc_html__( 'Scope', 'tm_builder' )
	);

	register_taxonomy( 'scope', array( 'tm_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Layout Type', 'tm_builder' )
	);

	register_taxonomy( 'layout_type', array( 'tm_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Module Width', 'tm_builder' )
	);

	register_taxonomy( 'module_width', array( 'tm_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Category', 'tm_builder' )
	);

	register_taxonomy( 'layout_category', array( 'tm_pb_layout' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );
}
tm_builder_register_layouts();

foreach( array( 'edit', 'post' ) as $hook ) {
	add_action( "admin_head-{$hook}.php", 'tm_builder_library_custom_styles' );
}

//remove "edit" action from the bulk changes on tm_pb_layout editor screen
function builder_customize_bulk( $actions ) {
	unset( $actions['edit'] );

	return $actions;
}
add_filter( 'bulk_actions-edit-tm_pb_layout', 'builder_customize_bulk' );


function tm_pb_get_used_built_for_post_types() {
	global $wpdb;

	$built_for_post_types = $wpdb->get_col(
		"SELECT DISTINCT( meta_value )
		FROM $wpdb->postmeta
		WHERE meta_key = '_tm_pb_built_for_post_type'
		AND meta_value IS NOT NULL
		AND meta_value != ''
		"
	);

	return $built_for_post_types;
}

function tm_pb_layout_restrict_manage_posts() {
	global $pagenow;

	if ( ! is_admin() || 'edit.php' !== $pagenow || ! isset( $_GET['post_type'] ) || 'tm_pb_layout' !== $_GET['post_type'] ) {
		return;
	}

	$used_built_for_post_types = tm_pb_get_used_built_for_post_types();

	if ( count( $used_built_for_post_types ) <= 1 ) {
		return;
	}

	$built_for_post_type_request = isset( $_GET['built_for'] ) ? sanitize_text_field( $_GET['built_for'] ) : '';

	if ( ! in_array( $built_for_post_type_request, $used_built_for_post_types ) ) {
		$built_for_post_type_request = '';
	}

	?>
	<select name="built_for">
		<option><?php esc_html_e( 'Built For Any', 'tm_builder' ); ?></option>
		<?php $is_default_added = false; ?>
		<?php foreach ( $used_built_for_post_types as $built_for_post_type ) { ?>
		<?php $is_default_post_type = in_array( $built_for_post_type, tm_pb_get_standard_post_types() );
			// do not add default post types into the menu if it was added already
			if ( $is_default_post_type && $is_default_added ) {
				continue;
			}
			?>
			<?php $built_for_post_type_display = apply_filters( 'tm_pb_built_for_post_type_display', $built_for_post_type ); ?>
			<option value="<?php echo esc_attr( $built_for_post_type ); ?>" <?php selected( $built_for_post_type_request, $built_for_post_type ); ?>><?php echo esc_html( ucwords( $built_for_post_type_display ) ); ?></option>
		<?php
			$is_default_added = $is_default_post_type ? true : $is_default_added;
		} ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'tm_pb_layout_restrict_manage_posts' );

function tm_pb_layout_manage_posts_columns( $columns ) {
	$_new_columns = array();
	foreach ( $columns as $column_key => $column ) {
		$_new_columns[ $column_key ] = $column;

		if ( 'taxonomy-layout_type' === $column_key ) {
			$_new_columns['built_for'] = esc_html__( 'Built For', 'tm_builder' );
			$_new_columns['layout_global'] = esc_html__( 'Global Layout', 'tm_builder' );
		}
	}

	return $_new_columns;
}
add_filter( 'manage_tm_pb_layout_posts_columns', 'tm_pb_layout_manage_posts_columns' );

function tm_pb_built_for_post_type_display( $post_type ) {
	$standard_post_types = tm_pb_get_standard_post_types();

	if ( in_array( $post_type, $standard_post_types ) ) {
		return esc_html__( 'Standard', 'tm_builder' );
	}

	return $post_type;
}

add_filter( 'tm_pb_layout_built_for_post_type_column', 'tm_pb_built_for_post_type_display' );
add_filter( 'tm_pb_built_for_post_type_display', 'tm_pb_built_for_post_type_display' );

function tm_pb_get_standard_post_types() {
	$standard_post_types = apply_filters( 'tm_pb_standard_post_types', array(
		'post',
		'page',
		'project',
	) );

	return $standard_post_types;
}

function tm_pb_layout_manage_posts_custom_column( $column_key, $post_id ) {
	switch ( $column_key ) {
		case 'built_for':
			$built_for = get_post_meta( $post_id, '_tm_pb_built_for_post_type', true );
			$built_for = apply_filters( 'tm_pb_layout_built_for_post_type_column', $built_for, $post_id );
			echo esc_html( ucwords( $built_for ) );
			break;
		case 'layout_global':
			$template_scope = wp_get_object_terms( $post_id, 'scope' );
			$is_global_template = ! empty( $template_scope[0] ) ? $template_scope[0]->slug : 'regular';
			$is_global_template = str_replace( '_', ' ', $is_global_template );
			echo esc_html( ucwords( $is_global_template ) );
			break;
	}
}
add_action( 'manage_tm_pb_layout_posts_custom_column', 'tm_pb_layout_manage_posts_custom_column', 10, 2 );

function tm_update_old_layouts_tax() {
	$layouts_updated = get_theme_mod( 'tm_pb_layouts_updated', 'no' );

	if ( 'yes' !== $layouts_updated ) {
		$query = new WP_Query( array(
			'meta_query'      => array(
				'relation' => 'AND',
				array(
					'key'     => '_tm_pb_predefined_layout',
					'value'   => 'on',
					'compare' => 'NOT EXISTS',
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
		) );

		wp_reset_postdata();

		if ( ! empty ( $query->posts ) ) {
			foreach( $query->posts as $single_post ) {

				$defined_layout_type = wp_get_post_terms( $single_post->ID, 'layout_type' );

				if ( empty( $defined_layout_type ) ) {
					wp_set_post_terms( $single_post->ID, 'layout', 'layout_type' );
				}
			}
		}

		set_theme_mod( 'tm_pb_layouts_updated', 'yes' );
	}
}
add_action( 'admin_init', 'tm_update_old_layouts_tax' );

// update existing layouts to support _tm_pb_built_for_post_type
function tm_update_layouts_built_for_post_types() {
	$layouts_updated = get_theme_mod( 'tm_updated_layouts_built_for_post_types', 'no' );
	if ( 'yes' !== $layouts_updated ) {
		$query = new WP_Query( array(
			'meta_query'      => array(
				'relation' => 'AND',
				array(
					'key'     => '_tm_pb_built_for_post_type',
					'compare' => 'NOT EXISTS',
				),
			),
			'post_type'       => TM_BUILDER_LAYOUT_POST_TYPE,
			'posts_per_page'  => '-1',
		) );

		wp_reset_postdata();

		if ( ! empty ( $query->posts ) ) {
			foreach( $query->posts as $single_post ) {
				update_post_meta( $single_post->ID, '_tm_pb_built_for_post_type', 'page' );
			}
		}

		set_theme_mod( 'tm_updated_layouts_built_for_post_types', 'yes' );
	}
}
add_action( 'admin_init', 'tm_update_layouts_built_for_post_types' );

function tm_builder_library_custom_styles() {
	global $typenow;

	if ( 'tm_pb_layout' === $typenow ) {
		$new_layout_modal = tm_pb_generate_new_layout_modal();

		wp_enqueue_style( 'library-styles', TM_BUILDER_URI . '/framework/admin/assets/css/library-pages.css' );
		wp_enqueue_script( 'library-scripts', TM_BUILDER_URI . '/framework/admin/assets/js/library-scripts.js', array( 'jquery' ) );
		wp_localize_script( 'library-scripts', 'tm_pb_new_template_options', array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'tm_admin_load_nonce' => wp_create_nonce( 'tm_admin_load_nonce' ),
				'modal_output'  => $new_layout_modal,
			)
		);
	}
}

define( 'TM_BUILDER_PREDEFINED_LAYOUTS_VERSION', 2 );

function tm_pb_update_predefined_layouts() {
	// don't do anything if layouts version have been updated and layouts exist
	if ( 'on' === get_theme_mod( 'tm_pb_predefined_layouts_version_' . TM_BUILDER_PREDEFINED_LAYOUTS_VERSION ) && ( tm_pb_predefined_layouts_exist() ) ) {
		return;
	}

	// delete default layouts
	// delete all default layouts w/o new built_for meta
	tm_pb_delete_predefined_layouts();
	// delete all default layouts w/ new built_for meta
	tm_pb_delete_predefined_layouts('page');

	// add predefined layouts
	tm_pb_add_predefined_layouts();

	set_theme_mod( 'tm_pb_predefined_layouts_version_' . TM_BUILDER_PREDEFINED_LAYOUTS_VERSION, 'on' );
}
add_action( 'admin_init', 'tm_pb_update_predefined_layouts' );

// check whether at least 1 predefined layout exists in DB and return its ID
if ( ! function_exists( 'tm_pb_predefined_layouts_exist' ) ) :
function tm_pb_predefined_layouts_exist() {
	$args = array(
		'posts_per_page' => 1,
		'post_type'      => TM_BUILDER_LAYOUT_POST_TYPE,
		'meta_query'      => array(
			'relation' => 'AND',
			array(
				'key'     => '_tm_pb_predefined_layout',
				'value'   => 'on',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => '_tm_pb_built_for_post_type',
				'value'   => 'page',
				'compare' => 'IN',
			)
		),
	);

	$predefined_layout = get_posts( $args );

	if ( ! $predefined_layout ) {
		return false;
	}

	return $predefined_layout[0]->ID;
}
endif;

if ( ! function_exists( 'tm_pb_delete_predefined_layouts' ) ) :
function tm_pb_delete_predefined_layouts( $built_for_post_type = '' ) {
	$args = array(
		'posts_per_page' => -1,
		'post_type'      => TM_BUILDER_LAYOUT_POST_TYPE,
		'meta_query'      => array(
			'relation' => 'AND',
			array(
				'key'     => '_tm_pb_predefined_layout',
				'value'   => 'on',
				'compare' => 'EXISTS',
			),
		),
	);

	if ( ! empty( $built_for_post_type ) ) {
		$args['meta_query'][] = array(
			'key'     => '_tm_pb_built_for_post_type',
			'value'   => $built_for_post_type,
			'compare' => 'IN',
		);
	} else {
		$args['meta_query'][] = array(
			'key'     => '_tm_pb_built_for_post_type',
			'compare' => 'NOT EXISTS',
		);
	}

	$predefined_layouts = get_posts( $args );

	if ( $predefined_layouts ) {
		foreach ( $predefined_layouts as $predefined_layout ) {
			if ( isset( $predefined_layout->ID ) ) {
				wp_delete_post( $predefined_layout->ID, true );
			}
		}
	}
}
endif;

if ( ! function_exists( 'tm_pb_add_predefined_layouts' ) ) :
function tm_pb_add_predefined_layouts() {
	$tm_builder_layouts = tm_pb_get_predefined_layouts();

	$meta = array(
		'_tm_pb_predefined_layout'   => 'on',
		'_tm_pb_built_for_post_type' => 'page',
	);

	if ( isset( $tm_builder_layouts ) && is_array( $tm_builder_layouts ) ) {
		foreach ( $tm_builder_layouts as $tm_builder_layout ) {
			tm_pb_create_layout( $tm_builder_layout['name'], $tm_builder_layout['content'], $meta );
		}
	}

	set_theme_mod( 'tm_pb_predefined_layouts_added', 'on' );
}
endif;

if ( ! function_exists( 'tm_pb_get_predefined_layouts' ) ) :
function tm_pb_get_predefined_layouts() {
	$layouts = array();

	$layouts[] = array(
		'name'    => esc_html__( 'About Us Button', 'tm_builder' ),
		'content' => '[tm_pb_section admin_label="section"][tm_pb_row admin_label="row"][tm_pb_column type="4_4"][tm_pb_button admin_label="Button" button_url="#about-us" url_new_window="off" button_text="About Us" button_alignment="left" custom_button="off" button_letter_spacing="0" button_use_icon="default" button_icon_placement="right" button_on_hover="on" button_letter_spacing_hover="0" /][/tm_pb_column][/tm_pb_row][/tm_pb_section]',
	);

	return $layouts;
}
endif;
