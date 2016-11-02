<?php
/**
 * Blog listing item template
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $this->classes['item'] ); ?>>
	<?php
		$format_template = sprintf( 'blog/formats/%s.php', $this->_var( 'post_format' ) );
		echo $this->get_template_part( $format_template, 'blog/formats/standard.php' );
	?>
</article>