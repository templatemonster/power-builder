<?php
class Tm_Builder_Module_Social_Media_Follow_Item extends Tm_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Social Network', 'tm_builder' );
		$this->slug                        = 'tm_pb_social_media_follow_network';
		$this->type                        = 'child';
		$this->child_title_var             = 'content_new';

		$this->whitelisted_fields = array(
			'social_network',
			'social_icon',
			'content_new',
			'url',
			'color',
			'bg_color',
			'hover_icon_color',
			'hover_bg_color',
			'skype_url',
			'skype_action',
		);

		$this->fields_defaults = array(
			'url'            => array( '#' ),
			'bg_color'       => array( tm_builder_accent_color(), 'only_default_setting' ),
			'hover_bg_color' => array( tm_builder_secondary_color(), 'only_default_setting' ),
			'skype_action'   => array( 'call' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Social Network', 'tm_builder' );
		$this->settings_text               = esc_html__( 'Social Network Settings', 'tm_builder' );

		$this->custom_css_options = array(
			'social_icon' => array(
				'label'    => esc_html__( 'Social Icon', 'tm_builder' ),
				'selector' => 'a.icon',
			),
			'follow_button' => array(
				'label'    => esc_html__( 'Follow Button', 'tm_builder' ),
				'selector' => 'a.follow_button',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'social_network' => array(
				'label'           => esc_html__( 'Social Network Label', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'class'           => 'tm-pb-social-network',
				'description'     => esc_html__( 'Set the social network label', 'tm_builder' ),
			),
			'social_icon' => array(
				'label'               => esc_html__( 'Icon', 'tm_builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'class'               => array( 'tm-pb-font-icon', 'tm-pb-trigger' ),
				'renderer'            => 'tm_pb_get_font_social_icon_list',
				'renderer_with_field' => true,
				'affects'           => array(
					'#tm_pb_url',
					'#tm_pb_skype_url',
					'#tm_pb_skype_action',
				),
				'description' => esc_html__( 'Choose the social network icon', 'tm_builder' ),
			),
			'content_new' => array(
				'label' => esc_html__( 'Content', 'tm_builder' ),
				'type'  => 'hidden',
			),
			'url' => array(
				'label'               => esc_html__( 'Account URL', 'tm_builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'description'         => esc_html__( 'The URL for this social network link.', 'tm_builder' ),
				'depends_show_if_not' => 'f17e',
			),
			'skype_url' => array(
				'label'           => esc_html__( 'Account Name', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The Skype account name.', 'tm_builder' ),
				'depends_show_if' => 'f17e',
			),
			'skype_action' => array(
				'label'           => esc_html__( 'Skype Button Action', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'call' => esc_html__( 'Call', 'tm_builder' ),
					'chat' => esc_html__( 'Chat', 'tm_builder' ),
				),
				'depends_show_if' => 'f17e',
				'description'     => esc_html__( 'Here you can choose which action to execute on button click', 'tm_builder' ),
			),
			'color' => array(
				'label'           => esc_html__( 'Icon Color', 'tm_builder' ),
				'type'            => 'color-alpha',
				'default'         => '#ffffff',
				'description'     => esc_html__( 'This will change the icon color.', 'tm_builder' ),
				'additional_code' => '<span class="tm-pb-reset-setting reset-default-color" style="display: none;"></span>',
			),
			'bg_color' => array(
				'label'           => esc_html__( 'Icon Background Color', 'tm_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'This will change the icon background color.', 'tm_builder' ),
				'additional_code' => '<span class="tm-pb-reset-setting reset-default-color" style="display: none;"></span>',
			),
			'hover_icon_color' => array(
				'label'           => esc_html__( 'Icon Hover Color', 'tm_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'This will change the icon hover background color.', 'tm_builder' ),
				'additional_code' => '<span class="tm-pb-reset-setting reset-default-color" style="display: none;"></span>',
			),
			'hover_bg_color' => array(
				'label'           => esc_html__( 'Hover Background Color', 'tm_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'This will change hover background color.', 'tm_builder' ),
				'additional_code' => '<span class="tm-pb-reset-setting reset-default-color" style="display: none;"></span>',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $tm_pb_social_media_follow_link;

		$this->set_vars(
			array(
				'social_network',
				'social_icon',
				'url',
				'color',
				'bg_color',
				'hover_icon_color',
				'hover_bg_color',
				'skype_url',
				'skype_action',
			)
		);

		$follow_button  = '';
		$is_skype       = false;
		$bg_style       = '';
		$color_style    = '';

		if ( false !== $this->_var( 'color' ) && '' !== $this->_var( 'color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_social_icon a',
				'declaration' => sprintf( 'color: %1$s;', esc_attr( $this->_var( 'color' ) ) ),
			) );
		}

		if ( false !== $this->_var( 'bg_color' ) && '' !== $this->_var( 'bg_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_social_icon a',
				'declaration' => sprintf( 'background-color: %1$s;', esc_attr( $this->_var( 'bg_color' ) ) ),
			) );
		}

		if ( false !== $this->_var( 'hover_icon_color' ) && '' !== $this->_var( 'hover_icon_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_social_icon a:hover',
				'declaration' => sprintf( 'color: %1$s;', esc_attr( $this->_var( 'hover_icon_color' ) ) ),
			) );
		}

		if ( false !== $this->_var( 'hover_bg_color' ) && '' !== $this->_var( 'hover_bg_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_social_icon a:hover',
				'declaration' => sprintf( 'background-color: %1$s;', esc_attr( $this->_var( 'hover_bg_color' ) ) ),
			) );
		}

		if ( 'f17e' === $this->_var( 'social_icon' ) ) {
			$this->_var( 'skype_url', sprintf(
				'skype:%1$s?%2$s',
				sanitize_text_field( $this->_var( 'skype_url' ) ),
				sanitize_text_field( $this->_var( 'skype_action' ) )
			) );
			$is_skype = true;
		}

		if ( 'on' === $tm_pb_social_media_follow_link['follow_button'] ) {
			$follow_button = sprintf(
				'<a href="%1$s" class="follow_button" title="%2$s"%3$s>%4$s</a>',
				! $is_skype ? esc_url( $this->_var( 'url' ) ) : $this->_var( 'skype_url' ),
				esc_attr( trim( wp_strip_all_tags( $content ) ) ),
				( 'on' === $tm_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
				esc_html__( 'Follow', 'tm_builder' )
			);
		}

		$this->_var( 'social_network', TM_Builder_Element::add_module_order_class( $this->_var( 'social_network' ), $function_name ) );

		$icon = esc_attr( tm_pb_process_font_icon( $this->_var( 'social_icon' ) ) );

		$output = sprintf(
			'<li class="tm_pb_social_icon tm_pb_social_network_link%1$s">
				<a href="%4$s" class="icon%2$s" title="%5$s"%7$s>
					<span class="tm-pb-icon" data-icon="%9$s"></span>
					<span class="tm-pb-tooltip">%6$s</span>
				</a>
				%8$s
			</li>',
			( '' !== $this->_var( 'social_network' ) ? sprintf( ' tm-social-%s', esc_attr( $this->_var( 'social_network' ) ) ) : '' ),
			( '' !== $tm_pb_social_media_follow_link['shape'] ? sprintf( ' %s', esc_attr( $tm_pb_social_media_follow_link['shape'] ) ) : '' ),
			$bg_style . $color_style,
			! $is_skype ? esc_url( $this->_var( 'url' ) ) : $this->_var( 'skype_url' ),
			esc_attr( trim( wp_strip_all_tags( $content ) ) ),
			sanitize_text_field( $content ),
			( 'on' === $tm_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
			$follow_button,
			$icon
		);

		return $output;
	}
}

new Tm_Builder_Module_Social_Media_Follow_Item;
