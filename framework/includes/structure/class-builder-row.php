<?php
class Tm_Builder_Row extends Tm_Builder_Structure_Element {
	function init() {
		$this->name = esc_html__( 'Row', 'tm_builder' );
		$this->slug = 'tm_pb_row';

		$this->advanced_options = array(
			'custom_margin_padding' => array(
				'use_padding'       => false,
				'custom_margin'     => array(
					'priority' => 1,
				),
			),
		);

		$this->whitelisted_fields = array(
			'make_fullwidth',
			'use_custom_width',
			'use_grid_padding',
			'width_unit',
			'custom_width_px',
			'custom_width_percent',
			'custom_padding',
			'custom_padding_tablet',
			'custom_padding_phone',
			'padding_mobile',
			'column_padding_mobile',
			'module_id',
			'module_class',
			'background_image',
			'background_color',
			'background_video_mp4',
			'background_video_webm',
			'background_video_width',
			'background_video_height',
			'allow_player_pause',
			'parallax',
			'parallax_method',
			'make_equal',
			'columns',
			'background_color_1',
			'background_color_2',
			'background_color_3',
			'background_color_4',
			'bg_img_1',
			'bg_img_2',
			'bg_img_3',
			'bg_img_4',
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
			'padding_top_4',
			'padding_right_4',
			'padding_bottom_4',
			'padding_left_4',
			'padding_1_laptop',
			'padding_2_laptop',
			'padding_3_laptop',
			'padding_4_laptop',
			'padding_1_tablet',
			'padding_2_tablet',
			'padding_3_tablet',
			'padding_4_tablet',
			'padding_1_phone',
			'padding_2_phone',
			'padding_3_phone',
			'padding_4_phone',
			'admin_label',
			'parallax_1',
			'parallax_method_1',
			'parallax_2',
			'parallax_method_2',
			'parallax_3',
			'parallax_method_3',
			'parallax_4',
			'parallax_method_4',
			'module_id_1',
			'module_id_2',
			'module_id_3',
			'module_id_4',
			'module_class_1',
			'module_class_2',
			'module_class_3',
			'module_class_4',
			'custom_css_before_1',
			'custom_css_before_2',
			'custom_css_before_3',
			'custom_css_before_4',
			'custom_css_main_1',
			'custom_css_main_2',
			'custom_css_main_3',
			'custom_css_main_4',
			'custom_css_after_1',
			'custom_css_after_2',
			'custom_css_after_3',
			'custom_css_after_4',
		);

		$this->fields_defaults = array(
			'make_fullwidth'        => array( 'off' ),
			'use_custom_width'      => array( 'off' ),
			'use_grid_padding'      => array( 'on' ),
			'width_unit'            => array( 'off' ),
			'custom_width_px'       => array( '1080px', 'only_default_setting' ),
			'custom_width_percent'  => array( '80%', 'only_default_setting' ),
			'padding_mobile'        => array( 'off' ),
			'column_padding_mobile' => array( 'on' ),
			'background_color'      => array( '', 'only_default_setting' ),
			'allow_player_pause'    => array( 'off' ),
			'parallax'              => array( 'off' ),
			'parallax_method'       => array( 'on' ),
			'make_equal'            => array( 'off' ),
			'parallax_1'            => array( 'off' ),
			'parallax_method_1'     => array( 'on' ),
			'parallax_2'            => array( 'off' ),
			'parallax_method_2'     => array( 'on' ),
			'parallax_3'            => array( 'off' ),
			'parallax_method_3'     => array( 'on' ),
			'parallax_4'            => array( 'off' ),
			'parallax_method_4'     => array( 'on' ),
			'custom_padding_laptop' => array( '' ),
			'custom_padding_tablet' => array( '' ),
			'custom_padding_phone'  => array( '' ),
		);
	}

