<?php
/**
 * Template fro custom taxonomy
 */
get_header();
?>

<?php 
// echo get_query_var( 'term' );
// echo get_query_var( 'taxonomy' );
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
//print_r( $term );
$term_img= get_term_meta( $term->term_id, 'term_img', true );
$term_text = get_term_meta( $term->term_id, 'term_custom_text', true );
//echo '<img src="' . $term_img . '">';
?>
<div class="wrapper">
  <div class="primary-content">
    <h1 class="archive-title">Genre: <?php echo  $term->name; ?></h1>

    
    <div class="archive-description">
      <img src="<?php  echo $term_img; ?>" width="120px" height="120px">
      <p><?php echo $term->description; ?></p>
    </div>
    <?php

    if ( have_posts() ){
        while ( have_posts() ) {
            the_post();
      ?>

    <div>
      <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
      <div class="content clearfix">
        <div class="post-info">
          <p><?php the_time(' F j, Y'); ?> by <?php the_author(); ?></p>
        </div>
        <div class="entry">
          <?php the_content(); ?>
        </div>
      </div>
    </div><!--// end #post-XX -->

    <?php 
        }
    } 
    ?>

    <div class="navigation clearfix">
      <div class="alignleft"><?php next_posts_link('« Previous Entries') ?></div>
      <div class="alignright"><?php previous_posts_link('Next Entries »') ?></div>
    </div>

  <div class="secondary-content">
    <?php get_sidebar(); ?>
  </div>
</div>
</div>
<?php get_footer(); ?>