<?php
/**
 * Plugin Name: Woo-Mailchimp
 * Description: Adding Subscribers to MailChimp list Using API key.
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
 * Function to add subscribers to Mailchimp list.
 *
 * @return string
 */
function add_subscriber_to_mailchimp() {

	$api_key = 'd338c3a163c3684c5b1bee646fa7a578-us17';
	$list_id = '68c239642e';

	$member_id   = md5( strtolower( $_POST['email'] ) );
	$data_center = 'us17';
	$url         = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . $member_id;

	$json = json_encode(
		array(
			'email_address' => $_POST['email'],
			'status'        => 'subscribed',
			'merge_fields'  => array(
				'FNAME' => $_POST['username'],
			),
		)
	);

	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt(
		$ch,
		CURLOPT_HTTPHEADER,
		array(
			'Content-Type: application/json',
			'Authorization: Basic ' . base64_encode( 'user:' . $api_key ),
		)
	);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );

	$result    = curl_exec( $ch );
	$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

	if ( $result === false ) {
		echo 'Error no:' . curl_errno( $ch ) . '<br> Error message' . curl_error( $ch );
	}

	curl_close( $ch );

	return $http_code . $result;

}

add_action( 'user_register', 'add_subscriber_to_mailchimp' );
