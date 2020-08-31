<?php
/**
 * Plugin Name: Hello World!
 * Description: This is just a test.
 * Author: brij1234
 * Version: 1.0
 */

function custom_mu_plugin_hello_world() {
	echo '<p>Hello World!</p>';
}
add_action( 'woocommerce_after_shop_loop_item', 'custom_mu_plugin_hello_world' );
