<?php
/**
 * Template part for displaying textarea
 */
?>
<textarea name="<?php echo $this->cf_field_name(); ?>" id="<?php echo $this->cf_field_name(); ?>" class="tm_pb_contact_message tm_pb_contact_form_input"<?php echo $this->cf_placeholder() . $this->cf_data_atts(); ?>><?php
	echo $this->cf_current_val();
?></textarea>