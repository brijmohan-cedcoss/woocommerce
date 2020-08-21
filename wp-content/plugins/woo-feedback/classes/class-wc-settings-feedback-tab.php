<?php
/**
 * Class file to add Points tab in Woocommerce settings.
 */
class WC_Settings_Feedback_Tab {

	/**
	 * Bootstraps the class and hook the requires actions and filters.
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_feedback_form_tab', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_feedback_form_tab', __CLASS__ . '::update_settings' );
	}

	/**
	 * Add the Feedback Form settings tab to the settings tabs array of woocommerce.
	 *
	 * @param array $settings_tabs is an array of Woocommerce settings tabs and their labels.
	 * @return array
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['feedback_form_tab'] = __( 'Feedback Form', 'woocommerce_extension' );
		return $settings_tabs;
	}

	/**
	 * Function to output settings of the custom tab via the woocommerce_admin_fields() function.
	 */
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Function to save settings fields values of the custom tab via woocommerce_update_options() function.
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Function to get all the settings fields for the woocommerce_admin_fields() function.
	 */
	public static function get_settings() {

		$settings = array(
			'section_title' => array(
				'id'   => 'wc_feedback_form_tab_section_title',
				'name' => __( 'Feedback Form Settings ', 'woocommerce-extension' ),
				'type' => 'title',
				'desc' => __( 'Manage the Feedback form fields here.', 'woocommerce-extension' ),
			),
			'form_enable'   => array(
				'id'   => 'wc_feedback_form_enable',
				'name' => __( 'Enable Feedback Form', 'woocommerce-extension' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable/disable Feedback Form', 'woocommerce-extension' ),
			),
			'first_field'   => array(
				'id'   => 'wc_feedback_form_first_field',
				'name' => __( 'Title/Label For the First Field', 'woocommerce-extension' ),
				'type' => 'text',
				'desc' => __( 'Type the Name of field that you want to see on front end', 'woocommerce-extension' ),
			),
			'first_field_type' => array(
				'id'   => 'wc_feedback_form_first_field_type',
				'name' => __( 'Input Type For the First Field', 'woocommerce-extension' ),
				'type' => 'text',
				'desc' => __( 'Input Type of the field', 'woocommerce-extension' ),
			),
			'second_field'  => array(
				'id'   => 'wc_feedback_form_second_field',
				'name' => __( 'Title/Label for the Second Field', 'woocommerce-extension' ),
				'type' => 'text',
				'desc' => __( 'Type the Name of field that you want to see on front end', 'woocommerce-extension' ),
			),
			'second_field_type' => array(
				'id'   => 'wc_feedback_form_second_field_type',
				'name' => __( 'Input Type For the Second Field', 'woocommerce-extension' ),
				'type' => 'text',
				'desc' => __( 'Input Type of the field', 'woocommerce-extension' ),
			),
			'third_field'   => array(
				'id'   => 'wc_feedback_form_third_field',
				'name' => __( 'Title/Label for the Third Field', 'woocommerce-extension' ),
				'type' => 'text',
				'desc' => __( 'Type the Name of field that you want to see on front end', 'woocommerce-extension' ),
			),
			'third_field_type' => array(
				'id'   => 'wc_feedback_form_third_field_type',
				'name' => __( 'Input Type For the Third Field', 'woocommerce-extension' ),
				'type' => 'text',
				'desc' => __( 'Input Type of the field', 'woocommerce-extension' ),
			),
			'fourth_field'  => array(
				'id'   => 'wc_feedback_form_fourth_field',
				'name' => __( 'Title/Label for the Fourth Field', 'woocommerce-extension' ),
				'type' => 'text',
				'desc' => __( 'Type the Name of field that you want to see on front end', 'woocommerce-extension' ),
			),
			'fourth_field_type' => array(
				'id'   => 'wc_feedback_form_fourth_field_type',
				'name' => __( 'Input Type For the Fourth Field', 'woocommerce-extension' ),
				'type' => 'text',
				'desc' => __( 'Input Type of the field', 'woocommerce-extension' ),
			),
			'section_end'   => array(
				'type' => 'sectionend',
				'id'   => 'wc_feedback_form_section_end',
			),
		);

		return apply_filters( 'wc_feedback_form_tab_settings', $settings );
	}

}
WC_Settings_Feedback_Tab::init();
