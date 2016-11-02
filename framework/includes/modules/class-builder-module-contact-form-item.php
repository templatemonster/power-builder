<?php
class Tm_Builder_Module_Contact_Form_item extends Tm_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Field', 'tm_builder' );
		$this->slug            = 'tm_pb_contact_field';
		$this->type            = 'child';
		$this->child_title_var = 'field_id';

		$this->whitelisted_fields = array(
			'field_title',
			'field_type',
			'field_id',
			'field_placeholder',
			'required_mark',
			'show_label',
			'field_width',
			'input_border_radius',
			'field_background_color',
			'select_options',
			'select_multiple',
			'select_first_blank',
		);

		$this->advanced_setting_title_text = esc_html__( 'New Field', 'tm_builder' );
		$this->settings_text               = esc_html__( 'Field Settings', 'tm_builder' );
		$this->main_css_element = '%%order_class%%.tm_pb_contact_field .input';
		$this->advanced_options = array(
			'fonts' => array(
				'form_field'   => array(
					'label'    => esc_html__( 'Field', 'tm_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element}",
					),
				),
			),
			'border' => array(
				'css'      => array(
					'main' => "{$this->main_css_element}",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'field_id' => array(
				'label'       => esc_html__( 'Field ID', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Define the unique ID of this field. You should use only English characters without special characters and spaces.', 'tm_builder' ),
			),
			'field_title' => array(
				'label'       => esc_html__( 'Title', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'tm_builder' ),
			),
			'field_placeholder' => array(
				'label'       => esc_html__( 'Placeholder', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Here you can define the placeholder for the current field.', 'tm_builder' ),
			),
			'field_type' => array(
				'label'       => esc_html__( 'Type', 'tm_builder' ),
				'type'        => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'input'  => esc_html__( 'Input Field', 'tm_builder' ),
					'email'  => esc_html__( 'Email Field', 'tm_builder' ),
					'select' => esc_html__( 'Select Field', 'tm_builder' ),
					'text'   => esc_html__( 'Textarea', 'tm_builder' ),
				),
				'affects'     => array(
					'#tm_pb_select_options',
					'#tm_pb_select_multiple',
					'#tm_pb_select_first_blank',
				),
				'description' => esc_html__( 'Choose the type of field', 'tm_builder' ),
			),
			'select_options' => array(
				'label'           => esc_html__( 'Type', 'tm_builder' ),
				'type'            => 'textarea',
				'option_category' => 'basic_option',
				'depends_default' => true,
				'depends_show_if' => 'select',
				'description'     => esc_html__( 'Define select options. Separate options with ";". Separate option value from option label with "=="', 'tm_builder' ),
			),
			'select_multiple' => array(
				'label'           => esc_html__( 'Allow multiple selections', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'depends_default' => true,
				'depends_show_if' => 'select',
			),
			'select_first_blank' => array(
				'label'           => esc_html__( 'Insert a placeholder as the first empty option', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'depends_default' => true,
				'depends_show_if' => 'select',
			),
			'required_mark' => array(
				'label'           => esc_html__( 'Required Field', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
				'description' => esc_html__( 'Define whether the field should be required or optional', 'tm_builder' ),
			),
			'show_label' => array(
				'label'           => esc_html__( 'Show field label', 'tm_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'field_width' => array(
				'label'           => esc_html__( 'Width', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'col-md-3'  => '1/4',
					'col-md-4'  => '1/3',
					'col-md-6'  => '1/2',
					'col-md-8'  => '2/3',
					'col-md-9'  => '3/4',
					'col-md-12' => esc_html__( 'Fullwidth', 'tm-builder-integrator' )
				),
			),
			'field_background_color' => array(
				'label'             => esc_html__( 'Background Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'input_border_radius'   => array(
				'label'             => esc_html__( 'Border Radius', 'tm_builder' ),
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
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array(
				'field_title',
				'field_type',
				'field_id',
				'field_placeholder',
				'required_mark',
				'show_label',
				'field_width',
				'field_background_color',
				'input_border_radius',
				'select_options',
				'select_multiple',
				'select_first_blank',
			)
		);

		global $tm_pb_contact_form_num;

		$field_id = strtolower( $this->_var( 'field_id' ) );
		$this->_var( 'field_id', $field_id );

		// do not output the fields with empty ID
		if ( '' === $this->_var( 'field_id' ) ) {
			return;
		}

		$current_module_num = '' === $tm_pb_contact_form_num ? 0 : intval( $tm_pb_contact_form_num ) + 1;
		$this->_var( 'current_module_num', $current_module_num );

		$module_class = TM_Builder_Element::add_module_order_class( '', $function_name );

		if ( ! $this->_var( 'field_width' ) ) {
			$this->_var( 'field_width', 'col-md-12' );
		}

		$input_field = '';

		if ( '' !== $this->_var( 'field_background_color' ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .input',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->_var( 'field_background_color' ) )
				),
			) );
		}

		if ( ! in_array( $this->_var( 'input_border_radius' ), array( '', '0' ) ) ) {
			TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .input',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s; -webkit-border-radius: %1$s; border-radius: %1$s;',
					esc_html(
						tm_builder_process_range_value( $this->_var( 'input_border_radius' ) )
					)
				),
			) );
		}

		switch( $this->_var( 'field_type' ) ) {
			case 'text':
				$input_field = $this->get_template_part( 'contact-form/textarea.php' );
				break;
			case 'input' :
				$input_field = $this->get_template_part( 'contact-form/input.php' );
				break;
			case 'email' :
				$input_field = $this->get_template_part( 'contact-form/email.php' );
				break;
			case 'select':
				$input_field = $this->get_template_part( 'contact-form/select.php' );
		}

		$this->_var( 'input_field', $input_field );

		$output = $this->get_template_part( 'contact-form/field.php' );

		return $output;
	}

	/**
	 * Returns select first option
	 *
	 * @return string
	 */
	public function cf_select_first_option() {

		if ( 'on' !== $this->_var( 'select_first_blank' ) ) {
			return '';
		}

		$label = ( '' !== $this->_var( 'field_placeholder' ) ) ? esc_html( $this->_var( 'field_placeholder' ) ) : '--';
		$selected = ( '' === $this->cf_current_val() ) ? ' selected' : '';

		return sprintf( '<option value="" disabled%2$s>%1$s</option>', $label, $selected );

	}

	/**
	 * Returns array of select options
	 *
	 * @return string
	 */
	public function cf_select_options() {

		$options = $this->_var( 'select_options' );

		$options = explode( ";", $options );

		if ( empty( $options ) ) {
			return '';
		}

		$result = '';

		foreach ( $options as $opt ) {

			$opt = explode( '==', $opt );

			if ( 1 < count( $opt ) ) {
				$val   = esc_attr( trim( $opt[0] ) );
				$label = esc_attr( trim( $opt[1] ) );
			} else {
				$val   = esc_attr( trim( $opt[0] ) );
				$label = esc_attr( trim( $opt[0] ) );
			}

			$selected = selected( $this->cf_current_val(), $val, false );

			$result .= sprintf( '<option value="%1$s" %3$s>%2$s</option>', $val, $label, $selected );

		}

		return $result;
	}

	/**
	 * Returns required mark
	 *
	 * @return string
	 */
	public function cf_is_required() {
		return ( 'off' === $this->_var( 'required_mark' ) ? 'not_required' : 'required' );
	}

	/**
	 * Returns current item column CSS classes string
	 *
	 * @return string
	 */
	public function cf_col_class() {
		$classes = array(
			'tm_pb_contact_field',
			$this->_var( 'field_width' ),
			esc_attr( $this->_var( 'module_class' ) )
		);

		return implode( ' ', $classes );
	}

	/**
	 * Returns current item field name
	 *
	 * @return string
	 */
	public function cf_field_name() {
		return implode( '_', array(
			'tm_pb_contact',
			esc_attr( $this->_var( 'field_id' ) ),
			intval( $this->_var( 'current_module_num' ) )
		) );
	}

	/**
	 * Returns placeholder attribute if value provided
	 *
	 * @return string
	 */
	public function cf_placeholder() {
		if ( '' !== $this->_var( 'field_placeholder' ) ) {
			return sprintf( ' placeholder="%s"', esc_attr( $this->_var( 'field_placeholder' ) ) );
		}
	}

	/**
	 * Return Contact Form data attributes
	 *
	 * @return string
	 */
	public function cf_data_atts() {

		$atts = array(
			'required_mark'  => $this->cf_is_required(),
			'field_type'     => esc_attr( $this->_var( 'field_type' ) ),
			'original_id'    => esc_attr( $this->_var( 'field_id' ) ),
			'original_title' => esc_attr( $this->_var( 'field_title' ) ),
		);

		$result = '';

		foreach ( $atts as $field => $value ) {
			$result .= sprintf( ' data-%1$s="%2$s"', $field, $value );
		}

		return $result;
	}

	/**
	 * Returns current form item value
	 *
	 * @return string
	 */
	public function cf_current_val() {

		$key = $this->cf_field_name();

		if ( isset( $_POST[ $key ] ) ) {
			return esc_html( sanitize_text_field( $_POST[ $key ] ) );
		} else {
			return '';
		}
	}

}

new Tm_Builder_Module_Contact_Form_item;
