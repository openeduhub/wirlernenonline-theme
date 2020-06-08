<?php // WP_Query arguments
$posts_per_page = get_field('posts_per_page');

$args = array(
	'post_type'       => array( 'post' ),
	'posts_per_page'  => $posts_per_page,
	'order'			  => 'DESC',
	'post_status'     => 'publish',
);

$category = get_field('cat');
if ($category){
    $cats = '';
    foreach ($category as $cat){
        $cats .= $cat->term_id.',';
    }
    $args['cat'] = $cats;
}

// The Query
$query = new WP_Query( $args );

// The Loop
if ( $query->have_posts() ) { ?>
	<div class="grid-container">
		<div class="grid-x grid-margin-x small-up-1 medium-up-2 large-up-4">
			<?php while ( $query->have_posts() ) {
				$query->the_post(); ?>
				<?php get_template_part('template-parts/post/content'); ?>
			<?php } ?>
		</div>
	</div>
<?php } else {
	// no posts found
}

// Restore original Post Data
wp_reset_postdata();
