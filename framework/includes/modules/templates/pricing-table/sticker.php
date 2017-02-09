<?php
/**
 * Template part for displaying pricing table sticker
 */

$sticker_visible = $this->_var( 'sticker' );

if ( 'on' === $sticker_visible ) { ?>
	<div class="<?php echo $this->pricing_sticker_classes(); ?>">
		<span></span>
	</div>
<?php } ?>


