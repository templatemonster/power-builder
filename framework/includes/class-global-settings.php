<?php

class Tm_Global_Settings {
	private static $_settings = array();

	public static function init() {
		// The class can only be initialized once
		if ( ! empty( self::$_settings ) ) {
			return;
		}

		self::set_values();
	}

	private static function set_values() {
		$font_defaults = array(
			'size'           => '14',
			'color'          => '#666666',
			'letter_spacing' => '0px',
			'line_height'    => '1.7em',
		);

		$defaults = array(
			'tm_pb_image-animation'                           => 'left',
			'tm_pb_gallery-hover_overlay_color'               => 'rgba(255,255,255,0.9)',
			'tm_pb_gallery-title_font_size'                   => '16',
			'tm_pb_gallery-title_color'                       => '#333333',
			'tm_pb_gallery-title_letter_spacing'              => $font_defaults['letter_spacing'],
			'tm_pb_gallery-title_line_height'                 => '1em',
			'tm_pb_gallery-title_font_style'                  => '',
			'tm_pb_gallery-caption_font_size'                 => '14',
			'tm_pb_gallery-caption_font_style'                => '',
			'tm_pb_gallery-caption_color'                     => '#f3f3f3',
			'tm_pb_gallery-caption_line_height'               => '18px',
			'tm_pb_gallery-caption_letter_spacing'            => $font_defaults['letter_spacing'],

			'tm_pb_tabs-tab_font_size'                        => $font_defaults['size'],
			'tm_pb_tabs-tab_line_height'                      => $font_defaults['line_height'],
			'tm_pb_tabs-tab_letter_spacing'                   => $font_defaults['letter_spacing'],
			'tm_pb_tabs-title_font_size'                      => $font_defaults['size'],
			'tm_pb_tabs-body_font_size'                       => $font_defaults['size'],
			'tm_pb_tabs-body_line_height'                     => $font_defaults['line_height'],
			'tm_pb_tabs-body_letter_spacing'                  => $font_defaults['letter_spacing'],
			'tm_pb_tabs-title_font_style'                     => '',
			'tm_pb_tabs-padding'                              => '30',

			'tm_pb_slider-header_font_size'                   => '46',
			'tm_pb_slider-header_line_height'                 => '1em',
			'tm_pb_slider-header_letter_spacing'              => $font_defaults['letter_spacing'],
			'tm_pb_slider-header_font_style'                  => '',
			'tm_pb_slider-body_font_size'                     => '16',
			'tm_pb_slider-body_letter_spacing'                => $font_defaults['letter_spacing'],
			'tm_pb_slider-body_line_height'                   => $font_defaults['line_height'],
			'tm_pb_slider-body_font_style'                    => '',
			'tm_pb_slider-padding'                            => '16',
			'tm_pb_slider-header_color'                       => '#ffffff',
			'tm_pb_slider-header_line_height'                 => '1em',
			'tm_pb_slider-body_color'                         => '#ffffff',

			'tm_pb_testimonial-portrait_border_radius'        => '90',
			'tm_pb_testimonial-portrait_width'                => '90',
			'tm_pb_testimonial-portrait_height'               => '90',
			'tm_pb_testimonial-author_name_font_style'        => 'bold',
			'tm_pb_testimonial-author_details_font_style'     => 'bold',
			'tm_pb_testimonial-border_color'                  => '#ffffff',
			'tm_pb_testimonial-border_width'                  => '1px',
			'tm_pb_testimonial-body_font_size'                => $font_defaults['size'],
			'tm_pb_testimonial-body_line_height'              => '1.5em',
			'tm_pb_testimonial-body_letter_spacing'           => $font_defaults['letter_spacing'],

			'tm_pb_pricing_tables-header_font_size'           => '22',
			'tm_pb_pricing_tables-header_font_style'          => '',
			'tm_pb_pricing_tables-subheader_font_size'        => '16',
			'tm_pb_pricing_tables-subheader_font_style'       => '',
			'tm_pb_pricing_tables-price_font_size'            => '80',
			'tm_pb_pricing_tables-price_font_style'           => '',
			'tm_pb_pricing_tables-header_color'               => '#ffffff',
			'tm_pb_pricing_tables-header_line_height'         => '1em',
			'tm_pb_pricing_tables-subheader_color'            => '#ffffff',
			'tm_pb_pricing_tables-currency_frequency_font_size' => '16px',
			'tm_pb_pricing_tables-currency_frequency_letter_spacing' => '0px',
			'tm_pb_pricing_tables-currency_frequency_line_height' => '1.7em',
			'tm_pb_pricing_tables-price_letter_spacing'       => '0px',
			'tm_pb_pricing_tables-price_color'                => '#2EA3F2',
			'tm_pb_pricing_tables-price_line_height'          => '82px',
			'tm_pb_pricing_tables-body_line_height'           => '24px',

			'tm_pb_fullwidth_post_title-title_font_size'      => '26px',
			'tm_pb_fullwidth_post_title-title_line_height'    => '1em',
			'tm_pb_fullwidth_post_title-title_letter_spacing' => $font_defaults['letter_spacing'],
			'tm_pb_fullwidth_post_title-meta_font_size'       => $font_defaults['size'],
			'tm_pb_fullwidth_post_title-meta_line_height'     => '1em',
			'tm_pb_fullwidth_post_title-meta_letter_spacing'  => $font_defaults['letter_spacing'],
			'tm_pb_fullwidth_post_title-module_bg_color'      => 'rgba(255,255,255,0)',
			'tm_pb_fullwidth_header-scroll_down_icon_size'    => '50px',
			'tm_pb_fullwidth_header-subhead_font_size'        => '18px',
			'tm_pb_fullwidth_header-button_one_font_size'     => '20px',
			'tm_pb_fullwidth_header-button_one_border_radius' => '3px',
			'tm_pb_fullwidth_header-button_two_font_size'     => '20px',
			'tm_pb_fullwidth_header-button_two_border_radius' => '3px',
			'tm_pb_post_title-title_font_size'                => '26px',
			'tm_pb_post_title-title_line_height'              => '1em',
			'tm_pb_post_title-title_letter_spacing'           => $font_defaults['letter_spacing'],
			'tm_pb_post_title-meta_font_size'                 => $font_defaults['size'],
			'tm_pb_post_title-meta_line_height'               => '1em',
			'tm_pb_post_title-meta_letter_spacing'            => $font_defaults['letter_spacing'],
			'tm_pb_post_title-module_bg_color'                => 'rgba(255,255,255,0)',
			'tm_pb_cta-header_font_size'                      => '26',
			'tm_pb_cta-header_font_style'                     => '',
			'tm_pb_cta-custom_padding'                        => '40',
			'tm_pb_cta-header_text_color'                     => '#333333',
			'tm_pb_cta-header_line_height'                    => '1em',
			'tm_pb_cta-header_letter_spacing'                 => $font_defaults['letter_spacing'],
			'tm_pb_cta-body_font_size'                        => $font_defaults['size'],
			'tm_pb_cta-body_line_height'                      => $font_defaults['line_height'],
			'tm_pb_cta-body_letter_spacing'                   => $font_defaults['letter_spacing'],

			'tm_pb_blurb-header_font_size'                    => '18',
			'tm_pb_blurb-header_color'                        => '#333333',
			'tm_pb_blurb-header_letter_spacing'               => $font_defaults['letter_spacing'],
			'tm_pb_blurb-header_line_height'                  => '1em',
			'tm_pb_blurb-body_font_size'                      => $font_defaults['size'],
			'tm_pb_blurb-body_color'                          => '#666666',
			'tm_pb_blurb-body_letter_spacing'                 => $font_defaults['letter_spacing'],
			'tm_pb_blurb-body_line_height'                    => $font_defaults['line_height'],

			'tm_pb_text-text_font_size'                       => $font_defaults['size'],
			'tm_pb_text-text_letter_spacing'                  => $font_defaults['letter_spacing'],
			'tm_pb_text-text_line_height'                     => $font_defaults['line_height'],
			'tm_pb_text-border_color'                         => '#ffffff',
			'tm_pb_text-border_width'                         => '1px',

			'tm_pb_slide-header_font_size'                    => '26px',
			'tm_pb_slide-header_color'                        => '#ffffff',
			'tm_pb_slide-header_line_height'                  => '1em',
			'tm_pb_slide-body_font_size'                      => '16px',
			'tm_pb_slide-body_color'                          => '#ffffff',
			'tm_pb_pricing_table-header_font_size'            => '22px',
			'tm_pb_pricing_table-header_color'                => '#ffffff',
			'tm_pb_pricing_table-header_line_height'          => '1em',
			'tm_pb_pricing_table-subheader_font_size'         => '16px',
			'tm_pb_pricing_table-subheader_color'             => '#ffffff',
			'tm_pb_pricing_table-price_font_size'             => '80px',
			'tm_pb_pricing_table-price_color'                 => '#2EA3F2',
			'tm_pb_pricing_table-price_line_height'           => '82px',
			'tm_pb_pricing_table-body_line_height'            => '24px',
			'tm_pb_audio-title_font_size'                     => '26',
			'tm_pb_audio-title_letter_spacing'                => $font_defaults['letter_spacing'],
			'tm_pb_audio-title_line_height'                   => $font_defaults['line_height'],
			'tm_pb_audio-title_font_style'                    => '',
			'tm_pb_audio-caption_font_size'                   => $font_defaults['size'],
			'tm_pb_audio-caption_letter_spacing'              => $font_defaults['letter_spacing'],
			'tm_pb_audio-caption_line_height'                 => $font_defaults['line_height'],
			'tm_pb_audio-caption_font_style'                  => '',
			'tm_pb_audio-title_text_color'                    => '#666666',
			'tm_pb_signup-header_font_size'                   => '26',
			'tm_pb_signup-header_letter_spacing'              => $font_defaults['letter_spacing'],
			'tm_pb_signup-header_line_height'                 => $font_defaults['line_height'],
			'tm_pb_signup-body_font_size'                     => $font_defaults['size'],
			'tm_pb_signup-body_letter_spacing'                => $font_defaults['letter_spacing'],
			'tm_pb_signup-body_line_height'                   => $font_defaults['line_height'],
			'tm_pb_signup-header_font_style'                  => '',
			'tm_pb_signup-padding'                            => '20',
			'tm_pb_signup-focus_border_color'                 => '#ffffff',
			'tm_pb_login-header_font_size'                    => '26',
			'tm_pb_login-header_letter_spacing'               => $font_defaults['letter_spacing'],
			'tm_pb_login-header_line_height'                  => $font_defaults['line_height'],
			'tm_pb_login-body_font_size'                      => $font_defaults['size'],
			'tm_pb_login-body_letter_spacing'                 => $font_defaults['letter_spacing'],
			'tm_pb_login-body_line_height'                    => $font_defaults['line_height'],
			'tm_pb_login-header_font_style'                   => '',
			'tm_pb_login-custom_padding'                      => '40',
			'tm_pb_login-focus_border_color'                  => '#ffffff',
			'tm_pb_portfolio-hover_overlay_color'             => 'rgba(255,255,255,0.9)',
			'tm_pb_portfolio-title_font_size'                 => '18',
			'tm_pb_portfolio-title_letter_spacing'            => $font_defaults['letter_spacing'],
			'tm_pb_portfolio-title_line_height'               => $font_defaults['line_height'],
			'tm_pb_portfolio-title_font_style'                => '',
			'tm_pb_portfolio-caption_font_size'               => '14',
			'tm_pb_portfolio-caption_letter_spacing'          => $font_defaults['letter_spacing'],
			'tm_pb_portfolio-caption_line_height'             => $font_defaults['line_height'],
			'tm_pb_portfolio-caption_font_style'              => '',
			'tm_pb_portfolio-title_color'                     => '#333333',
			'tm_pb_filterable_portfolio-hover_overlay_color'  => 'rgba(255,255,255,0.9)',
			'tm_pb_filterable_portfolio-title_font_size'      => '18',
			'tm_pb_filterable_portfolio-title_letter_spacing' => $font_defaults['letter_spacing'],
			'tm_pb_filterable_portfolio-title_line_height'    => $font_defaults['line_height'],
			'tm_pb_filterable_portfolio-title_font_style'     => '',
			'tm_pb_filterable_portfolio-caption_font_size'    => '14',
			'tm_pb_filterable_portfolio-caption_letter_spacing'=> $font_defaults['letter_spacing'],
			'tm_pb_filterable_portfolio-caption_line_height'  => $font_defaults['line_height'],
			'tm_pb_filterable_portfolio-caption_font_style'   => '',
			'tm_pb_filterable_portfolio-filter_font_size'     => '14',
			'tm_pb_filterable_portfolio-filter_letter_spacing'=> $font_defaults['letter_spacing'],
			'tm_pb_filterable_portfolio-filter_line_height'   => $font_defaults['line_height'],
			'tm_pb_filterable_portfolio-filter_font_style'    => '',
			'tm_pb_filterable_portfolio-title_color'          => '#333333',
			'tm_pb_counters-title_font_size'                  => '12',
			'tm_pb_counters-title_letter_spacing'             => $font_defaults['letter_spacing'],
			'tm_pb_counters-title_line_height'                => $font_defaults['line_height'],
			'tm_pb_counters-title_font_style'                 => '',
			'tm_pb_counters-percent_font_size'                => '12',
			'tm_pb_counters-percent_letter_spacing'           => $font_defaults['letter_spacing'],
			'tm_pb_counters-percent_line_height'              => $font_defaults['line_height'],
			'tm_pb_counters-percent_font_style'               => '',
			'tm_pb_counters-border_radius'                    => '0',
			'tm_pb_counters-padding'                          => '0',
			'tm_pb_counters-title_color'                      => '#999999',
			'tm_pb_counters-percent_color'                    => '#ffffff',
			'tm_pb_circle_counter-title_font_size'            => '16',
			'tm_pb_circle_counter-title_letter_spacing'       => $font_defaults['letter_spacing'],
			'tm_pb_circle_counter-title_line_height'          => '1em',
			'tm_pb_circle_counter-title_font_style'           => '',
			'tm_pb_circle_counter-number_font_size'           => '46',
			'tm_pb_circle_counter-number_font_style'          => '',
			'tm_pb_circle_counter-title_color'                => '#333333',
			'tm_pb_circle_counter-number_line_height'         => '225px',
			'tm_pb_circle_counter-number_letter_spacing'      => $font_defaults['letter_spacing'],
			'tm_pb_number_counter-title_font_size'            => '16',
			'tm_pb_number_counter-title_line_height'          => '1em',
			'tm_pb_number_counter-title_letter_spacing'       => $font_defaults['letter_spacing'],
			'tm_pb_number_counter-title_font_style'           => '',
			'tm_pb_number_counter-number_font_size'           => '72',
			'tm_pb_number_counter-number_line_height'         => '72px',
			'tm_pb_number_counter-number_letter_spacing'      => $font_defaults['letter_spacing'],
			'tm_pb_number_counter-number_font_style'          => '',
			'tm_pb_number_counter-title_color'                => '#333333',
			'tm_pb_accordion-toggle_font_size'                => '16',
			'tm_pb_accordion-toggle_font_style'               => '',
			'tm_pb_accordion-inactive_toggle_font_style'      => '',
			'tm_pb_accordion-toggle_icon_size'                => '16',
			'tm_pb_accordion-custom_padding'                  => '20',
			'tm_pb_accordion-toggle_line_height'              => '1em',
			'tm_pb_accordion-toggle_letter_spacing'           => $font_defaults['letter_spacing'],
			'tm_pb_accordion-body_font_size'                  => $font_defaults['size'],
			'tm_pb_accordion-body_line_height'                => $font_defaults['line_height'],
			'tm_pb_accordion-body_letter_spacing'             => $font_defaults['letter_spacing'],
			'tm_pb_toggle-title_font_size'                    => '16',
			'tm_pb_toggle-title_letter_spacing'               => $font_defaults['letter_spacing'],
			'tm_pb_toggle-title_font_style'                   => '',
			'tm_pb_toggle-inactive_title_font_style'          => '',
			'tm_pb_toggle-toggle_icon_size'                   => '16',
			'tm_pb_toggle-title_color'                        => '#333333',
			'tm_pb_toggle-title_line_height'                  => '1em',
			'tm_pb_toggle-custom_padding'                     => '20',
			'tm_pb_toggle-body_font_size'                     => $font_defaults['size'],
			'tm_pb_toggle-body_line_height'                   => $font_defaults['line_height'],
			'tm_pb_toggle-body_letter_spacing'                => $font_defaults['letter_spacing'],
			'tm_pb_contact_form-title_font_size'              => '26',
			'tm_pb_contact_form-title_font_style'             => '',
			'tm_pb_contact_form-form_field_font_size'         => '14',
			'tm_pb_contact_form-form_field_font_style'        => '',
			'tm_pb_contact_form-captcha_font_size'            => '14',
			'tm_pb_contact_form-captcha_font_style'           => '',
			'tm_pb_contact_form-padding'                      => '16',
			'tm_pb_contact_form-title_color'                  => '#333333',
			'tm_pb_contact_form-title_line_height'            => '1em',
			'tm_pb_contact_form-title_letter_spacing'         => $font_defaults['letter_spacing'],
			'tm_pb_contact_form-form_field_color'             => '#999999',
			'tm_pb_contact_form-form_field_line_height'       => $font_defaults['line_height'],
			'tm_pb_contact_form-form_field_letter_spacing'    => $font_defaults['letter_spacing'],
			'tm_pb_sidebar-header_font_size'                  => '18',
			'tm_pb_sidebar-header_font_style'                 => '',
			'tm_pb_sidebar-header_color'                      => '#333333',
			'tm_pb_sidebar-header_line_height'                => '1em',
			'tm_pb_sidebar-header_letter_spacing'             => $font_defaults['letter_spacing'],
			'tm_pb_sidebar-remove_border'                     => 'off',
			'tm_pb_sidebar-body_font_size'                    => $font_defaults['size'],
			'tm_pb_sidebar-body_line_height'                  => $font_defaults['line_height'],
			'tm_pb_sidebar-body_letter_spacing'               => $font_defaults['letter_spacing'],
			'tm_pb_divider-show_divider'                      => 'off',
			'tm_pb_divider-divider_style'                     => 'none',
			'tm_pb_divider-divider_weight'                    => '1',
			'tm_pb_divider-height'                            => '1',
			'tm_pb_divider-divider_position'                  => 'none',
			'tm_pb_team_member-header_font_size'              => '18',
			'tm_pb_team_member-header_font_style'             => '',
			'tm_pb_team_member-subheader_font_size'           => '14',
			'tm_pb_team_member-subheader_font_style'          => '',
			'tm_pb_team_member-social_network_icon_size'      => '16',
			'tm_pb_team_member-header_color'                  => '#333333',
			'tm_pb_team_member-header_line_height'            => '1em',
			'tm_pb_team_member-header_letter_spacing'         => $font_defaults['letter_spacing'],
			'tm_pb_team_member-body_font_size'                => $font_defaults['size'],
			'tm_pb_team_member-body_line_height'              => $font_defaults['line_height'],
			'tm_pb_team_member-body_letter_spacing'           => $font_defaults['letter_spacing'],
			'tm_pb_shop-title_font_size'                      => '16',
			'tm_pb_shop-title_font_style'                     => '',
			'tm_pb_shop-sale_badge_font_size'                 => '16',
			'tm_pb_shop-sale_badge_font_style'                => '',
			'tm_pb_shop-price_font_size'                      => '14',
			'tm_pb_shop-price_font_style'                     => '',
			'tm_pb_shop-sale_price_font_size'                 => '14',
			'tm_pb_shop-sale_price_font_style'                => '',
			'tm_pb_shop-title_color'                          => '#333333',
			'tm_pb_shop-title_line_height'                    => '1em',
			'tm_pb_shop-title_letter_spacing'                 => $font_defaults['letter_spacing'],
			'tm_pb_shop-price_line_height'                    => '26px',
			'tm_pb_shop-price_letter_spacing'                 => $font_defaults['letter_spacing'],
			'tm_pb_countdown_timer-header_font_size'          => '22',
			'tm_pb_countdown_timer-header_font_style'         => '',
			'tm_pb_countdown_timer-header_color'              => '#333333',
			'tm_pb_countdown_timer-header_line_height'        => '1em',
			'tm_pb_countdown_timer-header_letter_spacing'     => $font_defaults['letter_spacing'],
			'tm_pb_countdown_timer-numbers_font_size'         => '64px',
			'tm_pb_countdown_timer-numbers_line_height'       => '64px',
			'tm_pb_countdown_timer-numbers_letter_spacing'    => $font_defaults['letter_spacing'],
			'tm_pb_countdown_timer-label_line_height'         => '25px',
			'tm_pb_countdown_timer-label_letter_spacing'      => $font_defaults['letter_spacing'],
			'tm_pb_countdown_timer-label_font_size'           => $font_defaults['size'],
			'tm_pb_social_media_follow-icon_size'             => '14',
			'tm_pb_social_media_follow-button_font_style'     => '',
			'tm_pb_fullwidth_slider-header_font_size'         => '46',
			'tm_pb_fullwidth_slider-header_font_style'        => '',
			'tm_pb_fullwidth_slider-body_font_size'           => '16',
			'tm_pb_fullwidth_slider-body_font_style'          => '',
			'tm_pb_fullwidth_slider-body_line_height'         => $font_defaults['line_height'],
			'tm_pb_fullwidth_slider-body_letter_spacing'      => $font_defaults['letter_spacing'],
			'tm_pb_fullwidth_slider-padding'                  => '16',
			'tm_pb_fullwidth_slider-header_color'             => '#ffffff',
			'tm_pb_fullwidth_slider-header_line_height'       => '1em',
			'tm_pb_fullwidth_slider-header_letter_spacing'    => $font_defaults['letter_spacing'],
			'tm_pb_fullwidth_slider-body_color'               => '#ffffff',
			'tm_pb_blog-header_font_size'                     => '18',
			'tm_pb_blog-header_font_style'                    => '',
			'tm_pb_blog-meta_font_size'                       => '14',
			'tm_pb_blog-meta_font_style'                      => '',
			'tm_pb_blog-meta_line_height'                     => $font_defaults['line_height'],
			'tm_pb_blog-meta_letter_spacing'                  => $font_defaults['letter_spacing'],
			'tm_pb_blog-header_color'                         => '#333333',
			'tm_pb_blog-header_line_height'                   => '1em',
			'tm_pb_blog-header_letter_spacing'                => $font_defaults['letter_spacing'],
			'tm_pb_blog-body_font_size'                       => $font_defaults['size'],
			'tm_pb_blog-body_line_height'                     => $font_defaults['line_height'],
			'tm_pb_blog-body_letter_spacing'                  => $font_defaults['letter_spacing'],
			'tm_pb_blog_masonry-header_font_size'             => '26',
			'tm_pb_blog_masonry-header_font_style'            => '',
			'tm_pb_blog_masonry-meta_font_size'               => '14',
			'tm_pb_blog_masonry-meta_font_style'              => '',

			'all_buttons_font_size'                           => '20',
			'all_buttons_border_width'                        => '2',
			'all_buttons_border_radius'                       => '3',
			'all_buttons_spacing'                             => '0',
			'all_buttons_font_style'                          => '',
			'all_buttons_border_radius_hover'                 => '3',
			'all_buttons_spacing_hover'                       => '0',
		);

		if ( ! tm_is_builder_plugin_active() ) {
			$defaults['tm_pb_gallery-zoom_icon_color']              = tm_get_option( 'accent_color', '#2EA3F2' );
			$defaults['tm_pb_portfolio-zoom_icon_color']            = tm_get_option( 'accent_color', '#2EA3F2' );
			$defaults['tm_pb_filterable_portfolio-zoom_icon_color'] = tm_get_option( 'accent_color', '#2EA3F2' );
		}

		// reformat defaults array and add actual values to it
		foreach( $defaults as $setting_name => $default_value ) {
			$defaults[ $setting_name ] = array(
				'default' => $default_value,
			);

			$actual_value = ! tm_is_builder_plugin_active() ? tm_get_option( $setting_name, '', '', true ) : '';
			if ( '' !== $actual_value ) {
				$defaults[ $setting_name ]['actual']  = $actual_value;
			}
		}

		self::$_settings = apply_filters( 'tm_set_default_values', $defaults );
	}

