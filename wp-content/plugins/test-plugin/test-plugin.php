<?php
/**
 * Plugin Name: Test Plugin
 * Description: This is just a test plugin
 * Version: 1.0.0
 * Author: brij1234
 * Author URI: http://yourdomain.com/
 * Developer: brij1234
 * Text Domain: test-plugin
 * Domain Path: /languages
 *
 * @package custom
 */

/**
 * Activate th plugin
 */
function test_activate_plugin() {
	test_custom_post_type_books();
	test_custom_taxonomy_books();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'test_activate_plugin' );

/**
 * Creating Custom post type named Books
 */
function test_custom_post_type_books() {

	$labels = array(
		'name'               => __( 'Books', 'test-plugin' ),
		'singular_name'      => __( 'Book', 'test-plugin' ),
		'add_new'            => __( 'Add Book', 'test-plugin' ),
		'all_items'          => __( 'All Books', 'test-plugin' ),
		'add_new_item'       => __( 'Add New Book', 'test-plugin' ),
		'edit_item'          => __( 'Edit Book', 'test-plugin' ),
		'new_item'           => __( 'New Book', 'test-plugin' ),
		'view_item'          => __( 'View Book', 'test-plugin' ),
		'search_item'        => __( 'Search Book', 'test-plugin' ),
		'not_found'          => __( 'No Books Found', 'test-plugin' ),
		'not_found_in_trash' => __( 'No Books Found In Trash', 'test-plugin' ),
	);

	register_post_type(
		'cpt_books',
		array(
			'labels'              => $labels,
			'public'              => true,
			'has_archive'         => true,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'show_in_rest'        => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-book-alt',
			'description'         => 'It is a custom post type for books.',
			'supports'            => array(
				'title',
				'comments',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
				'author',
			),
			'menu_position'       => 5,
			'exclude_from_search' => false,
			'rewrite'             => array(
				'slug' => 'book',
			),
		)
	);
}
add_action( 'init', 'test_custom_post_type_books' );

/**
 * Custom taxonomy for custom post type
 */
function test_custom_taxonomy_books() {

	$labels = array(
		'name'          => __( 'Genres', 'test-plugin' ),
		'singular_name' => __( 'Genre', 'test-plugin' ),
		'search_items'  => __( 'Search Genre', 'test-plugin' ),
		'all_items'     => __( 'All Genres', 'test-plugin' ),
		'parent_item'   => __( 'Parent Genre', 'test-plugin' ),
		'edit_item'     => __( 'Edit Genre', 'test-plugin' ),
		'update_item'   => __( 'Update Genre', 'test-plugin' ),
		'add_new_item'  => __( 'Add New Genre', 'test-plugin' ),
		'new_item_name' => __( 'New Genre Name', 'test-plugin' ),
		'menu_name'     => __( 'Genre', 'test-plugin' ),
	);

	// Hierarchical taxonomy.
	register_taxonomy(
		'book_genre',
		array(
			'cpt_books',
		),
		array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'genre',
			),
		),
	);
}
add_action( 'init', 'test_custom_taxonomy_books' );

/**
 * Function to add custom fields to custom taxonomy
 */
function test_custom_fields_taxonomy() {
	?>
	<div class="form-field">
		<label><?php esc_html_e( 'Custom Text Field', 'test-plugin' ); ?> </label>
		<input type="text" id="custom_text_field" name="custom_text_field" value="">
		<p class="description"><?php esc_html_e( 'Enter any text', 'test-plugin' ); ?></p></br>
		<label><?php esc_html_e( 'Upload an Image', 'test-plugin' ); ?></label>
		<div id="img_thumbnail" style="float: left; margin-right: 10px;">
			<img src=" " width="60px" height="60px"/>
		</div>
		<input type="text" id="custom_image_field" name="custom_image_field" value="">
		<input type="button" id="upload_img" class="button" value="Upload an Image">
	</div>
	<?php
}
add_action( 'book_genre_add_form_fields', 'test_custom_fields_taxonomy' );

/**
 * Saving term meta for created terms
 *
 * @param int $term_id is the id of the term created.
 * @param int $term_tax_id is the term taxnomony id of the term created.
 */
function test_save_term_meta_taxonomy( $term_id, $term_tax_id ) {
	if ( isset( $_POST['custom_text_field'] ) && '' !== $_POST['custom_text_field'] ) {
		$text = sanitize_text_field( $_POST['custom_text_field'] );
		add_term_meta( $term_id, 'term_custom_text', $text, true );
	}
	if ( isset( $_POST['custom_image_field'] ) && '' !== $_POST['custom_image_field'] ) {
		$img = esc_url_raw( $_POST['custom_image_field'] );
		add_term_meta( $term_id, 'term_img', $img, true );
	}
}
add_action( 'created_book_genre', 'test_save_term_meta_taxonomy', 10, 2 );

/**
 * Create custom field for term edit
 *
 * @param mixed  $term is the object of the current edited term.
 * @param string $taxonomy is the taxonomy of thre current edited term.
 */
function test_edit_custom_fields_taxonomy( $term, $taxonomy ) {
	$term_text_custom = get_term_meta( $term->term_id, 'term_custom_text', true );
	$term_img         = get_term_meta( $term->term_id, 'term_img', true );
	?>
	<tr class="form-field">
		<th><label><?php esc_html_e( 'Custom Text Field', 'test-plugin' ); ?> </label></th>
		<td><input type="text" id="custom_text_field" name="custom_text_field" value="<?php echo $term_text_custom; ?>">
		<p class="description"><?php esc_html_e( 'Enter any text', 'test-plugin' ); ?></p></td>
	</tr>
	<tr>
		<th><label><?php esc_html_e( 'Upload an Image', 'test-plugin' ); ?></label></th>
		<td>
			<input type="text" id="custom_image_field" name="custom_image_field" value="<?php echo $term_img; ?>">
			<input type="button" id="upload_img" class="button" value="Upload an Image">
			<input type="button" id="remove_img" class="button" value="Remove Image">
		</td>
		<td id="img_thumbnail" style="float: left; margin-right: 10px;">
			<img src="<?php echo $term_img; ?>" width="60px" height="60px"/>
		</td>
	</tr>
	<?php
}
add_action( 'book_genre_edit_form_fields', 'test_edit_custom_fields_taxonomy', 10, 2 );

/**
 * Saving/updating term meta for edited terms
 *
 * @param int $term_id is the id of the term created.
 * @param int $term_tax_id is the term taxnomony id of the term created.
 */
function test_update_term_meta_taxonomy( $term_id, $term_tax_id ) {
	if ( isset( $_POST['custom_text_field'] ) && '' !== $_POST['custom_text_field'] ) {
		$text = sanitize_text_field( $_POST['custom_text_field'] );
		update_term_meta( $term_id, 'term_custom_text', $text );
	}
	if ( isset( $_POST['custom_image_field'] ) ) {
		$img = esc_url_raw( $_POST['custom_image_field'] );
		update_term_meta( $term_id, 'term_img', $img );
	}
}
add_action( 'edited_book_genre', 'test_update_term_meta_taxonomy', 10, 2 );

/**
 * Enqueue scripts for media
 */
function test_enqueue_scripts() {

	wp_enqueue_media();

	wp_register_script( 'test_media_script', plugins_url( '/js/img-upload.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
	wp_enqueue_script( 'test_media_script' );
}
add_action( 'admin_enqueue_scripts', 'test_enqueue_scripts' );
