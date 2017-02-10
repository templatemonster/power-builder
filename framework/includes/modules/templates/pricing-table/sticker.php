<?php
/**
 * Template part for displaying pricing table sticker
 */

$sticker_visible = $this->_var( 'sticker' );

if ( 'on' === $sticker_visible ) { ?>
	<div class="<?php echo $this->pricing_sticker_classes(); ?>">
		<div class="tm_pb_sticker_inner">
			<?php echo $this->_var( 'sticker_icon' ); ?>
		</div>
	</div>
<?php } ?>


