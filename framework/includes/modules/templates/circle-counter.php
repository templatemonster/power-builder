<?php
/**
 * Template part for displaying circle counter
 */
?>
<div class="tm_pb_circle_counter_bar container-width-change-notify"<?php echo $this->circle_data_atts(); ?>>
	<div class="percent">
		<p>
			<span class="percent-value"></span><?php echo $this->circle_sign( '%' ); ?>
		</p>
	</div>
	<?php echo $this->html( $this->_var( 'title' ), '<h3>%s</h3>' ); ?>
</div>