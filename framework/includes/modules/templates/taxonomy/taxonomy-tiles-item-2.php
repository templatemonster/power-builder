<?php
/**
 * Template part for displaying taxonomy tiles item #1
 */
$term_id = $this->_var( 'term_id' );
$image = tm_builder_core()->utility()->media->get_image(
	apply_filters( 'tm_pb_module_taxonomy_img_settings_item_2',
		array(
			'size'			=> 'medium',
			'class'			=> 'term-img',
			'html'			=> '<a href="%1$s" title="%4$s"><span %2$s title="%4$s" style="background-image:url(%3$s); padding:0 0 100%% 0;"></span></a>',
		)
	),
	'term',
	$term_id
);
?>
<!-- div.tm_pb_taxonomy__holder start -->
<div class="tm_pb_taxonomy__holder col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4" >
	<div class="tiles-item tiles-item-type-2 tm_pb_taxonomy__inner">
		<figure>
			<?php echo $image ?>
			<figcaption class="tm_pb_taxonomy__content">
				<?php echo $this->_var( 'term_title' ); ?>
				<?php echo $this->_var( 'description' ); ?>
				<?php echo $this->_var( 'count' ); ?>
				<?php echo $this->_var( 'button' ); ?>
			</figcaption>
		</figure>
	</div>
