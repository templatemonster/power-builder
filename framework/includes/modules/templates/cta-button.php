<?php
/**
 * Template part for displaying Call to ation button
 */

$atts = array(
	'target'    => 'on' === $this->_var( 'url_new_window' ) ? '_blank' : '_self',
	'data-icon' => '' !== $this->_var( 'icon' ) && 'on' === $this->_var( 'custom_button' ) ? $this->_var( 'icon' ) : '',
);
$icon_class = ( '' !== $this->_var( 'icon' ) && 'on' === $this->_var( 'custom_button' ) ) ? ' tm_pb_custom_button_icon' : '';

$icon = $this->_var( 'button_icon' );

if ( '' === $this->_var( 'button_icon' ) ) {
	$icon = 'f18e';
}

$icon        = esc_attr( tm_pb_process_font_icon( $this->_var( 'button_icon' ) ) );
$icon_marker = ( '' !== $icon && 'on' === $this->_var( 'custom_button' ) ) ? '<span class="tm_pb_button_icon">' . $icon . '</span>' : '';

$class = 'tm_pb_promo_button tm_pb_button';
$class .= ' ' . $icon_class;
$class .= ( 'left' === $this->_var( 'button_icon_placement' ) ) ? ' tm_pb_icon_left' : ' tm_pb_icon_right';

?>
<a class="<?php echo $class; ?>" href="<?php echo tm_builder_tools()->render_url( $this->_var( 'button_url' ) ); ?>"<?php echo $this->prepare_atts( $atts ); ?>>
	<?php
		echo sprintf(
			'%1$s%2$s%3$s',
			'left' === $this->_var( 'button_icon_placement' ) ? $icon_marker : '',
			esc_html( $this->_var( 'button_text' ) ),
			'right' === $this->_var( 'button_icon_placement' ) ? $icon_marker : ''
		);
	?>
</a>
