<?php
/*
Template Name: WLO News
Template Post Type: post, page, portal
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

            <?php
            $blog_query_args = array(
                'posts_per_page'	=> 3,
                'post_type' => 'post',
                'post_status' => 'publish',
                'orderby'     => 'date',
                'order'       => 'DESC',
                'category_name' => 'wlo-in-eigener-sache,wlo-news,themenartikel',
            );
            $blog_query = new WP_Query( $blog_query_args );
            ?>

            <hr class="wlo-spacer" style="margin: 1em auto;">

            <h3>Blog</h3>

            <?php
            if ($blog_query->have_posts()) : ?>

            <div class="wlo-news-content-wrapper content">

                <?php
                while ($blog_query->have_posts()) :
                    $blog_query->the_post(); ?>

                    <div class="widget-content">
                        <img class="main-image" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="">
                        <div class="content-info">
                            <div class="content-header">
                                <p class="content-source"><?php echo get_the_author(); ?></p>
                                <div class="badge ">Blog-Beitrag</div>
                            </div>
                            <div class="content-title"><?php echo get_the_title(); ?></div>
                            <p class="content-description"><?php echo get_the_excerpt(); ?></p>
                            <a class="content-button" href="<?php the_permalink(); ?>" aria-label="Zum-Blogbeitrag: <?php echo get_the_title(); ?>">Zum Blogbeitrag</a>
                        </div>
                    </div>


                <?php endwhile; ?>

                <div class="wlo-news-more-tile">
                    <div class="wlo-news-more-tile-text">
                        Besuche unseren Blog für mehr Blogbeiträge
                    </div>
                    <a class="wlo-button" href="<?php echo get_page_link(7); ?>" aria-label="Zum-Blog: <?php echo get_the_title(); ?>">
                        Zum Blog
                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg"  alt="">
                    </a>

                </div>

            </div>

            <?php endif;
            wp_reset_query();
            ?>

            <hr class="wlo-spacer" style="margin: 1em auto;">


            <?php
            $press_query_args = array(
                'posts_per_page'	=> 3,
                'post_type' => 'presse',
                'post_status' => 'publish',
                'orderby'     => 'date',
                'order'       => 'DESC',
            );
            $press_query = new WP_Query( $press_query_args );
            ?>

            <h3>Presse</h3>
            <?php
            if ($press_query->have_posts()) : ?>

                <div class="wlo-news-content-wrapper content">

                    <?php
                    while ($press_query->have_posts()) :
                        $press_query->the_post(); ?>

                        <div class="widget-content">
                            <?php
                            $image = get_field('thumbnail');
                            if( !empty( $image ) ):?>
                                <img class="main-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                            <?php endif; ?>

                            <div class="content-info">
                                <div class="content-header">
                                    <div class="badge ">Pressebeitrag</div>
                                </div>
                                <div class="content-title"><?php echo get_the_title(); ?></div>
                                <p class="content-description"><?php echo get_field('short_description'); ?></p>
                                <a class="content-button" href="<?php echo get_field('url'); ?>" aria-label="Zum Pressebeitrag">Zum Pressebeitrag</a>
                            </div>
                        </div>


                    <?php endwhile; ?>

                    <div class="wlo-news-more-tile">
                        <div class="wlo-news-more-tile-text">
                            Besuche unsere Presseseite für mehr Informationen
                        </div>
                        <a class="wlo-button" href="<?php echo get_page_link(34744); ?>" aria-label="Zur-Presseseite: <?php echo get_the_title(); ?>">
                            Zur Presseseite
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg"  alt="">
                        </a>

                    </div>

                </div>

            <?php endif;
            wp_reset_query();
            ?>

        </div>

    </div>


<?php
endwhile;
get_footer();
