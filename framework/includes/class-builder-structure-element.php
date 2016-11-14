<?php

class Tm_Builder_Structure_Element extends Tm_Builder_Element {
	public $is_structure_element = true;

	function wrap_settings_option( $option_output, $field ) {
		if ( ! empty( $field['type'] ) && 'column_settings' == $field['type'] ) {
			$output = $this->generate_columns_settings();
		} elseif ( ! empty( $field['type'] ) && 'column_settings_css_fields' == $field['type'] ) {
			$output = $this->generate_columns_settings_css_fields();
		} elseif ( ! empty( $field['type'] ) && 'column_settings_css' == $field['type'] ) {
			$output = $this->generate_columns_settings_css();
		} else {
			$depends = false;
			if ( isset( $field['depends_show_if'] ) || isset( $field['depends_show_if_not'] ) ) {
				$depends = true;
				if ( isset( $field['depends_show_if_not'] ) ) {
					$depends_attr = sprintf( ' data-depends_show_if_not="%s"', esc_attr( $field['depends_show_if_not'] ) );
				} else {
					$depends_attr = sprintf( ' data-depends_show_if="%s"', esc_attr( $field['depends_show_if'] ) );
				}
			}

			$output = sprintf(
				'%6$s<div class="tm-pb-option%1$s%2$s%3$s%8$s%9$s"%4$s>%5$s</div> <!-- .tm-pb-option -->%7$s',
				( ! empty( $field['type'] ) && 'tiny_mce' == $field['type'] ? ' tm-pb-option-main-content' : '' ),
				( ( $depends || isset( $field['depends_default'] ) ) ? ' tm-pb-depends' : '' ),
				( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? ' tm_pb_hidden' : '' ),
				( $depends ? $depends_attr : '' ),
				"\n\t\t\t\t" . $option_output . "\n\t\t\t",
				"\t",
				"\n\n\t\t",
				( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? esc_attr( sprintf( ' tm-pb-option-%1$s', $field['name'] ) ) : '' ),
				( ! empty( $field['option_class'] ) ? ' ' . $field['option_class'] : '' )
			);
		}

		return $output;
	}

