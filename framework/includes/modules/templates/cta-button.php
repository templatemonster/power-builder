<?php
/**
 * Template part for displaying Call to ation button
 */

$atts = array(
	'target'    => 'on' === $this->_var( 'url_new_window' ) ? '_blank' : '_self',
	'data-icon' => '' !== $this->_var( 'icon' ) && 'on' === $this->_var( 'custom_button' ) ? $this->_var( 'icon' ) : '',
);
$icon_class = ( '' !== $this->_var( 'icon' ) && 'on' === $this->_var( 'custom_button' ) ) ? ' tm_pb_custom_button_icon' : '';
?>
<a class="tm_pb_promo_button tm_pb_button<?php echo $icon_class; ?>" href="<?php echo tm_builder_tools()->render_url( $this->_var( 'button_url' ) ); ?>"<?php echo $this->prepare_atts( $atts ); ?>>
	<?php echo esc_html( $this->_var( 'button_text' ) ) ?>
</a>
