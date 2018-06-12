<?php
/**
 * Template part for displaying contact form checkbox.
 */
?>
<input type="checkbox" id="<?php echo $this->cf_field_name(); ?>" class="tm_pb_contact_form_input" value="<?php echo $this->cf_current_val(); ?>" name="<?php echo $this->cf_field_name(); ?>"<?php echo $this->cf_data_atts(); ?>><?php echo $this->cf_placeholder(); ?>