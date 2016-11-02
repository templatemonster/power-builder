<?php
/**
 * Main wrapping template for blog module
 */
if ( $this->_var( 'posts' )->have_posts() ) {
	echo $this->open_posts_list();
	while ( $this->_var( 'posts' )->have_posts() ) {
		$this->setup_loop();
		echo $this->open_grid_col();
		echo $this->get_template_part( 'blog/item.php' );
		echo $this->close_grid_col();
	}
	echo $this->close_posts_list();
	echo $this->get_pagination();
} else {
	echo $this->get_template_part( 'blog/no-results.php' );
}