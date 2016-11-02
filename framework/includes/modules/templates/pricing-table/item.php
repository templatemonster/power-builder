<?php
/**
 * Template part for displaying pricing table item module
 */
?>
<div class="<?php echo $this->pricing_table_item_classes(); ?>">
	<div class="tm_pb_pricing_heading"><?php
		echo $this->html( esc_html( $this->_var( 'title' ) ), '<h2 class="tm_pb_pricing_title">%s</h2>' );
		echo $this->html( esc_html( $this->_var( 'subtitle' ) ), '<span class="tm_pb_best_value">%s</span>' );
	?></div> <!-- .tm_pb_pricing_heading -->
	<div class="tm_pb_pricing_content_top">
		<span class="tm_pb_tm_price"><?php
			echo $this->html( esc_html( $this->_var( 'currency' ) ), '<span class="tm_pb_dollar_sign">%s</span>' );
			echo $this->html( esc_html( $this->_var( 'sum' ) ), '<span class="tm_pb_sum">%s</span>' );
			echo $this->html( esc_html( $this->_var( 'per' ) ), '<span class="tm_pb_frequency">%s</span>' );
		?></span>
	</div> <!-- .tm_pb_pricing_content_top -->
	<div class="tm_pb_pricing_content">
		<ul class="tm_pb_pricing">
			<?php echo $this->pricing_table_features_list(); ?>
		</ul>
	</div> <!-- .tm_pb_pricing_content -->
	<?php echo $this->get_template_part( 'pricing-table/button.php' ); ?>
</div>