	function generate_column_vars_css() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_module_id_value = typeof tm_pb_module_id_%1$s !== \'undefined\' ? tm_pb_module_id_%1$s : \'\',
					current_module_class_value = typeof tm_pb_module_class_%1$s !== \'undefined\' ? tm_pb_module_class_%1$s : \'\',
					current_custom_css_before_value = typeof tm_pb_custom_css_before_%1$s !== \'undefined\' ? tm_pb_custom_css_before_%1$s : \'\',
					current_custom_css_main_value = typeof tm_pb_custom_css_main_%1$s !== \'undefined\' ? tm_pb_custom_css_main_%1$s : \'\',
					current_custom_css_after_value = typeof tm_pb_custom_css_after_%1$s !== \'undefined\' ? tm_pb_custom_css_after_%1$s : \'\'
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_column_vars() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_value_bg = typeof tm_pb_background_color_%1$s !== \'undefined\' ? tm_pb_background_color_%1$s : \'\',
					current_value_pt = typeof tm_pb_padding_top_%1$s !== \'undefined\' ? tm_pb_padding_top_%1$s : \'\',
					current_value_pr = typeof tm_pb_padding_right_%1$s !== \'undefined\' ? tm_pb_padding_right_%1$s : \'\',
					current_value_pb = typeof tm_pb_padding_bottom_%1$s !== \'undefined\' ? tm_pb_padding_bottom_%1$s : \'\',
					current_value_pl = typeof tm_pb_padding_left_%1$s !== \'undefined\' ? tm_pb_padding_left_%1$s : \'\',
					current_value_padding_laptop = typeof tm_pb_padding_%1$s_laptop !== \'undefined\' ? tm_pb_padding_%1$s_laptop : \'\',
					current_value_padding_tablet = typeof tm_pb_padding_%1$s_tablet !== \'undefined\' ? tm_pb_padding_%1$s_tablet : \'\',
					current_value_padding_phone = typeof tm_pb_padding_%1$s_phone !== \'undefined\' ? tm_pb_padding_%1$s_phone : \'\',
					last_edited_padding_field = typeof tm_pb_padding_%1$s_last_edited !== \'undefined\' ?  tm_pb_padding_%1$s_last_edited : \'\',
					has_laptop_padding = typeof tm_pb_padding_%1$s_laptop !== \'undefined\' ? \'yes\' : \'no\',
					has_tablet_padding = typeof tm_pb_padding_%1$s_tablet !== \'undefined\' ? \'yes\' : \'no\',
					has_phone_padding = typeof tm_pb_padding_%1$s_phone !== \'undefined\' ? \'yes\' : \'no\',
					current_value_bg_img = typeof tm_pb_bg_img_%1$s !== \'undefined\' ? tm_pb_bg_img_%1$s : \'\';
					current_value_parallax = typeof tm_pb_parallax_%1$s !== \'undefined\' && \'on\' === tm_pb_parallax_%1$s ? \' selected="selected"\' : \'\';
					current_value_parallax_method = typeof tm_pb_parallax_method_%1$s !== \'undefined\' && \'on\' === tm_pb_parallax_method_%1$s ? \' selected="selected"\' : \'\';
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_columns_settings() {
		$output = sprintf(
			'<%% var columns = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter = 1;
				_.each( columns, function ( column_type ) {
					var current_value_bg,
						current_value_pt,
						current_value_pr,
						current_value_pb,
						current_value_pl,
						current_value_padding_laptop,
						current_value_padding_tablet,
						current_value_padding_phone,
						current_value_bg_img,
						current_value_parallax,
						current_value_parallax_method,
						has_laptop_padding,
						has_tablet_padding,
						has_phone_padding;
					switch ( counter ) {
						%1$s
					}
			%%>',
			$this->generate_column_vars()
		);

		$output .= sprintf(
			'<div class="tm-pb-option">
				<label for="tm_pb_bg_img_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%2$s:
				</label>

				<div class=tm-pb-option-container>
					<input id="tm_pb_bg_img_<%%= counter %%>" type="text" class="regular-text tm-pb-upload-field tm-pb-main-setting" value="<%%= current_value_bg_img  %%>" />
					<input type="button" class="button button-upload tm-pb-upload-button" value="%3$s" data-choose="%4$s" data-update="%5$s" data-type="image" />
					<span class="tm-pb-reset-setting" style="display: none;"></span>
				</div>
			</div> <!-- .tm-pb-option -->

			<div class="tm-pb-option">
				<label for="tm_pb_parallax_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%13$s:
				</label>

				<div class="tm-pb-option-container">
					<div class="tm_pb_yes_no_button_wrapper ">
						<div class="tm_pb_yes_no_button tm_pb_off_state">
							<span class="tm_pb_value_text tm_pb_on_value">%14$s</span>
							<span class="tm_pb_button_slider"></span>
							<span class="tm_pb_value_text tm_pb_off_value">%15$s</span>
						</div>
						<select name="tm_pb_parallax_<%%= counter %%>" id="tm_pb_parallax_<%%= counter %%>" class="tm-pb-main-setting regular-text tm-pb-affects" data-affects="#tm_pb_parallax_method_<%%= counter %%>">
							<option value="off">%15$s</option>
							<option value="on" <%%= current_value_parallax %%>>%14$s</option>
						</select>
					</div>
					<span class="tm-pb-reset-setting" style="display: none;"></span>
				</div> <!-- .tm-pb-option-container -->
			</div>

			<div class="tm-pb-option tm-pb-depends" data-depends_show_if="on">
				<label for="tm_pb_parallax_method_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%16$s:
				</label>

				<div class="tm-pb-option-container">
					<select name="tm_pb_parallax_method_<%%= counter %%>" id="tm_pb_parallax_method_<%%= counter %%>" class="tm-pb-main-setting">
						<option value="off">%17$s</option>
						<option value="on" <%%= current_value_parallax_method %%>>%18$s</option>
					</select>
					<span class="tm-pb-reset-setting" style="display: none;"></span>
				</div> <!-- .tm-pb-option-container -->
			</div>

			<div class="tm-pb-option">
				<label for="tm_pb_background_color_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%6$s:
				</label>
				<div class="tm-pb-option-container">
					<input id="tm_pb_background_color_<%%= counter %%>" class="tm-pb-color-picker-hex tm-pb-color-picker-hex-alpha wp-color-picker tm-pb-main-setting" type="text" data-alpha="true" placeholder="%7$s" value="<%%= current_value_bg %%>" />
					<span class="tm-pb-reset-setting" style="display: none;"></span>
				</div> <!-- .tm-pb-option-container -->
			</div> <!-- .tm-pb-option -->

			<div class="tm-pb-option">
				<label for="tm_pb_padding_<%%= counter %%>">
					%1$s
					<%% if ( "4_4" !== column_type ) { %%>
						<%%= counter + " " %%>
					<%% } %%>
					%8$s:
				</label>
				<div class="tm-pb-option-container">
				%19$s
					<div class="tm_custom_margin_padding">
						<label>
							%9$s
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_top tm-pb-validate-unit tm_pb_setting_mobile tm_pb_setting_mobile_desktop tm_pb_setting_mobile_active" id="tm_pb_padding_top_<%%= counter %%>" name="tm_pb_padding_top_<%%= counter %%>" value="<%%= current_value_pt %%>" data-device="desktop">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_top tm_pb_setting_mobile tm_pb_setting_mobile_laptop" data-device="laptop">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_top tm_pb_setting_mobile tm_pb_setting_mobile_tablet" data-device="tablet">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_top tm_pb_setting_mobile tm_pb_setting_mobile_phone" data-device="phone">
						</label>
						<label>
							%10$s
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_right tm-pb-validate-unit tm_pb_setting_mobile tm_pb_setting_mobile_desktop tm_pb_setting_mobile_active" id="tm_pb_padding_right_<%%= counter %%>" name="tm_pb_padding_right_<%%= counter %%>" value="<%%= current_value_pr %%>" data-device="desktop">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_right tm_pb_setting_mobile tm_pb_setting_mobile_laptop" data-device="laptop">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_right tm_pb_setting_mobile tm_pb_setting_mobile_tablet" data-device="tablet">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_right tm_pb_setting_mobile tm_pb_setting_mobile_phone" data-device="phone">
						</label>
						<label>
							%11$s
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_bottom tm-pb-validate-unit tm_pb_setting_mobile tm_pb_setting_mobile_desktop tm_pb_setting_mobile_active" id="tm_pb_padding_bottom_<%%= counter %%>" name="tm_pb_padding_bottom_<%%= counter %%>" value="<%%= current_value_pb %%>" data-device="desktop">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_bottom tm_pb_setting_mobile tm_pb_setting_mobile_laptop" data-device="laptop">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_bottom tm_pb_setting_mobile tm_pb_setting_mobile_tablet" data-device="tablet">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_bottom tm_pb_setting_mobile tm_pb_setting_mobile_phone" data-device="phone">
						</label>
						<label>
							%12$s
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_left tm-pb-validate-unit tm_pb_setting_mobile tm_pb_setting_mobile_desktop tm_pb_setting_mobile_active" id="tm_pb_padding_left_<%%= counter %%>" name="tm_pb_padding_left_<%%= counter %%>" value="<%%= current_value_pl %%>" data-device="desktop">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_left tm_pb_setting_mobile tm_pb_setting_mobile_laptop" data-device="laptop">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_left tm_pb_setting_mobile tm_pb_setting_mobile_tablet" data-device="tablet">
							<input type="text" class="medium-text tm_custom_margin tm_custom_margin_left tm_pb_setting_mobile tm_pb_setting_mobile_phone" data-device="phone">
						</label>
						<input type="hidden" class="tm_custom_margin_main tm_pb_setting_mobile tm_pb_setting_mobile_desktop tm-pb-main-setting tm_pb_setting_mobile_active" value="<%%= \'\' === current_value_pt && \'\' === current_value_pr && \'\' === current_value_pb && \'\' === current_value_pl ? \'\' : current_value_pt + \'|\' + current_value_pr + \'|\' + current_value_pb + \'|\' + current_value_pl %%>" data-device="desktop">
						<input type="hidden" class="tm_custom_margin_main tm_pb_setting_mobile tm_pb_setting_mobile_laptop tm-pb-main-setting" id="tm_pb_padding_<%%= counter %%>_laptop" name="tm_pb_padding_<%%= counter %%>_laptop" value="<%%= current_value_padding_laptop %%>" data-device="laptop" data-has_saved_value="<%%= has_laptop_padding %%>">
						<input type="hidden" class="tm_custom_margin_main tm_pb_setting_mobile tm_pb_setting_mobile_tablet tm-pb-main-setting" id="tm_pb_padding_<%%= counter %%>_tablet" name="tm_pb_padding_<%%= counter %%>_tablet" value="<%%= current_value_padding_tablet %%>" data-device="tablet" data-has_saved_value="<%%= has_tablet_padding %%>">
						<input type="hidden" class="tm_custom_margin_main tm_pb_setting_mobile tm_pb_setting_mobile_phone tm-pb-main-setting" id="tm_pb_padding_<%%= counter %%>_phone" name="tm_pb_padding_<%%= counter %%>_phone" value="<%%= current_value_padding_phone %%>" data-device="phone" data-has_saved_value="<%%= has_phone_padding %%>">
						<input id="tm_pb_padding_<%%= counter %%>_last_edited" type="hidden" class="tm_pb_mobile_last_edited_field" value="<%%= last_edited_padding_field %%>">
					</div> <!-- .tm_custom_margin_padding -->
					<span class="tm-pb-mobile-settings-toggle"></span>
					<span class="tm-pb-reset-setting"></span>
				</div><!-- .tm-pb-option-container -->
			</div><!-- .tm-pb-option -->

			<%% counter++;
			}); %%>',
			esc_html__( 'Column', 'tm_builder' ),
			esc_html__( 'Background Image', 'tm_builder' ),
			esc_html__( 'Upload an image', 'tm_builder' ),
			esc_html__( 'Choose a Background Image', 'tm_builder' ),
			esc_html__( 'Set As Background', 'tm_builder' ), // #5
			esc_html__( 'Background Color', 'tm_builder' ),
			esc_html__( 'Hex Value', 'tm_builder' ),
			esc_html__( 'Padding', 'tm_builder' ),
			esc_html__( 'Top', 'tm_builder' ),
			esc_html__( 'Right', 'tm_builder' ), // #10
			esc_html__( 'Bottom', 'tm_builder' ),
			esc_html__( 'Left', 'tm_builder' ),
			esc_html__( 'Parallax Effect', 'tm_builder' ),
			esc_html__( 'Yes', 'tm_builder' ),
			esc_html__( 'No', 'tm_builder' ), // #15
			esc_html__( 'Parallax Method', 'tm_builder' ),
			esc_html__( 'CSS', 'tm_builder' ),
			esc_html__( 'True Parallax', 'tm_builder' ),
			tm_pb_generate_mobile_options_tabs() // #19
		);

		return $output;
	}

