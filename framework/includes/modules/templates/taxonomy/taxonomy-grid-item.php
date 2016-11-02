<?php
/**
 * Template part for displaying taxonomy items
 */

$term_id = $this->_var( 'term_id' );
$image = tm_builder_core()->utility()->media->get_image(
	apply_filters( 'tm_pb_module_taxonomy_img_settings_grid_item',
		array(
			'class'			=> 'term-img',
			'html'			=> '<a href="%1$s" title="%4$s"><img src="%3$s" %2$s alt="%4$s" %5$s ></a>',
		)
	),
	'term',
	$term_id
);
?>
<div class="tm_pb_taxonomy__holder grid-item <?php echo $this->_var( 'items_class' ); ?>" >
	<figure class="tm_pb_taxonomy__inner" >
		<?php echo $image ?>
		<figcaption class="tm_pb_taxonomy__content">
			<?php echo $this->_var( 'term_title' ); ?>
			<?php echo $this->_var( 'description' ); ?>
			<?php echo $this->_var( 'count' ); ?>
			<?php echo $this->_var( 'button' ); ?>
		</figcaption>
	</figure>
</div>
