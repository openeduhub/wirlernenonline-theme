<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: News</div>';
} ?>

<?php

$newsItems = 6;
if (get_field('newsItems')){
    $newsItems = get_field('newsItems');
}

$news_query_args = array(
    'posts_per_page'	=> $newsItems,
    'post_type' => 'post',
    'post_status' => 'publish',
    'orderby'     => 'date',
    'order'       => 'DESC',
    'category_name' => 'wlo-in-eigener-sache,wlo-news',
);
$news_query = new WP_Query( $news_query_args );

if ($news_query->have_posts()) : ?>
<div class="wlo-news-wrapper">

    <?php
    while ($news_query->have_posts()) :
        $news_query->the_post();

    ?>

        <div class="wlo-news">
            <div class="wlo-news-title"><?php echo get_the_title(); ?></div>
            <div class="wlo-news-text">
                <?php //echo get_the_date( 'd.m.Y' ); ?>
                <p>
                    <?php echo get_the_excerpt(); ?>
                    <a href="<?php echo get_permalink(); ?>">[weiterlesen]</a>
                </p>
            </div>
        </div>

    <?php endwhile; ?>
</div>
<?php endif;
wp_reset_query();
?>

<?php if (is_admin()) {
    echo '</div>';
} ?>
