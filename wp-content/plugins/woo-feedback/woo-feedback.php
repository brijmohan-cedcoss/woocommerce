<?php
/**
 * Plugin Name: Woo-Feedback Form
 * Description: Adding Custom Tab to Woocommerce settings to manage feedback form fields.
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

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * Defining path constant
	 */
	if ( ! defined( 'WOO_FEEDBACK_DIR_PATH' ) ) {
		define( 'WOO_FEEDBACK_DIR_PATH', plugin_dir_path( __FILE__ ) );
	}

	require WOO_FEEDBACK_DIR_PATH . '/classes/class-wc-settings-feedback-tab.php'; // Including the class file.
	/**
	 * Function to redirect to home page if feedback form not enabled
	 */
	function redirect_feedback() {
		$enable = get_option( 'wc_feedback_form_enable' );

		if ( is_page( 233 ) ) {
			if ( 'no' === $enable ) {
				wp_safe_redirect( esc_url( home_url() ) );
				echo 'Feedback from not enabled';
			}
		}
	}
	add_action( 'template_redirect', 'redirect_feedback' );

	/**
	 * Custom shortcode for contact us/ feedback form
	 *
	 * @param mixed $params user-defined attributes for the shortcode tag.
	 * @param mixed $content contains the output of the shortcode.
	 * @return $content
	 */
	function feedback_form_shortcode( $params, $content = 'null' ) {

		$first_field      = get_option( 'wc_feedback_form_first_field' );
		$first_field_type = get_option( 'wc_feedback_form_first_field_type' );

		$second_field      = get_option( 'wc_feedback_form_second_field' );
		$second_field_type = get_option( 'wc_feedback_form_second_field_type' );

		$third_field      = get_option( 'wc_feedback_form_third_field' );
		$third_field_type = get_option( 'wc_feedback_form_third_field_type' );

		$fourth_field      = get_option( 'wc_feedback_form_fourth_field' );
		$fourth_field_type = get_option( 'wc_feedback_form_fourth_field_type' );

		$atts = shortcode_atts(
			array(

				'label1' => $first_field,
				'type1'  => $first_field_type,
				'name1'  => $first_field,

				'label2' => $second_field,
				'type2'  => $second_field_type,
				'name2'  => $second_field,

				'label3' => $third_field,
				'type3'  => $third_field_type,
				'name3'  => $third_field,

				'label4' => $fourth_field,
				'type4'  => $fourth_field_type,
				'name4'  => $fourth_field,
			),
			$params,
		);

		$content .= '<form action=" " id="feedback_form" method="post" >';

		if ( '' !== $atts['label1'] && '' !== $atts['type1'] ) {
			$content .= '<label>' . $atts['label1'] . ' :</label>
						<input type="' . $atts['type1'] . '"  name="' . $atts['name1'] . '" id="first_field"></br></br>';
		}

		if ( '' !== $atts['label2'] && '' !== $atts['type2'] ) {
			$content .= '<label>' . $atts['label2'] . ' :</label>
			<input type="' . $atts['type2'] . '"  name="' . $atts['name2'] . '" id="second_field"></br></br>';
		}

		if ( '' !== $atts['label3'] && '' !== $atts['type3'] ) {
			$content .= '<label>' . $atts['label3'] . ' :</label>
			<input type="' . $atts['type3'] . '"  name="' . $atts['name3'] . '" id="third_field"></br></br>';
		}

		if ( '' !== $atts['label4'] && '' !== $atts['type4'] ) {
			$content .= '<label>' . $atts['label4'] . ' :</label>
			<input type="' . $atts['type4'] . '"  name="' . $atts['name4'] . '" id="fourth_field"></br></br>';
		}

		$content .= '<textarea name="user_query" id="user_query" placeholder="Give Your Feedback"></textarea></br></br>
					<button type="submit" class="button alt" name="submit_feedback" id="submit_feedback">Submit</button>';

		$content .= '</form>';

		return $content;
	}
	add_shortcode( 'feedback_form', 'feedback_form_shortcode' );


	/**
	 * Enqueue and localize script.
	 */
	function ajax_script_enqueue() {

		// Enqueue script on the frontend.
		wp_enqueue_script(
			'ajax-script-enqueue',
			plugins_url( '/js/ajax-script.js', __FILE__ ),
			array( 'jquery' ),
			'1.0.0',
			true,
		);

		// The wp_localize_script allows us to output the ajax_url path for our script to use.
		wp_localize_script(
			'ajax-script-enqueue',
			'ajax_script_obj',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ajax-nonce' ),
			),
		);
	}
	add_action( 'wp_enqueue_scripts', 'ajax_script_enqueue' );

	/**
	 * Function to check for key values
	 *
	 * @param string $value is the key value.
	 * @return string
	 */
	function check_field_values( $value ) {

		if ( get_option( 'wc_feedback_form_first_field' ) === $value ) {
			$key = get_option( 'wc_feedback_form_first_field' );
		} elseif ( get_option( 'wc_feedback_form_second_field' ) === $value ) {
			$key = get_option( 'wc_feedback_form_second_field' );
		} elseif ( get_option( 'wc_feedback_form_third_field' ) === $value ) {
			$key = get_option( 'wc_feedback_form_third_field' );
		} elseif ( get_option( 'wc_feedback_form_fourth_field' ) === $value ) {
			$key = get_option( 'wc_feedback_form_fourth_field' );
		}

		return $key;

	}

	/**
	 * Ajax request handler
	 *
	 * @return void
	 */
	function ajax_handler_function() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( 'Nonce value cannot be verified.' );
		}

		$first_value      = isset( $_POST['1st_field'] ) ? sanitize_text_field( wp_unslash( $_POST['1st_field'] ) ) : '';
		$first_value_attr = isset( $_POST['1st_field_attr'] ) ? sanitize_text_field( wp_unslash( $_POST['1st_field_attr'] ) ) : '';

		$second_value      = isset( $_POST['2nd_field'] ) ? sanitize_text_field( wp_unslash( $_POST['2nd_field'] ) ) : '';
		$second_value_attr = isset( $_POST['2nd_field_attr'] ) ? sanitize_text_field( wp_unslash( $_POST['2nd_field_attr'] ) ) : '';

		$third_value      = isset( $_POST['3rd_field'] ) ? sanitize_text_field( wp_unslash( $_POST['3rd_field'] ) ) : '';
		$third_value_attr = isset( $_POST['3rd_field_attr'] ) ? sanitize_text_field( wp_unslash( $_POST['3rd_field_attr'] ) ) : '';

		$fourth_value      = isset( $_POST['4th_field'] ) ? sanitize_text_field( wp_unslash( $_POST['4th_field'] ) ) : '';
		$fourth_value_attr = isset( $_POST['4th_field_attr'] ) ? sanitize_text_field( wp_unslash( $_POST['4th_field_attr'] ) ) : '';

		$query = isset( $_POST['message'] ) ? sanitize_text_field( wp_unslash( $_POST['message'] ) ) : '';

		$key1 = check_field_values( $first_value_attr );
		$key2 = check_field_values( $second_value_attr );
		$key3 = check_field_values( $third_value_attr );
		$key4 = check_field_values( $fourth_value_attr );

		if ( is_user_logged_in() ) {

			$user_id  = get_current_user_id();
			$userdata = get_userdata( $user_id );
			$to       = $userdata->user_email;
			$subject  = 'Feedback D
			etails Confirmation';
			$message  = '<h3>This is the Confirmation mail, do not reply. Following are your details</h3></br>
						<p>' . $key1 . ' : ' . $first_value . '</p></br>
						<p>' . $key2 . ' : ' . $second_value . '</p></br>
						<p>' . $key3 . ' : ' . $third_value . '</p></br>
						<p>' . $key4 . ' : ' . $fourth_value . '</p></br>
						<p>' . 'Feedback :' . $query . '</p>';

			$header = array( 'Content-Type: text/html; charset=UTF-8' );

			$admin_email   = get_bloginfo( 'admin_email' );
			$subject_admin = 'Feedback from a Customer';
			$message_admin = '<h3>This is the Feedback from Customer with user id #' . $user_id . ' </h3></br>
							<h4>Follwing are the details of the customer</h4></br>
							<p>' . $key1 . ' : ' . $first_value . '</p></br>
							<p>' . $key2 . ' : ' . $second_value . '</p></br>
							<p>' . $key3 . ' : ' . $third_value . '</p></br>
							<p>' . $key4 . ' : ' . $fourth_value . '</p></br>
							<p>' . 'Feedback :' . $query . '</p>';

			$meta_values = array(
				$key1     => $first_value,
				$key2     => $second_value,
				$key3     => $third_value,
				$key4     => $fourth_value,
				'message' => $query,
			);

			foreach ( $meta_values as $keys => $values ) {
				$mail = filter_var( $values, FILTER_VALIDATE_EMAIL );

				if ( $mail ) {
					wp_mail( $mail, $subject, $message, $header ); // Sending mail on the email address which was filled in the form.
				}
			}

			update_user_meta( $user_id, 'feedback_details', $meta_values ); // updating details of feedback in usermeta.

			wp_mail( $to, $subject, $message, $header ); // mail to user.

			wp_mail( $admin_email, $subject_admin, $message_admin, $header ); // mail to admin.

			echo 'Thank you for your feedback!! <br> A copy of your feedback details has been sent to your mail as well as the admin';

		} else {
			echo 'User Not Registered, Register first!';
		}

		die();

	}
	add_action( 'wp_ajax_nopriv_ajax_request_call', 'ajax_handler_function' );
	add_action( 'wp_ajax_ajax_request_call', 'ajax_handler_function' );

	/**
	 * Function to set gmail as SMTP
	 *
	 * @param [type] $phpmailer is the object of PHPmailer class.
	 */
	function phpmailer_gmail_setup( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host       = 'smtp.gmail.com';
		$phpmailer->SMTPAuth   = true; // Ask it to use authenticate using the Username and Password properties.
		$phpmailer->Port       = 25;
		$phpmailer->Username   = 'brijmohan11.1996@gmail.com';
		$phpmailer->Password   = '';
		$phpmailer->SMTPSecure = 'tls'; // Choose 'ssl' for SMTPS on port 465, or 'tls' for SMTP+STARTTLS on port 25 or 587.

	}
	add_action( 'phpmailer_init', 'phpmailer_gmail_setup' );

}
