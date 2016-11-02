<?php
/**
 * Template part for displaying module listing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $this->posts_query->have_posts() ) : ?>
	<div class="tm-posts_listing">
		<div class="<?php echo tm_builder_tools()->get_row_classes( $this ); ?>">
		<?php while ( $this->posts_query->have_posts() ) : $this->posts_query->the_post(); ?>
			<?php echo $this->get_template_part( $this->get_layout_template() ); ?>
		<?php endwhile; // end of the loop. ?>
		</div>
	</div>
<?php endif; ?>