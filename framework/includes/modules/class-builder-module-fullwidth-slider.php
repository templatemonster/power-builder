<?php
class Tm_Builder_Module_Fullwidth_Slider extends Tm_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Fullwidth Slider', 'tm_builder' );
		$this->slug            = 'tm_pb_fullwidth_slider';
		$this->fullwidth       = true;
		$this->child_slug      = 'tm_pb_slide';
		$this->child_item_text = esc_html__( 'Slide', 'tm_builder' );

		$this->whitelisted_fields = array(
			'show_arrows',
			'show_pagination',
			'auto',
			'auto_speed',
			'auto_ignore_hover',
			'parallax',
			'parallax_method',
			'remove_inner_shadow',
			'background_position',
			'background_size',
			'admin_label',
			'module_id',
			'module_class',
			'top_padding',
			'bottom_padding',
			'hide_content_on_mobile',
			'hide_cta_on_mobile',
			'show_image_video_mobile',
			'bottom_padding_tablet',
			'bottom_padding_phone',
			'top_padding_tablet',
			'top_padding_phone',

		);

		$this->fields_defaults = array(
			'show_arrows'             => array( 'on' ),
			'show_pagination'         => array( 'on' ),
			'auto'                    => array( 'off' ),
			'auto_speed'              => array( '7000' ),
			'auto_ignore_hover'       => array( 'off' ),
			'parallax'                => array( 'off' ),
			'parallax_method'         => array( 'off' ),
			'remove_inner_shadow'     => array( 'off' ),
			'background_position'     => array( 'default' ),
			'background_size'         => array( 'default' ),
			'hide_content_on_mobile'  => array( 'off' ),
			'hide_cta_on_mobile'      => array( 'off' ),
			'show_image_video_mobile' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_slider';
		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tm_pb_slide_description .tm_pb_slide_title",
						'important' => array(
							'color',
						),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'tm_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element} .tm_pb_slide_content",
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'tm_builder' ),
				),
			),
		);
		$this->custom_css_options = array(
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'tm_builder' ),
				'selector' => '.tm_pb_slide_description',
			),
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'tm_builder' ),
				'selector' => '.tm_pb_slide_description .tm_pb_slide_title',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'tm_builder' ),
				'selector' => 'a.tm_pb_more_button',
			),
			'slide_controllers' => array(
				'label'    => esc_html__( 'Slide Controllers', 'tm_builder' ),
				'selector' => '.et-pb-controllers',
			),
			'slide_active_controller' => array(
				'label'    => esc_html__( 'Slide Active Controller', 'tm_builder' ),
				'selector' => '.et-pb-controllers .et-pb-active-control',
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'tm_builder' ),
				'selector' => '.tm_pb_slide_image',
			),
			'slide_arrows' => array(
				'label'    => esc_html__( 'Slide Arrows', 'tm_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_arrows' => array(
				'label'           => esc_html__( 'Arrows', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Show Arrows', 'tm_builder' ),
					'off' => esc_html__( 'Hide Arrows', 'tm_builder' ),
				),
				'description'        => esc_html__( 'This setting allows you to turn the navigation arrows on or off.', 'tm_builder' ),
			),
			'show_pagination' => array(
				'label'           => esc_html__( 'Controls', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Show Slider Controls', 'tm_builder' ),
					'off' => esc_html__( 'Hide Slider Controls', 'tm_builder' ),
				),
				'description'        => esc_html__( 'Disabling this option will remove the circle button at the bottom of the slider.', 'tm_builder' ),
			),
			'auto' => array(
				'label'             => esc_html__( 'Automatic Animation', 'tm_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off'  => esc_html__( 'Off', 'tm_builder' ),
					'on' => esc_html__( 'On', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_auto_speed, #tm_pb_auto_ignore_hover',
				),
				'description'        => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'tm_builder' ),
			),
			'auto_speed' => array(
				'label'             => esc_html__( 'Automatic Animation Speed (in ms)', 'tm_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_default'   => true,
				'description'       => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'tm_builder' ),
			),
			'auto_ignore_hover' => array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options' => array(
					'off' => esc_html__( 'Off', 'tm_builder' ),
					'on'  => esc_html__( 'On', 'tm_builder' ),
				),
				'description' => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'tm_builder' ),
			),
			'parallax' => array(
				'label'           => esc_html__( 'Use Parallax effect', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off'  => esc_html__( 'No', 'tm_builder' ),
					'on' => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects'           => array(
					'#tm_pb_parallax_method',
				),
				'description'        => esc_html__( 'If enabled, your background images will have a fixed position as your scroll, creating a fun parallax-like effect.', 'tm_builder' ),
			),
			'parallax_method' => array(
				'label'           => esc_html__( 'Parallax method', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'CSS', 'tm_builder' ),
					'on'  => esc_html__( 'True Parallax', 'tm_builder' ),
				),
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Define the method, used for the parallax effect.', 'tm_builder' ),
			),
			'remove_inner_shadow' => array(
				'label'           => esc_html__( 'Remove Inner Shadow', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
			),
			'background_position' => array(
				'label'           => esc_html__( 'Background Image Position', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'default'       => esc_html__( 'Default', 'tm_builder' ),
					'top_left'      => esc_html__( 'Top Left', 'tm_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'tm_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'tm_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'tm_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'tm_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'tm_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'tm_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'tm_builder' ),
				),
				'depends_show_if'   => 'off',
			),
			'background_size' => array(
				'label'           => esc_html__( 'Background Image Size', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'default' => esc_html__( 'Default', 'tm_builder' ),
					'contain' => esc_html__( 'Fit', 'tm_builder' ),
					'initial' => esc_html__( 'Actual Size', 'tm_builder' ),
				),
				'depends_show_if'   => 'off',
			),
			'top_padding' => array(
				'label'           => esc_html__( 'Top Padding', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'bottom_padding' => array(
				'label'           => esc_html__( 'Bottom Padding', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'top_padding_tablet' => array(
				'type' => 'skip',
			),
			'top_padding_phone' => array(
				'type' => 'skip',
			),
			'bottom_padding_tablet' => array(
				'type' => 'skip',
			),
			'bottom_padding_phone' => array(
				'type' => 'skip',
			),
			'hide_content_on_mobile' => array(
				'label'           => esc_html__( 'Hide Content On Mobile', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'          => 'advanced',
			),
			'hide_cta_on_mobile' => array(
				'label'           => esc_html__( 'Hide CTA On Mobile', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'          => 'advanced',
			),
			'show_image_video_mobile' => array(
				'label'            => esc_html__( 'Show Image / Video On Mobile', 'tm_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'tab_slug'          => 'advanced',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'tm_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'tm_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'tm_builder' ),
					'desktop' => esc_html__( 'Desktop', 'tm_builder' ),
				),
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
		global $tm_pb_slider_has_video, $tm_pb_slider_parallax, $tm_pb_slider_parallax_method, $tm_pb_slider_hide_mobile, $tm_pb_slider_custom_icon, $tm_pb_slider_item_num;

		$tm_pb_slider_item_num = 0;

		$parallax        = $this->shortcode_atts['parallax'];
		$parallax_method = $this->shortcode_atts['parallax_method'];
		$hide_content_on_mobile  = $this->shortcode_atts['hide_content_on_mobile'];
		$hide_cta_on_mobile      = $this->shortcode_atts['hide_cta_on_mobile'];
		$button_custom           = $this->shortcode_atts['custom_button'];
		$custom_icon             = $this->shortcode_atts['button_icon'];

		$tm_pb_slider_has_video = false;

		$tm_pb_slider_parallax = $parallax;

		$tm_pb_slider_parallax_method = $parallax_method;

		$tm_pb_slider_hide_mobile = array(
			'hide_content_on_mobile'  => $hide_content_on_mobile,
			'hide_cta_on_mobile'      => $hide_cta_on_mobile,
		);

		$tm_pb_slider_custom_icon = 'on' === $button_custom ? $custom_icon : '';

	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$show_arrows             = $this->shortcode_atts['show_arrows'];
		$show_pagination         = $this->shortcode_atts['show_pagination'];
		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$auto                    = $this->shortcode_atts['auto'];
		$auto_speed              = $this->shortcode_atts['auto_speed'];
		$auto_ignore_hover       = $this->shortcode_atts['auto_ignore_hover'];
		$top_padding             = $this->shortcode_atts['top_padding'];
		$bottom_padding          = $this->shortcode_atts['bottom_padding'];
		$top_padding_tablet      = $this->shortcode_atts['top_padding_tablet'];
		$bottom_padding_tablet   = $this->shortcode_atts['bottom_padding_tablet'];
		$top_padding_phone       = $this->shortcode_atts['top_padding_phone'];
		$bottom_padding_phone    = $this->shortcode_atts['bottom_padding_phone'];
		$remove_inner_shadow     = $this->shortcode_atts['remove_inner_shadow'];
		$show_image_video_mobile = $this->shortcode_atts['show_image_video_mobile'];
		$background_position     = $this->shortcode_atts['background_position'];
		$background_size         = $this->shortcode_atts['background_size'];

		global $tm_pb_slider_has_video, $tm_pb_slider_parallax, $tm_pb_slider_parallax_method, $tm_pb_slider_hide_mobile, $tm_pb_slider_custom_icon;

		$content = $this->shortcode_content;

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $top_padding || '' !== $top_padding_tablet || '' !== $top_padding_phone ) {
			$padding_values = array(
				'desktop' => $top_padding,
				'tablet'  => $top_padding_tablet,
				'phone'   => $top_padding_phone,
			);

			tm_pb_generate_responsive_css( $padding_values, '%%order_class%% .tm_pb_slide_description', 'padding-top', $function_name );
		}

		if ( '' !== $bottom_padding || '' !== $bottom_padding_tablet || '' !== $bottom_padding_phone ) {
			$padding_values = array(
				'desktop' => $bottom_padding,
				'tablet'  => $bottom_padding_tablet,
				'phone'   => $bottom_padding_phone,
			);

			tm_pb_generate_responsive_css( $padding_values, '%%order_class%% .tm_pb_slide_description', 'padding-bottom', $function_name );
		}

		if ( 'default' !== $background_position && 'off' === $parallax ) {
			$processed_position = str_replace( '_', ' ', $background_position ); TM_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_slide',
				'declaration' => sprintf(
					'background-position: %1$s;',
					esc_html( $processed_position )
				),
			) );
		}

		if ( 'default' !== $background_size && 'off' === $parallax ) { TM_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_slide',
				'declaration' => sprintf(
					'-moz-background-size: %1$s;
					-webkit-background-size: %1$s;
					background-size: %1$s;',
					esc_html( $background_size )
				),
			) );
		}

		$fullwidth = 'tm_pb_fullwidth_slider' === $function_name ? 'on' : 'off';

		$class  = '';
		$class .= 'off' === $fullwidth ? ' tm_pb_slider_fullwidth_off' : '';
		$class .= 'off' === $show_arrows ? ' tm_pb_slider_no_arrows' : '';
		$class .= 'off' === $show_pagination ? ' tm_pb_slider_no_pagination' : '';
		$class .= 'on' === $parallax ? ' tm_pb_slider_parallax' : '';
		$class .= 'on' === $auto ? ' tm_slider_auto tm_slider_speed_' . esc_attr( $auto_speed ) : '';
		$class .= 'on' === $auto_ignore_hover ? ' tm_slider_auto_ignore_hover' : '';
		$class .= 'on' === $remove_inner_shadow ? ' tm_pb_slider_no_shadow' : '';
		$class .= 'on' === $show_image_video_mobile ? ' tm_pb_slider_show_image' : '';

		$output = sprintf(
			'<div%4$s class="tm_pb_module tm_pb_slider%1$s%3$s%5$s">
				<div class="tm_pb_slides">
					%2$s
				</div> <!-- .tm_pb_slides -->
			</div> <!-- .tm_pb_slider -->
			',
			$class,
			$content,
			( $tm_pb_slider_has_video ? ' tm_pb_preload' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new Tm_Builder_Module_Fullwidth_Slider;

