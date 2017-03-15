<?php
class Tm_Builder_Module_Fullwidth_Menu extends Tm_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Menu', 'power-builder' );
		$this->slug       = 'tm_pb_fullwidth_menu';
		$this->fullwidth  = true;

		$this->whitelisted_fields = array(
			'menu_id',
			'background_color',
			'background_layout',
			'text_orientation',
			'submenu_direction',
			'admin_label',
			'module_id',
			'module_class',
			'fullwidth_menu',
			'active_link_color',
			'dropdown_menu_bg_color',
			'dropdown_menu_line_color',
			'dropdown_menu_text_color',
			'dropdown_menu_animation',
			'mobile_menu_bg_color',
			'mobile_menu_text_color',
		);

		$this->main_css_element = '%%order_class%%.tm_pb_fullwidth_menu';

		$this->advanced_options = array(
			'fonts' => array(
				'menu' => array(
					'label'    => esc_html__( 'Menu', 'power-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} ul li a",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
						'range_settings' => array(
							'min'  => '12',
							'max'  => '24',
							'step' => '1',
						),
					),
					'letter_spacing' => array(
						'default' => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '8',
							'step' => '1',
						),
					),
				),
			),
		);

		$this->custom_css_options = array(
			'menu_link' => array(
				'label'    => esc_html__( 'Menu Link', 'power-builder' ),
				'selector' => '.fullwidth-menu-nav li a',
			),
			'active_menu_link' => array(
				'label'    => esc_html__( 'Active Menu Link', 'power-builder' ),
				'selector' => '.fullwidth-menu-nav li.current-menu-item a',
			),
			'dropdown_container' => array(
				'label'    => esc_html__( 'Dropdown Menu Container', 'power-builder' ),
				'selector' => '.fullwidth-menu-nav li ul.sub-menu',
			),
			'dropdown_links' => array(
				'label'    => esc_html__( 'Dropdown Menu Links', 'power-builder' ),
				'selector' => '.fullwidth-menu-nav li ul.sub-menu a',
			),
		);

		$this->fields_defaults = array(
			'background_color'  => array( '#ffffff', 'only_default_setting' ),
			'background_layout' => array( 'light' ),
			'text_orientation'  => array( 'left' ),
		);
	}

	function get_fields() {
		$fields = array(
			'menu_id' => array(
				'label'           => esc_html__( 'Menu', 'power-builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => tm_builder_get_nav_menus_options(),
				'description'     => sprintf(
					'<p class="description">%2$s. <a href="%1$s" target="_blank">%3$s</a>.</p>',
					esc_url( admin_url( 'nav-menus.php' ) ),
					esc_html__( 'Select a menu that should be used in the module', 'power-builder' ),
					esc_html__( 'Click here to create new menu', 'power-builder' )
				),
			),
			'background_color' => array(
				'label'       => esc_html__( 'Background Color', 'power-builder' ),
				'type'        => 'color-alpha',
				'description' => esc_html__( 'Use the color picker to choose a background color for this module.', 'power-builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'power-builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'power-builder' ),
					'dark'  => esc_html__( 'Light', 'power-builder' ),
				),
				'description' => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'power-builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'power-builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => tm_builder_get_text_orientation_options(),
				'description'       => esc_html__( 'This controls the how your text is aligned within the module.', 'power-builder' ),
			),
			'submenu_direction' => array(
				'label'           => esc_html__( 'Sub-Menus Open', 'power-builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'downwards' => esc_html__( 'Downwards', 'power-builder' ),
					'upwards'   => esc_html__( 'Upwards', 'power-builder' ),
				),
				'description' => esc_html__( 'Here you can choose the direction that your sub-menus will open. You can choose to have them open downwards or upwards.', 'power-builder' ),
			),
			'fullwidth_menu' => array(
				'label'           => esc_html__( 'Make Menu Links Fullwidth', 'power-builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'power-builder' ),
					'on'  => esc_html__( 'Yes', 'power-builder' ),
				),
				'tab_slug'          => 'advanced',
			),
			'active_link_color' => array(
				'label'        => esc_html__( 'Active Link Color', 'power-builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'dropdown_menu_bg_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Background Color', 'power-builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'dropdown_menu_line_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Line Color', 'power-builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'dropdown_menu_text_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Text Color', 'power-builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'dropdown_menu_animation' => array(
				'label'             => esc_html__( 'Dropdown Menu Animation', 'power-builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'fade'     => esc_html__( 'Fade', 'power-builder' ),
					'expand'   => esc_html__( 'Expand', 'power-builder' ),
					'slide'	   => esc_html__( 'Slide', 'power-builder' ),
					'flip'	   => esc_html__( 'Flip', 'power-builder' ),
				),
				'tab_slug'     => 'advanced',
			),
			'mobile_menu_bg_color' => array(
				'label'        => esc_html__( 'Mobile Menu Background Color', 'power-builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'mobile_menu_text_color' => array(
				'label'        => esc_html__( 'Mobile Menu Text Color', 'power-builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'power-builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'power-builder' ),
					'tablet'  => esc_html__( 'Tablet', 'power-builder' ),
					'desktop' => esc_html__( 'Desktop', 'power-builder' ),
				),
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
		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$background_color  = $this->shortcode_atts['background_color'];
		$background_layout = $this->shortcode_atts['background_layout'];
		$text_orientation  = $this->shortcode_atts['text_orientation'];
		$menu_id           = $this->shortcode_atts['menu_id'];
		$submenu_direction = $this->shortcode_atts['submenu_direction'];
		$fullwidth_menu           = $this->shortcode_atts['fullwidth_menu'] === 'on' ? ' tm_pb_fullwidth_menu_fullwidth' : '';
		$active_link_color        = $this->shortcode_atts['active_link_color'];
		$dropdown_menu_bg_color   = $this->shortcode_atts['dropdown_menu_bg_color'];
		$dropdown_menu_line_color = $this->shortcode_atts['dropdown_menu_line_color'];
		$dropdown_menu_text_color = $this->shortcode_atts['dropdown_menu_text_color'];
		$dropdown_menu_animation  = $this->shortcode_atts['dropdown_menu_animation'];
		$mobile_menu_bg_color     = $this->shortcode_atts['mobile_menu_bg_color'];
		$mobile_menu_text_color   = $this->shortcode_atts['mobile_menu_text_color'];

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$style = '';

		if ( '' !== $background_color ) {
			$style .= sprintf( ' style="background-color: %s;"',
				esc_attr( $background_color )
			);
		}

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		$class = " tm_pb_module tm_pb_bg_layout_{$background_layout} tm_pb_text_align_{$text_orientation} tm_dropdown_animation_{$dropdown_menu_animation}{$fullwidth_menu}";

		$menu = '<nav class="fullwidth-menu-nav">';
		$menuClass = 'fullwidth-menu nav';
		if ( ! tm_is_builder_plugin_active() && 'on' == tm_get_option( 'divi_disable_toptier' ) ) {
			$menuClass .= ' tm_disable_top_tier';
		}
		$menuClass .= ( '' !== $submenu_direction ? sprintf( ' %s', esc_attr( $submenu_direction ) ) : '' );

		$primaryNav = '';

		$menu_args = array(
			'theme_location' => 'primary-menu',
			'container'      => '',
			'fallback_cb'    => '',
			'menu_class'     => $menuClass,
			'menu_id'        => '',
			'echo'           => false,
		);

		if ( '' !== $menu_id ) {
			$menu_args['menu'] = (int) $menu_id;
		}

		$primaryNav = wp_nav_menu( apply_filters( 'tm_fullwidth_menu_args', $menu_args ) );

		if ( '' == $primaryNav ) {
			$menu .= sprintf(
				'<ul class="%1$s">
					%2$s',
				esc_attr( $menuClass ),
				( ! tm_is_builder_plugin_active() && 'on' === tm_get_option( 'divi_home_link' )
					? sprintf( '<li%1$s><a href="%2$s">%3$s</a></li>',
						( is_home() ? ' class="current_page_item"' : '' ),
						esc_url( home_url( '/' ) ),
						esc_html__( 'Home', 'power-builder' )
					)
					: ''
				)
			);

			ob_start();

			// @todo: check if Fullwidth Menu module works fine with no menu selected in settings
			if ( tm_is_builder_plugin_active() ) {
				wp_page_menu();
			} else {
				show_page_menu( $menuClass, false, false );
				show_categories_menu( $menuClass, false );
			}

			$menu .= ob_get_contents();

			$menu .= '</ul>';

			ob_end_clean();
		} else {
			$menu .= $primaryNav;
		}

		$menu .= '</nav>';

		if ( '' !== $active_link_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_menu ul li a:active',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $active_link_color )
				),
			) );
		}

		if ( '' !== $dropdown_menu_bg_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_menu .nav li ul',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $dropdown_menu_bg_color )
				),
			) );
		}

		if ( '' !== $dropdown_menu_line_color ) {

			$dropdown_menu_line_color_selector = 'upwards' === $submenu_direction ? '%%order_class%%.tm_pb_fullwidth_menu .fullwidth-menu-nav > ul.upwards li ul' : '%%order_class%%.tm_pb_fullwidth_menu .nav li ul'; TM_Builder_Element::set_style( $function_name, array(
				'selector'    => $dropdown_menu_line_color_selector,
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $dropdown_menu_line_color )
				),
			) ); TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_menu .tm_mobile_menu',
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $dropdown_menu_line_color )
				),
			) );
		}

		if ( '' !== $dropdown_menu_text_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_menu .nav li ul a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $dropdown_menu_text_color )
				),
			) );
		}

		if ( '' !== $mobile_menu_bg_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_menu .tm_mobile_menu, %%order_class%%.tm_pb_fullwidth_menu .tm_mobile_menu ul',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $mobile_menu_bg_color )
				),
			) );
		}

		if ( '' !== $mobile_menu_text_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.tm_pb_fullwidth_menu .tm_mobile_menu a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $mobile_menu_text_color )
				),
			) );
		}

		$output = sprintf(
			'<div%4$s class="tm_pb_fullwidth_menu%3$s%5$s"%2$s%6$s>
				<div class="tm_pb_row clearfix">
					%1$s
					<div class="tm_mobile_nav_menu">
						<a href="#" class="mobile_nav closed">
							<span class="mobile_menu_bar"></span>
						</a>
					</div>
				</div>
			</div>',
			$menu,
			$style,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $style ? sprintf( ' data-bg_color=%1$s', esc_attr( $background_color ) ) : '' )
		);

		return $output;
	}
}
new Tm_Builder_Module_Fullwidth_Menu;

