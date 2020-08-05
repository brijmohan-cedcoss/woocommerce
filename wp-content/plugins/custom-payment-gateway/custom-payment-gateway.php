<?php
/**
 * Plugin Name: Custom Payment Gateway
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Custom Payment Gateway for WooCommerce
 * Version: 1.0.0
 * Author: brij1234
 * Author URI: http://yourdomain.com/
 * Developer: brij1234
 * Developer URI: http://yourdomain.com/
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocomerce extension
 */

/**
 * Check if WooCommerce is active.
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Define constants
	 */
	if ( ! defined( 'CPG_PLUGIN_VERSION' ) ) {
		define( 'CPG_PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'CPG_PLUGIN_DIR_PATH' ) ) {
		define( 'CPG_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Function to add a payment gateway
	 *
	 * @return void
	 */
	function woocommerce_gateway_cpg_init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		/**
		 * Gateway class
		 */
		require CPG_PLUGIN_DIR_PATH . '/classes/class-wc-gateway-cpg.php'; // Includin class file for gateway.
		new WC_Gateway_CPG();

		/**
		 * Add the Gateway to WooCommerce
		 *
		 * @param [type] $methods is used to store the custom payment gateway.
		 */
		function woocommerce_add_gateway_cpg( $methods ) {
			$methods[] = 'WC_Gateway_CPG';
			return $methods;
		}
		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_cpg' );
	}
	add_action( 'plugins_loaded', 'woocommerce_gateway_cpg_init', 0 );

	/**
	 * Function to add COD payment gateway
	 *
	 * @return void
	 */
	function woocommerce_gateway_cod_init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		/**
		 * Gateway class
		 */
		require CPG_PLUGIN_DIR_PATH . '/classes/class-wc-gateway-cod.php'; // Includin class file for gateway.
		new WC_Gateway_COD();

		/**
		 * Add the Gateway to WooCommerce
		 *
		 * @param [type] $methods is used to store the custom payment gateway.
		 */
		function woocommerce_add_gateway_cod( $methods ) {
			$methods[] = 'WC_Gateway_COD';
			return $methods;
		}
		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_cod' );
	}
	add_action( 'plugins_loaded', 'woocommerce_gateway_cod_init', 0 );
}