	function generate_columns_settings_css() {
		$output = sprintf(
			'<%%
			var columns_css = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter_css = 1;

			_.each( columns_css, function ( column_type ) {
				var current_module_id_value,
					current_module_class_value,
					current_custom_css_before_value,
					current_custom_css_main_value,
					current_custom_css_after_value;
				switch ( counter_css ) {
					%1$s
				} %%>

				<div class="tm-pb-option">
					<label for="tm_pb_custom_css_before_<%%= counter_css %%>">
						%2$s
						<%% if ( "4_4" !== column_type ) { %%>
							<%%= counter_css + " " %%>
						<%% } %%>
						%3$s:<span>.tm_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%>:before</span>
					</label>

					<div class="tm-pb-option-container tm-pb-custom-css-option">
						<textarea id="tm_pb_custom_css_before_<%%= counter_css %%>" class="tm-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_before_value.replace( /\|\|/g, "\n" ) %%></textarea>
					</div><!-- .tm-pb-option-container -->
				</div><!-- .tm-pb-option -->

				<div class="tm-pb-option">
					<label for="tm_pb_custom_css_main_<%%= counter_css %%>">
						%2$s
						<%% if ( "4_4" !== column_type ) { %%>
							<%%= counter_css + " " %%>
						<%% } %%>
						%4$s:<span>.tm_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%></span>
					</label>

					<div class="tm-pb-option-container tm-pb-custom-css-option">
						<textarea id="tm_pb_custom_css_main_<%%= counter_css %%>" class="tm-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_main_value.replace( /\|\|/g, "\n" ) %%></textarea>
					</div><!-- .tm-pb-option-container -->
				</div><!-- .tm-pb-option -->

				<div class="tm-pb-option">
					<label for="tm_pb_custom_css_after_<%%= counter_css %%>">
						%2$s
						<%% if ( "4_4" !== column_type ) { %%>
							<%%= counter_css + " " %%>
						<%% } %%>
						%5$s:<span>.tm_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%>:after</span>
					</label>

					<div class="tm-pb-option-container tm-pb-custom-css-option">
						<textarea id="tm_pb_custom_css_after_<%%= counter_css %%>" class="tm-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_after_value.replace( /\|\|/g, "\n" ) %%></textarea>
					</div><!-- .tm-pb-option-container -->
				</div><!-- .tm-pb-option -->

			<%% counter_css++;
			}); %%>',
			$this->generate_column_vars_css(),
			esc_html__( 'Column', 'tm_builder' ),
			esc_html__( 'Before', 'tm_builder' ),
			esc_html__( 'Main Element', 'tm_builder' ),
			esc_html__( 'After', 'tm_builder' )
		);

		return $output;
	}

