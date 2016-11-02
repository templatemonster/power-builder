<?php
/**
 * Template part for displaying pricing table button
 */

$btn_classes = 'tm_pb_pricing_table_button tm_pb_button' . $this->html( $this->_var( 'icon' ), ' tm_pb_custom_button_icon' );
$btn_url     = esc_url( $this->_var( 'button_url' ) );
$btn_data    = $this->html( esc_attr( $this->_var( 'icon' ) ), ' data-icon="%s"' );
$btn_text    = esc_html( $this->_var( 'button_text' ) );

?>
<a class="<?php echo $btn_classes; ?>" href="<?php echo tm_builder_tools()->render_url( $btn_url ); ?>"<?php echo $btn_data; ?>><?php
	echo $btn_text;
?></a>