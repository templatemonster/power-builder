<?php
class Tm_Builder_Module_Social_Media_Follow extends Tm_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Social Media Follow', 'tm_builder' );
		$this->slug            = 'tm_pb_social_media_follow';
		$this->icon            = 'f1e0';
		$this->child_slug      = 'tm_pb_social_media_follow_network';
		$this->child_item_text = esc_html__( 'Social Network', 'tm_builder' );

		$this->whitelisted_fields = array(
			'link_shape',
			'url_new_window',
			'follow_button',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'link_shape'        => array( 'rounded_rectangle' ),
			'url_new_window'    => array( 'on' ),
			'follow_button'     => array( 'off' ),
		);

		$this->custom_css_options = array(
			'social_follow' => array(
				'label'    => esc_html__( 'Social Follow', 'tm_builder' ),
				'selector' => 'li',
			),
			'social_icon' => array(
				'label'    => esc_html__( 'Social Icon', 'tm_builder' ),
				'selector' => 'li a.icon',
			),
			'follow_button' => array(
				'label'    => esc_html__( 'Follow Button', 'tm_builder' ),
				'selector' => 'li a.follow_button',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'link_shape' => array(
				'label'           => esc_html__( 'Link Shape', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'rounded_rectangle' => esc_html__( 'Rounded Rectangle', 'tm_builder' ),
					'circle'            => esc_html__( 'Circle', 'tm_builder' ),
				),
				'description' => esc_html__( 'Here you can choose the shape of your social network icons.', 'tm_builder' ),
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'tm_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'tm_builder' ),
				),
				'description' => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'tm_builder' ),
			),
			'follow_button' => array(
				'label'           => esc_html__( 'Follow Button', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'Off', 'tm_builder' ),
					'on'  => esc_html__( 'On', 'tm_builder' ),
				),
				'description' => esc_html__( 'Here you can choose whether or not to include the follow button next to the icon.', 'tm_builder' ),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'tm_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => tm_pb_media_breakpoints(),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'tm_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'tm_builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function pre_shortcode_content() {
		global $tm_pb_social_media_follow_link;

		$link_shape        = $this->shortcode_atts['link_shape'];
		$url_new_window    = $this->shortcode_atts['url_new_window'];
		$follow_button     = $this->shortcode_atts['follow_button'];

		$tm_pb_social_media_follow_link = array(
			'url_new_window' => $url_new_window,
			'shape'          => $link_shape,
			'follow_button'  => $follow_button,
		);
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $tm_pb_social_media_follow_link;

		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];

		$class = " tm_pb_module tm_pb_bg_layout_light";

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		$output = sprintf(
			'<ul%3$s class="tm_pb_social_media_follow%2$s%4$s%5$s clearfix">
				%1$s
			</ul> <!-- .tm_pb_counters -->',
			$this->shortcode_content,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'on' === $tm_pb_social_media_follow_link['follow_button'] ? ' has_follow_button' : '' )
		);

		return $output;
	}
}
new Tm_Builder_Module_Social_Media_Follow;