	function get_fields() {
		$fields = array(
			'make_fullwidth' => array(
				'label'             => esc_html__( 'Make This Row Fullwidth', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Enable this option to extend the width of this row to the edge of the browser window.', 'tm_builder' ),
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
				'description'       => esc_html__( 'Change to Yes if you would like to adjust the width of this row to a non-standard width.', 'tm_builder' ),
			),
			'use_grid_padding' => array(
				'label'             => esc_html__( 'Use grid padding', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'description'       => esc_html__( 'Change to No if you would like delete grid padding.', 'tm_builder' ),
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
				'description'     => esc_html__( 'Define custom width for this Row', 'tm_builder' ),
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
				'description'     => esc_html__( 'Define custom width for this Row', 'tm_builder' ),
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
			'custom_margin' => array(
				'label'           => esc_html__( 'Custom Margin', 'tm_builder' ),
				'type'            => 'custom_margin',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
			),
			'background_image' => array(
				'label'              => esc_html__( 'Background Image', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'configuration',
				'upload_button_text' => esc_attr__( 'Upload an image', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Image', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background', 'tm_builder' ),
				'tab_slug'           => 'advanced',
			),
			'background_color' => array(
				'label'        => esc_html__( 'Background Color', 'tm_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'background_video_mp4' => array(
				'label'              => esc_html__( 'Background Video MP4', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'tm_builder' ),
				'tab_slug'           => 'advanced',
			),
			'background_video_webm' => array(
				'label'              => esc_html__( 'Background Video Webm', 'tm_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'tm_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'tm_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'tm_builder' ),
				'tab_slug'           => 'advanced',
			),
			'background_video_width' => array(
				'label'           => esc_html__( 'Background Video Width', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'tab_slug'        => 'advanced',
			),
			'background_video_height' => array(
				'label'           => esc_html__( 'Background Video Height', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'tab_slug'        => 'advanced',
			),
			'allow_player_pause' => array(
				'label'           => esc_html__( 'Pause Video', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'          => 'advanced',
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
				'tab_slug'          => 'advanced',
			),
			'parallax_method' => array(
				'label'             => esc_html__( 'Parallax Method', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'CSS', 'tm_builder' ),
					'on'  => esc_html__( 'True Parallax', 'tm_builder' ),
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
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
			'columns' => array(
				'type'            => 'column_settings',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
			),
			'column_padding_mobile' => array(
				'label'             => esc_html__( 'Keep Column Padding on Mobile', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'tab_slug'          => 'advanced',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
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
			'background_color_4' => array(
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
			'bg_img_4' => array(
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
			'padding_top_4' => array(
				'type' => 'skip',
			),
			'padding_right_4' => array(
				'type' => 'skip',
			),
			'padding_bottom_4' => array(
				'type' => 'skip',
			),
			'padding_left_4' => array(
				'type' => 'skip',
			),
			'parallax_1' => array(
				'type' => 'skip',
			),
			'parallax_method_1' => array(
				'type' => 'skip',
			),
			'parallax_2' => array(
				'type' => 'skip',
			),
			'parallax_method_2' => array(
				'type' => 'skip',
			),
			'parallax_3' => array(
				'type' => 'skip',
			),
			'parallax_method_3' => array(
				'type' => 'skip',
			),
			'parallax_4' => array(
				'type' => 'skip',
			),
			'parallax_method_4' => array(
				'type' => 'skip',
			),
			'padding_1_laptop' => array(
				'type' => 'skip',
			),
			'padding_2_laptop' => array(
				'type' => 'skip',
			),
			'padding_3_laptop' => array(
				'type' => 'skip',
			),
			'padding_4_laptop' => array(
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
			'padding_4_tablet' => array(
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
			'padding_4_phone' => array(
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
			'module_id_4' => array(
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
			'module_class_4' => array(
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
			'custom_css_before_4' => array(
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
			'custom_css_main_4' => array(
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
			'custom_css_after_4' => array(
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
				'description' => esc_html__( 'This will change the label of the row in the builder for easy identification when collapsed.', 'tm_builder' ),
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
		$custom_padding          = $this->shortcode_atts['custom_padding'];
		$custom_padding_tablet   = $this->shortcode_atts['custom_padding_tablet'];
		$custom_padding_phone    = $this->shortcode_atts['custom_padding_phone'];
		$padding_mobile          = $this->shortcode_atts['padding_mobile'];
		$column_padding_mobile   = $this->shortcode_atts['column_padding_mobile'];
		$make_fullwidth          = $this->shortcode_atts['make_fullwidth'];
		$make_equal              = $this->shortcode_atts['make_equal'];
		$background_image        = $this->shortcode_atts['background_image'];
		$background_color        = $this->shortcode_atts['background_color'];
		$background_video_mp4    = $this->shortcode_atts['background_video_mp4'];
		$background_video_webm   = $this->shortcode_atts['background_video_webm'];
		$background_video_width  = $this->shortcode_atts['background_video_width'];
		$background_video_height = $this->shortcode_atts['background_video_height'];
		$allow_player_pause      = $this->shortcode_atts['allow_player_pause'];
		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$background_color_1      = $this->shortcode_atts['background_color_1'];
		$background_color_2      = $this->shortcode_atts['background_color_2'];
		$background_color_3      = $this->shortcode_atts['background_color_3'];
		$background_color_4      = $this->shortcode_atts['background_color_4'];
		$bg_img_1                = $this->shortcode_atts['bg_img_1'];
		$bg_img_2                = $this->shortcode_atts['bg_img_2'];
		$bg_img_3                = $this->shortcode_atts['bg_img_3'];
		$bg_img_4                = $this->shortcode_atts['bg_img_4'];
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
		$padding_top_4           = $this->shortcode_atts['padding_top_4'];
		$padding_right_4         = $this->shortcode_atts['padding_right_4'];
		$padding_bottom_4        = $this->shortcode_atts['padding_bottom_4'];
		$padding_left_4          = $this->shortcode_atts['padding_left_4'];
		$padding_1_laptop        = $this->shortcode_atts['padding_1_laptop'];
		$padding_2_laptop        = $this->shortcode_atts['padding_2_laptop'];
		$padding_3_laptop        = $this->shortcode_atts['padding_3_laptop'];
		$padding_4_laptop        = $this->shortcode_atts['padding_4_laptop'];
		$padding_1_tablet        = $this->shortcode_atts['padding_1_tablet'];
		$padding_2_tablet        = $this->shortcode_atts['padding_2_tablet'];
		$padding_3_tablet        = $this->shortcode_atts['padding_3_tablet'];
		$padding_4_tablet        = $this->shortcode_atts['padding_4_tablet'];
		$padding_1_phone         = $this->shortcode_atts['padding_1_phone'];
		$padding_2_phone         = $this->shortcode_atts['padding_2_phone'];
		$padding_3_phone         = $this->shortcode_atts['padding_3_phone'];
		$padding_4_phone         = $this->shortcode_atts['padding_4_phone'];
		$use_custom_width        = $this->shortcode_atts['use_custom_width'];
		$use_grid_padding        = $this->shortcode_atts['use_grid_padding'];
		$custom_width_px         = $this->shortcode_atts['custom_width_px'];
		$custom_width_percent    = $this->shortcode_atts['custom_width_percent'];
		$width_unit              = $this->shortcode_atts['width_unit'];
		$global_module           = $this->shortcode_atts['global_module'];
		$parallax_1              = $this->shortcode_atts['parallax_1'];
		$parallax_method_1       = $this->shortcode_atts['parallax_method_1'];
		$parallax_2              = $this->shortcode_atts['parallax_2'];
		$parallax_method_2       = $this->shortcode_atts['parallax_method_2'];
		$parallax_3              = $this->shortcode_atts['parallax_3'];
		$parallax_method_3       = $this->shortcode_atts['parallax_method_3'];
		$parallax_4              = $this->shortcode_atts['parallax_4'];
		$parallax_method_4       = $this->shortcode_atts['parallax_method_4'];
		$module_id_1             = $this->shortcode_atts['module_id_1'];
		$module_id_2             = $this->shortcode_atts['module_id_2'];
		$module_id_3             = $this->shortcode_atts['module_id_3'];
		$module_id_4             = $this->shortcode_atts['module_id_4'];
		$module_class_1          = $this->shortcode_atts['module_class_1'];
		$module_class_2          = $this->shortcode_atts['module_class_2'];
		$module_class_3          = $this->shortcode_atts['module_class_3'];
		$module_class_4          = $this->shortcode_atts['module_class_4'];
		$custom_css_before_1     = $this->shortcode_atts['custom_css_before_1'];
		$custom_css_before_2     = $this->shortcode_atts['custom_css_before_2'];
		$custom_css_before_3     = $this->shortcode_atts['custom_css_before_3'];
		$custom_css_before_4     = $this->shortcode_atts['custom_css_before_4'];
		$custom_css_main_1       = $this->shortcode_atts['custom_css_main_1'];
		$custom_css_main_2       = $this->shortcode_atts['custom_css_main_2'];
		$custom_css_main_3       = $this->shortcode_atts['custom_css_main_3'];
		$custom_css_main_4       = $this->shortcode_atts['custom_css_main_4'];
		$custom_css_after_1      = $this->shortcode_atts['custom_css_after_1'];
		$custom_css_after_2      = $this->shortcode_atts['custom_css_after_2'];
		$custom_css_after_3      = $this->shortcode_atts['custom_css_after_3'];
		$custom_css_after_4      = $this->shortcode_atts['custom_css_after_4'];

		global $tm_pb_column_backgrounds, $tm_pb_column_paddings, $tm_pb_columns_counter, $keep_column_padding_mobile, $tm_pb_column_parallax, $tm_pb_column_css, $tm_pb_column_paddings_mobile;

		$keep_column_padding_mobile = $column_padding_mobile;

		if ( '' !== $global_module ) {
			$global_content = tm_pb_load_global_module( $global_module, $function_name );

			if ( '' !== $global_content ) {
				return do_shortcode( $global_content );
			}
		}

		$padding_mobile_values = array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		);

		$tm_pb_columns_counter = 0;
		$tm_pb_column_backgrounds = array(
			array( $background_color_1, $bg_img_1 ),
			array( $background_color_2, $bg_img_2 ),
			array( $background_color_3, $bg_img_3 ),
			array( $background_color_4, $bg_img_4 ),
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
			array(
				'padding-top'    => $padding_top_4,
				'padding-right'  => $padding_right_4,
				'padding-bottom' => $padding_bottom_4,
				'padding-left'   => $padding_left_4
			),
		);

		$padding_1_tablet = $padding_1_tablet ? $padding_1_tablet : $padding_1_laptop;
		$padding_2_tablet = $padding_2_tablet ? $padding_2_tablet : $padding_2_laptop;
		$padding_3_tablet = $padding_3_tablet ? $padding_3_tablet : $padding_3_laptop;
		$padding_4_tablet = $padding_4_tablet ? $padding_4_tablet : $padding_4_laptop;
		$padding_1_phone  = $padding_1_phone  ? $padding_1_phone  : $padding_1_tablet;
		$padding_2_phone  = $padding_2_phone  ? $padding_2_phone  : $padding_2_tablet;
		$padding_3_phone  = $padding_3_phone  ? $padding_3_phone  : $padding_3_tablet;
		$padding_4_phone  = $padding_4_phone  ? $padding_4_phone  : $padding_4_tablet;

		$tm_pb_column_paddings_mobile = array(
			array(
				'laptop' => explode( '|', $padding_1_laptop ),
				'tablet' => explode( '|', $padding_1_tablet ),
				'phone'  => explode( '|', $padding_1_phone ),
			),
			array(
				'laptop' => explode( '|', $padding_2_laptop ),
				'tablet' => explode( '|', $padding_2_tablet ),
				'phone'  => explode( '|', $padding_2_phone ),
			),
			array(
				'laptop' => explode( '|', $padding_3_laptop ),
				'tablet' => explode( '|', $padding_3_tablet ),
				'phone'  => explode( '|', $padding_3_phone ),
			),
			array(
				'laptop' => explode( '|', $padding_4_laptop ),
				'tablet' => explode( '|', $padding_4_tablet ),
				'phone'  => explode( '|', $padding_4_phone ),
			),
		);

		$tm_pb_column_parallax = array(
			array( $parallax_1, $parallax_method_1 ),
			array( $parallax_2, $parallax_method_2 ),
			array( $parallax_3, $parallax_method_3 ),
			array( $parallax_4, $parallax_method_4 ),
		);

		$tm_pb_column_css = array(
			'css_class'         => array( $module_class_1, $module_class_2, $module_class_3, $module_class_4 ),
			'css_id'            => array( $module_id_1, $module_id_2, $module_id_3, $module_id_4 ),
			'custom_css_before' => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3, $custom_css_before_4 ),
			'custom_css_main'   => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3, $custom_css_main_4 ),
			'custom_css_after'  => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3, $custom_css_after_4 ),
		);

		$background_video = '';

		$module_class .= ' row tm_pb_row';

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		$module_class .= 'on' === $make_equal ? ' tm_pb_equal_columns' : '';

		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of Rows support only top and bottom padding, so we need to handle it along with the full padding in the recent version
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

		if ( ! empty( $padding_mobile_values['laptop'] ) || ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'laptop', 'tablet', 'phone' ) as $device ) {
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

		if ( 'on' === $make_fullwidth && 'off' === $use_custom_width ) {
			$module_class .= ' tm_pb_row_fullwidth';
		}

		if ( 'off' === $use_grid_padding ) {
			$module_class .= ' tm_pb_col_padding_reset';
		}

		if ( 'on' === $use_custom_width ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'max-width:%1$s !important;',
					'on' === $width_unit ? esc_attr( $custom_width_px ) : esc_attr( $custom_width_percent )
				),
			) );
		}

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


		if ( '' !== $background_color ) { TM_Builder_Element::set_style( $function_name, array(
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

		$inner_content = do_shortcode( tm_pb_fix_shortcodes( $content ) );
		$module_class .= '' == trim( $inner_content ) ? ' tm_pb_row_empty' : '';

		$output = sprintf(
			'%9$s
				<div%4$s class="%2$s%6$s%7$s">
					%8$s
					%1$s
						%5$s
				</div> <!-- .%3$s -->
			%10$s',
			$inner_content,
			esc_attr( $module_class ),
			esc_html( $function_name ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			$background_video,
			( '' !== $background_video ? ' tm_pb_section_video tm_pb_preload' : '' ),
			( 'on' === $parallax ? ' tm_pb_section_parallax' : '' ),
			( '' !== $background_image && 'on' === $parallax
				? sprintf(
					'<div class="tm_parallax_bg%2$s" style="background-image: url(%1$s);"></div>',
					esc_attr( $background_image ),
					( 'off' === $parallax_method ? ' tm_pb_parallax_css' : '' )
				)
				: ''
			),
			( 'off' === $make_fullwidth ? '<div class="container">' : '' ),
			( 'off' === $make_fullwidth ? '</div>' : '' )
		);

		return $output;
	}
}
new Tm_Builder_Row;
