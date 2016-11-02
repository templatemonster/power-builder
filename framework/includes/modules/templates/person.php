<?php
/**
 * Template part for Person module displaying
 */
?>
<?php echo $this->_var( 'image' ); ?>
<div class="tm_pb_team_member_description">
	<h4 class="tm_pb_team_member_name"><?php
		echo $this->html( tm_builder_tools()->render_url( $this->_var( 'custom_url' ) ), '<a href="%s">' );
		echo esc_html( $this->_var( 'name' ) );
		echo $this->html( $this->_var( 'custom_url' ), '</a>' );
	?></h4>
	<p class="tm_pb_member_position"><?php echo esc_html( $this->_var( 'position' ) ); ?></p>
	<?php echo $this->shortcode_content; ?>
	<?php echo $this->_var( 'social_links' ); ?>
</div> <!-- .tm_pb_team_member_description -->
