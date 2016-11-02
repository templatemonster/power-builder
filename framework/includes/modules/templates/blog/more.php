<?php
/**
 * Template for displaying read more button
 */
?>
<a href="<?php echo esc_url( get_permalink() ); ?>" class="more-link" ><?php
	echo esc_html__( 'Read more', 'tm_builder' );
?></a>