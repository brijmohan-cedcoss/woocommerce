<?php
/**
 * Plugin Name: WooCommerce Credit System
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Adds Credit for performing certain actions.
 * Version: 1.0.0
 * Author: brij1234
 * Author URI: http://yourdomain.com/
 * Developer: brij1234
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

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Define constants
	 */
	if ( ! defined( 'WC_CREDIT_POINTS_PLUGIN_VERSION' ) ) {
		define( 'WC_CREDIT_POINTS_PLUGIN_VERSION', '1.0.0' );
	}
	if ( ! defined( 'WC_CREDIT_POINTS_PLUGIN_DIR_PATH' ) ) {
		define( 'WC_CREDIT_POINTS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	require WC_CREDIT_POINTS_PLUGIN_DIR_PATH . '/classes/class-wc-settings-tab-points.php'; // Including settings tab class file.
	new WC_Settings_Tab_Points();

	/**
	 * Adding Custom Column to User table
	 *
	 * @param [type] $user_columns represents array of user table columns.
	 * @return $user_columns
	 */
	function add_credit_point_col( $user_columns ) {
		$user_columns['credit_points'] = 'Credit Points';
		return $user_columns;
	}
	add_action( 'manage_users_columns', 'add_credit_point_col' );

	/**
	 * Populate Custom Credit Points column
	 */
	function populate_credit_points_col( $row_output, $user_column_name, $user_id ) {

		if ( 'credit_points' === $user_column_name ) {
			$credited_points = get_user_meta( $user_id, 'points', true );
			return $credited_points;
		}
	}
	add_action( 'manage_users_custom_column', 'populate_credit_points_col', 10, 3 );

	/**
	 * Getting user roles.
	 */
	function user_role() {
		$current_user = wp_get_current_user();

		$user_roles = $current_user->roles;
		$user_role  = array_shift( $user_roles );

		return $user_role;
	}


	/**
	 * Adding custom meta key (points) for all Customers
	 */
	function add_user_meta_credit_points() {
		// Create the WP_User_Query object.
		$wp_user_query = new WP_User_Query( array( 'role' => 'Customer' ) );

		// Get the results.
		$users = $wp_user_query->get_results();

		// Check for results.
		if ( ! empty( $users ) ) {

			// loop through each user.
			foreach ( $users as $user ) {
				// add meta key as points for all the user's data.
				add_user_meta( $user->id, 'points', '0', false ); // Set value place with your own value for newly to give credit to newly registered users.
			}
		}
	}
	//add_action( 'init', 'add_user_meta_credit_points' ); // Creates meta field for already registered user.
	add_action( 'user_register', 'add_user_meta_credit_points' ); // Create meta key points for newly registered customer.

	/**
	 * Calculate points.
	 */
	function calculate_credits() {
		$user_id   = get_current_user_id();
		$user_role = user_role();
		global $woocommerce;
		$cart_count = $woocommerce->cart->cart_contents_count;
		//echo $cart_count;

		if ( 'customer' === $user_role ) {

			$total_points = get_user_meta( $user_id, 'points', true );
			echo $total_points;

			if ( is_product() ) {
				$credits_on_visit  = get_option( 'wc_settings_tab_points_per_product_visit' );
				$total_after_visit = $total_points + $credits_on_visit;
				update_user_meta( $user_id, 'points', $total_after_visit );
				wc_add_notice( 'Congrats! You get ' . $credits_on_visit . ' credit points for visiting this product', 'success' );
			}

			if ( 0 !== $cart_count && is_cart() ) {
				$credits_add_cart     = $cart_count * get_option( 'wc_settings_tab_points_add_to_cart' );
				$total_after_add_cart = $total_points + $credits_add_cart;
				update_user_meta( $user_id, 'points', $total_after_add_cart );
				wc_add_notice( 'Congrats! You get ' . $credits_add_cart . ' credit points for adding products to cart.', 'success' );
			}

			if ( is_wc_endpoint_url( 'order-received' ) ) {
				$credits_after_checkout = get_option( 'wc_settings_tab_points_on_checkout' );
				$total_after_checkout   = $total_points + $credits_after_checkout;
				update_user_meta( $user_id, 'points', $total_after_checkout );
				//wc_add_notice( 'Congrats! You get ' . $credits_after_checkout . ' credit points for shopping with us.', 'success' );
			}
		}

	}
	add_action( 'wp_head', 'calculate_credits' );

	/**
	 * Register new endpoint to use inside My Account page.
	 */
	function credit_points_endpoints() {
		add_rewrite_endpoint( 'credit-points-endpoint', EP_ROOT | EP_PAGES, true );
	}

	add_action( 'init', 'credit_points_endpoints' );

	/**
	 * Flush rewrite rules for the new credit points menu item on plugin activation.
	 */
	function credit_points_flush_rewrite_rules() {
		credit_points_endpoints();
		flush_rewrite_rules();
	}

	register_activation_hook( __FILE__, 'credit_points_flush_rewrite_rules' );
	register_deactivation_hook( __FILE__, 'credit_points_flush_rewrite_rules' );

	/**
	 * Insert new Credit Points menu into the My Account menu.
	 *
	 * @param array $items consists an array of items of my account page.
	 * @return array
	 */
	function add_credit_points_to_account_menu_items( $items ) {

		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] ); // Removing menu item (logout).

		$items['credit-points-endpoint'] = __( 'Credit Points', 'woocommerce' ); // Inserting Credit Points item in menu.

		$items['customer-logout'] = $logout; // Reinserting logout to the menu item.

		return $items;
	}

	add_filter( 'woocommerce_account_menu_items', 'add_credit_points_to_account_menu_items' );

	/**
	 * Changing the title of the new credit points menu item.
	 *
	 * @param string $title stores the title of an endpoint.
	 * @return string
	 */
	function change_credit_points_endpoint_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars['credit-points-endpoint'] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {

			$title = __( 'Credit Points', 'woocommerce' ); // Setting the new title.

			remove_filter( 'the_title', 'change_credit_points_endpoint_title' );
		}

		return $title;
	}

	add_filter( 'the_title', 'change_credit_points_endpoint_title' );

	/**
	 * Shows the content of the credit point menu item.
	 */
	function populate_credit_points_content() {
		$user_id              = get_current_user_id();
		$total_credits_earned = get_user_meta( $user_id, 'points', true );
		?>
		<p>Here are your reward points for shopping with us and staying with us.</p>
		<p>You can also use your credit points for shopping. </p>
		<table>
			<tr>
				<td>Credit Points Earned :</td>
				<td><?php echo $total_credits_earned; ?> Points</td>
			</tr>
		</table>
		<?php
	}

	add_action( 'woocommerce_account_credit-points-endpoint_endpoint', 'populate_credit_points_content' );

}
