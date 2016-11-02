<?php
/**
 * Template part for dispalying main form content
 */
?>
<h2 class="tm_pb_contact_main_title"><?php echo $this->_var( 'title' ); ?></h2>
<form class="tm_pb_contact_form clearfix" method="post" action="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>">
	<div class="tm-pb-contact-message"><?php echo $this->_var( 'tm_error_message' ); ?></div>
	<div class="row">
		<?php echo $this->_var( 'content' ); ?>
	</div>
	<input type="hidden" value="tm_contact_proccess" name="<?php echo $this->cf_id( 'tm_pb_contactform_submit_%s' ); ?>">
	<input type="text" value="" name="<?php echo $this->cf_id( 'tm_pb_contactform_validate_%s' ); ?>" class="tm_pb_contactform_validate_field" />
	<div class="tm_contact_bottom_container">
		<?php echo $this->get_template_part( 'contact-form/captcha.php' ); ?>
		<?php echo $this->get_template_part( 'contact-form/submit.php' ); ?>
	</div>
	<?php wp_nonce_field( 'tm-pb-contact-form-submit', '_wpnonce-tm-pb-contact-form-submitted', true, true ); ?>
</form>