<?php
/**
 * Class to create a payment gateway.
 *
 * @package WooCommerce/Templates
 * @version 1.0.0
 */
class WC_Gateway_CPG extends WC_Payment_Gateway {
	/**
	 * Construct function
	 */
	public function __construct() {
		$this->id                 = 'custom_payment_gateway';
		$this->method_title       = __( 'Custom Payment Gateway', 'woocommerce-cpg' );
		$this->title              = __( 'Custom Payment Gateway', 'woocommerce-cpg' );
		$this->method_description = __( 'Custom payment Gateway for Credit card processing', 'woocommerce-cpg' );
		$this->has_fields         = true;

		// Method with all the options fields.
		$this->init_form_fields();

		// Load the settings, Store all settings in a single database entry.
		$this->init_settings();
		$this->enabled         = $this->get_option( 'enabled' );
		$this->title           = $this->get_option( 'title' );
		$this->description     = $this->get_option( 'description' );

		// This action hook saves the settings.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

	}
	/**
	 * Function to create form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'              => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-cpg' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Custom Payment', 'woocommerce-cpg' ),
				'default' => 'yes',
			),
			'title'                => array(
				'title'       => __( 'Gateway Title', 'woocommerce-cpg' ),
				'type'        => 'text',
				'description' => __( 'This controls the title', 'woocommerce-cpg' ),
				'default'     => __( 'Custom Payment', 'woocommerce-cpg' ),
				'desc_tip'    => true,
			),
			'description'          => array(
				'title'       => __( 'Customer Message', 'woocommerce-cpg' ),
				'type'        => 'textarea',
				'css'         => 'width:500px;',
				'default'     => 'None of the other payment options are suitable for you? please drop us a note about your favourable payment option and we will contact you as soon as possible.',
				'description' => __( 'The message which you want it to appear to the customer in the checkout page.', 'woocommerce-cpg' ),
			),

		);
	}

	/**
	 * Funcrtion to create custom Payment Gateway form.
	 */
	public function payment_fields() {

		if ( $this->description ) {
			echo wpautop( wp_kses_post( $this->description ) );
		}
		// It will echo the form.
		echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

		// Add this action hook if you want your custom payment gateway to support it.
		do_action( 'woocommerce_credit_card_form_start', $this->id );

		// It is recommended to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc.
		echo '<div class="form-row form-row-wide">
				<label>Card Number <span class="required">*</span></label>
				<input id="cpg_card_number" type="text" autocomplete="off">
			</div>
			<div class="form-row form-row-first">
				<label>Expiry Date <span class="required">*</span></label>
				<input id="cpg_exp_date" type="text" autocomplete="off" placeholder="MM / YY">
			</div>
			<div class="form-row form-row-last">
				<label>Card Code (CVV) <span class="required">*</span></label>
				<input id="cpg_cvv_number" type="password" autocomplete="off" placeholder="CVC">
			</div>
			<div class="clear"></div>';

		do_action( 'woocommerce_credit_card_form_end', $this->id );

		echo '<div class="clear"></div></fieldset>';

	}

	/**
	 * Function to validate credit card form fields.
	 */
	public function validate_fields() {

		if ( empty( $_POST['cpg_card_number'] ) ) {
			wc_add_notice( 'Credit Card No. is Required!', 'error' );
			return false;
		} elseif ( empty( $_POST['cpg_exp_date'] ) ) {
			wc_add_notice( 'Expiry date is Required!', 'error' );
			return false;
		} elseif ( empty( $_POST['cpg_cvv_number'] ) ) {
			wc_add_notice( 'CVV No. is Required!', 'error' );
			return false;
		}

		return true;

	}

}
