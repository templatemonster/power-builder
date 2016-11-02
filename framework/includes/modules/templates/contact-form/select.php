<?php
/**
 * Template part for displaying contact form input.
 */
?>
<select id="<?php echo $this->cf_field_name(); ?>" class="tm_pb_contact_form_input" name="<?php echo $this->cf_field_name(); ?>"<?php echo $this->cf_data_atts(); ?>>
	<?php echo $this->cf_select_first_option(); ?>
	<?php echo $this->cf_select_options(); ?>
</select>