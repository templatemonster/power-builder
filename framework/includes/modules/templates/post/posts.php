<?php
/**
 * Main template for posts module.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<?php echo $this->get_template_part( 'post/module-title.php' ); ?>
<?php echo $this->get_template_part( 'post/listing.php' ); ?>
<?php echo $this->get_template_part( 'post/module-button.php' ); ?>