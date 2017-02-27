<?php
/**
 * Template for displaying read more button
 */
?>
<a href="<?php echo esc_url( get_permalink() ); ?>" class="more-link" ><?php
	$read_more_text = empty( $this->_var( 'more_text' ) ) ? esc_html__( 'Read more', 'tm_builder' ) : $this->_var( 'more_text' );
	echo $read_more_text;
?></a>
