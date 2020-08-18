<?php
/**
 * Plugin Name: Woo-Feedback Form
 * Description: Adding Custom Tab to Woocommerce settings to manage feedback form fields.
 * Version: 1.0.0
 * Author: brij1234
 * Developer: brij1234
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocomerce extension
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Defining path constant
	 */
	if ( ! defined( 'WOO_FEEDBACK_DIR_PATH' ) ) {
		define( 'WOO_FEEDBACK_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	require WOO_FEEDBACK_DIR_PATH . '/classes/class-wc-settings-feedback-tab.php'; // Including the class file.

	/**
	 * Register endpoint for the Feedback with flush rewrite rules on plugin activation.
	 */
	function register_endpoint_feedback() {
		add_rewrite_endpoint( 'feedback-endpoint', EP_PERMALINK | EP_PAGES );
		flush_rewrite_rules();
	}
	register_activation_hook( __FILE__, 'register_endpoint_feedback' );
	register_deactivation_hook( __FILE__, 'register_endpoint_feedback' );

	/**
	 * Function to add a query variable.
	 *
	 * @param array $vars is an array of query variable.
	 * @return array
	 */
	function add_endpoint_query_var_feedback( $vars ) {
		$vars[] = 'feedback-endpoint';

		return $vars;
	}
	add_action( 'query_vars', 'add_endpoint_query_var_feedback', 0 );

	/**
	 * Only Enable the form if the condition turn true.
	 */
	if ( 'yes' === get_option( 'wc_feedback_form_enable' ) ) {
		/**
		 * Inserting the custom endpoint in my account page.
		 *
		 * @param array $items is an array of my account menu items.
		 * @return array
		 */
		function custom_feedback_tab( $items ) {
			$logout = $items['customer-logout'];
			unset( $items['customer-logout'] );

			$items['feedback-endpoint'] = __( 'Feedback', 'woocommerce-extension' );

			$items['customer-logout'] = $logout;

			return $items;
		}
		add_action( 'woocommerce_account_menu_items', 'custom_feedback_tab' );
	}

	/**
	 * Function to add content to the custom tab.
	 */
	function populate_feedback_endpoint() {
		echo 'Give Your Precious Feedbacks Here. </br></br>';

		$user_name  = get_option( 'wc_feedback_form_guest_name' );
		$user_email = get_option( 'wc_feedback_form_guest_email' );
		$user_phone = get_option( 'wc_feedback_form_guest_phone' );
		//$user_id    = get_current_user_id();
		//echo $user_phone;

		if ( is_user_logged_in() ) {

			if ( 'yes' === $user_name ) {
				echo '<input type="text" name="user_name" id="user_name" placeholder="Enter Your Name"></br></br>';
			}

			if ( 'yes' === $user_email ) {
				echo '<input type="text" name="user_email" id="user_email" placeholder="Enter Your Email"></br></br>';
			}

			if ( 'yes' === $user_phone ) {
				echo '<input type="text" name="user_phone" id="user_phone" placeholder="Enter Your Phone No."></br></br>';
			}

			echo '<textarea name="user_query" id="user_query" placeholder="Give Your Feedback"></textarea></br></br>';

			echo '<button type="submit" class="button alt" name="submit_feedback" id="submit_feedback">Submit</button>';
		}
	}
	add_action( 'woocommerce_account_feedback-endpoint_endpoint', 'populate_feedback_endpoint' );

	/**
	 * Function to change the title of last order details tab
	 *
	 * @param string $title stores the title of the endpoint.
	 * @return string
	 */
	function feedback_endpoint_title( $title ) {
		global $wp_query;

		$endpoint = isset( $wp_query->query_vars['feedback-endpoint'] );

		if ( $endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {

			$title = __( 'Give Your Feedback', 'woocommerce-extension' );

		}
		return $title;
	}
	add_filter( 'the_title', 'feedback_endpoint_title' );

	/**
	 * Enqueue and localize script.
	 */
	function ajax_script_enqueue() {

		// Enqueue script on the frontend.
		wp_enqueue_script(
			'ajax-script-enqueue',
			plugins_url( '/js/ajax-script.js', __FILE__ ),
			array( 'jquery' ),
			'1.0.0',
			true,
		);

		// The wp_localize_script allows us to output the ajax_url path for our script to use.
		wp_localize_script(
			'ajax-script-enqueue',
			'ajax_script_obj',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ajax-nonce' ),
			),
		);
	}
	add_action( 'wp_enqueue_scripts', 'ajax_script_enqueue' );

	/**
	 * Ajax request handler
	 *
	 * @return void
	 */
	function ajax_handler_function() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( 'Nonce value cannot be verified.' );
		}
		$name  = isset( $_POST['uname'] ) ? sanitize_text_field( wp_unslash( $_POST['uname'] ) ) : '';
		$email = isset( $_POST['uemail'] ) ? sanitize_text_field( wp_unslash( $_POST['uemail'] ) ) : '';
		$phone = isset( $_POST['uphone'] ) ? sanitize_text_field( wp_unslash( $_POST['uphone'] ) ) : '';
		$query = isset( $_POST['uquery'] ) ? sanitize_text_field( wp_unslash( $_POST['uquery'] ) ) : '';

		$user_id = get_current_user_id();

		$meta = array(
			'user_name'  => $name,
			'user_email' => $email,
			'user_phone' => $phone,
			'user_query' => $query,
		);

		foreach ( $meta as $key => $value ) {
			update_user_meta( $user_id, $key, $value );
		}

		wp_mail( $email, 'Feedback', $query );

	}
	add_action( 'wp_ajax_nopriv_ajax_request_call', 'ajax_handler_function' );
	add_action( 'wp_ajax_ajax_request_call', 'ajax_handler_function' );

}
