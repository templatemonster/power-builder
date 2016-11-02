<?php
/**
 * Posts module title template
 */
?>
<div class="tm-posts_title_group">
	<?php echo $this->html( $this->_var( 'super_title' ), '<h4 class="tm-posts_supertitle">%s</h4>' ); ?>
	<?php echo $this->html( $this->_var( 'title' ), '<h2 class="tm-posts_title">%s</h2>' ); ?>
	<?php echo $this->esc_switcher( 'title_delimiter', '<div class="tm-posts_title_divider"></div>' ); ?>
	<?php echo $this->html( $this->_var( 'subtitle' ), '<h5 class="tm-posts_subtitle">%s</h5>' ); ?>
</div>