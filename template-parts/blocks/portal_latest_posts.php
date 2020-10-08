<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Neueste Blogbeiträge</div>';
};

if (get_field('active')) {

    $slider_tags = get_field('tag');
    if (!empty($slider_tags)) {
        $tag_list = '';
        foreach ($slider_tags as $tag) {
            $tag_list .= $tag->name . ',';
        }
    }

    $slider_cats = get_field('category');
    $cat_list = [];
    if (!empty($slider_cats)) {
        foreach ($slider_cats as $cat) {
            array_push($cat_list, $cat->term_id);
        }
    }

    $count = -1;
    if (get_field('count')) {
        $count = get_field('count');
    }

    $args = array(
        'posts_per_page' => $count,
        'tag' => $tag_list,
        'category__in' => $cat_list,
        'post_status' => 'publish'
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) :


        echo '<div class="portal_block">';
        if (!empty($block['anchor'])) {
            echo '<a name="' . $block['anchor'] . '"></a>';
        }
        if (!empty(get_field('headline'))){
            echo '<h3>' . get_field('headline') . '</h3>';
        } else {
            echo '<h3>Blogbeiträge</h3>';
        }

        if (get_field('as_list')) { ?>
            <div class="portal_latest_posts_block">
                <div class="portal_latest_posts_list"><?php
                    while ($query->have_posts()) :
                        $query->the_post();
                        ?>

                        <div class="portal_latest_posts_list_content">
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo get_the_post_thumbnail_url(); ?>">
                            </a>
                            <div class="portal_latest_posts_list_content_text">
                                <a href="<?php the_permalink(); ?>"><h5><?php the_title(); ?></h5></a>
                                <?php the_excerpt(); ?>
                            </div>
                        </div>

                    <?php endwhile; ?>

                </div>
            </div>
            <?php

        } else {
            $sliderId = uniqid('slider-');
            $slidesToShow = 3;
            $slidesToScroll = 1;
            if (get_field('slidesToShow')) {
                $slidesToShow = get_field('slidesToShow');
            }
            if (get_field('slidesToScroll')) {
                $slidesToScroll = get_field('slidesToScroll');
            }

            echo '<div class="portal_latest_posts_block">';
            echo '<div class="portal_latest_posts_slider" id="' . $sliderId . '">';
            while ($query->have_posts()) : $query->the_post();
                ?>
                <div>
                    <div class="portal_latest_posts_slider_content">
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php echo get_the_post_thumbnail_url(); ?>">
                        </a>
                        <div class="portal_latest_posts_slider_content_text">
                            <a href="<?php the_permalink(); ?>"><h6><?php the_title(); ?></h6></a>
                            <?php the_excerpt(); ?>
                        </div>
                        <div class="portal_latest_posts_slider_content_bottom">
                            <a href="<?php the_permalink(); ?>" class="button primary small" target="_blank">Zum Beitrag</a>
                        </div>
                    </div>
                </div>
            <?php
            endwhile;
            echo '</div>';
            echo '</div>';

            ?>
            <script type="text/javascript">
                jQuery(function () {
                    // Handler for .ready() called. Put the Slick Slider etc. init code here.
                    function loadBlogSlider() {
                        if (typeof jQuery().slick === "function") {
                            jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                                infinite: false,
                                slidesToShow: <?php echo $slidesToShow; ?>,
                                slidesToScroll: <?php echo $slidesToScroll; ?>,
                                arrows: true,
                                dots: true,
                                zIndex: 0,
                                responsive: [
                                    {
                                        breakpoint: 950,
                                        settings: {
                                            slidesToShow: 2,
                                            slidesToScroll: 2
                                        }
                                    },
                                    {
                                        breakpoint: 750,
                                        settings: {
                                            slidesToShow: 1,
                                            slidesToScroll: 1
                                        }
                                    }
                                ]
                            });
                        }
                    }

                    loadBlogSlider();
                });
            </script>
            <?php
        }
        echo '</div>';

    endif;
    wp_reset_query();
}

?>


<?php if (is_admin()) {
    echo '</div>';
}; ?>
