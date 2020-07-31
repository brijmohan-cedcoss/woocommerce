<?php
/**
 * Class file to add Points tab in Woocommerce settings.
 */
class WC_Settings_Tab_Points {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_settings_tab_points', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_settings_tab_points', __CLASS__ . '::update_settings' );
	}

	/**
	 * Add the Points settings tab to the Wocommerce settings tabs array
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs ['settings_tab_points'] = __( 'Credit Points', 'woocommerce-settings-tab-points' );
		return $settings_tabs;
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public static function get_settings() {

		$settings = array(
			'section_title'     => array(
				'id'   => 'wc_settings_tab_points_section_title',
				'name' => __( 'Credit Points Settings', 'woocommerce-settings-tab-points' ),
				'type' => 'title',
				'desc' => __( 'Make Your Credit Point Settings Here', 'woocommerce-settings-tab-points' ),
			),
			'per_buy'           => array(
				'id'       => 'wc_settings_tab_points_per_buy',
				'name'     => __( 'Point Per Buy', 'woocommerce-settings-tab-points' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the number of points awarded per buy', 'woocommerce-settings-tab-points' ),
			),
			'per_product_visit' => array(
				'id'       => 'wc_settings_tab_points_per_product_visit',
				'name'     => __( 'Points Per Product Visit', 'woocommerce-settings-tab-points' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the number of points awarded per product visit', 'woocommerce-settings-tab-points' ),
			),
			'add_to_cart'       => array(
				'id'       => 'wc_settings_tab_points_add_to_cart',
				'name'     => __( 'Ponits on product add to cart', 'woocommerce-settings-tab-points' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the number of points awarded on product add to cart', 'woocommerce-settings-tab-points' ),
			),
			'on_checkout'       => array(
				'id'       => 'wc_settings_tab_points_on_checkout',
				'name'     => __( 'Points on checkout', 'woocommerce-settings-tab-points' ),
				'type'     => 'number',
				'desc_tip' => __( 'Set the number of points awarded on product add to cart', 'woocommerce-settings-tab-points' ),
			),
			'section_end'       => array(
				'type' => 'sectionend',
				'id'   => 'wc_settings_tab_demo_section_end',
			),

		);

		return apply_filters( 'wc_settings_tab_points_settings', $settings );
	}
}

WC_Settings_Tab_Points::init();
