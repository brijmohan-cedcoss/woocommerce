<?php
/**
 * Plugin Name: Woo-Mailchimp
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Mailchimp Integration to wooCommerce.
 * Version: 1.0.0
 * Author: brij1234
 * Author URI: http://yourdomain.com/
 * Developer: brij1234
 * Developer URI: http://yourdomain.com/
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocomerce extension
 */

// for MailChimp API v3.0.

require 'MailChimp.php';  // path to API wrapper downloaded from GitHub.

use \DrewM\MailChimp\MailChimp;

function storeAddress() {

	$key     = 'd338c3a163c3684c5b1bee646fa7a578-us17';
	$list_id = '68c239642e';

	$merge_vars = array(
		'FNAME'     => $_POST['fname'],
		'LNAME'     => $_POST['lname'],
	);

	$mc = new MailChimp($key);

	// add the email to your list.
	$result = $mc->post( '/lists/' . $list_id . '/members',
		array(
			'email_address' => $_POST['email'],
			'merge_fields'  => $merge_vars,
			'status'        => 'pending',  // double opt-in
			// 'status'     => 'subscribed'  // single opt-in
		)
	);

	return json_encode( $result );

}

// If being called via ajax, run the function, else fail.

if ( $_POST['ajax'] ) {
	echo storeAddress(); // send the response back through Ajax.
} else {
	echo 'Method not allowed - please ensure JavaScript is enabled in this browser';
}
