<?php
/**
 * Class to create a payment gateway.
 *
 * @package WooCommerce/Templates
 * @version 2.1.0
 */
class WC_Gateway_Epay extends WC_Payment_Gateway {
	/**
	 * Construct function
	 */
	public function __construct() {
		$this->id           = 'other_payment';
		$this->method_title = __( 'Custom Payment', 'woocommerce-other-payment-gateway' );
		$this->title        = __( 'Custom Payment', 'woocommerce-other-payment-gateway' );
		$this->has_fields   = true;
		$this->init_form_fields();
		$this->init_settings();
		$this->enabled     = $this->get_option( 'enabled' );
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}
	/**
	 * Function to create form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'     => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-other-payment-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Custom Payment', 'woocommerce-other-payment-gateway' ),
				'default' => 'yes',
			),
			'title'       => array(
				'title'       => __( 'Method Title', 'woocommerce-other-payment-gateway' ),
				'type'        => 'text',
				'description' => __( 'This controls the title', 'woocommerce-other-payment-gateway' ),
				'default'     => __( 'Custom Payment', 'woocommerce-other-payment-gateway' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Customer Message', 'woocommerce-other-payment-gateway' ),
				'type'        => 'textarea',
				'css'         => 'width:500px;',
				'default'     => 'None of the other payment options are suitable for you? please drop us a note about your favourable payment option and we will contact you as soon as possible.',
				'description' => __( 'The message which you want it to appear to the customer in the checkout page.', 'woocommerce-other-payment-gateway' ),
			),
		);
	}

	/**
	 * Function for payment processing.
	 *
	 * @param mixed $order_id stores the specific order id.
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		// Mark as on-hold (we're awaiting the payment).
		$order->update_status( 'on-hold', __( 'Awaiting offline payment', 'wc-gateway-offline' ) );

		// Reduce stock levels.
		$order->reduce_order_stock();

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
