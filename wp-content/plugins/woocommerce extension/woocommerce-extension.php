<?php
/**
 * Plugin Name: WooCommerce Extension
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Your extension's description text.
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
	 *
	 * @param [type] $q is the query variable.
	 */
	function custom_pre_get_posts_query( $q ) {

		$tax_query = (array) $q->get( 'tax_query' );

		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array( 'clothing' ), // Don't display products in the clothing category on the shop page.
			'operator' => 'NOT IN',
		);

		$q->set( 'tax_query', $tax_query );

	}
	// add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );

	/**
	 * Allow HTML in term (category, tag) descriptions
	 */
	function allow_html_term() {
		foreach ( array( 'pre_term_description' ) as $filter ) {
			remove_filter( $filter, 'wp_filter_kses' );
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				add_filter( $filter, 'wp_filter_post_kses' );
			}
		}

		foreach ( array( 'term_description' ) as $filter ) {
			remove_filter( $filter, 'wp_kses_data' );
		}
	}
	// add_action( 'plugins_loaded', 'allow_html_term' );

	/**
	 * Override loop template and show quantities next to add to cart buttons
	 *
	 * @param [type] $html outputs the html for input field.
	 * @param [type] $product is used to get the spacific product info.
	 */
	function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
			$html  = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
			$html .= woocommerce_quantity_input( array(), $product, false );
			$html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
			$html .= '</form>';
		}
		return $html;
	}
	add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );

	/**
	 * Change the default country on the checkout page
	 */
	function change_default_checkout_country() {
		return 'IN'; // country code.
	}
	/**
	 * Change the default state on the checkout page
	 */
	function change_default_checkout_state() {
		return 'UP'; // state code.
	}
	add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );
	add_filter( 'default_checkout_billing_state', 'change_default_checkout_state' );

	/**
	 * Add custom tracking code to the thank-you page
	 *
	 * @param [type] $order_id gives the order id of the specific product.
	 */
	function my_custom_tracking( $order_id ) {

		// Lets grab the order.
		$order = wc_get_order( $order_id );

		/**
		 * Put your tracking code here
		 * You can get the order total etc e.g. $order->get_total();
		 */
		// This is how to grab line items from the order.
		$line_items = $order->get_items();

		// This loops over line items.
		foreach ( $line_items as $item ) {
			// This will be a product.
			$product = $order->get_product_from_item( $item );

			// This is the products SKU.
			$sku = $product->get_sku();

			// This is the qty purchased.
			$qty = $item['qty'];

			// Line item total cost including taxes and rounded.
			$total = $order->get_line_total( $item, true, true );

			// Line item subtotal (before discounts).
			$subtotal = $order->get_line_subtotal( $item, true, true );
		}
	}
	// add_action( 'woocommerce_thankyou', 'my_custom_tracking' );

	/**
	 * Add custom sorting options (asc/desc)
	 *
	 * @param [type] $args is used to set the args for sorting.
	 */
	function custom_woocommerce_get_catalog_ordering_args( $args ) {
		$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		if ( 'random_list' === $orderby_value ) {
			$args['orderby']  = 'rand';
			$args['order']    = '';
			$args['meta_key'] = '';
		}
		return $args;
	}
	add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );

	/**
	 * Function to randomly sort the products
	 *
	 * @param [type] $sortby sorts the products randomly.
	 * @return $sortby
	 */
	function custom_woocommerce_catalog_orderby( $sortby ) {
		$sortby['random_list'] = 'Random';
		return $sortby;
	}
	add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
	add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );

	/**
	 * Remove product data tabs
	 *
	 * @param [type] $tabs outputs the tabs on product page.
	 */
	function woo_remove_product_tabs( $tabs ) {

		unset( $tabs['description'] ); // Remove the description tab.
		unset( $tabs['reviews'] ); // Remove the reviews tab.
		unset( $tabs['additional_information'] ); // Remove the additional information tab.

		return $tabs;
	}
	// add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

	/**
	 * Rename product data tabs
	 *
	 * @param [type] $tabs outputs the tabs on product page.
	 */
	function woo_rename_tabs( $tabs ) {

		$tabs['description']['title']            = __( 'More Information' ); // Rename the description tab.
		$tabs['reviews']['title']                = __( 'Ratings' ); // Rename the reviews tab.
		$tabs['additional_information']['title'] = __( 'Product Data' ); // Rename the additional information tab.

		return $tabs;
	}
	add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );

	/**
	 * Reorder product data tabs
	 *
	 * @param [type] $tabs outputs the tabs on product page.
	 */
	function woo_reorder_tabs( $tabs ) {

		$tabs['reviews']['priority']                = 5; // Reviews first.
		$tabs['description']['priority']            = 10; // Description second.
		$tabs['additional_information']['priority'] = 15; // Additional information third.

		return $tabs;
	}
	add_filter( 'woocommerce_product_tabs', 'woo_reorder_tabs', 98 );

	/**
	 * Customize product data tabs
	 *
	 * @param [type] $tabs outputs the tabs on product page.
	 */
	function woo_custom_description_tab( $tabs ) {
		echo '<h2>Custom Description</h2>';
		echo '<p>Here\'s a custom description</p>';
	}

	/**
	 * Add custom product data tabs
	 *
	 * @param [type] $tabs outputs the tabs on product page.
	 */
	function woo_new_product_tab( $tabs ) {

		// Adds the new tab.

		$tabs['test_tab'] = array(
			'title'    => __( 'New Product Tab', 'woocommerce' ),
			'priority' => 50,
			'callback' => 'woo_new_product_tab_content',
		);

		return $tabs;
	}
	add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
	/**
	 * Callback function for description tab
	 *
	 * @return void
	 */
	function woo_new_product_tab_content() {

		// The new tab content.

		echo '<h2>New Product Tab</h2>';
		echo '<p>Here\'s your new product tab.</p>';

	}

	/**
	 * Check if product has attributes, dimensions or weight to override the call_user_func() expects parameter 1 to be a valid callback error when changing the additional tab
	 *
	 * @param [type] $tabs outputs the tabs on product page.
	 */
	function woo_rename1_tabs( $tabs ) {

		global $product;

		if ( $product->has_attributes() || $product->has_dimensions() || $product->has_weight() ) { // Check if product has attributes, dimensions or weight.
			$tabs['additional_information']['title'] = __( 'Product Data' ); // Rename the additional information tab.
		}
		return $tabs;
	}
	add_filter( 'woocommerce_product_tabs', 'woo_rename1_tabs', 98 );

	/**
	 * Hide category product count in product archives
	 */
	add_filter( 'woocommerce_subcategory_count_html', '__return_false' );

	/**
	 * Remove product content based on category
	 */
	function remove_product_content() {
		// If a product in the 'Cookware' category is being viewed...
		if ( is_product() && has_term( 'Clothing', 'product_cat' ) ) {
			// Remove the images.
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			// For a full list of what can be removed please see woocommerce-hooks.php.
		}
	}
	add_action( 'wp', 'remove_product_content' );


	/**
	 * Auto Complete all WooCommerce orders.
	 *
	 * @param [type] $order_id gives the order id of the specific product.
	 */
	function custom_woocommerce_auto_complete_order( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		$order->update_status( 'processing' );
	}
	add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );

	/**
	 * Add a 1% surcharge to your cart / checkout
	 * change the $percentage to set the surcharge to a value to suit
	 */
	function woocommerce_custom_surcharge() {
		global $woocommerce;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		$percentage = 0.01;
		$surcharge  = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;
		$woocommerce->cart->add_fee( 'Surcharge', $surcharge, true, '' );

	}
	add_action( 'woocommerce_cart_calculate_fees', 'woocommerce_custom_surcharge' );

	/**
	 * Change several of the breadcrumb defaults
	 */
	function jk_woocommerce_breadcrumbs() {
		return array(
			'delimiter'   => ' &#47; ',
			'wrap_before' => '<nav cl
			ass="woocommerce-breadcrumb" itemprop="breadcrumb">',
			'wrap_after'  => '</nav>',
			'before'      => '',
			'after'       => '',
			'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
		);
	}
	//add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs', 20 );

	/**
	 * Add a message above the login / register form on my-account page
	 */
	function jk_login_message() {
		if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
			?>
			<div class="woocommerce-info">
				<p><?php esc_html_e( 'Returning customers login. New users register for next time so you can:' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'View your order history' ); ?></li>
					<li><?php esc_html_e( 'Check on your orders' ); ?></li>
					<li><?php esc_html_e( 'Edit your addresses' ); ?></li>
					<li><?php esc_html_e( 'Change your password' ); ?></li>
				</ul>
			</div>
			<?php
		}
	}
	add_action( 'woocommerce_before_customer_login_form', 'jk_login_message' );

	/**
	 * Apply a coupon for minimum cart total
	 */
	function add_coupon_notice() {

			$cart_total     = WC()->cart->get_subtotal();
			$minimum_amount = 1500;
			$currency_code  = get_woocommerce_currency();
			wc_clear_notices();

		if ( $cart_total < $minimum_amount ) {
				WC()->cart->remove_coupon( 'FIRST_buy' );
				wc_print_notice( "Get &#8377;500 off if you spend more than $minimum_amount $currency_code!", 'notice' );
		} else {
				WC()->cart->apply_coupon( 'FIRST_buy' );
				wc_print_notice( 'You just got &#8377;500 off your order!', 'notice' );
		}
			wc_clear_notices();
	}
	add_action( 'woocommerce_before_cart', 'add_coupon_notice' );
	add_action( 'woocommerce_before_checkout_form', 'add_coupon_notice' );

	/**
	 * Display custom field on the front end
	 *
	 * @since 1.0.0
	 */
	function cfwc_display_custom_field() {
		global $post;
		// Check for the custom field value.
		$product       = wc_get_product( $post->ID );
		$giftwrap      = $product->get_meta( 'include_giftwrap_option' );
		$giftwrap_cost = $product->get_meta( 'giftwrap_cost' );
		$giftwrap_msg  = $product->get_meta( 'include_custom_message' );
		if ( 'yes' === $giftwrap ) {
			// Only display our field if we've got a value for the field title.
			printf(
				'<div class="cfwc-custom-field-wrapper"><input type="checkbox" id="giftwrap-cost-field" name="cfwc-title-field" value="' . $giftwrap_cost . '">Giftwrap this product?(&#8377;%s)</div>',
				esc_html( number_format( (float) $giftwrap_cost, 2 ) )
			);
		}
		if ( 'yes' === $giftwrap_msg ) {
			printf(
				'<div><label>Add a custom message?</label></br><input type="text" id="giftwrap-msg-field" name="cfwc-title-field" value=""></div></br>'
			);
		}
	}
	add_action( 'woocommerce_before_add_to_cart_button', 'cfwc_display_custom_field' );

	/**
	 * Add the text field as item data to the cart object
	 *
	 * @since 1.0.0
	 * @param Array   $cart_item_data Cart item meta data.
	 * @param Integer $product_id Product ID.
	 * @param Integer $variation_id Variation ID.
	 * @param Boolean $quantity Quantity.
	 */
	function cfwc_add_custom_field_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {

		// if ( isset( $_POST['giftwrap-cost-field'] ) ) {
			// Add the item data.
			$cart_item_data['giftwrap_price'] = $_POST['giftwrap-cost-field'];
			// $product        = wc_get_product( $product_id ); // Expanded function.
			// $price          = $product->get_price(); // Expanded function.
			// $cart_item_data['total_price']    = $price + $cart_item_data['giftwrap_price']; // Expanded function.
		// }
		return $cart_item_data;
	}
	add_filter( 'woocommerce_add_cart_item_data', 'cfwc_add_custom_field_item_data', 10, 4 );

	/**
	 * Step 3. Display giftwrap price on the pages: Cart, Checkout, Order Received
	 *
	 * @param [type] $item_data used to store item data.
	 * @param [type] $cart_item used to store item data.
	 */
	function giftwrap_display_field( $item_data, $cart_item ) {

		if ( ! empty( $cart_item['giftwrap_price'] ) ) {
			$item_data[] = array(
				'key'     => 'Giftwrap',
				'value'   => $cart_item['giftwrap_price'],
				'display' => '', // in case you would like to display "value" in another way (for users).
			);
		}

		return $item_data;

	}
	add_filter( 'woocommerce_get_item_data', 'giftwrap_display_field', 10, 2 );

	/**
	 * Custom currency and currency symbol
	 *
	 * @param [type] $currencies is used to store the custom currency.
	 */
	function add_my_currency( $currencies ) {
		$currencies['ABC'] = __( 'Currency name', 'woocommerce' );
		return $currencies;
	}
	// add_filter( 'woocommerce_currencies', 'add_my_currency' );

	/**
	 * Custom currency symbol
	 *
	 * @param [type] $currency_symbol is used to store custom currency symbol.
	 * @param [type] $currency is used to store the custom currency.
	 * @return $currency_symbol.
	 */
	function add_my_currency_symbol( $currency_symbol, $currency ) {
		switch ( $currency ) {
			case 'ABC':
				$currency_symbol = '$';
				break;
		}
		return $currency_symbol;
	}
	// add_filter( 'woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2 );

	/**
	 * Allow shortcodes in product excerpts
	 */
	if ( ! function_exists( 'woocommerce_template_single_excerpt' ) ) {
		/**
		 * Allow shortcodes in product excerpts
		 *
		 * @param [type] $post is used to store the particular post.
		 * @return void
		 */
		function woocommerce_template_single_excerpt( $post ) {
			global $post;
			if ( $post->post_excerpt ) {
				echo '<div itemprop="description">' . do_shortcode( wpautop( wptexturize( $post->post_excerpt ) ) ) . '</div>';
			}
		}
	}

	/**
	 * Send an email each time an order with coupon(s) is completed
	 * The email contains coupon(s) used during checkout process
	 *
	 * @param [type] $order_id is used for the specific order id.
	 */
	function woo_email_order_coupons( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( $order->get_used_coupons() ) {

			$to      = 'youremail@yourcompany.com';
			$subject = 'New Order Completed';
			$headers = 'From: My Name <youremail@yourcompany.com>' . "\r\n";

			$message  = 'A new order has been completed.\n';
			$message .= 'Order ID: ' . $order_id . '\n';
			$message .= 'Coupons used:\n';

			foreach ( $order->get_used_coupons() as $coupon ) {
				$message .= $coupon . '\n';
			}
			@wp_mail( $to, $subject, $message, $headers );
		}
	}
	add_action( 'woocommerce_thankyou', 'woo_email_order_coupons' );

	/**
	 * Goes in theme functions.php or a custom plugin
	 *
	 * Subject filters:
	 *   woocommerce_email_subject_new_order
	 *   woocommerce_email_subject_customer_processing_order
	 *   woocommerce_email_subject_customer_completed_order
	 *   woocommerce_email_subject_customer_invoice
	 *   woocommerce_email_subject_customer_note
	 *   woocommerce_email_subject_low_stock
	 *   woocommerce_email_subject_no_stock
	 *   woocommerce_email_subject_backorder
	 *   woocommerce_email_subject_customer_new_account
	 *   woocommerce_email_subject_customer_invoice_paid
	 *
	 * @param [type] $subject is used to stroe the subject of email.
	 * @param [type] $order is used for specified order.
	 */
	function change_admin_email_subject( $subject, $order ) {
		global $woocommerce;

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$subject = sprintf( '[%s] New Customer Order (# %s) from Name %s %s', $blogname, $order->id, $order->billing_first_name, $order->billing_last_name );

		return $subject;
	}
	add_filter( 'woocommerce_email_subject_new_order', 'change_admin_email_subject', 1, 2 );

	/**
	 * Adjust the quantity input values of simple products
	 *
	 * @param [type] $args used to pass arguments.
	 * @param [type] $product used for particular product.
	 */
	function jk_woocommerce_quantity_input_args( $args, $product ) {
		if ( is_singular( 'product' ) ) {
			$args['input_value'] = 2; // Starting value (we only want to affect product pages, not cart).
		}
		$args['max_value'] = 80; // Maximum value.
		$args['min_value'] = 2; // Minimum value.
		$args['step']      = 2; // Quantity steps.
		return $args;
	}
	add_filter( 'woocommerce_quantity_input_args', 'jk_woocommerce_quantity_input_args', 10, 2 ); // Simple products.
	/**
	 * Adjust the quantity input values of simple products
	 *
	 * @param [type] $args used to pass arguments.
	 */
	function jk_woocommerce_available_variation( $args ) {
		$args['max_qty'] = 80; // Maximum value (variations).
		$args['min_qty'] = 2;  // Minimum value (variations).
		return $args;
	}
	add_filter( 'woocommerce_available_variation', 'jk_woocommerce_available_variation' ); // Variations.

	/**
	 * Function to add a payment gateway
	 *
	 * @return void
	 */
	function woocommerce_gateway_name_init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}
		/**
		 * Localisation
		*/
		load_plugin_textdomain( 'wc-gateway-name', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		/**
		 * Gateway class
		 */
		require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-gateway-epay.php'; // Includin class file for gateway.
		new WC_Gateway_Epay();

		/**
		 * Add the Gateway to WooCommerce
		 *
		 * @param [type] $methods is used to store the custom payment gateway.
		 */
		function woocommerce_add_gateway_name_gateway( $methods ) {
			$methods[] = 'WC_Gateway_Epay';
			return $methods;
		}
		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_name_gateway' );
	}
	add_action( 'plugins_loaded', 'woocommerce_gateway_name_init', 0 );

	/**
	 * Add a new country to countries list
	 *
	 * @param [type] $countries is used to store the country name.
	 */
	function woo_add_my_country( $countries ) {
		$new_countries = array(
			'HIN' => __( 'Hindustan', 'woocommerce' ),
		);

		return array_merge( $countries, $new_countries );
	}
	add_filter( 'woocommerce_countries', 'woo_add_my_country' );

	/**
	 * Undocumented function
	 *
	 * @param [type] $continents is used to store the continent.
	 * @return $continents
	 */
	function woo_add_my_country_to_continents( $continents ) {
		$continents['ASIA']['countries'][] = 'HIN';
		return $continents;
	}
	add_filter( 'woocommerce_continents', 'woo_add_my_country_to_continents' );

	/**
	 * Add a custom field (in an order) to the emails
	 *
	 * @param [type] $fields is used to store fields.
	 * @param [type] $sent_to_admin is for sending the mail to admin.
	 * @param [type] $order is for particular order.
	 */
	function custom_woocommerce_email_order_meta_fields( $fields, $sent_to_admin, $order ) {
		$fields['meta_key'] = array(
			'label' => __( 'Label' ),
			'value' => get_post_meta( $order->id, 'meta_key', true ),
		);
		return $fields;
	}
	add_filter( 'woocommerce_email_order_meta_fields', 'custom_woocommerce_email_order_meta_fields', 10, 3 );

	/**
	 * Allow customers to access wp-admin
	 */
	// add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
	// add_filter( 'woocommerce_disable_admin_bar', '__return_false' );

	/**
	 * Automatically add product to cart on visit
	 */
	function add_product_to_cart() {
		if ( ! is_admin() ) {
			$product_id = 58; // replace with your own product id.
			$found      = false;
			// check if product already in cart.
			if ( count( WC()->cart->get_cart() ) > 0 ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->get_id() === $product_id ) {
						$found = true;
					}
				}
				// if product not found, add it.
				if ( ! $found ) {
					WC()->cart->add_to_cart( $product_id );
				}
			} else {
				// if no products in cart, add it.
				WC()->cart->add_to_cart( $product_id );
			}
		}
	}
	// add_action( 'template_redirect', 'add_product_to_cart' );

	/**
	 * Show product weight on archive pages
	 */
	function rs_show_weights() {

		global $product;
		$weight = $product->get_weight();

		if ( $product->has_weight() ) {
			echo '<div class="product-meta"><span class="product-meta-label">Weight: </span>' . $weight . get_option( 'woocommerce_weight_unit' ) . '</div></br>';
		}
	}
	add_action( 'woocommerce_after_shop_loop_item', 'rs_show_weights', 9 );


	/**
	 * Prevent PO box shipping
	 *
	 * @param [type] $posted is used to store po box address.
	 */
	function deny_pobox_postcode( $posted ) {
		global $woocommerce;

		$address  = ( isset( $posted['shipping_address_1'] ) ) ? $posted['shipping_address_1'] : $posted['billing_address_1'];
		$postcode = ( isset( $posted['shipping_postcode'] ) ) ? $posted['shipping_postcode'] : $posted['billing_postcode'];

		$replace  = array( ' ', '.', ',' );
		$address  = strtolower( str_replace( $replace, '', $address ) );
		$postcode = strtolower( str_replace( $replace, '', $postcode ) );

		if ( strstr( $address, 'pobox' ) || strstr( $postcode, 'pobox' ) ) {
			wc_add_notice( sprintf( __( 'Sorry, we cannot ship to PO BOX addresses.' ) ), 'error' );
		}
	}
	add_action( 'woocommerce_after_checkout_validation', 'deny_pobox_postcode' );

	/**
	 * Notify admin when a new customer account is created
	 *
	 * @param [type] $customer_id is used to store the customer id.
	 */
	function woocommerce_created_customer_admin_notification( $customer_id ) {
		wp_send_new_user_notifications( $customer_id, 'admin' );
	}
	add_action( 'woocommerce_created_customer', 'woocommerce_created_customer_admin_notification' );

	/**
	 * Display product attribute archive links
	 */
	function wc_show_attribute_links() {
		global $post;
		$attribute_names = array( 'pa_color', 'pa_size' ); // Add attribute names here and remember to add the pa_ prefix to the attribute name.

		foreach ( $attribute_names as $attribute_name ) {
			$taxonomy = get_taxonomy( $attribute_name );

			if ( $taxonomy && ! is_wp_error( $taxonomy ) ) {
				$terms       = wp_get_post_terms( $post->ID, $attribute_name );
				$terms_array = array();

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$archive_link = get_term_link( $term->slug, $attribute_name );
						$full_line    = '<a href="' . $archive_link . '">' . $term->name . '</a>';
						array_push( $terms_array, $full_line );
					}
					echo $taxonomy->labels->name . ' ' . implode( $terms_array, ',' );
				}
			}
		}
	}
	add_action( 'woocommerce_product_meta_end', 'wc_show_attribute_links' );
	add_action( 'woocommerce_shop_loop_item_title', 'wc_show_attribute_links' );
	// if you'd like to show it on archive page, replace "woocommerce_product_meta_end" with "woocommerce_shop_loop_item_title".

	/**
	 * Trim zeros in price decimals
	 */
	add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

	/**
	 * Show product dimensions on archive pages for WC 3+
	 */
	function rs_show_dimensions() {
		global $product;
		$dimensions = wc_format_dimensions( $product->get_dimensions( false ) );

		if ( $product->has_dimensions() ) {
			echo '<div class="product-meta"><span class="product-meta-label">Dimensions: </span>' . $dimensions . '</div>';
		}
	}
	add_action( 'woocommerce_after_shop_loop_item', 'rs_show_dimensions', 9 );

	/**
	 * Add or modify States
	 *
	 * @param [type] $states is used to store the name of states.
	 */
	function custom_woocommerce_states( $states ) {

		$states['IN']['IN1'] = 'lko';
			// 'IN1' => 'lko',
			// 'IN2' => 'gzb',

		return $states;
	}
	add_filter( 'woocommerce_states', 'custom_woocommerce_states' );

	/**
	 * Unhook and remove WooCommerce default emails.
	 *
	 * @param mixed $email_class used to store the email class.
	 */
	function unhook_those_pesky_emails( $email_class ) {

			/**
			 * Hooks for sending emails during store events
			 */
			remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
			remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
			remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );

			// New order emails.
			remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

			// Processing order emails.
			remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

			// Completed order emails.
			remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );

			// Note emails.
			remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
	}
	// add_action( 'woocommerce_email', 'unhook_those_pesky_emails' );

	/**
	 * Hide shipping rates when free shipping is available.
	 * Updated to support WooCommerce 2.6 Shipping Zones.
	 *
	 * @param array $rates Array of rates found for the package.
	 * @return array
	 */
	function my_hide_shipping_when_free_is_available( $rates ) {
		$free = array();
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'free_shipping' === $rate->method_id ) {
				$free[ $rate_id ] = $rate;
				break;
			}
		}
		return ! empty( $free ) ? $free : $rates;
	}
	add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );

	/**
	 * Rename a country
	 *
	 * @param mixed $countries used to store the country name.
	 */
	function rename_india( $countries ) {
		$countries['IN'] = 'Bharat';
		return $countries;
	}
	add_filter( 'woocommerce_countries', 'rename_india' );

	/**
	 * Change a currency symbol
	 *
	 * @param mixed $currency_symbol use to store the symbol of the currency.
	 * @param mixed $currency used to store the currency.
	 */
	function change_existing_currency_symbol( $currency_symbol, $currency ) {
		switch ( $currency ) {
			case 'INR':
				$currency_symbol = '$';
				break;
		}
		return $currency_symbol;
	}
	// add_filter( 'woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2 );

	/**
	 * Set a minimum order amount for checkout
	 */
	function wc_minimum_order_amount() {
		// Set this variable to specify a minimum order value.
		$minimum = 510;

		if ( WC()->cart->total < $minimum ) {

			if ( is_cart() ) {

				wc_print_notice(
					sprintf(
						'Your current order total is %s — you must have an order with a minimum of %s to place your order ',
						wc_price( WC()->cart->total ),
						wc_price( $minimum )
					),
					'error'
				);

			} else {

				wc_add_notice(
					sprintf(
						'Your current order total is %s — you must have an order with a minimum of %s to place your order',
						wc_price( WC()->cart->total ),
						wc_price( $minimum )
					),
					'error'
				);

			}
		}
	}
	add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
	add_action( 'woocommerce_before_cart', 'wc_minimum_order_amount' );

	/**
	 * Change number of products that are displayed per page (shop page)
	 *
	 * @param mixed $cols used to store the no. of columns to disply.
	 */
	function new_loop_shop_per_page( $cols ) {
		// $cols contains the current number of products per page based on the value stored on Options -> Reading.
		// Return the number of products you wanna show per page.
		$cols = 3;
		return $cols;
	}
	add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

	/**
	 * Change number or products per row to
	 */
	if ( ! function_exists( 'loop_columns' ) ) {
		/**
		 * Change no. of products per row.
		 *
		 * @return integer
		 */
		function loop_columns() {
			return 2; // 2 products per row
		}
	}
	add_filter( 'loop_shop_columns', 'loop_columns', 999 );

	/**
	 * Set WooCommerce image dimensions upon theme activation
	 *
	 * @param mixed $enqueue_styles is used to enqueue styles.
	 */
	function jk_dequeue_styles( $enqueue_styles ) {
		unset( $enqueue_styles['woocommerce-general'] ); // Remove the gloss.
		unset( $enqueue_styles['woocommerce-layout'] ); // Remove the layout.
		unset( $enqueue_styles['woocommerce-smallscreen'] );// Remove the smallscreen optimisation.
		return $enqueue_styles;
	}
	// Remove each style one by one.
	// add_filter( 'woocommerce_enqueue_styles', 'jk_dequeue_styles' );

	// Or just remove them all in one line.
	// add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );


	/**
	 * Show cart contents / total Ajax
	 *
	 * @param [type] $fragments is uded to fragmnets of page for ajax.
	 */
	function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;

		ob_start();

		?>
		<a class="cart-customlocation" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_html_e( 'View your shopping cart', 'woothemes' ); ?>"><?php echo sprintf( _n( '%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes' ), $woocommerce->cart->cart_contents_count ); ?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
		<?php
		$fragments['a.cart-customlocation'] = ob_get_clean();
		return $fragments;
	}
	add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );

	/**
	 * Hide loop read more buttons for out of stock items
	 */
	if ( ! function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
		/**
		 * Hide loop read more buttons for out of stock items
		 *
		 * @return void
		 */
		function woocommerce_template_loop_add_to_cart() {
			global $product;
			if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
				return;
			}
			wc_get_template( 'loop/add-to-cart.php' );
		}
	}

	/**
	 * Change number of related products output
	 *
	 * @param mixed $args is used to pass the arguments.
	 */
	function jk_related_products_args( $args ) {
		$args['posts_per_page'] = 2; // 4 related products.
		$args['columns']        = 2; // arranged in 2 columns.
		return $args;
	}
	add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );

	/**
	 * Change number of upsells output
	 *
	 * @param mixed $args is used to pass the arguments.
	 */
	function wc_change_number_related_products( $args ) {

		$args['posts_per_page'] = 2;
		$args['columns']        = 2; // change number of upsells here.
		return $args;
	}
	add_filter( 'woocommerce_upsell_display_args', 'wc_change_number_related_products', 20 );

	/**
	 * Change the placeholder image
	 *
	 * @param mixed $src used to store the source of image.
	 */
	function custom_woocommerce_placeholder_img_src( $src ) {
		$upload_dir = wp_upload_dir();
		$uploads    = untrailingslashit( $upload_dir['baseurl'] );
		// replace with path to your image.
		$src = $uploads . '/woocommerce-placeholder.png';

		return $src;
	}
	add_filter( 'woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src' );

	/**
	 * Show product categories in Woorramework breadcrumbs
	 *
	 * @param [type] $trail used for trail.
	 */
	function woo_custom_breadcrumbs_trail_add_product_categories( $trail ) {
		if ( ( get_post_type() === 'product' ) && is_singular() ) {
			global $post;

			$taxonomy = 'product_cat';

			$terms = get_the_terms( $post->ID, $taxonomy );
			$links = array();

			if ( $terms && ! is_wp_error( $terms ) ) {
				$count = 0;
				foreach ( $terms as $c ) {
					$count++;
					if ( $count > 1 ) {
						continue;
					}
					$parents = woo_get_term_parents( $c->term_id, $taxonomy, true, ', ', $c->name, array() );

					if ( '' !== $parents && ! is_wp_error( $parents ) ) {
						$parents_arr = explode( ', ', $parents );

						foreach ( $parents_arr as $p ) {
							if ( '' !== $p ) {
								$links[] = $p;
							}
						}
					}
				}

				// Add the trail back on to the end.
				// $links[] = $trail['trail_end'];
				$trail_end = get_the_title( $post->ID );

				// Add the new links, and the original trail's end, back into the trail.
				array_splice( $trail, 2, count( $trail ) - 1, $links );

				$trail['trail_end'] = $trail_end;
			}
		}

		return $trail;
	}
	// Get breadcrumbs on product pages that read: Home > Shop > Product category > Product Name.
	add_filter( 'woo_breadcrumbs_trail', 'woo_custom_breadcrumbs_trail_add_product_categories', 20 );



	if ( ! function_exists( 'woo_get_term_parents' ) ) {
		/**
		 * Retrieve term parents with separator.
		 *
		 * @param int    $id Term ID.
		 * @param string $taxonomy used to store taxonomy.
		 * @param bool   $link Optional, default is false. Whether to format with link.
		 * @param string $separator Optional, default is '/'. How to separate terms.
		 * @param bool   $nicename Optional, default is false. Whether to use nice name for display.
		 * @param array  $visited Optional. Already linked to terms to prevent duplicates.
		 * @return string
		 */
		function woo_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
			$chain  = '';
			$parent = &get_term( $id, $taxonomy );
			if ( is_wp_error( $parent ) ) {
				return $parent;
			}

			if ( $nicename ) {
				$name = $parent->slug;
			} else {
				$name = $parent->name;
			}

			if ( $parent->parent && ( $parent->parent !== $parent->term_id ) && ! in_array( $parent->parent, $visited, true ) ) {
				$visited[] = $parent->parent;
				$chain    .= woo_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
			}

			if ( $link ) {
				$chain .= '<a  href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s' ), $parent->name ) ) . '">' . $parent->name . '</a>' . $separator;
			} else {
				$chain .= $name . $separator;
			}
			return $chain;
		}
	}


	/**
	 * Remove related products output
	 */
	function remove_related_prod() {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}
	// add_action( 'after_setup_theme', 'remove_related_prod' );


	/**
	 *  Our hooked in function - $address_fields is passed via the filter!
	 *
	 * @param [type] $fields is for the address fields.
	 * @return $address_fields
	 */
	function custom_override_default_address_fields( $fields ) {
		$fields['billing']['billing_address_2']['required'] = true;

		return $fields;
	}
	// Hook in.
	add_filter( 'woocommerce_checkout_fields', 'custom_override_default_address_fields' );

	/**
	 * Changes the redirect URL for the Return To Shop button in the cart.
	 * EVEN THOUGH THIS FUNCTION WOULD NORMALLY RUN LATER BECAUSE IT'S CODED AFTERWARDS, THE 10 PRIORITY IS LOWER THAN 20 ABOVE
	 */
	function wc_empty_cart_redirect_url() {
		return 'https://www.google.com';
	}
	//add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url', 10 );


	/**
	 * Creating phone in the shipping fields
	 *
	 * @param [type] $fields isused to store fields.
	 * @return $fields.
	 */
	function custom_override_checkout_fields( $fields ) {
		$fields['shipping']['shipping_phone'] = array(
			'label'       => __( 'Phone', 'woocommerce' ),
			'placeholder' => _x( 'Phone', 'placeholder', 'woocommerce' ),
			'required'    => false,
			'class'       => array( 'form-row-wide' ),
			'clear'       => true,
		);

		return $fields;
	}
	// Hook in.
	add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

	/**
	 * Display field value on the order edit page
	 *
	 * @param [type] $order is used to specific oreder.
	 */
	function my_custom_checkout_field_display_admin_order_meta( $order ){
		echo '<p><strong>' . esc_html_e( 'Phone From Checkout Form' ) . ':</strong> ' . get_post_meta( $order->get_id(), '_shipping_phone', true ) . '</p>';
	}
	add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );


	/**
	 * Add the field to the checkout
	 */
	function my_custom_checkout_field( $checkout ) {

		echo '<div id="my_custom_checkout_field"><h2>' . esc_html_e( 'My Field' ) . '</h2>';

		woocommerce_form_field(
			'my_field_name',
			array(
				'type'        => 'text',
				'class'       => array( 'my-field-class form-row-wide' ),
				'label'       => __( 'Fill in this field' ),
				'placeholder' => __( 'Enter something' ),
			),
			$checkout->get_value( 'my_field_name' )
		);

		echo '</div>';

	}
	add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );

	/**
	 * Process the checkout
	 */
	function my_custom_checkout_field_process() {
		// Check if set, if its not set add an error.
		if ( ! $_POST['my_field_name'] )
			wc_add_notice( __( 'Please enter something into this new shiny field.' ), 'error' );
	}
	add_action( 'woocommerce_checkout_process', 'my_custom_checkout_field_process' );

	/**
	 * Update the order meta with field value
	 *
	 * @param mixed $order_id is used to store the order id.
	 */
	function my_custom_checkout_field_update_order_meta( $order_id ) {
		if ( ! empty( $_POST['my_field_name'] ) ) {
			update_post_meta( $order_id, 'My Field', sanitize_text_field( wp_unslash( $_POST['my_field_name'] ) ) );
		}
	}
	add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );


	/**
	 * Add back to store button on WooCommerce cart page
	 *
	 * @return void
	 */
	function themeprefix_back_to_store() {
		?>
	<a class="button wc-backward" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"> <?php esc_html_e( 'Return to shop', 'woocommerce' ); ?> </a>

		<?php
	}
	// add_action( 'woocommerce_cart_coupon', 'themeprefix_back_to_store' );
	// add_action( 'woocommerce_cart_actions', 'themeprefix_back_to_store' );

	/**
	 * Creating custom shipping class
	 */
	function your_shipping_method_init() {
		if ( ! class_exists( 'WC_Your_Shipping_Method' ) ) {
			/**
			 * Custom shipping class.
			 */
			require TPWCP_PLUGIN_DIR_PATH . '/classes/class-wc-your-shipping-method.php'; // Including class file.
			new WC_Your_Shipping_Method();
		}
	}

	add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );
	/**
	 * Adding shipping method
	 *
	 * @param [type] $methods use dto add shipping methods.
	 * @return $methods
	 */
	function add_your_shipping_method( $methods ) {
		$methods['your_shipping_method'] = 'WC_Your_Shipping_Method';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );


}


