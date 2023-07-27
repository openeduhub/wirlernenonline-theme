<?php
/*
Template Name: Pressespiegel
Template Post Type: page
 */

get_header();
?>
<?php
while (have_posts()) : the_post(); ?>

    <div class="wlo-page">

        <div class="wlo-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-header-content">
                    <h1><?php the_title(); ?></h1>
                    <?php the_excerpt(); ?>
                </div>
                <div class="wlo-header-bottom"></div>
            </div>
        </div>

        <div class="wlo-wrapper">

            <?php the_content(); ?>

            <div class="wlo-blog-wrapper">

                <div class="wlo-blog-mobile-menu">
                    <button class="wlo-blog-mobile-button wlo-blog-mobile-button-active" id="wlo-blog-news-button">
                        <img class="wlo-blog-mobile-button-img" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg">
                        Pressespiegel
                    </button>
                    <button class="wlo-blog-mobile-button" id="wlo-blog-themen-button">
                        <img class="wlo-blog-mobile-button-img" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg">
                        Pressemitteilungen
                    </button>
                </div>

                <div class="wlo-blog-news">
                    <h3>Pressespiegel</h3>
                    <div class="wlo-blog-tiles" id="wlo-blog-news-mobile">
                        <?php
                        $query = new WP_Query(array(
                            'post_type' => 'presse',
                            'post_status' => 'publish',
                            'orderby'     => 'date',
                            'order'       => 'DESC',
                            'posts_per_page' => -1,
                        ));
                        if ( $query->have_posts() ):
                            while($query -> have_posts()) : $query -> the_post(); ?>
                                <div class="wlo-blog-tile">
                                    <?php $image = get_field('thumbnail');
                                    if( !empty( $image ) ):?>
                                        <img class="main-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                                    <?php endif; ?>
                                    <div class="wlo-blog-tile-content">
                                        <h3><?php echo get_the_title(); ?></h3>
                                        <p><?php echo get_field('short_description'); ?></p>
                                        <a class="wlo-blog-button" href="<?php echo esc_url( get_field('url') ); ?>">
                                            Zum Pressebeitrag
                                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                        </a>
                                    </div>

                                </div>

                            <?php endwhile;
                        endif;

                        // Added this now
                        wp_reset_query() ;
                        ?>
                    </div>
                </div>

                <div class="wlo-blog-themen">
                    <h3>Pressemitteilungen</h3>
                    <div class="wlo-blog-tiles" id="wlo-blog-themen-mobile">
                        <?php
                        $query = new WP_Query(array(
                            'category_name' => 'pressemitteilungen',
                            'post_status' => 'publish',
                            'post_type' => 'post',
                            'posts_per_page' => -1,
                        ));
                        if ( $query->have_posts() ):
                            while($query -> have_posts()) : $query -> the_post(); ?>
                                <div class="wlo-blog-tile">
                                    <div class="wlo-blog-tile-content">
                                        <h3><?php the_title(); ?></h3>
                                        <p><?php echo get_the_excerpt(); ?></p>
                                        <a class="wlo-blog-button" href="<?php echo esc_url( get_permalink() ); ?>">
                                            zum Blogbeitrag
                                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile;
                        endif;

                        // Added this now
                        wp_reset_query() ;
                        ?>
                    </div>
                </div>


            </div>


        </div>

    </div>

    <script>
        jQuery( "#wlo-blog-news-button" ).click(function() {
            jQuery('#wlo-blog-news-button').toggleClass('wlo-blog-mobile-button-active');
            jQuery('#wlo-blog-themen-button').toggleClass('wlo-blog-mobile-button-active');
            jQuery('#wlo-blog-news-button .wlo-blog-mobile-button-img').show('slow');
            jQuery('#wlo-blog-themen-button .wlo-blog-mobile-button-img').hide('slow');
            jQuery('#wlo-blog-news-mobile').fadeIn();
            jQuery('#wlo-blog-themen-mobile').fadeOut();
        });

        jQuery( "#wlo-blog-themen-button" ).click(function() {
            console.log('themen');
            jQuery('#wlo-blog-news-button').toggleClass('wlo-blog-mobile-button-active');
            jQuery('#wlo-blog-themen-button').toggleClass('wlo-blog-mobile-button-active');
            jQuery('#wlo-blog-themen-button .wlo-blog-mobile-button-img').show('slow');
            jQuery('#wlo-blog-news-button .wlo-blog-mobile-button-img').hide('slow');
            jQuery('#wlo-blog-news-mobile').fadeOut();
            jQuery('#wlo-blog-themen-mobile').fadeIn();
        });
    </script>

<?php
endwhile;
get_footer();
