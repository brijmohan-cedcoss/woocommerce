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
			'name_guest'    => array(
				'id'   => 'wc_feedback_form_guest_name',
				'name' => __( 'Enable Name', 'woocommerce-extension' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable/disable Name option for users', 'woocommerce-extension' ),
			),
			'email_guest'   => array(
				'id'   => 'wc_feedback_form_guest_email',
				'name' => __( 'Enable Email', 'woocommerce-extension' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable/disable Email option for users', 'woocommerce-extension' ),
			),
			'phone_guest'   => array(
				'id'   => 'wc_feedback_form_guest_phone',
				'name' => __( 'Enable Phone', 'woocommerce-extension' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable/disable Phone option for users', 'woocommerce-extension' ),
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
