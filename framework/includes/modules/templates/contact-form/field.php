<?php
/**
 * Template for displaying contact form field wrapper.
 */
?>
<div class="<?php echo $this->cf_col_class(); ?>">
	<?php if ( 'off' !== $this->_var( 'show_label' ) ) : ?>
	<label for="<?php echo $this->cf_field_name(); ?>" class="tm_pb_contact_form_label"><?php
		echo $this->_var( 'field_title' );
	?></label>
	<?php endif; ?>
	<?php echo $this->_var( 'input_field' ); ?>
</div>