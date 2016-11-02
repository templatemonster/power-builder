<?php
/**
 * Template part for displaying countdown timer
 */
?>
<div class="tm_pb_countdown_timer_container clearfix">
	<?php echo $this->html( $this->_var( 'title' ), '<h4>%s</h4>' ) ?>
	<div class="days section values" data-short="<?php esc_attr_e( 'Day', 'tm_builder' ); ?>" data-full="<?php esc_html_e( 'Day(s)', 'tm_builder' ); ?>">
		<span class="value"></span>
		<span class="label"><?php esc_html_e( 'Day(s)', 'tm_builder' ); ?></span>
	</div>
	<div class="sep section"><span class="countdown-sep"></span></div>
	<div class="hours section values" data-short="<?php esc_attr_e( 'Hrs', 'tm_builder' ); ?>" data-full="<?php esc_html_e( 'Hour(s)', 'tm_builder' ); ?>">
		<span class="value"></span>
		<span class="label"><?php esc_html_e( 'Hour(s)', 'tm_builder' ); ?></span>
	</div>
	<div class="sep section"><span class="countdown-sep"></span></div>
	<div class="minutes section values" data-short="<?php esc_attr_e( 'Min', 'tm_builder' ); ?>" data-full="<?php esc_html_e( 'Minute(s)', 'tm_builder' ); ?>">
		<span class="value"></span>
		<span class="label"><?php esc_html_e( 'Minute(s)', 'tm_builder' ); ?></span>
	</div>
	<div class="sep section"><span class="countdown-sep"></span></div>
	<div class="seconds section values" data-short="<?php esc_attr_e( 'Sec', 'tm_builder' ); ?>" data-full="<?php esc_html_e( 'Second(s)', 'tm_builder' ); ?>">
		<span class="value"></span>
		<span class="label"><?php esc_html_e( 'Second(s)', 'tm_builder' ); ?></span>
	</div>
</div>