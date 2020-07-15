<?php
/**
 * Plugin Name: WooCommerce Extension
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Your extension's description text.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: http://yourdomain.com/
 * Developer: Your Name
 * Developer URI: http://yourdomain.com/
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * Woo: 12345:342928dfsfhsf8429842374wdf4234sfd
 * WC requires at least: 2.2
 * WC tested up to: 2.3
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocomerce extension
 */

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Define constants
	 */
	if ( ! defined( 'TPWCP_PLUGIN_VERSION' ) ) {
		define( 'TPWCP_PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'TPWCP_PLUGIN_DIR_PATH' ) ) {
		define( 'TPWCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-settings-tab-demo.php'; // Including settings tab class file.
	new WC_Settings_Tab_Demo();

	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-tpwcp-admin.php';

	/**
	 * Start the plugin.
	 */
	function tpwcp_init() {
		if ( is_admin() ) {
			$tp_wcp = new TPWCP_Admin();
			$tp_wcp->init();
		}
	}
	add_action( 'plugins_loaded', 'tpwcp_init' );

	require TPWCP_PLUGIN_DIR_PATH . '/classes/class-sports-admin.php';
	/**
	 * Start the plugin for sports panel.
	 */
	function sports_init() {
		if ( is_admin() ) {
			$tp_wcp = new Sports_Admin();
			$tp_wcp->init();
		}
	}
	add_action( 'plugins_loaded', 'sports_init' );

	/**
	 * Exclude products from a particular category on the shop page
	 */
	function custom_pre_get_posts_query( $q ) {

		$tax_query = (array) $q->get( 'tax_query' );

		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array( 'clothing' ), // Don't display products in the clothing category on the shop page.
			'operator' => 'NOT IN'
		);


		$q->set( 'tax_query', $tax_query );

	}
	add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );
}

