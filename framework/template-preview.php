<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die('-1');
}

// Early nonce check
if ( ! isset( $_GET['tm_pb_preview_nonce'] ) || ! wp_verify_nonce( $_GET['tm_pb_preview_nonce'], 'tm_pb_preview_nonce' ) ) {
	wp_die( esc_html__( 'Authentication failed. You cannot preview this item.', 'tm_builder' ) );
}

// Logged in check
if ( ! is_user_logged_in() ) {
	wp_die( esc_html__( 'Authentication failed. You are not logged in.', 'tm_builder' ) );
}

// Early permission check
if ( ! current_user_can( 'edit_posts' ) ) {
	wp_die( esc_html__( 'Authentication failed. You have no permission to preview this item.', 'tm_builder' ) );
}

?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
	<!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />

		<?php do_action( 'tm_head_meta' ); ?>

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

		<?php $template_directory_uri = get_template_directory_uri(); ?>
		<!--[if lt IE 9]>
		<script src="<?php echo esc_url( $template_directory_uri . '/js/html5.js"' ); ?>" type="text/javascript"></script>
		<![endif]-->

		<script type="text/javascript">
			document.documentElement.className = 'js';
		</script>

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div id="page-container">
			<div id="main-content">
				<div class="container">
					<div id="<?php echo esc_attr( apply_filters( 'tm_pb_preview_wrap_id', 'content' ) ); ?>">
					<div class="<?php echo esc_attr( apply_filters( 'tm_pb_preview_wrap_class', 'entry-content post-content entry content' ) ); ?>">

					<?php
						if ( isset( $_POST['shortcode' ] ) ) {
							if( ! isset( $_POST['tm_pb_preview_nonce'] ) || ! wp_verify_nonce( $_POST['tm_pb_preview_nonce'], 'tm_pb_preview_nonce' ) ) {
								// Auth nonce
								printf( '<p class="tm-pb-preview-message">%1$s</p>', esc_html__( 'Authentication failed. You cannot preview this item.', 'tm_builder' ) );
							} elseif( ! current_user_can( 'edit_posts' ) ) {
								// Auth user
								printf( '<p class="tm-pb-preview-message">%1$s</p>', esc_html__( 'Authentication failed. You have no permission to preview this item.', 'tm_builder' ) );
							} else {
								// process content for builder plugin
								if ( tm_is_builder_plugin_active() ) {
									$content = do_shortcode( wp_unslash( $_POST['shortcode'] ) );
									$content = str_replace( ']]>', ']]&gt;', $content );

									$outer_class   = apply_filters( 'tm_builder_outer_content_class', array( 'tm_builder_outer_content' ) );
									$outer_classes = implode( ' ', $outer_class );

									$outer_id      = apply_filters( "tm_builder_outer_content_id", "tm_builder_outer_content" );

									$inner_class   = apply_filters( 'tm_builder_inner_content_class', array( 'tm_builder_inner_content' ) );
									$inner_classes = implode( ' ', $inner_class );

									$content = sprintf(
										'<div class="%2$s" id="%4$s">
											<div class="%3$s">
												%1$s
											</div>
										</div>',
										$content,
										esc_attr( $outer_classes ),
										esc_attr( $inner_classes ),
										esc_attr( $outer_id )
									);
								} else {
									$content = apply_filters( 'the_content', wp_unslash( $_POST['shortcode'] ) );
									$content = str_replace( ']]>', ']]&gt;', $content );
								}

								echo $content;
							}
						} else {
							printf( '<p class="tm-pb-preview-loading"><span>%1$s</span></p>', esc_html__( 'Loading preview...', 'tm_builder' ) );
						}
					?>

					</div> <!-- .entry-content.post-content.entry -->
					</div> <!-- #content -->
					<div class="tm_pb_modal_overlay link-disabled">
						<div class="tm_pb_prompt_modal">
							<h3><?php esc_html_e( 'Link Disabled', 'tm_builder' ); ?></h3>
							<p><?php esc_html_e( 'During preview, link to different page is disabled', 'tm_builder' ); ?></p>

							<div class="tm_pb_prompt_buttons">
								<a href="#" class="tm_pb_prompt_proceed"><?php esc_html_e( 'Close', 'tm_builder' ); ?></a>
							</div>
						</div><!-- .tm_pb_prompt_modal -->
					</div><!-- .tm_pb_modal_overlay -->
				</div><!-- .container -->
			</div><!-- #main-content -->
		</div> <!-- #page-container -->
		<?php wp_footer(); ?>
	</body>
</html>