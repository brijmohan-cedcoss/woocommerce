<?php
/**
 * Plugin Name: Woo Custom Tab
 * Description: Adding Custom Tab to My accounts page to display the last order deatils of the customer.
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

/**
 * Register endpoint for the custom tab with flush rewrite rules on plugin activation.
 */
function register_endpoint_custom_tab() {
	add_rewrite_endpoint( 'last-order-endpoint', EP_PERMALINK | EP_PAGES );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'register_endpoint_custom_tab' );
register_deactivation_hook( __FILE__, 'register_endpoint_custom_tab' );

/**
 * Function to add a query variable.
 *
 * @param array $vars is an array of query variable.
 * @return array
 */
function add_custom_endpoint_query_var( $vars ) {
	$vars[] = 'last-order-endpoint';

	return $vars;
}
add_action( 'query_vars', 'add_custom_endpoint_query_var', 0 );

/**
 * Inserting the custom endpoint in my account page.
 *
 * @param array $items is an array of my account menu items.
 * @return array
 */
function custom_last_order_tab( $items ) {
	$logout = $items['customer-logout'];
	unset( $items['customer-logout'] );

	$items['last-order-endpoint'] = __( 'Last Order Details', 'woocommerce-extension' );

	$items['customer-logout'] = $logout;

	return $items;
}
add_action( 'woocommerce_account_menu_items', 'custom_last_order_tab' );

/**
 * Function to check if the customer has bought something earlier or not.
 *
 * @return boolean
 */
function check_customers_order() {
	$customer_orders = get_posts(
		array(
			'numberposts' => 1, // one order is enough to check whether the customer has bought somthing earlier or not.
			'meta_value'  => get_current_user_id(),
			'post_type'   => 'shop_order',
			'post_status' => array( 'wc-processing', 'wc-completed', 'wc-cancelled' ),
		)
	);
	return count( $customer_orders ) > 0 ? true : false;
}

/**
 * Function to add content to the custom tab.
 */
function populate_last_order_endpoint() {

	$customer_id = get_current_user_id();
	$order       = wc_get_customer_last_order( $customer_id );

	if ( is_user_logged_in() ) {
		if ( check_customers_order() ) {
			?>

		<p>Order #<mark class="order-number"><?php esc_html_e( $order->id ); ?></mark> was placed on <mark class="order-date"><?php esc_html_e( $order->date_created->date( ' F j, Y ' ) ); ?></mark> and is currently <mark class="order-status"><?php esc_html_e( $order->status ); ?></mark>.</p>

		<section class="woocommerce-order-details">
			<h2 class="woocommerce-order-details__title">Order details</h2>
			<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
				<thead>
					<tr>
						<th class="woocommerce-table__product-name product-name">Product</th>
						<th class="woocommerce-table__product-table product-total">Total</th>
					</tr>
				</thead>

				<tbody>
					<?php
					foreach ( $order->get_items() as $item ) {
						?>
					<tr class="woocommerce-table__line-item order_item">
						<td class="woocommerce-table__product-name product-name">
							<a href=<?php esc_attr_e( get_permalink( $item->get_product_id() ) ); ?> ><?php esc_html_e( $item->get_name() ); ?> </a> <strong class="product-quantity">&times;&nbsp;<?php esc_html_e( $item->get_quantity() ); ?></strong>	</td>
						<td class="woocommerce-table__product-total product-total">
							<span class="woocommerce-Price-amount amount"><?php echo wc_price( $item->get_total() ); ?></span></td>
					</tr>
						<?php
					}
					?>
				</tbody>

				<tfoot>
					<tr>
						<th scope="row">Subtotal:</th>
						<td><span class="woocommerce-Price-amount amount"><?php echo wc_price( $order->get_subtotal() ); ?></span></td>
					</tr>
					<tr>
						<th scope="row">Shipping:</th>
						<td><?php esc_html_e( $order->get_shipping_method() ); ?></td>
					</tr>
					<tr>
						<th scope="row">Payment method:</th>
						<td><?php esc_html_e( $order->get_payment_method_title() ); ?></td>
					</tr>
					<tr>
						<th scope="row">Total:</th>
						<td><span class="woocommerce-Price-amount amount"><?php echo wc_price( $order->get_total() ); ?></span></td>
					</tr>
				</tfoot>
			</table>

		</section>

		<section class="woocommerce-customer-details">

			<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
				<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">

					<h2 class="woocommerce-column__title">Billing address</h2>
					<address>
						<?php echo $order->get_formatted_billing_address(); ?>
						<p class="woocommerce-customer-details--phone"><?php esc_html_e( $order->get_billing_phone() ); ?></p>
						<p class="woocommerce-customer-details--email"><?php esc_html_e( $order->get_billing_email() ); ?></p>
					</address>

				</div>

				<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
					<h2 class="woocommerce-column__title">Shipping address</h2>
					<address>
					<?php echo $order->get_formatted_shipping_address(); ?>
					</address>
				</div>

			</section>

		</section>
			<?php
		} else {
			echo 'You have not made any Purchase yet!';
		}
	}

}
add_action( 'woocommerce_account_last-order-endpoint_endpoint', 'populate_last_order_endpoint' );

/**
 * Function to change the title of last order details tab
 *
 * @param string $title stores the title of the endpoint.
 * @return string
 */
function last_order_endpoint_title( $title ) {
	global $wp_query;

	$endpoint = isset( $wp_query->query_vars['last-order-endpoint'] );

	if ( $endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {

		$title = __( 'Last Order Details', 'woocommerce-extension' );

	}
	return $title;
}
add_filter( 'the_title', 'last_order_endpoint_title' );

