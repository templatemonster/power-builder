<?php
/**
 * Template part for displaying capthca field.
 */

if ( 'on' !== $this->_var( 'captcha' ) ) {
	return;
}

?>
<div class="tm_pb_contact_right">
	<div class="clearfix">
		<span class="tm_pb_contact_captcha_question"><?php
			echo $this->_var( 'first_digit' ); ?> + <?php echo $this->_var( 'second_digit' );
		?></span> = <input type="text" size="2" class="tm_pb_contact_captcha" data-original_title="<?php esc_attr_e( 'Captcha', 'tm_builder' ) ?>" data-first_digit="<?php echo $this->_var( 'first_digit' ); ?>" data-second_digit="<?php echo $this->_var( 'second_digit' ); ?>" value="" name="<?php echo $this->cf_id( 'tm_pb_contact_captcha_%s' ); ?>" data-required_mark="required">
	</div>
</div> <!-- .tm_pb_contact_right -->
