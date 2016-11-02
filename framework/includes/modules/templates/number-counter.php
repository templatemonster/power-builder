<?php
/**
 * Template part for displaying number counter item
 */
?>
<div class="percent">
	<span class="percent-value"></span><?php echo $this->nc_sign( '%' ); ?>
</div>
<?php echo $this->html( $this->_var( 'title' ), '<h3>%s</h3>' ); ?>