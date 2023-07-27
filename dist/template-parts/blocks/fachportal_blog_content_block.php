<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenseite: Blogbeiträge</div>';
} ?>
<?php

require_once(get_template_directory().'/functions/wlo-config.php');



$headline = 'Überschrift';
if(!empty(get_field('headline'))){
    $headline = get_field('headline');
}

$blockIcon = get_field('icon')['url'];

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

$sliderId = uniqid('slider-');
$slidesToShow = 4;
$slidesToScroll = 4;
if (get_field('slidesToShow')) {
    $slidesToShow = get_field('slidesToShow');
}
if (get_field('slidesToScroll')) {
    $slidesToScroll = get_field('slidesToScroll');
}
$showSliderDots = 'true';
if ($query->post_count <= 4){
    $showSliderDots = 'false';
}

?>

<div class="fachportal-content-block">

    <div class="header">
        <?php if(!empty($blockIcon)){?>
            <img class="header-icon" src="<?php echo $blockIcon ?>" alt=""/>
        <?php }?>
        <h2>
            <?php echo $headline; ?>
        </h2>
        <?php if(!empty(get_field('descrText'))){?>
            <p><?php echo get_field('descrText') ?></p>
        <?php }?>
    </div>

    <?php if ($query->have_posts()) : ?>

    <div class="content" id="<?php echo $sliderId; ?>">

    <?php
        while ($query->have_posts()) :
        $query->the_post(); ?>

        <div class="widget-content">
            <img class="main-image" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="">
            <div class="content-info">
                <div class="content-header">
                    <p class="content-source"><?php echo get_the_author(); ?></p>
                    <div class="badge ">Blog-Beitrag</div>
                </div>
                <div class="content-title"><?php echo get_the_title(); ?></div>
                <p class="content-description"><?php echo get_the_excerpt(); ?></p>
                <a class="content-button" href="<?php the_permalink(); ?>" aria-label="Zum-Beitrag: <?php echo get_the_title(); ?>">Zum Beitrag</a>
            </div>
        </div>

    <?php endwhile; ?>

    </div>

    <script type="text/javascript">
        jQuery(function () {
            // Handler for .ready() called. Put the Slick Slider etc. init code here.
            function loadWLOSlider() {
                if (typeof jQuery().slick === "function") {
                    jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                        infinite: false,
                        slidesToShow: <?php echo $slidesToShow; ?>,
                        slidesToScroll: <?php echo $slidesToScroll; ?>,
                        arrows: true,
                        dots:  <?php echo $showSliderDots; ?>,
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

            loadWLOSlider();

            jQuery(window).on('resize', function(){
                jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
            });
        });

    </script>


    <?php endif;
    wp_reset_query();
    ?>


</div>



<?php if (is_admin()) {
    echo '</div>';
} ?>
