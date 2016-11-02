<?php
/**
 * Main template part for tabs module
 */
?>
<?php echo $this->get_template_part( 'tabs/nav.php' ); ?>
<div class="tm_pb_all_tabs"><?php
	echo $this->_var( 'tabs_content' );
?></div> <!-- .tm_pb_all_tabs -->