<?php
/**
 * Template part for displaying view more button after posts listing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $this->_var( 'more' ) || 'off' === $this->_var( 'more' ) ) {
	return;
}

if ( 'on' === $this->_var( 'ajax_more' ) && 1 >= $this->posts_query->max_num_pages ) {
	return;
}

$atts = array(
	'data-icon' => '' !== $this->_var( 'icon' ) ? $this->_var( 'icon' ) : ''
);

$btn_classes = array(
	'btn',
	'btn-primary',
	'tm_pb_button',
	( '' !== $this->_var( 'icon' ) && 'on' === $this->_var( 'custom_button' ) ) ? 'tm_pb_custom_button_icon' : false,
	( 'on' === $this->_var( 'ajax_more' ) ) ? 'tm_pb_ajax_more' : false,
);

$btn_classes = array_filter( $btn_classes );

?>
<div class="tm-posts_button_wrap">
	<a href="<?php echo tm_builder_tools()->render_url( $this->_var( 'more_url' ) ); ?>" class=" <?php echo implode( ' ', $btn_classes ); ?>"<?php echo $this->prepare_atts( $atts ); ?>><?php
		echo esc_html( $this->_var( 'more_text' ) );
	?></a>
</div>