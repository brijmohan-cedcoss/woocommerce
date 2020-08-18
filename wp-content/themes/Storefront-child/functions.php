<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Setting parent style as the dependancy in the child theme.
 */
function theme_enqueue_styles() {
	$parent_style = 'parent-style';
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array(), '2.5.8' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

/**
 * Creating Custom post type named Books
 */
function custom_post_type_book() {
	$labels = array(
		'name'               => __( 'Books', 'storefront-child-theme' ),
		'singular_name'      => __( 'Book', 'storefront-child-theme' ),
		'add_new'            => 'Add Book',
		'all_items'          => 'All Books',
		'add_new_item'       => 'Add New Book',
		'edit_item'          => 'Edit Book',
		'new_item'           => 'New Book',
		'view_item'          => 'View Book',
		'search_item'        => 'Search Book',
		'not_found'          => 'No Books Found',
		'not_found_in_trash' => 'No Books Found In Trash',
	);

	register_post_type(
		'book',
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
add_action( 'init', 'custom_post_type_book' );

/**
 * Custom Taxonomy for custom post type book.
 */
function custom_taxonomy_post_type_book() {
	$labels = array(
		'name'              => 'Genres',
		'singular_name'     => 'Genre',
		'search_items'      => 'Search Genre',
		'all_items'         => 'All Genres',
		'parent_item'       => 'Parent Genre',
		'parent_item_colon' => 'Parent Genre:',
		'edit_item'         => 'Edit Genre',
		'update_item'       => 'Update Genre',
		'add_new_item'      => 'Add New Genre',
		'new_item_name'     => 'New Genre Name',
		'menu_name'         => 'Genre',
	);

	// Registering custom category taxanomy.
	register_taxonomy(
		'genre',
		array(
			'book',
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

	// Registering custom tag taxonomy.
	register_taxonomy(
		'book_tags',
		array(
			'book',
		),
		array(
			'hierarchical'      => false,
			'label'             => 'Tag',
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug' => 'tag',
			),
		),
	);
}
add_action( 'init', 'custom_taxonomy_post_type_book' );

