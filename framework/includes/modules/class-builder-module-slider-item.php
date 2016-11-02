<?php
class Tm_Builder_Module_Slider_Item extends Tm_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Slide', 'tm_builder' );
		$this->slug                        = 'tm_pb_slide';
		$this->type                        = 'child';
		$this->child_title_var             = 'admin_title';
		$this->child_title_fallback_var    = 'heading';

		$this->whitelisted_fields = array(
			'heading',
			'admin_title',
			'button_text',
			'button_link',
			'button_2_text',
			'button_2_link',
			'background_image',
			'background_position',
			'background_size',
			'background_color',
			'image',
			'alignment',
			'video_url',
			'image_alt',
			'video_bg_mp4',
			'video_bg_webm',
			'video_bg_width',
			'video_bg_height',
			'allow_player_pause',
			'content_new',
			'arrows_custom_color',
			'dot_nav_custom_color',
			'use_bg_overlay',
			'use_text_overlay',
			'bg_overlay_color',
			'text_overlay_color',
			'text_border_radius',
		);

		$this->fields_defaults = array(
			'button_link'         => array( '#' ),
			'background_position' => array( 'default' ),
			'background_size'     => array( 'default' ),
			'background_color'    => array( '#ffffff', 'only_default_setting' ),
			'alignment'           => array( 'center' ),
			'allow_player_pause'  => array( 'off' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Slide', 'tm_builder' );
		$this->settings_text               = esc_html__( 'Slide Settings', 'tm_builder' );
		$this->main_css_element = '%%order_class%%';
		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_slide_description .tm_pb_slide_title",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '0.1',
						),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element} .tm_pb_slide_content",
						'line_height' => "{$this->main_css_element} p",
						'important'   => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '0.1',
						),
					),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button #1', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element}.tm_pb_slide .tm_pb_button.tm_btn_1",
					),
				),
				'button_2' => array(
					'label' => esc_html__( 'Button #2', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element}.tm_pb_slide .tm_pb_button.tm_btn_2",
					),
				),
			),
		);

		$this->custom_css_options = array(
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'tm_builder' ),
				'selector' => '.tm_pb_slide_description h2',
			),
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'tm_builder' ),
				'selector' => '.tm_pb_slide_description',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'tm_builder' ),
				'selector' => 'a.tm_pb_more_button.tm_btn_1',
			),
			'slide_button_2' => array(
				'label'    => esc_html__( 'Slide Button', 'tm_builder' ),
				'selector' => 'a.tm_pb_more_button.tm_btn_2',
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'tm_builder' ),
				'selector' => '.tm_pb_slide_image',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'heading' => array(
				'label'           => esc_html__( 'Heading', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the title text for your slide.', 'tm_builder' ),
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button 1 Text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the text for the slide button #1', 'tm_builder' ),
			),
			'button_link' => array(
				'label'           => esc_html__( 'Button 1 URL', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a destination URL for the slide button #1.', 'tm_builder' ),
			),
			'button_2_text' => array(
				'label'           => esc_html__( 'Button 2 Text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the text for the slide button #2', 'tm_builder' ),
			),
			'button_2_link' => array(
				'label'           => esc_html__( 'Button 2 URL', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a destination URL for the slide button #2.', 'tm_builder' ),
			),
			'background_image' => array(
				'label'              => esc_html__( 'Background Image', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background', 'tm_builder' ),
				'description'        => esc_html__( 'If defined, this image will be used as the background for this module. To remove a background image, simply delete the URL from the settings field.', 'tm_builder' ),
			),
			'background_position' => array(
				'label'           => esc_html__( 'Background Image Position', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'default'       => esc_html__( 'Default', 'tm_builder' ),
					'center'        => esc_html__( 'Center', 'tm_builder' ),
					'top_left'      => esc_html__( 'Top Left', 'tm_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'tm_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'tm_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'tm_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'tm_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'tm_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'tm_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'tm_builder' ),
				),
			),
			'background_size' => array(
				'label'           => esc_html__( 'Background Image Size', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'default' => esc_html__( 'Default', 'tm_builder' ),
					'cover'   => esc_html__( 'Cover', 'tm_builder' ),
					'contain' => esc_html__( 'Fit', 'tm_builder' ),
					'initial' => esc_html__( 'Actual Size', 'tm_builder' ),
				),
			),
			'background_color' => array(
				'label'       => esc_html__( 'Background Color', 'tm_builder' ),
				'type'        => 'color-alpha',
				'description' => esc_html__( 'Use the color picker to choose a background color for this module.', 'tm_builder' ),
			),
			'image' => array(
				'label'              => esc_html__( 'Slide Image', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'configuration',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Slide Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Slide Image', 'tm_builder' ),
				'description'        => esc_html__( 'If defined, this slide image will appear to the left of your slide text. Upload an image, or leave blank for a text-only slide.', 'tm_builder' ),
			),
			'use_bg_overlay'      => array(
				'label'           => esc_html__( 'Use Background Overlay', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_bg_overlay_color',
				),
				'description'     => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'tm_builder' ),
			),
			'bg_overlay_color' => array(
				'label'             => esc_html__( 'Background Overlay Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'tm_builder' ),
			),
			'use_text_overlay'      => array(
				'label'           => esc_html__( 'Use Text Overlay', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_text_overlay_color',
				),
				'description'     => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'tm_builder' ),
			),
			'text_overlay_color' => array(
				'label'             => esc_html__( 'Text Overlay Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'tm_builder' ),
			),
			'alignment' => array(
				'label'           => esc_html__( 'Slide Image Vertical Alignment', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'center' => esc_html__( 'Center', 'tm_builder' ),
					'bottom' => esc_html__( 'Bottom', 'tm_builder' ),
				),
				'description' => esc_html__( 'This setting determines the vertical alignment of your slide image. Your image can either be vertically centered, or aligned to the bottom of your slide.', 'tm_builder' ),
			),
			'video_url' => array(
				'label'           => esc_html__( 'Slide Video', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If defined, this video will appear to the left of your slide text. Enter youtube or vimeo page url, or leave blank for a text-only slide.', 'tm_builder' ),
			),
			'image_alt' => array(
				'label'           => esc_html__( 'Image Alternative Text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you have a slide image defined, input your HTML ALT text for the image here.', 'tm_builder' ),
			),
			'video_bg_mp4' => array(
				'label'              => esc_html__( 'Background Video MP4', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'tm_builder' ),
				'description'        => tm_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .MP4 version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'tm_builder' ) ),
			),
			'video_bg_webm' => array(
				'label'              => esc_html__( 'Background Video Webm', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'tm_builder' ),
				'description'        => tm_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .WEBM version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'tm_builder' ) ),
			),
			'video_bg_width' => array(
				'label'           => esc_html__( 'Background Video Width', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'In order for videos to be sized correctly, you must input the exact width (in pixels) of your video here.' ,'tm_builder' ),
			),
			'video_bg_height' => array(
				'label'           => esc_html__( 'Background Video Height', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'In order for videos to be sized correctly, you must input the exact height (in pixels) of your video here.' ,'tm_builder' ),
			),
			'allow_player_pause' => array(
				'label'           => esc_html__( 'Pause Video', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'description'     => esc_html__( 'Allow video to be paused by other players when they begin playing' ,'tm_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'tm_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your main slide text content here.', 'tm_builder' ),
			),
			'arrows_custom_color' => array(
				'label'        => esc_html__( 'Arrows Custom Color', 'tm_builder' ),
				'type'         => 'color',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'dot_nav_custom_color' => array(
				'label'        => esc_html__( 'Dot Nav Custom Color', 'tm_builder' ),
				'type'         => 'color',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'admin_title' => array(
				'label'       => esc_html__( 'Admin Label', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the slide in the builder for easy identification.', 'tm_builder' ),
			),
			'text_border_radius' => array(
				'label'           => esc_html__( 'Text Overlay Border Radius', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'default'         => '3',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'tab_slug'        => 'advanced',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array(
				'alignment',
				'heading',
				'button_text',
				'button_link',
				'button_2_text',
				'button_2_link',
				'background_color',
				'background_image',
				'image',
				'image_alt',
				'video_bg_webm',
				'video_bg_mp4',
				'video_bg_width',
				'video_bg_height',
				'video_url',
				'allow_player_pause',
				'dot_nav_custom_color',
				'arrows_custom_color',
				'button_icon',
				'custom_button',
				'button_2_icon',
				'custom_button_2',
				'background_position',
				'background_size',
				'use_bg_overlay',
				'bg_overlay_color',
				'use_text_overlay',
				'text_overlay_color',
				'text_border_radius',
			)
		);

		global $tm_pb_slider_has_video, $tm_pb_slider_parallax, $tm_pb_slider_parallax_method, $tm_pb_slider_hide_mobile, $tm_pb_slider_custom_icon, $tm_pb_slider_item_num;

		$tm_pb_slider_item_num++;

		$background_video     = '';
		$hide_on_mobile_class = self::HIDE_ON_MOBILE;
		$first_video          = false;
		$custom_icon          = $this->_var( 'button_icon' );
		$button_custom        = $this->_var( 'custom_button' );
		$custom_icon_2        = $this->_var( 'button_2_icon' );
		$button_2_custom      = $this->_var( 'custom_button_2' );

		$custom_slide_icon   = 'on' === $button_custom && '' !== $custom_icon ? $custom_icon : $tm_pb_slider_custom_icon;
		$custom_slide_icon_2 = 'on' === $button_2_custom && '' !== $custom_icon_2 ? $custom_icon_2 : $tm_pb_slider_custom_icon;

		if ( '' !== $this->_var( 'video_bg_mp4' ) || '' !== $this->_var( 'video_bg_webm' ) ) {
			if ( ! $tm_pb_slider_has_video )
				$first_video = true;

			$background_video = sprintf(
				'<div class="tm_pb_section_video_bg%2$s%3$s">
					%1$s
				</div>',
				do_shortcode( sprintf( '
					<video loop="loop" autoplay="autoplay"%3$s%4$s>
						%1$s
						%2$s
					</video>',
					( '' !== $this->_var( 'video_bg_mp4' ) ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $this->_var( 'video_bg_mp4' ) ) ) : '' ),
					( '' !== $this->_var( 'video_bg_webm' ) ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $this->_var( 'video_bg_webm' ) ) ) : '' ),
					( '' !== $this->_var( 'video_bg_width' ) ? sprintf( ' width="%s"', esc_attr( intval( $this->_var( 'video_bg_width' ) ) ) ) : '' ),
					( '' !== $this->_var( 'video_bg_height' ) ? sprintf( ' height="%s"', esc_attr( intval( $this->_var( 'video_bg_height' ) ) ) ) : '' ),
					( '' !== $this->_var( 'background_image' ) ? sprintf( ' poster="%s"', esc_url( $this->_var( 'background_image' ) ) ) : '' )
				) ),
				( $first_video ? ' tm_pb_first_video' : '' ),
				( 'on' === $this->_var( 'allow_player_pause' ) ? ' tm_pb_allow_player_pause' : '' )
			);

			$tm_pb_slider_has_video = true;

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		$this->_var( 'background_video', $background_video );

		if ( '' !== $this->_var( 'heading' ) ) {
			$this->_var( 'heading', '<h2 class="tm_pb_slide_title">' . $this->_var( 'heading' ) . '</h2>' );
		}

		$this->_var( 'button', false );
		if ( '' !== $this->_var( 'button_text' ) ) {

			$icon        = esc_attr( tm_pb_process_font_icon( $custom_slide_icon ) );
			$icon_family = tm_builder_get_icon_family();

			if ( $icon_family ) {
				TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_btn_1.tm_pb_custom_button_icon:before, %%order_class%% .tm_btn_1.tm_pb_custom_button_icon:after',
					'declaration' => sprintf(
						'font-family: "%1$s" !important;',
						esc_attr( $icon_family )
					),
				) );
			}

			$this->_var(
				'button',
				sprintf(
					'<a href="%1$s" class="tm_pb_more_button tm_btn_1 tm_pb_button%3$s%5$s"%4$s>%2$s</a>',
					tm_builder_tools()->render_url( $this->_var( 'button_link' ) ),
					esc_html( $this->_var( 'button_text' ) ),
					( 'on' === $tm_pb_slider_hide_mobile['hide_cta_on_mobile'] ? esc_attr( " {$hide_on_mobile_class}" ) : '' ),
					( '' !== $custom_slide_icon ? sprintf( ' data-icon="%1$s"', $icon ) : '' ),
					( '' !== $custom_slide_icon ? ' tm_pb_custom_button_icon' : '' )
				)
			);
		}

		$this->_var( 'button_2', false );
		if ( '' !== $this->_var( 'button_2_text' ) ) {

			$icon        = esc_attr( tm_pb_process_font_icon( $custom_slide_icon_2 ) );
			$icon_family = tm_builder_get_icon_family();

			if ( $icon_family ) {
				TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_btn_2.tm_pb_custom_button_icon:before, %%order_class%% .tm_btn_2.tm_pb_custom_button_icon:after',
					'declaration' => sprintf(
						'font-family: "%1$s" !important;',
						esc_attr( $icon_family )
					),
				) );
			}

			$this->_var(
				'button_2',
				sprintf(
					'<a href="%1$s" class="tm_pb_more_button tm_btn_2 tm_pb_button%3$s%5$s"%4$s>%2$s</a>',
					tm_builder_tools()->render_url( $this->_var( 'button_2_link' ) ),
					esc_html( $this->_var( 'button_2_text' ) ),
					( 'on' === $tm_pb_slider_hide_mobile['hide_cta_on_mobile'] ? esc_attr( " {$hide_on_mobile_class}" ) : '' ),
					( '' !== $custom_slide_icon_2 ? sprintf( ' data-icon="%1$s"', $icon ) : '' ),
					( '' !== $custom_slide_icon_2 ? ' tm_pb_custom_button_icon' : '' )
				)
			);
		}

		$style = $class = '';

		if ( '' !== $this->_var( 'background_color' ) ) {
			$style .= sprintf( 'background-color:%s;',
				esc_attr( $this->_var( 'background_color' ) )
			);
		}

		if ( '' !== $this->_var( 'background_image' ) && 'on' !== $tm_pb_slider_parallax ) {
			$style .= sprintf( 'background-image:url(%s);',
				esc_attr( $this->_var( 'background_image' ) )
			);
		}

		if ( 'on' === $this->_var( 'use_bg_overlay' ) && '' !== $this->_var( 'bg_overlay_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_slide .tm_pb_slide_overlay_container',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->_var( 'bg_overlay_color' ) )
				),
			) );
		}

		if ( 'on' === $this->_var( 'use_text_overlay' ) && '' !== $this->_var( 'text_overlay_color' ) ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_slide .tm_pb_slide_title, %%order_class%%.tm_pb_slide .tm_pb_slide_content',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->_var( 'text_overlay_color' ) )
				),
			) );
		}

		if ( '' !== $this->_var( 'text_border_radius' ) ) {

			$border_radius_value = tm_builder_process_range_value( $this->_var( 'text_border_radius' ) ); TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_slider_with_text_overlay h2.tm_pb_slide_title',
				'declaration' => sprintf(
					'-webkit-border-top-left-radius: %1$s;
					-webkit-border-top-right-radius: %1$s;
					-moz-border-radius-topleft: %1$s;
					-moz-border-radius-topright: %1$s;
					border-top-left-radius: %1$s;
					border-top-right-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );

			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_slider_with_text_overlay .tm_pb_slide_content',
				'declaration' => sprintf(
					'-webkit-border-bottom-right-radius: %1$s;
					-webkit-border-bottom-left-radius: %1$s;
					-moz-border-radius-bottomright: %1$s;
					-moz-border-radius-bottomleft: %1$s;
					border-bottom-right-radius: %1$s;
					border-bottom-left-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );
		}

		$style = '' !== $style ? " style='{$style}'" : '';
		$this->_var( 'style', $style );

		$image = '' !== $this->_var( 'image' )
			? sprintf( '<div class="tm_pb_slide_image"><img src="%1$s" alt="%2$s" /></div>',
				esc_url( $this->_var( 'image' ) ),
				esc_attr( $this->_var( 'image_alt' ) )
			)
			: '';

		if ( '' !== $this->_var( 'video_url' ) ) {
			global $wp_embed;

			$video_embed = apply_filters( 'the_content', $wp_embed->shortcode( '', esc_url( $this->_var( 'video_url' ) ) ) );

			$video_embed = preg_replace( '/<embed /', '<embed wmode="transparent" ', $video_embed );
			$video_embed = preg_replace( '/<\/object>/', '<param name="wmode" value="transparent" /></object>', $video_embed );

			$image = sprintf( '<div class="tm_pb_slide_video">%1$s</div>',
				$video_embed
			);
		}

		$this->_var( 'image', $image );

		if ( '' !== $this->_var( 'image' ) ) {
			$class = ' tm_pb_slide_with_image';
		}

		if ( '' !== $this->_var( 'video_url' ) ) {
			$class .= ' tm_pb_slide_with_video';
		}

		$class .= ' tm_pb_bg_layout_light';

		$class .= 'on' === $this->_var( 'use_bg_overlay' ) ? ' tm_pb_slider_with_overlay' : '';
		$class .= 'on' === $this->_var( 'use_text_overlay' ) ? ' tm_pb_slider_with_text_overlay' : '';

		if ( 'bottom' !== $this->_var( 'alignment' ) ) {
			$class .= ' tm_pb_media_alignment_' . $this->_var( 'alignment' );
		}

		$data_dot_nav_custom_color = '' !== $this->_var( 'dot_nav_custom_color' )
			? sprintf( ' data-dots_color="%1$s"', esc_attr( $this->_var( 'dot_nav_custom_color' ) ) )
			: '';

		$data_arrows_custom_color = '' !== $this->_var( 'arrows_custom_color' )
			? sprintf( ' data-arrows_color="%1$s"', esc_attr( $this->_var( 'arrows_custom_color' ) ) )
			: '';

		if ( 'default' !== $this->_var( 'background_position' ) && 'off' === $tm_pb_slider_parallax ) {
			$processed_position = str_replace( '_', ' ', $this->_var( 'background_position' ) );
			TM_Builder_Module::set_style(
				$function_name,
				array(
					'selector'    => '.tm_pb_slider %%order_class%%',
					'declaration' => sprintf(
						'background-position: %1$s;',
						esc_html( $processed_position )
					),
				)
			);
		}

		if ( 'default' !== $this->_var( 'background_size' ) && 'off' === $tm_pb_slider_parallax ) {
			TM_Builder_Module::set_style(
				$function_name,
				array(
					'selector'    => '.tm_pb_slider %%order_class%%',
					'declaration' => sprintf(
						'-moz-background-size: %1$s; -webkit-background-size: %1$s; background-size: %1$s;',
						esc_html( $this->_var( 'background_size' ) )
					),
				)
			);
		}

		$class = TM_Builder_Element::add_module_order_class( $class, $function_name );

		if ( 1 === $tm_pb_slider_item_num ) {
			$class .= " tm-pb-active-slide";
		}

		$classes = array(
			'tm_pb_slide',
			$class
		);

		$this->_var( 'classes', $this->parse_classes( $classes ) );

		$output = sprintf(
			'<div class="tm_pb_slide%6$s"%4$s%10$s%11$s>
				%8$s
				%12$s
				<div class="tm_pb_container clearfix">
					%5$s
					<div class="tm_pb_slide_description">
						%1$s
						<div class="tm_pb_slide_content%9$s">%2$s</div>
						%3$s
					</div> <!-- .tm_pb_slide_description -->
				</div> <!-- .tm_pb_container -->
				%7$s
			</div> <!-- .tm_pb_slide -->
			',
			$this->_var( 'heading' ),
			$this->shortcode_content,
			$this->_var( 'button' ) . $this->_var( 'button_2' ),
			$this->_var( 'style' ),
			$this->_var( 'image' ),
			esc_attr( $class ),
			( '' !== $this->_var( 'background_video' ) ? $this->_var( 'background_video' ) : '' ),
			( '' !== $this->_var( 'background_image' ) && 'on' === $tm_pb_slider_parallax ? sprintf( '<div class="tm_parallax_bg%2$s" style="background-image: url(%1$s);"></div>', esc_attr( $this->_var( 'background_image' ) ),
			( 'off' === $tm_pb_slider_parallax_method ? ' tm_pb_parallax_css' : '' ) ) : '' ),
			( 'on' === $tm_pb_slider_hide_mobile['hide_content_on_mobile'] ? esc_attr( " {$hide_on_mobile_class}" ) : '' ),
			$data_dot_nav_custom_color,
			$data_arrows_custom_color,
			'on' === $this->_var( 'use_bg_overlay' ) ? '<div class="tm_pb_slide_overlay_container"></div>' : ''
		);

		return $output;
	}
}

new Tm_Builder_Module_Slider_Item;
