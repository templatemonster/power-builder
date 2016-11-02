<?php
/**
 * Template part for displaying Call to action module template
 */
?>
<div class="tm_pb_promo_description">
	<?php echo $this->html( $this->_var( 'title' ), '<h2>%s</h2>' ); ?>
	<?php echo $this->shortcode_content; ?>
</div>
<?php echo $this->_var( 'button' ); ?>