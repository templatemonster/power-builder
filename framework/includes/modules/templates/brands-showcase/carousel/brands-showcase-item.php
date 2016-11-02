<?php
/**
 * Template part for displaying brands showcase item
 */

$brand = $this->_var( 'brand' );

if ( ! is_array( $brand ) ) {
	return;
}

$brand_title = Cherry_Toolkit::get_arg( $brand, 'brand_title' );
$brand_logo = Cherry_Toolkit::get_arg( $brand, 'brand_logo' );
$brand_url = Cherry_Toolkit::get_arg( $brand, 'brand_url' );
$url_new_window = Cherry_Toolkit::get_arg( $brand, 'url_new_window', 'off' );
$brand_name = $this->html( Cherry_Toolkit::get_arg( $brand, 'brand_name' ), '<span class="tm_pb_brands_showcase_module__item__title">%s</span>' );
$brand_description = sprintf( '<span class="tm_pb_brands_showcase_module__item__description">%s</span>', Cherry_Toolkit::get_arg( $brand, 'brand_description' ) );

$wrapper_atts = $this->prepare_atts( array(
	'id' => Cherry_Toolkit::get_arg( $brand, 'id' ),
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
	'src' => esc_attr( $brand_logo ),
	'alt' => esc_attr( $brand_title ),
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
