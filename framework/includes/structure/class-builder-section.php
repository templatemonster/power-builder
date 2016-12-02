<?php
class Tm_Builder_Section extends Tm_Builder_Structure_Element {
	function init() {
		$this->name = esc_html__( 'Section', 'tm_builder' );
		$this->slug = 'tm_pb_section';

		$this->whitelisted_fields = array(
			'background_image',
			'transparent_background',
			'background_color',
			'background_video_mp4',
			'background_video_webm',
			'background_video_width',
			'background_video_height',
			'allow_player_pause',
			'inner_shadow',
			'parallax',
			'parallax_method',
			'custom_padding',
			'custom_padding_tablet',
			'custom_padding_phone',
			'padding_mobile',
			'module_id',
			'module_class',
			'make_fullwidth',
			'use_custom_width',
			'width_unit',
			'custom_width_px',
			'custom_width_percent',
			'make_equal',
			'use_custom_gutter',
			'gutter_width',
			'columns',
			'fullwidth',
			'specialty',
			'background_color_1',
			'background_color_2',
			'background_color_3',
			'bg_img_1',
			'bg_img_2',
			'bg_img_3',
			'padding_top_1',
			'padding_right_1',
			'padding_bottom_1',
			'padding_left_1',
			'padding_top_2',
			'padding_right_2',
			'padding_bottom_2',
			'padding_left_2',
			'padding_top_3',
			'padding_right_3',
			'padding_bottom_3',
			'padding_left_3',
			'padding_1_tablet',
			'padding_2_tablet',
			'padding_3_tablet',
			'padding_1_phone',
			'padding_2_phone',
			'padding_3_phone',
			'admin_label',
			'module_id_1',
			'module_id_2',
			'module_id_3',
			'module_class_1',
			'module_class_2',
			'module_class_3',
			'custom_css_before_1',
			'custom_css_before_2',
			'custom_css_before_3',
			'custom_css_main_1',
			'custom_css_main_2',
			'custom_css_main_3',
			'custom_css_after_1',
			'custom_css_after_2',
			'custom_css_after_3',
		);

		$this->fields_defaults = array(
			'transparent_background' => array( 'default' ),
			'background_color'       => array( '', 'only_default_setting' ),
			'allow_player_pause'     => array( 'off' ),
			'inner_shadow'           => array( 'off' ),
			'parallax'               => array( 'off' ),
			'parallax_method'        => array( 'on' ),
			'padding_mobile'         => array( 'off' ),
			'make_fullwidth'         => array( 'off' ),
			'use_custom_width'       => array( 'off' ),
			'width_unit'             => array( 'off' ),
			'custom_width_px'        => array( '1080px', 'only_default_setting' ),
			'custom_width_percent'   => array( '80%', 'only_default_setting' ),
			'make_equal'             => array( 'off' ),
			'use_custom_gutter'      => array( 'off' ),
			'gutter_width'           => array( '3', 'only_default_setting' ),
			'fullwidth'              => array( 'off' ),
			'specialty'              => array( 'off' ),
			'custom_padding_tablet'  => array( '' ),
			'custom_padding_phone'   => array( '' ),
		);
	}

