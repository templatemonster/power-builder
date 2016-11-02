<?php
class Tm_Builder_Module_Contact_Form extends Tm_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Contact Form', 'tm_builder' );
		$this->slug = 'tm_pb_contact_form';
		$this->icon = 'f003';
		$this->child_slug      = 'tm_pb_contact_field';
		$this->child_item_text = esc_html__( 'Field', 'tm_builder' );

		$this->whitelisted_fields = array(
			'captcha',
			'email',
			'title',
			'admin_label',
			'module_id',
			'module_class',
			'form_background_color',
			'input_border_radius',
			'custom_message',
			'use_redirect',
			'redirect_url',
			'success_message',
			'submit_text',
		);

		$this->fields_defaults = array(
			'captcha'      => array( 'on' ),
			'use_redirect' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.tm_pb_contact_form_container';
		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h1",
					),
				),
				'form_field'   => array(
					'label'    => esc_html__( 'Form Field', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .input",
					),
				),
			),
			'border' => array(
				'css'      => array(
					'main' => "{$this->main_css_element} .input",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'tm_builder' ),
				),
			),
		);
		$this->custom_css_options = array(
			'contact_title' => array(
				'label'    => esc_html__( 'Contact Title', 'tm_builder' ),
				'selector' => '.tm_pb_contact_main_title',
			),
			'contact_button' => array(
				'label'    => esc_html__( 'Contact Button', 'tm_builder' ),
				'selector' => '.tm_pb_contact_submit',
			),
			'contact_fields' => array(
				'label'    => esc_html__( 'Form Fields', 'tm_builder' ),
				'selector' => '.tm_pb_contact_left input',
			),
			'text_field' => array(
				'label'    => esc_html__( 'Message Field', 'tm_builder' ),
				'selector' => 'textarea.tm_pb_contact_message',
			),
			'captcha_field' => array(
				'label'    => esc_html__( 'Captcha Field', 'tm_builder' ),
				'selector' => 'input.tm_pb_contact_captcha',
			),
			'captcha_label' => array(
				'label'    => esc_html__( 'Captcha Text', 'tm_builder' ),
				'selector' => '.tm_pb_contact_right p',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'captcha' => array(
				'label'           => esc_html__( 'Display Captcha', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'description' => esc_html__( 'Turn the captcha on or off using this option.', 'tm_builder' ),
			),
			'email' => array(
				'label'           => esc_html__( 'Email', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the email address where messages should be sent.', 'tm_builder' ),
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a title for your contact form.', 'tm_builder' ),
			),
			'custom_message' => array(
				'label'           => esc_html__( 'Message Pattern', 'tm_builder' ),
				'type'            => 'textarea',
				'option_category' => 'configuration',
				'description'     => tm_get_safe_localization( __( 'Here you can define the custom pattern for the email Message. Fields should be included in following format - <strong>%%field_id%%</strong>. For example if you want to include the field with id = <strong>phone</strong> and field with id = <strong>message</strong>, then you can use the following pattern: <strong>My message is %%message%% and phone number is %%phone%%</strong>. Leave blank for default.', 'tm_builder' ) ),
			),
			'use_redirect' => array(
				'label'           => esc_html__( 'Enable Redirect URL', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm_builder' ),
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
				),
				'affects' => array(
					'#tm_pb_redirect_url',
				),
				'description' => esc_html__( 'Redirect users after successful form submission.', 'tm_builder' ),
			),
			'redirect_url' => array(
				'label'           => esc_html__( 'Redirect URL', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'Type the Redirect URL', 'tm_builder' ),
			),
			'success_message' => array(
				'label'           => esc_html__( 'Success Message', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Type the message you want to display after successful form submission. Leave blank for default', 'tm_builder' ),
			),
			'submit_text' => array(
				'label'           => esc_html__( 'Submit button text', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Input the label for submit button.', 'tm_builder' ),
			),
			'form_background_color' => array(
				'label'             => esc_html__( 'Form Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'input_border_radius'   => array(
				'label'             => esc_html__( 'Input Border Radius', 'tm_builder' ),
				'type'              => 'range',
				'default'           => '0',
				'range_settings'    => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'option_category'   => 'layout',
				'tab_slug'          => 'advanced',
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

	function predefined_child_modules() {
		$output = sprintf(
			'[tm_pb_contact_field field_title="%1$s" field_type="input" field_id="Name" required_mark="on" fullwidth_field="off" /][tm_pb_contact_field field_title="%2$s" field_type="email" field_id="Email" required_mark="on" fullwidth_field="off" /][tm_pb_contact_field field_title="%3$s" field_type="text" field_id="Message" required_mark="on" /]',
			esc_attr__( 'Name', 'tm_builder' ),
			esc_attr__( 'Email Address', 'tm_builder' ),
			esc_attr__( 'Message', 'tm_builder' )
		);

		return $output;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array(
				'module_id',
				'module_class',
				'captcha',
				'email',
				'title',
				'form_background_color',
				'input_border_radius',
				'custom_button',
				'button_icon',
				'custom_message',
				'use_redirect',
				'redirect_url',
				'success_message',
				'submit_text',
			)
		);

		global $tm_pb_contact_form_num;
		$tm_pb_contact_form_num = $this->shortcode_callback_num();

		if ( ! $this->_var( 'module_id' ) ) {
			$this->_var( 'module_id', $this->cf_id( 'tm_pb_contact_form_%s' ) );
		}

		if ( ! $this->_var( 'submit_text' ) ) {
			$this->_var( 'submit_text', esc_html__( 'Submit', 'tm_builder' ) );
		}

		if ( '' !== $this->_var( 'form_background_color' ) ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .input',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $this->_var( 'form_background_color' ) )
					),
				)
			);
		}

		if ( ! in_array( $this->_var( 'input_border_radius' ), array( '', '0' ) ) ) {
			TM_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => '%%order_class%% .input',
					'declaration' => sprintf(
						'-moz-border-radius: %1$s; -webkit-border-radius: %1$s; border-radius: %1$s;',
						esc_html( tm_builder_process_range_value( $this->_var( 'input_border_radius' ) ) )
					),
				)
			);
		}

		if ( '' == $this->_var( 'success_message' ) ) {
			$this->_var( 'success_message', esc_html__( 'Thanks for contacting us', 'tm_builder' ) );
		}

		$content = $this->shortcode_content;

		$tm_error_message = '';
		$tm_contact_error = false;

		$current_form_fields = isset( $_POST[ 'tm_pb_contact_email_fields_' . $tm_pb_contact_form_num ] ) ? $_POST[ 'tm_pb_contact_email_fields_' . $tm_pb_contact_form_num ] : '';

		$contact_email = 'demo@demo.org';
		$processed_fields_values = array();

		$nonce_result = isset( $_POST['_wpnonce-tm-pb-contact-form-submitted'] ) && wp_verify_nonce( $_POST['_wpnonce-tm-pb-contact-form-submitted'], 'tm-pb-contact-form-submit' ) ? true : false;

		// check that the form was submitted and tm_pb_contactform_validate field is empty to protect from spam
		if ( $nonce_result && isset( $_POST['tm_pb_contactform_submit_' . $tm_pb_contact_form_num] ) && empty( $_POST['tm_pb_contactform_validate_' . $tm_pb_contact_form_num] ) ) {
			if ( '' !== $current_form_fields ) {
				$fields_data_json = str_replace( '\\', '' ,  $current_form_fields );
				$fields_data_array = json_decode( $fields_data_json, true );

				// check whether captcha field is not empty
				if ( 'on' === $this->_var( 'captcha' ) && ( ! isset( $_POST['tm_pb_contact_captcha_' . $tm_pb_contact_form_num] ) || empty( $_POST['tm_pb_contact_captcha_' . $tm_pb_contact_form_num] ) ) ) {
					$tm_error_message .= sprintf( '<p class="tm_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you entered the captcha.', 'tm_builder' ) );
					$tm_contact_error = true;
				}

				// check all fields on current form and generate error message if needed
				if ( ! empty( $fields_data_array ) ) {
					foreach( $fields_data_array as $index => $value ) {
						// check all the required fields, generate error message if required field is empty
						if ( 'required' === $value['required_mark'] && empty( $_POST[ $value['field_id'] ] ) ) {
							$tm_error_message .= sprintf( '<p class="tm_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you fill in all required fields.', 'tm_builder' ) );
							$tm_contact_error = true;
							continue;
						}

						// additional check for email field
						if ( 'email' === $value['field_type'] && ! empty( $_POST[ $value['field_id'] ] ) ) {
							$contact_email = sanitize_email( $_POST[ $value['field_id'] ] );
							if ( ! is_email( $contact_email ) ) {
								$tm_error_message .= sprintf( '<p class="tm_pb_contact_error_text">%1$s</p>', esc_html__( 'Invalid Email.', 'tm_builder' ) );
								$tm_contact_error = true;
							}
						}

						// prepare the array of processed field values in convenient format
						if ( false === $tm_contact_error ) {
							$processed_fields_values[ $value['original_id'] ]['value'] = isset( $_POST[ $value['field_id'] ] ) ? $_POST[ $value['field_id'] ] : '';
							$processed_fields_values[ $value['original_id'] ]['label'] = $value['field_label'];
						}
					}
				}
			} else {
				$tm_error_message .= sprintf( '<p class="tm_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you fill in all required fields.', 'tm_builder' ) );
				$tm_contact_error = true;
			}
		} else {
			if ( false === $nonce_result && isset( $_POST['tm_pb_contactform_submit_' . $tm_pb_contact_form_num] ) && empty( $_POST['tm_pb_contactform_validate_' . $tm_pb_contact_form_num] ) ) {
				$tm_error_message .= sprintf( '<p class="tm_pb_contact_error_text">%1$s</p>', esc_html__( 'Please refresh the page and try again.', 'tm_builder' ) );
			}
			$tm_contact_error = true;
		}

		// generate digits for captcha
		$this->_var( 'first_digit', rand( 1, 15 ) );
		$this->_var( 'second_digit', rand( 1, 15 ) );

		if ( ! $tm_contact_error && $nonce_result ) {

			$tm_email_to = '' !== $this->_var( 'email' )
				? $this->_var( 'email' )
				: get_site_option( 'admin_email' );

			$tm_site_name = get_option( 'blogname' );

			$contact_name = isset( $processed_fields_values['name'] ) ? stripslashes( sanitize_text_field( $processed_fields_values['name']['value'] ) ) : '';

			if ( '' !== $this->_var( 'custom_message' ) ) {
				$message_pattern = $this->_var( 'custom_message' );
				// insert the data from contact form into the message pattern
				foreach ( $processed_fields_values as $key => $value ) {
					$message_pattern = str_ireplace( "%%{$key}%%", $value['value'], $message_pattern );
				}
			} else {
				// use default message pattern if custom pattern is not defined
				$message_pattern = isset( $processed_fields_values['message']['value'] ) ? $processed_fields_values['message']['value'] : '';

				// Add all custom fields into the message body by default
				foreach ( $processed_fields_values as $key => $value ) {
					if ( ! in_array( $key, array( 'message', 'name', 'email' ) ) ) {
						$message_pattern .= "\r\n";
						$message_pattern .= sprintf(
							'%1$s: %2$s',
							'' !== $value['label'] ? $value['label'] : $key,
							$value['value']
						);
					}
				}
			}

			$headers[] = "From: \"{$contact_name}\" <{$contact_email}>";
			$headers[] = "Reply-To: <{$contact_email}>";

			$safe_loc = tm_get_safe_localization( sprintf(
				__( 'New Message From %1$s%2$s', 'tm_builder' ),
				sanitize_text_field( html_entity_decode( $tm_site_name ) ),
				( '' !== $this->_var( 'title' ) ? tm_get_safe_localization( sprintf( _x( ' - %s', 'contact form title separator', 'tm_builder' ), sanitize_text_field( html_entity_decode( $this->_var( 'title' ) ) ) ) ) : '' )
			) );

			$mess = stripslashes( $message_pattern );
			$head = apply_filters( 'tm_contact_page_headers', $headers, $contact_name, $contact_email );

			$res = wp_mail(
				apply_filters( 'tm_contact_page_email_to', $tm_email_to ),
				$safe_loc,
				$mess,
				$head
			);

			$tm_error_message = sprintf(
				'<div class="cf-success-message">%1$s</div>',
				esc_html( $this->_var( 'success_message' ) )
			);
		}

		$this->_var( 'tm_error_message', $tm_error_message );

		$form = '';

		if ( '' === trim( $content ) ) {
			$content = do_shortcode( $this->predefined_child_modules() );
		}
		$this->_var( 'content', $content );

		if ( $tm_contact_error ) {

			$this->_var( 'icon', esc_attr( tm_pb_process_font_icon( $this->_var( 'button_icon' ) ) ) );
			$icon_family = tm_builder_get_icon_family();

			if ( $icon_family ) {
				TM_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .tm_pb_custom_button_icon:before, %%order_class%% .tm_pb_custom_button_icon:after',
					'declaration' => sprintf(
						'font-family: "%1$s" !important;',
						esc_attr( $icon_family )
					),
				) );
			}

			if ( 'on' === $this->_var( 'custom_button' ) && '' !== $this->_var( 'icon' ) ) {
				$this->_var( 'icon', sprintf( ' data-icon="%s"', $this->_var( 'icon' ) ) );
				$this->_var( 'icon_class', ' tm_pb_custom_button_icon' );
			} else {
				$this->_var( 'icon', false );
				$this->_var( 'icon_class', null );
			}

		}

		$form = $this->get_template_part( 'contact-form/form.php' );

		$classes = array(
			'tm_pb_contact_form_container',
			'clearfix',
		);

		$atts = array(
			'data-form_unique_num' => $this->cf_id( '%s' ),
		);

		if ( 'on' === $this->_var( 'use_redirect' ) && '' !== $this->_var( 'redirect_url' ) ) {
			$atts['data-redirect_url'] = esc_url( $this->_var( 'redirect_url' ) );
		}

		$output = $this->wrap_module( $form, $classes, $function_name, $atts );

		return $output;
	}

	/**
	 * Return current contact form ID.
	 *
	 * @return string
	 */
	public function cf_id( $format = '%s' ) {
		global $tm_pb_contact_form_num;
		return sprintf( $format, esc_attr( $tm_pb_contact_form_num ) );
	}

}

new Tm_Builder_Module_Contact_Form;
