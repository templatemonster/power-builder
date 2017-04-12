<?php
/**
 * Template part for displaying pricing table button
 */

$icon_placement= ( 'left' === $this->_var( 'button_icon_placement' ) ) ? ' tm_pb_icon_left' : ' tm_pb_icon_right';

$btn_classes = 'tm_pb_pricing_table_button tm_pb_button' . $this->html( $this->_var( 'icon' ), ' tm_pb_custom_button_icon' ) . $icon_placement;
$btn_url     = esc_url( $this->_var( 'button_url' ) );
$btn_data    = $this->html( esc_attr( $this->_var( 'icon' ) ), ' data-icon="%s"' );
$btn_text    = esc_html( $this->_var( 'button_text' ) );

$icon = $this->_var( 'button_icon' );

if ( '' === $this->_var( 'button_icon' ) ) {
	$icon = 'f18e';
}

$icon        = esc_attr( tm_pb_process_font_icon( $this->_var( 'button_icon' ) ) );
$icon_marker = ( '' !== $icon && 'on' === $this->_var( 'custom_button' ) ) ? '<span class="tm_pb_button_icon">' . $icon . '</span>' : '';

?>
<a class="<?php echo $btn_classes; ?>" href="<?php echo tm_builder_tools()->render_url( $btn_url ); ?>"<?php echo $btn_data; ?>><?php
	echo sprintf(
		'%1$s%2$s%3$s',
		'left' === $this->_var( 'button_icon_placement' ) ? $icon_marker : '',
		$btn_text,
		'right' === $this->_var( 'button_icon_placement' ) ? $icon_marker : ''
	);
?></a>