	function generate_columns_settings_css_fields() {
		$output = sprintf(
			'<%%
			var columns_css = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter_css = 1;

			_.each( columns_css, function ( column_type ) {
				var current_module_id_value,
					current_module_class_value;
				switch ( counter_css ) {
					%1$s
				} %%>

				<div class="tm-pb-option tm_pb_custom_css_regular">
					<label for="tm_pb_module_id_<%%= counter_css %%>">
						%2$s
						<%% if ( "4_4" !== column_type ) { %%>
							<%%= counter_css + " " %%>
						<%% } %%>
						%3$s:
					</label>

					<div class="tm-pb-option-container">
						<input id="tm_pb_module_id_<%%= counter_css %%>" type="text" class="regular-text tm_pb_custom_css_regular tm-pb-main-setting" value="<%%= current_module_id_value %%>">
					</div><!-- .tm-pb-option-container -->
				</div><!-- .tm-pb-option -->

				<div class="tm-pb-option tm_pb_custom_css_regular">
					<label for="tm_pb_module_class_<%%= counter_css %%>">
						%2$s
						<%% if ( "4_4" !== column_type ) { %%>
							<%%= counter_css + " " %%>
						<%% } %%>
						%4$s:
					</label>

					<div class="tm-pb-option-container">
						<input id="tm_pb_module_class_<%%= counter_css %%>" type="text" class="regular-text tm_pb_custom_css_regular tm-pb-main-setting" value="<%%= current_module_class_value %%>">
					</div><!-- .tm-pb-option-container -->
				</div><!-- .tm-pb-option -->
			<%% counter_css++;
			}); %%>',
			$this->generate_column_vars_css(),
			esc_html__( 'Column', 'tm_builder' ),
			esc_html__( 'CSS ID', 'tm_builder' ),
			esc_html__( 'CSS Class', 'tm_builder' )
		);

		return $output;
	}
}
