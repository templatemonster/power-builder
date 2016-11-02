<?php
class Tm_Builder_Module_Video_Slider_Item extends Tm_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Video', 'tm_builder' );
		$this->slug                        = 'tm_pb_video_slider_item';
		$this->type                        = 'child';
		$this->custom_css_tab              = false;
		$this->child_title_var             = 'admin_title';
		$this->advanced_setting_title_text = esc_html__( 'New Video', 'tm_builder' );
		$this->settings_text               = esc_html__( 'Video Settings', 'tm_builder' );

		$this->whitelisted_fields = array(
			'admin_title',
			'src',
			'src_webm',
			'image_src',
		);

		$this->fields_defaults = array();
	}

	function get_fields() {
		$fields = array(
			'admin_title' => array(
				'label'       => esc_html__( 'Admin Label', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the video in the builder for easy identification.', 'tm_builder' ),
			),
			'src' => array(
				'label'              => esc_html__( 'Video MP4/URL', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'tm_builder' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'tm_builder' ),
			),
			'src_webm' => array(
				'label'              => esc_html__( 'Video Webm', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'tm_builder' ),
				'description'        => esc_html__( 'Upload the .WEBM version of your video here. All uploaded videos should be in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers.', 'tm_builder' ),
			),
			'image_src' => array(
				'label'              => esc_html__( 'Image Overlay URL', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'tm_builder' ),
				'additional_button'  => sprintf(
					'<input type="button" class="button tm-pb-video-image-button" value="%1$s" />',
					esc_attr__( 'Generate From Video', 'tm_builder' )
				),
				'classes'            => 'tm_pb_video_overlay',
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display over your video. You can also generate a still image from your video.', 'tm_builder' ),
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$src               = $this->shortcode_atts['src'];
		$src_webm          = $this->shortcode_atts['src_webm'];
		$image_src         = $this->shortcode_atts['image_src'];
		$video_src         = '';

		global $tm_pb_slider_image_overlay;

		$class  = '';
		$class .= " tm_pb_bg_layout_light";

		if ( '' !== $image_src ) {
			$image_overlay_output = tm_pb_set_video_oembed_thumbnail_resolution( $image_src, 'high' );
			$thumbnail_track_output = $image_src;
		} else {
			$image_overlay_output = '';
			if ( false !== tm_pb_check_oembed_provider( esc_url( $src ) ) ) {
				add_filter( 'oembed_dataparse', 'tm_pb_video_oembed_data_parse', 10, 3 );
				// Save thumbnail
				$thumbnail_track_output = wp_oembed_get( esc_url( $src ) );
				// Set back to normal
				remove_filter( 'oembed_dataparse', 'tm_pb_video_oembed_data_parse', 10, 3 );
			} else {
				$thumbnail_track_output = '';
			}
		}

		if ( '' !== $src ) {
			if ( false !== tm_pb_check_oembed_provider( esc_url( $src ) ) ) {
				$video_src = wp_oembed_get( esc_url( $src ) );
			} else {
				$video_src = sprintf( '
					<video controls>
						%1$s
						%2$s
					</video>',
					( '' !== $src ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $src ) ) : '' ),
					( '' !== $src_webm ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $src_webm ) ) : '' )
				);

				wp_enqueue_style( 'wp-mediaelement' );
				wp_enqueue_script( 'wp-mediaelement' );
			}
		}

		$video_output = sprintf(
			'<div class="tm_pb_video_wrap">
				<div class="tm_pb_video_box">
					%1$s
				</div>
				%2$s
			</div>',
			( '' !== $video_src ? $video_src : '' ),
			(
				( '' !== $image_overlay_output && $tm_pb_slider_image_overlay == 'show' )
					? sprintf(
						'<div class="tm_pb_video_overlay" style="background-image: url(%1$s);">
							<div class="tm_pb_video_overlay_hover">
								<a href="#" class="tm_pb_video_play"></a>
							</div>
						</div>',
						esc_attr( $image_overlay_output )
					)
					: ''
			)
		);

		$output = sprintf(
			'<div class="tm_pb_slide%1$s"%3$s>
				%2$s
			</div> <!-- .tm_pb_slide -->
			',
			esc_attr( $class ),
			( '' !== $video_output ? $video_output : '' ),
			( '' !== $thumbnail_track_output ? sprintf( ' data-image="%1$s"', esc_attr( $thumbnail_track_output ) ) : '' )
		);

		return $output;
	}
}
new Tm_Builder_Module_Video_Slider_Item;

