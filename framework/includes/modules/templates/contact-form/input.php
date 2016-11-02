<?php
/**
 * Template part for displaying contact form input.
 */
?>
<input type="text" id="<?php echo $this->cf_field_name(); ?>" class="tm_pb_contact_form_input" value="<?php echo $this->cf_current_val(); ?>" name="<?php echo $this->cf_field_name(); ?>"<?php echo $this->cf_placeholder() . $this->cf_data_atts(); ?>>