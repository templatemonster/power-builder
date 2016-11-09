<?php
/**
 * Template part for displaying brands showcase item
 */


$brand_title = $this->_var( 'brand_title' );
$brand_logo = $this->_var( 'brand_logo' );
$brand_url = $this->_var( 'brand_url' );
$url_new_window = $this->_var( 'url_new_window' );
$brand_name = $this->html( $this->_var( 'brand_name' ), '<span class="tm_pb_brands_showcase_module__item__title">%s</span>' );
$brand_description = sprintf( '<span class="tm_pb_brands_showcase_module__item__description">%s</span>', $this->_var( 'brand_description' ) );

$wrapper_atts = $this->prepare_atts( array(
	'id'    => $this->_var( 'id' ),
	'class' => 'swiper-slide tm_pb_brands_showcase_module__item__wrapper',
), true );

$anchor_atts = $this->prepare_atts( array(
	'href'   => tm_builder_tools()->render_url( $brand_url ),
	'target' => array( $url_new_window, 'blank' ),
	'title'  => $brand_title,
	'class'  => 'tm_pb_brands_showcase_module__item',
), true );

$img_atts = $this->prepare_atts( array(
	'class' => 'tm_pb_brands_showcase_module__item__logo',
	'src'   => esc_attr( $brand_logo ),
	'alt'   => esc_attr( $brand_title ),
), true );

?>
<?php if ( ! empty( $brand_logo ) || ! empty( $brand_name ) || ! empty( $brand_title ) ) : ?>

	<div<?php echo $wrapper_atts; ?>>
		<a<?php echo $anchor_atts; ?>>
			<?php if ( ! empty( $brand_logo ) ) : ?>
				<img<?php echo $img_atts; ?>>
			<?php endif; ?>
			<?php echo $brand_name; ?>
			<?php echo $brand_description; ?>
		</a>
	</div>

<?php endif; ?>
