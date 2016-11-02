<?php
/**
 * Template part for displaying single social item for person module
 */
?>
<li>
	<a href="<?php echo $this->_var( 'current_url' ); ?>" class="tm_pb_font_icon tm_pb_<?php echo $this->_var( 'current_social' ); ?>_icon">
		<span class="tm-pb-icon" data-icon="<?php echo $this->_var( 'current_icon' ); ?>"></span>
		<span class="tm-pb-tooltip"><?php echo $this->_var( 'current_label' ); ?></span>
	</a>
</li>