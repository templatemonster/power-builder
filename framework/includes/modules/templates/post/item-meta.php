<?php
/**
 * Template part for displaying cureent item meta
 */

if ( ! $this->_var( 'meta_data' ) || 'off' === $this->_var( 'meta_data' ) ) {
	return;
}
?>
<div class="tm-posts_item_meta"><?php

	tm_builder_core()->utility()->meta_data->get_author( array(
		'icon'    => apply_filters( 'cherry_author_icon', '<i class="fa fa-user" aria-hidden="true"></i>' ),
		'echo'    => true,
	) );

	tm_builder_core()->utility()->meta_data->get_date( array(
		'icon'    => apply_filters( 'cherry_date_icon', '<i class="fa fa-calendar" aria-hidden="true"></i>' ),
		'echo'    => true,
	) );

	tm_builder_core()->utility()->meta_data->get_comment_count( array(
		'icon'    => apply_filters( 'cherry_comment_icon', '<i class="fa fa-comment-o" aria-hidden="true"></i>' ),
		'sufix'   => _n_noop( '%s comment', '%s comments', 'tm_builder' ),
		'echo'    => true,
	) );

?></div>