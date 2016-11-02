<?php
/**
 * Templat epart for displaying posts listing item
 */
?>
<div class="<?php echo tm_builder_tools()->get_col_classes( $this ); ?>">
	<div class="tm-posts_item">
		<div class="tm-posts_item_image"><?php

			tm_builder_core()->utility()->media->get_image( array(
				'html'        => '<a href="%1$s" %2$s><img src="%3$s" alt="%4$s"></a>',
				'class'       => 'tm-posts_img',
				'size'        => esc_attr( $this->_var( 'image_size' ) ),
				'placeholder' => true,
				'echo'        => true,
			) );

		?></div>
		<div class="tm-posts_item_content"><?php

			tm_builder_core()->utility()->attributes->get_title( array(
				'visible'      => true,
				'trimmed_type' => 'word',
				'ending'       => '&hellip;',
				'html'         => '<h5 %1$s><a href="%2$s" %3$s rel="bookmark">%4$s</a></h5>',
				'class'        => 'tm-posts_item_title',
				'echo'         => true,
			) );

			echo $this->get_template_part( 'post/item-meta.php' );

		?></div>
	</div>
</div>