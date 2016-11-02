<?php
/**
 * Templat epart for displaying posts listing item
 */
?>
<div class="<?php echo tm_builder_tools()->get_col_classes( $this ); ?>">
	<div class="tm-posts_item">
	<?php

		tm_builder_core()->utility()->attributes->get_title( array(
			'visible'      => true,
			'trimmed_type' => 'word',
			'ending'       => '&hellip;',
			'html'         => '<h4 %1$s><a href="%2$s" %3$s rel="bookmark">%4$s</a></h4>',
			'class'        => 'tm-posts_item_title',
			'echo'         => true,
		) );

		echo $this->get_template_part( 'post/item-meta.php' );

		tm_builder_core()->utility()->attributes->get_content( array(
			'visible'      => ( $this->_var( 'excerpt' ) && 0 < $this->_var( 'excerpt' ) ) ? true : false,
			'content_type' => 'post_content',
			'length'       => $this->_var( 'excerpt' ),
			'trimmed_type' => 'word',
			'ending'       => '&hellip;',
			'html'         => '<div %1$s>%2$s</div>',
			'class'        => 'tm-posts_item_excerpt',
			'echo'         => true,
		) );

		tm_builder_core()->utility()->attributes->get_button( array(
			'visible'   => true,
			'text'      => __( 'Read More', '__tm' ),
			'icon'      => apply_filters( '__tm_button_icon', '<i class="material-icons">arrow_forward</i>' ),
			'class'     => 'btn',
			'html'      => '<a href="%1$s" %2$s %3$s><span class="btn__text">%4$s</span>%5$s</a>',
			'echo'      => true,
		) );
	?>
	</div>
</div>