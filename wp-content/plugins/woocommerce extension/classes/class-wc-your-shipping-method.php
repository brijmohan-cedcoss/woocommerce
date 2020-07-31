<?php
/**
 * Class to create a custom shipping method
 *
 * @package WooCommerce/Templates
 */
class WC_Your_Shipping_Method extends WC_Shipping_Method {
	/**
	 * Constructor for your shipping class
	 *
	 * @param mixed $instance_id used to store instance.
	 * @access public
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'your_shipping_method'; // Id for your shipping method. Should be uunique.
		$this->method_title       = __( 'Your Shipping Method' );  // Title shown in admin.
		$this->method_description = __( 'Description of your shipping method' ); // Description shown in admin.
		$this->instance_id        = absint( $instance_id );
		$this->enabled            = 'yes'; // This can be added as an setting but for this example its forced enabled.
		$this->title              = 'My Shipping Method'; // This can be added as an setting but for this example its forced.
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		$this->init();
	}

	/**
	 * Init your settings
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		// Load the settings API.
		$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings.
		$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

		// Define user set variables.
		$this->title = $this->get_option( 'title' );
		$this->cost  = $this->get_option( 'cost' );

		// Save settings in admin if you have any defined.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}
	/**
	 * Creating form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$this->instance_form_fields = array(

			'enabled' => array(
				'title'       => __( 'Enable' ),
				'type'        => 'checkbox',
				'description' => __( 'Enable this shipping method.' ),
				'default'     => 'yes',
			),

			'title'   => array(
				'title'       => __( 'Title' ),
				'type'        => 'text',
				'description' => __( 'Title to be displayed on site' ),
				'default'     => __( 'Request a Quote' ),
			),

			'cost'    => array(
				'title'       => __( 'Cost' ),
				'type'        => 'text',
				'description' => __( 'Enter cost for your shipping method' ),
				'desc_tip'    => true,
			),

		);

	}

	/**
	 * Calculate_shipping function.
	 *
	 * @access public
	 * @param mixed $package used to store package.
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$rate = array(
			'id'       => $this->id,
			'label'    => $this->title,
			'cost'     => $this->cost,
			'calc_tax' => 'per_item',
		);

		// Register the rate.
		$this->add_rate( $rate );
	}
}
