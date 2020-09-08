<?php 
/**
 * The template for displaying all single posts
 *
 * @package test-theme
 */

get_header();
?>

<?php
    $genre = get_terms(
        array(
            'taxonomy' => 'book_genre',
            'hide_empty' => false,
        )
    );
    if ( ! empty( $genre ) && is_array( $genre ) ) {
        foreach ( $genre as $category ) {
            $term_img = get_term_meta( $category->term_id, 'term_img', true );
            $term_text = get_term_meta( $category->term_id, 'term_custom_text', true );
            ?>
            <p><?php echo $category->name; ?><img src="<?php echo $term_img; ?>" width="60px" height="60px"></p>
            <?php
        }
    } 

    $args= array(
        'post_type' => 'cpt_books',
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ){
            $query->the_post();
            ?>
            <div>
                <div>
                    <div>
                        <?php the_post_thumbnail(); ?>
                    </div>
                    <div>
                        <span><?php echo get_the_date(); ?></span>
                        <a href="<?php echo get_permalink(); ?>"><h2><?php the_title(); ?></h2></a>
                        <div>
                            <p>By <a href="#"><?php the_author(); ?></a> | 
                            <?php $terms = wp_get_post_terms( $query->post->ID, 'book_genre', array( 'fields' => 'all' ) );
                            foreach ( $terms as $term) { ?>
                            <a href="<?php echo( get_term_link($term->slug, 'book_genre') ); ?>"><?php echo $term->name; ?></a> <?php } ?>| 
                            <a href="#"><?php the_time(); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            wp_reset_postdata();
        }
    }

get_sidebar();
get_footer();
