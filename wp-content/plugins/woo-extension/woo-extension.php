<?php
/**
 * Plugin Name: Woo-Extension
 * Description: This is an extension for Woocommerce regarding the task given to me.
 * Version: 1.0.0
 * Author: brij1234
 * Developer: brij1234
 * Text Domain: woo-extension
 *
 * @package woocomerce extension
 */

/**
 * Checking if Woocommerce plugin is active or not, if active then only this extension will work.
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	// Defining own path constant.
	if ( ! defined( 'WOO_PLUGIN_DIR_PATH' ) ) {
		define( 'WOO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Function to add custom field in General tab of Products.
	 */
	function add_custom_rating_field() {

		echo '<div class="option_group">';

		woocommerce_wp_text_input(
			array(
				'id'          => 'custom_rating',
				'label'       => __( 'Ratings', 'woo-extension' ),
				'type'        => 'number',
				'description' => __( 'Enter Rating for the Product on a Scale of 1 to 5', 'woo-extension' ),
				'desc_tip'    => true,
				'value'       => get_post_meta( get_the_ID(), 'custom_rating', true ),
			)
		);

		echo '</div>';
	}
	add_action( 'woocommerce_product_options_general_product_data', 'add_custom_rating_field' );

	/**
	 * Function to save the Ratings field data.
	 *
	 * @param int $id specifies the product id.
	 */
	function save_custom_rating_field( $id ) {

		if ( ! empty( $_POST['custom_rating'] ) ) {
			update_post_meta( $id, 'custom_rating', $_POST['custom_rating'] );
		}
	}
	add_action( 'woocommerce_process_product_meta', 'save_custom_rating_field', 10, 1 );

	/**
	 * Function to display ratings on shop archive page.
	 */
	function display_custom_rating_field() {

		$rating = get_post_meta( get_the_ID(), 'custom_rating', true );
		$count  = 1;

		echo '<div class="product-meta>"';
		while ( $count <= 5 ) {
			if ( $rating >= $count ) {
				echo '<span>&#11088</span>';
			}
			$count++;
		}
		echo '</div>';
	}
	add_action( 'woocommerce_after_shop_loop_item', 'display_custom_rating_field' );

	/**
	 * Function to create custom data tab in product data section.
	 *
	 * @param array $tabs is an array of all setting tabs in product data section.
	 */
	function custom_print_name_on_back_tab( $tabs ) {

		$tabs['print_name_on_back'] = array(
			'label'    => __( 'Print Name On Back', 'woo-extension' ),
			'target'   => 'print_on_back',
			'class'    => array( 'show_if_simple' ),
			'priority' => 80,
		);
		return $tabs;
	}
	add_action( 'woocommerce_product_data_tabs', 'custom_print_name_on_back_tab' );

	/**
	 * Function to populate custom tab (Print Name On Back)
	 */
	function populate_print_name_on_back_tab() {

		echo '<div id="print_on_back" class="panel woocommerce_options_panel">';

		woocommerce_wp_checkbox(
			array(
				'id'          => 'enable_print_name_on_back_option',
				'label'       => __( 'Enable/Disable', 'woo-extension' ),
				'description' => __( 'Enable this option to show Print Name on back option', 'woo-extension' ),
				'desc_tip'    => true,
			)
		);

		echo '</div>';
	}
	add_action( 'woocommerce_product_data_panels', 'populate_print_name_on_back_tab' );

	/**
	 * Function to save field values of custom tab (Print Name On Back).
	 *
	 * @param int $id specifies the product id.
	 */
	function save_print_name_on_back_tab_fields( $id ) {

		$enable = isset( $_POST['enable_print_name_on_back_option'] ) ? 'yes' : 'no';

		update_post_meta( $id, 'enable_print_name_on_back_option', $enable );

	}
	add_action( 'woocommerce_process_product_meta', 'save_print_name_on_back_tab_fields' );

	/**
	 * Function to display Print Name on Back on Single product page.
	 */
	function display_print_name_on_back_option() {

		$option = get_post_meta( get_the_ID(), 'enable_print_name_on_back_option', true );

		if ( 'yes' === $option ) {
			echo '<div class="form-row">
					<input type="text" id="name_to_print" name="name_to_print" maxlength="10" placeholder="Enter Name To Print(10 characters)">
				</div>';
		}
	}
	add_action( 'woocommerce_before_add_to_cart_button', 'display_print_name_on_back_option' );

	/**
	 * Function to add name to print text to cart item.
	 *
	 * @param array $cart_item_data an array containing cart item data for this product.
	 *
	 * @return array
	 */
	function add_name_to_print_cart_item( $cart_item_data ) {

		$text_to_print = $_POST['name_to_print'];

		if ( empty( $text_to_print ) ) {
			return $cart_item_data;
		}

		$cart_item_data['name_to_print'] = $text_to_print;
		return $cart_item_data;
	}
	add_filter( 'woocommerce_add_cart_item_data', 'add_name_to_print_cart_item', 10, 1 );

	/**
	 * Function to display custom name to print text in cart
	 *
	 * @param array $item_data is an array containing additional data for the cart item.
	 * @param array $cart_item is an array of cart item and associated data.
	 *
	 * @return array
	 */
	function display_name_to_print_incart( $item_data, $cart_item ) {

		if ( empty( $cart_item['name_to_print'] ) ) {
			return $item_data;
		}

		$item_data[] = array(
			'key'   => __( 'Name To Print', 'woo-extension' ),
			'value' => $cart_item['name_to_print'],
		);

		return $item_data;
	}
	add_filter( 'woocommerce_get_item_data', 'display_name_to_print_incart', 10, 2 );

}
