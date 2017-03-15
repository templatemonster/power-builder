<?php
class Tm_Builder_Module_Video extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Video', 'power-builder' );
		$this->slug = 'tm_pb_video';
		$this->icon = 'f03d';

		$this->whitelisted_fields = array(
			'src',
			'src_webm',
			'image_src',
			'controls',
			'auto_play',
			'loop_play',
			'mute',
			'play_icon_color',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'auto_play' => array( 'off' ),
			'loop_play' => array( 'off' ),
			'mute'      => array( 'off' ),
		);

		$this->custom_css_options = array(
			'video_icon' => array(
				'label'    => esc_html__( 'Video Icon', 'power-builder' ),
				'selector' => '.tm_pb_video_play',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Video MP4/URL', 'power-builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'power-builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'power-builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'power-builder' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'power-builder' ),
			),
			'src_webm' => array(
				'label'              => esc_html__( 'Video Webm', 'power-builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'power-builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'power-builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'power-builder' ),
				'description'        => esc_html__( 'Upload the .WEBM version of your video here. All uploaded videos should be in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers.', 'power-builder' ),
			),
			'image_src' => array(
				'label'              => esc_html__( 'Image Overlay URL', 'power-builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'power-builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'power-builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'power-builder' ),
				'additional_button'  => sprintf(
					'<input type="button" class="button tm-pb-video-image-button" value="%1$s" />',
					esc_attr__( 'Generate From Video', 'power-builder' )
				),
				'classes'            => 'tm_pb_video_overlay',
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display over your video. You can also generate a still image from your video.', 'power-builder' ),
			),
			'auto_play' => array(
				'label'           => esc_html__( 'Auto play', 'power-builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'power-builder' ),
					'off' => esc_html__( 'Off', 'power-builder' ),
				),
				'description'        => esc_html__( 'Automatically plaing video when the page is loaded', 'power-builder' ),
			),
			'loop_play' => array(
				'label'           => esc_html__( 'Loop', 'power-builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'power-builder' ),
					'off' => esc_html__( 'Off', 'power-builder' ),
				),
				'description'        => esc_html__( 'Here you can choose whether or not to loop the video', 'power-builder' ),
			),
			'mute' => array(
				'label'           => esc_html__( 'Mute', 'power-builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'power-builder' ),
					'off' => esc_html__( 'Off', 'power-builder' ),
				),
				'description'        => esc_html__( 'Here you can choose whether or not to mute the video', 'power-builder' ),
			),
			'play_icon_color' => array(
				'label'             => esc_html__( 'Play Icon Color', 'power-builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'power-builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => tm_pb_media_breakpoints(),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'power-builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'power-builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'power-builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'power-builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),

		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id       = $this->shortcode_atts['module_id'];
		$module_class    = $this->shortcode_atts['module_class'];
		$src             = $this->shortcode_atts['src'];
		$src_webm        = $this->shortcode_atts['src_webm'];
		$image_src       = $this->shortcode_atts['image_src'];
		$auto_play       = $this->shortcode_atts['auto_play'];
		$loop_play       = $this->shortcode_atts['loop_play'];
		$mute            = $this->shortcode_atts['mute'];
		$play_icon_color = $this->shortcode_atts['play_icon_color'];
		$video_src       = '';

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $play_icon_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_video_play:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $play_icon_color )
				),
			) );
		}

		if ( '' !== $src || '' !== $src_webm ) {
			if ( ! empty( $src ) && false !== tm_pb_check_oembed_provider( esc_url( $src ) ) ) {
				$video_src = wp_oembed_get( esc_url( $src ) );
			} else {
				$video_src = sprintf( '
					<video %3$s%4$s%5$s%6$s>
						%1$s
						%2$s
					</video>',
					( '' !== $src ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $src ) ) : '' ),
					( '' !== $src_webm ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $src_webm ) ) : '' ),
					' controls',
					( 'on' === $auto_play ? ' data-autoplay="true"' : ' data-autoplay="false"' ),
					( 'on' === $loop_play ? ' loop' : '' ),
					( 'on' === $mute ? ' muted' : '' )
				);

				wp_enqueue_style( 'wp-mediaelement' );
				wp_enqueue_script( 'wp-mediaelement' );
			}
		}

		$output = sprintf(
			'<div%2$s class="tm_pb_module tm_pb_video%3$s">
				<div class="tm_pb_video_box">
					%1$s
				</div>
				%4$s
			</div>',
			( '' !== $video_src ? $video_src : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $image_src && 'off' === $auto_play ? sprintf(
					'<div class="tm_pb_video_overlay" style="background-image: url(%1$s);">
						<div class="tm_pb_video_overlay_hover">
							<a href="#" class="tm_pb_video_play"></a>
						</div>
					</div>',
					esc_attr( $image_src )
				) : '' )
		);

		return $output;
	}
}
new Tm_Builder_Module_Video;

