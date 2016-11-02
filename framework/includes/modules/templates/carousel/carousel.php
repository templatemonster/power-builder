<?php
/**
 * Template part for carousel module displaying
 */
?>
<?php
	$delimiter = $this->_var( 'delimiter' );
	$show_all = $this->_var( 'show_all' );
	$super_title = $this->_var('super_title');
	$title = $this->_var('title');
	$subtitle = $this->_var('subtitle');
	$html = '';

	if ( $super_title ) {
		$html .= '<h2>' . $this->_var('super_title') . '</h2>';
	}

	$html .= $delimiter;

	if ( $title ) {
		$html .= '<h3>' . $this->_var('title') . '</h3>';
	}

	$html .= $delimiter;

	if ( $subtitle ) {
		$html .= '<h4>' . $this->_var('subtitle') . '</h4>';
	}

	echo $html;
?>
<!-- Slider main container -->
<div class="swiper-container" >
	<!-- Additional required wrapper -->
	<div class="swiper-wrapper">
		<!-- Slides -->
		<?php
			echo $this->_var('slides');
		?>
	</div>
	<!-- If we need pagination -->
	<div class="swiper-pagination"></div>
	<!-- If we need navigation buttons -->
	<div class="swiper-button-prev"></div>
	<div class="swiper-button-next"></div>
</div>
<?php
	echo $show_all;
?>