	/**
	 * Get default global setting value
	 * @param  string $name      Setting name
	 * @param  string $get_value Defines the value it should get: actual or default
	 *
	 * @return mixed             Global setting value or FALSE
	 */
	public static function get_value( $name, $get_value = 'actual' ) {
		$settings = self::$_settings;

		if ( ! isset( $settings[ $name ] ) ) {
			return false;
		}

		if ( isset( $settings[ $name ][ $get_value ] ) ) {
			$result = $settings[ $name ][ $get_value ];
		} elseif ( 'actual' === $get_value && isset( $settings[ $name ][ 'default' ] ) ) {
			$result = $settings[ $name ][ 'default' ];
		} else {
			$result = false;
		}

		return $result;
	}

	/**
	 * Translate 'on'/'off' into true/false
	 * Pagebuilder use pseudo checkbox with 'on'/'off' value while customizer use true/false
	 * which cause TM_Global_Settings' default value incompatibilities.
	 */
	public static function get_checkbox_value( $name, $get_value = 'actual', $source = 'pagebuilder' ) {
		// Get value
		$value = self::get_value( $name, $get_value );

		// customizer to pagebuilder || pagebuilder to customizer
		if ( 'customizer' === $source ) {
			if ( false === $value ) {
				return 'off';
			} else {
				return 'on';
			}
		} else {
			if ( 'off' === $value || false === $value ) {
				return false;
			} else {
				return true;
			}
		}
	}
}

function tm_builder_init_global_settings() { TM_Global_Settings::init();
}