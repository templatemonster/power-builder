<?php
/**
 * Template part for displaying progress bar
 */
?>
<div class="<?php echo $this->_var( 'module_class' ); ?>">
	<span class="tm_pb_counter_title"><?php echo $this->_var( 'content' ); ?></span>
	<span class="tm_pb_counter_container"<?php echo $this->_var( 'background_color_style' ); ?>>
		<span class="tm_pb_counter_amount" style="<?php echo $this->_var( 'bar_color_style' ); ?>" data-width="<?php echo $this->_var( 'percent' ); ?>"><span class="tm_pb_counter_amount_number"><?php echo $this->_var( 'percent_label' ); ?></span></span>
	</span>
</div>