<?php
/**
 * Plugin Name: Woo-Mailchimp using wp_remote*
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
 * Function to add users to MailChimp list.
 *
 * @param [int] $user_id stores the user id of newly registered user.
 */
function add_users_to_mailchimp_list( $user_id ) {

	$api_key = 'd338c3a163c3684c5b1bee646fa7a578-us17';
	$list_id = '68c239642e';

	$new_user   = get_userdata( $user_id );
	$user_email = $new_user->user_email;

	$member_id   = md5( strtolower( $user_email ) );
	$data_center = 'us17';
	$url         = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members';

	$user_nickname = get_user_meta( $user_id, 'nickname', true );

	$body = array(
		'email_address' => $user_email,
		'status'        => 'subscribed',
		'merge_fields'  => array(
			'FNAME' => $user_nickname,
			'LNAME' => '',
		),
	);

	$args = array(
		'method'    => 'POST',
		'timeout'   => 10,
		'sslverify' => false,
		'headers'   => array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
		),
		'body' => json_encode( $body ),
	);

	$request = wp_remote_post( $url, $args );
	//print_r($request);

}
add_action( 'user_register', 'add_users_to_mailchimp_list', 10, 1 );