	function get_fields() {
		$fields = array(
			'background_image' => array(
				'label'              => esc_html__( 'Background Image', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background', 'tm_builder' ),
				'description'        => esc_html__( 'If defined, this image will be used as the background for this module. To remove a background image, simply delete the URL from the settings field.', 'tm_builder' ),
			),
			'transparent_background' => array(
				'label'             => esc_html__( 'Transparent Background Color', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'color_option',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_background_color',
				),
				'description'       => esc_html__( 'Enabling this option will remove the background color of this section, allowing the website background color or background image to show through.', 'tm_builder' ),
			),
			'background_color' => array(
				'label'           => esc_html__( 'Background Color', 'tm_builder' ),
				'type'            => 'color',
				'depends_show_if' => 'off',
				'description'     => esc_html__( 'Define a custom background color for your module, or leave blank to use the default color.', 'tm_builder' ),
				'additional_code' => '<span class="tm-pb-reset-setting reset-default-color" style="display: none;"></span>',
			),
			'background_video_mp4' => array(
				'label'              => esc_html__( 'Background Video MP4', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'tm_builder' ),
				'description'        => tm_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .MP4 version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'tm_builder' ) ),
			),
			'background_video_webm' => array(
				'label'              => esc_html__( 'Background Video Webm', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'tm_builder' ),
				'description'        => tm_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .WEBM version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'tm_builder' ) ),
			),
			'background_video_width' => array(
				'label'           => esc_html__( 'Background Video Width', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'In order for videos to be sized correctly, you must input the exact width (in pixels) of your video here.', 'tm_builder' ),
			),
			'background_video_height' => array(
				'label'           => esc_html__( 'Background Video Height', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'In order for videos to be sized correctly, you must input the exact height (in pixels) of your video here.', 'tm_builder' ),
			),
			'allow_player_pause' => array(
				'label'           => esc_html__( 'Pause Video', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Allow video to be paused by other players when they begin playing', 'tm_builder' ),
			),
			'inner_shadow' => array(
				'label'           => esc_html__( 'Show Inner Shadow', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Here you can select whether or not your section has an inner shadow. This can look great when you have colored backgrounds or background images.', 'tm_builder' ),
			),
			'parallax' => array(
				'label'             => esc_html__( 'Use Parallax Effect', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_parallax_method',
				),
				'description'       => esc_html__( 'If enabled, your background image will stay fixed as your scroll, creating a fun parallax-like effect.', 'tm_builder' ),
			),
			'parallax_method' => array(
				'label'             => esc_html__( 'Parallax Method', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off'  => esc_html__( 'CSS', 'tm_builder' ),
					'on'   => esc_html__( 'True Parallax', 'tm_builder' ),
				),
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Define the method, used for the parallax effect.', 'tm_builder' ),
			),
			'custom_padding' => array(
				'label'           => esc_html__( 'Custom Padding', 'tm_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'tm_builder' ),
			),
			'custom_padding_tablet' => array(
				'type' => 'skip',
			),
			'custom_padding_phone' => array(
				'type' => 'skip',
			),
			'padding_mobile' => array(
				'label'             => esc_html__( 'Keep Custom Padding on Mobile', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Allow custom padding to be retained on mobile screens', 'tm_builder' ),
			),
			'make_fullwidth' => array(
				'label'             => esc_html__( 'Make This Section Fullwidth', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'depends_show_if'   => 'off',
				'tab_slug' => 'advanced',
			),
			'use_custom_width' => array(
				'label'             => esc_html__( 'Use Custom Width', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_make_fullwidth',
					'#tm_pb_custom_width',
					'#tm_pb_width_unit',
				),
				'tab_slug' => 'advanced',
			),
			'width_unit' => array(
				'label'             => esc_html__( 'Unit', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'px', 'tm_builder' ),
					'off' => '%',
				),
				'button_options' => array(
						'button_type'       => 'equal',
				),
				'depends_show_if' => 'on',
				'affects'           => array(
					'#tm_pb_custom_width_px',
					'#tm_pb_custom_width_percent',
				),
				'tab_slug' => 'advanced',
			),
			'custom_width_px' => array(
				'label'           => esc_html__( 'Custom Width', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'depends_show_if' => 'on',
				'range_settings'  => array(
					'min'  => 500,
					'max'  => 2600,
					'step' => 1,
				),
				'tab_slug' => 'advanced',
			),
			'custom_width_percent' => array(
				'label'           => esc_html__( 'Custom Width', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'depends_show_if' => 'off',
				'range_settings'  => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'tab_slug' => 'advanced',
			),
			'make_equal' => array(
				'label'             => esc_html__( 'Equalize Column Heights', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'          => 'advanced',
			),
			'use_custom_gutter' => array(
				'label'             => esc_html__( 'Use Custom Gutter Width', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_gutter_width',
				),
				'tab_slug' => 'advanced',
			),
			'gutter_width' => array(
				'label'           => esc_html__( 'Gutter Width', 'tm_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'range_settings'  => array(
					'min'  => 1,
					'max'  => 4,
					'step' => 1,
				),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
			),
			'columns' => array(
				'type'            => 'column_settings',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
			),
			'fullwidth' => array(
				'type' => 'skip',
			),
			'specialty' => array(
				'type' => 'skip',
			),
			'background_color_1' => array(
				'type' => 'skip',
			),
			'background_color_2' => array(
				'type' => 'skip',
			),
			'background_color_3' => array(
				'type' => 'skip',
			),
			'bg_img_1' => array(
				'type' => 'skip',
			),
			'bg_img_2' => array(
				'type' => 'skip',
			),
			'bg_img_3' => array(
				'type' => 'skip',
			),
			'padding_top_1' => array(
				'type' => 'skip',
			),
			'padding_right_1' => array(
				'type' => 'skip',
			),
			'padding_bottom_1' => array(
				'type' => 'skip',
			),
			'padding_left_1' => array(
				'type' => 'skip',
			),
			'padding_top_2' => array(
				'type' => 'skip',
			),
			'padding_right_2' => array(
				'type' => 'skip',
			),
			'padding_bottom_2' => array(
				'type' => 'skip',
			),
			'padding_left_2' => array(
				'type' => 'skip',
			),
			'padding_top_3' => array(
				'type' => 'skip',
			),
			'padding_right_3' => array(
				'type' => 'skip',
			),
			'padding_bottom_3' => array(
				'type' => 'skip',
			),
			'padding_left_3' => array(
				'type' => 'skip',
			),
			'padding_1_tablet' => array(
				'type' => 'skip',
			),
			'padding_2_tablet' => array(
				'type' => 'skip',
			),
			'padding_3_tablet' => array(
				'type' => 'skip',
			),
			'padding_1_phone' => array(
				'type' => 'skip',
			),
			'padding_2_phone' => array(
				'type' => 'skip',
			),
			'padding_3_phone' => array(
				'type' => 'skip',
			),
			'module_id_1' => array(
				'type' => 'skip',
			),
			'module_id_2' => array(
				'type' => 'skip',
			),
			'module_id_3' => array(
				'type' => 'skip',
			),
			'module_class_1' => array(
				'type' => 'skip',
			),
			'module_class_2' => array(
				'type' => 'skip',
			),
			'module_class_3' => array(
				'type' => 'skip',
			),
			'custom_css_before_1' => array(
				'type' => 'skip',
			),
			'custom_css_before_2' => array(
				'type' => 'skip',
			),
			'custom_css_before_3' => array(
				'type' => 'skip',
			),
			'custom_css_main_1' => array(
				'type' => 'skip',
			),
			'custom_css_main_2' => array(
				'type' => 'skip',
			),
			'custom_css_main_3' => array(
				'type' => 'skip',
			),
			'custom_css_after_1' => array(
				'type' => 'skip',
			),
			'custom_css_after_2' => array(
				'type' => 'skip',
			),
			'custom_css_after_3' => array(
				'type' => 'skip',
			),
			'columns_css' => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'priority'        => '20',
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
				'description' => esc_html__( 'This will change the label of the section in the builder for easy identification when collapsed.', 'tm_builder' ),
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
			'columns_css_fields' => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$background_image        = $this->shortcode_atts['background_image'];
		$background_color        = $this->shortcode_atts['background_color'];
		$background_video_mp4    = $this->shortcode_atts['background_video_mp4'];
		$background_video_webm   = $this->shortcode_atts['background_video_webm'];
		$background_video_width  = $this->shortcode_atts['background_video_width'];
		$background_video_height = $this->shortcode_atts['background_video_height'];
		$allow_player_pause      = $this->shortcode_atts['allow_player_pause'];
		$inner_shadow            = $this->shortcode_atts['inner_shadow'];
		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$fullwidth               = $this->shortcode_atts['fullwidth'];
		$specialty               = $this->shortcode_atts['specialty'];
		$transparent_background  = $this->shortcode_atts['transparent_background'];
		$custom_padding          = $this->shortcode_atts['custom_padding'];
		$custom_padding_tablet   = $this->shortcode_atts['custom_padding_tablet'];
		$custom_padding_phone    = $this->shortcode_atts['custom_padding_phone'];
		$padding_mobile          = $this->shortcode_atts['padding_mobile'];
		$background_color_1      = $this->shortcode_atts['background_color_1'];
		$background_color_2      = $this->shortcode_atts['background_color_2'];
		$background_color_3      = $this->shortcode_atts['background_color_3'];
		$bg_img_1                = $this->shortcode_atts['bg_img_1'];
		$bg_img_2                = $this->shortcode_atts['bg_img_2'];
		$bg_img_3                = $this->shortcode_atts['bg_img_3'];
		$padding_top_1           = $this->shortcode_atts['padding_top_1'];
		$padding_right_1         = $this->shortcode_atts['padding_right_1'];
		$padding_bottom_1        = $this->shortcode_atts['padding_bottom_1'];
		$padding_left_1          = $this->shortcode_atts['padding_left_1'];
		$padding_top_2           = $this->shortcode_atts['padding_top_2'];
		$padding_right_2         = $this->shortcode_atts['padding_right_2'];
		$padding_bottom_2        = $this->shortcode_atts['padding_bottom_2'];
		$padding_left_2          = $this->shortcode_atts['padding_left_2'];
		$padding_top_3           = $this->shortcode_atts['padding_top_3'];
		$padding_right_3         = $this->shortcode_atts['padding_right_3'];
		$padding_bottom_3        = $this->shortcode_atts['padding_bottom_3'];
		$padding_left_3          = $this->shortcode_atts['padding_left_3'];
		$padding_1_tablet        = $this->shortcode_atts['padding_1_tablet'];
		$padding_2_tablet        = $this->shortcode_atts['padding_2_tablet'];
		$padding_3_tablet        = $this->shortcode_atts['padding_3_tablet'];
		$padding_1_phone         = $this->shortcode_atts['padding_1_phone'];
		$padding_2_phone         = $this->shortcode_atts['padding_2_phone'];
		$padding_3_phone         = $this->shortcode_atts['padding_3_phone'];
		$gutter_width            = $this->shortcode_atts['gutter_width'];
		$use_custom_width        = $this->shortcode_atts['use_custom_width'];
		$custom_width_px         = $this->shortcode_atts['custom_width_px'];
		$custom_width_percent    = $this->shortcode_atts['custom_width_percent'];
		$width_unit              = $this->shortcode_atts['width_unit'];
		$make_equal              = $this->shortcode_atts['make_equal'];
		$make_fullwidth          = $this->shortcode_atts['make_fullwidth'];
		$global_module           = $this->shortcode_atts['global_module'];
		$use_custom_gutter       = $this->shortcode_atts['use_custom_gutter'];
		$module_id_1             = $this->shortcode_atts['module_id_1'];
		$module_id_2             = $this->shortcode_atts['module_id_2'];
		$module_id_3             = $this->shortcode_atts['module_id_3'];
		$module_class_1          = $this->shortcode_atts['module_class_1'];
		$module_class_2          = $this->shortcode_atts['module_class_2'];
		$module_class_3          = $this->shortcode_atts['module_class_3'];
		$custom_css_before_1     = $this->shortcode_atts['custom_css_before_1'];
		$custom_css_before_2     = $this->shortcode_atts['custom_css_before_2'];
		$custom_css_before_3     = $this->shortcode_atts['custom_css_before_3'];
		$custom_css_main_1       = $this->shortcode_atts['custom_css_main_1'];
		$custom_css_main_2       = $this->shortcode_atts['custom_css_main_2'];
		$custom_css_main_3       = $this->shortcode_atts['custom_css_main_3'];
		$custom_css_after_1      = $this->shortcode_atts['custom_css_after_1'];
		$custom_css_after_2      = $this->shortcode_atts['custom_css_after_2'];
		$custom_css_after_3      = $this->shortcode_atts['custom_css_after_3'];

		if ( '' !== $global_module ) {
			$global_content = tm_pb_load_global_module( $global_module );

			if ( '' !== $global_content ) {
				return do_shortcode( $global_content );
			}
		}

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );
		$gutter_class = '';

		$padding_mobile_values = array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		);

		if ( 'on' === $specialty ) {
			global $tm_pb_column_backgrounds, $tm_pb_column_paddings, $tm_pb_columns_counter, $tm_pb_column_css, $tm_pb_column_paddings_mobile;
			$module_class .= 'on' === $make_equal ? ' tm_pb_equal_columns' : '';

			if ( 'on' === $use_custom_gutter ) {
				$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter to 1 if 0 entered by user
				$gutter_class .= ' tm_pb_gutters' . $gutter_width;
			}

			$tm_pb_columns_counter = 0;
			$tm_pb_column_backgrounds = array(
				array( $background_color_1, $bg_img_1 ),
				array( $background_color_2, $bg_img_2 ),
				array( $background_color_3, $bg_img_3 ),
			);

			$tm_pb_column_paddings = array(
				array(
					'padding-top'    => $padding_top_1,
					'padding-right'  => $padding_right_1,
					'padding-bottom' => $padding_bottom_1,
					'padding-left'   => $padding_left_1
				),
				array(
					'padding-top'    => $padding_top_2,
					'padding-right'  => $padding_right_2,
					'padding-bottom' => $padding_bottom_2,
					'padding-left'   => $padding_left_2
				),
				array(
					'padding-top'    => $padding_top_3,
					'padding-right'  => $padding_right_3,
					'padding-bottom' => $padding_bottom_3,
					'padding-left'   => $padding_left_3
				),
			);

			$tm_pb_column_paddings_mobile = array(
				array(
					'tablet' => explode( '|', $padding_1_tablet ),
					'phone'  => explode( '|', $padding_1_phone ),
				),
				array(
					'tablet' => explode( '|', $padding_2_tablet ),
					'phone'  => explode( '|', $padding_2_phone ),
				),
				array(
					'tablet' => explode( '|', $padding_3_tablet ),
					'phone'  => explode( '|', $padding_3_phone ),
				),
			);

			if ( 'on' === $make_fullwidth && 'off' === $use_custom_width ) {
				$module_class .= ' tm_pb_specialty_fullwidth';
			}

			if ( 'on' === $use_custom_width ) { TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% > .tm_pb_row',
					'declaration' => sprintf(
						'max-width:%1$s !important;',
						'on' === $width_unit ? esc_attr( $custom_width_px ) : esc_attr( $custom_width_percent )
					),
				) );
			}

			$tm_pb_column_css = array(
				'css_class'         => array( $module_class_1, $module_class_2, $module_class_3 ),
				'css_id'            => array( $module_id_1, $module_id_2, $module_id_3 ),
				'custom_css_before' => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3 ),
				'custom_css_main'   => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3 ),
				'custom_css_after'  => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3 ),
			);
		}

		$background_video = '';

		if ( '' !== $background_video_mp4 || '' !== $background_video_webm ) {
			$background_video = sprintf(
				'<div class="tm_pb_section_video_bg%2$s">
					%1$s
				</div>',
				do_shortcode( sprintf( '
					<video loop="loop" autoplay="autoplay"%3$s%4$s>
						%1$s
						%2$s
					</video>',
					( '' !== $background_video_mp4 ? sprintf( '<source type="video/mp4" src="%s" />', esc_attr( $background_video_mp4 ) ) : '' ),
					( '' !== $background_video_webm ? sprintf( '<source type="video/webm" src="%s" />', esc_attr( $background_video_webm ) ) : '' ),
					( '' !== $background_video_width ? sprintf( ' width="%s"', esc_attr( intval( $background_video_width ) ) ) : '' ),
					( '' !== $background_video_height ? sprintf( ' height="%s"', esc_attr( intval( $background_video_height ) ) ) : '' )
				) ),
				( 'on' === $allow_player_pause ? ' tm_pb_allow_player_pause' : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		// set the correct default value for $transparent_background option if plugin activated.
		if ( tm_is_builder_plugin_active() && 'default' === $transparent_background ) {
			$transparent_background = '' !== $background_color ? 'off' : 'on';
		} elseif ( 'default' === $transparent_background ) {
			$transparent_background = 'off';
		}

		if ( '' !== $background_color && 'off' === $transparent_background ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-color:%s;',
					esc_attr( $background_color )
				),
			) );
		}

		if ( '' !== $background_image && 'on' !== $parallax ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-image:url(%s);',
					esc_attr( $background_image )
				),
			) );
		}

		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of sections supports only top and bottom padding, so we need to handle it along with the full padding in the recent version
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right' => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left' => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '%%order_class%%',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					if ( 'on' !== $padding_mobile ) {
						$element_style['media_query'] = TM_Builder_Element::get_media_query( 'min_width_981' );
					} TM_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				tm_pb_generate_responsive_css( $padding_mobile_values_processed, '%%order_class%%', '', $function_name );
			}
		}

		if ( '' !== $background_video_mp4 || '' !== $background_video_webm || ( '' !== $background_color && 'off' === $transparent_background ) || '' !== $background_image ) {
			$module_class .= ' tm_pb_with_background';
		}

		$output = sprintf(
			'<div%7$s class="tm_pb_section%3$s%4$s%5$s%6$s%8$s%12$s%13$s">
				%11$s
				%9$s
					%2$s
					%1$s
				%10$s
			</div> <!-- .tm_pb_section -->',
			do_shortcode( tm_pb_fix_shortcodes( $content ) ),
			$background_video,
			( '' !== $background_video ? ' tm_pb_section_video tm_pb_preload' : '' ),
			( ( 'off' !== $inner_shadow && ! ( '' !== $background_image && 'on' === $parallax && 'off' === $parallax_method ) ) ? ' tm_pb_inner_shadow' : '' ),
			( 'on' === $parallax ? ' tm_pb_section_parallax' : '' ),
			( 'off' !== $fullwidth ? ' tm_pb_fullwidth_section' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'on' === $specialty ?
				sprintf( '<div class="row tm_pb_row%1$s">', $gutter_class )
				: '' ),
			( 'on' === $specialty ? '</div> <!-- .tm_pb_row -->' : '' ),
			( '' !== $background_image && 'on' === $parallax
				? sprintf(
					'<div class="tm_parallax_bg%2$s%3$s" style="background-image: url(%1$s);"></div>',
					esc_attr( $background_image ),
					( 'off' === $parallax_method ? ' tm_pb_parallax_css' : '' ),
					( ( 'off' !== $inner_shadow && 'off' === $parallax_method ) ? ' tm_pb_inner_shadow' : '' )
				)
				: ''
			),
			( 'on' === $specialty ? ' tm_section_specialty' : ' tm_section_regular' ),
			( 'on' === $transparent_background ? ' tm_section_transparent' : '' )
		);

		return $output;

	}

}
new Tm_Builder_Section;
