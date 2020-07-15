<?php
/**
 * Class to create addinational settings product panel
 *
 * @package Sports
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Sports_Admin' ) ) {

	/**
	 * Class to create additional product panel in admin
	 */
	class Sports_Admin {

		/**
		 * Construct function
		 */
		public function __construct() {
		}

		/**
		 * Funtion to initialze the tab
		 *
		 * @return void
		 */
		public function init() {
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'create_sports_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'display_sports_fields' ) );
			add_action( 'woocommerce_product_data_meta', array( $this, 'save_fields' ) );
		}

		/**
		 * Function to create the custom tab
		 *
		 * @param [type] $tabs displayes the custom tab.
		 * @return $tabs
		 */
		public function create_sports_tab( $tabs ) {
			$tabs['sports'] = array(
				'label'    => __( 'Sports', 'sports' ),
				'target'   => 'sports_panel',
				'class'    => array( 'sports_tab', 'show_if_downloadable', 'show_if_virtual', 'show_if_variable' ),
				'priority' => 70,
			);
			return $tabs;
		}

		/**
		 * Function to display fields of custom tab.
		 *
		 * @return void
		 */
		public function display_sports_fields() { ?>

			<div id='sports_panel' class='panel woocommerce_options_panel'>
				<div class="options_group">
				<?php
				woocommerce_wp_text_input(
					array(
						'id'          => 'sports_item',
						'label'       => __( 'Sports Item', 'sports' ),
						'type'        => 'text',
						'desc_tip'    => true,
						'description' => __( 'Add sports item here' ),
					)
				);
				woocommerce_wp_textarea_input(
					array(
						'id'          => 'sports_description',
						'label'       => __( 'Sports Item Description', 'sports' ),
						'type'        => '',
						'desc_tip'    => true,
						'description' => __( 'Enter the description of this product', 'sports' ),
					)
				);
				?>
				</div>
			</div>

				<?php
		}

		/**
		 * Function to save the field values.
		 *
		 * @param [type] $post_id save fields of that particular post.
		 * @return void
		 */
		public function save_fields( $post_id ) {

			$product = wc_get_product( $post_id );

			$sports_item = isset( $_POST['sports_item'] ) ? 'yes' : 'no';
			$product->update_meta_data( 'sports_item', sanitize_text_field( $sports_item ) );

			$sports_desc = isset( $_POST['sports_description'] ) ? 'yes' : 'no';
			$product->update_meta_data( 'sports_description', sanitize_text_field( $sports_desc ) );

			$product->save();

		}
	}
}
