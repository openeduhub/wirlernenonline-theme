<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Banner</div>';
};


/* Filter Values prioritized GET/POST > Block-Setting > Portal-Setting */
/*
* collectionUrl
* disciplines
* educationalContexts
* intendedEndUserRoles
* oer
*/
/* ------------------------------------------------------------------- */
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$backgroundColor = '#003E82';
if ( get_field('background_color', $postID)){
   $backgroundColor = get_field('background_color', $postID);
}


// echo '<pre style="background-color: lightgrey">' , var_dump($educational_filter_values) , '</pre>';
// echo '<script>console.log(' , json_encode($educational_filter_values) , ')</script>';

$collectionUrl = $educational_filter_values["collectionUrl"];
$disciplines = $educational_filter_values["disciplines"];
$educationalContexts = $educational_filter_values["educationalContexts"];
$intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
$oer = $educational_filter_values["oer"];
/* ------------------------------------------------------------------- */


$count = 5;
if (get_field('count')) {
    $count = intval(get_field('count'));
}

//Category
$category_id = get_cat_ID('Themenportal');

$count = -1;
if (get_field('count')) {
    $count = get_field('count');
}

$meta_query_args = array(
    'relation' => 'AND'
);

if (!empty($disciplines)) {
    $meta_query_args_disciplines = array(
        'relation' => 'OR'
    );
    foreach ($disciplines as $discipline) {
        $meta_query_args_disciplines[] = array(
            'key' => 'discipline',
            'value' => '"'.$discipline.'"',
            'compare' => 'LIKE'
        );
    }
    $meta_query_args[] = $meta_query_args_disciplines;
}


if (!empty($educationalContexts) && false) {   // deactivated!
    $meta_query_args_educationalContexts = array(
        'relation' => 'OR'
    );
    foreach ($educationalContexts as $educationalContext) {
        $meta_query_args_educationalContexts[] = array(
            'key' => 'educationalContext',
            'value' => $educationalContext,
            'compare' => 'LIKE'
        );
    }
    $meta_query_args[] = $meta_query_args_educationalContexts;
}
if (!empty($intendedEndUserRoles)) {
    $meta_query_args_intendedEndUserRoles = array(
        'relation' => 'OR'
    );
    foreach ($intendedEndUserRoles as $intendedEndUserRole) {
        $meta_query_args_intendedEndUserRoles[] = array(
            'key' => 'intendedEndUserRole',
            'value' => $intendedEndUserRole,
            'compare' => 'LIKE'
        );
    }
    $meta_query_args[] = $intendedEndUserRole;
}

$args = array(
    'posts_per_page' => $count,
    'cat' => $category_id,
    'post_status' => 'publish',
    'meta_query' => $meta_query_args
);
$query = new WP_Query($args);

if ($query->have_posts()) :
    $sliderId = uniqid('slider-');
    echo '<div class="portal_block">';
    if (!empty($block['anchor'])) {
        echo '<a name="' . $block['anchor'] . '"></a>';
    }
    echo '<div class="portal_banner_slider" id="' . $sliderId . '">';
        while ($query->have_posts()) :
            $query->the_post();
            $bannerUrl = get_permalink();
            if (get_field('url', get_the_ID())){
                $bannerUrl = get_field('url', get_the_ID());
            }
            ?>
            <div>
                <div class="portal_banner_slider_content" style="background: <?php echo $backgroundColor; ?>">
                    <div>
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                    </div>
                    <div class="portal_banner_slider_content_text">
                        <h3><?php the_title(); ?></h3>
                        <p><?php the_excerpt(); ?></p>
                        <div class="text-left">
                            <a href="<?php echo $bannerUrl; ?>" target="_blank" class="button">Zum Inhalt</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        endwhile;
        echo '</div>';?>
    <script type="text/javascript">
        jQuery(function() {
            // Handler for .ready() called. Put the Slick Slider etc. init code here.
            function loadBannerSlider(){
                if (typeof jQuery().slick === "function") {
                    jQuery('#<?php echo $sliderId ?>').not('.slick-initialized').slick({
                        infinite: true,
                        slidesToShow: 1,
                        autoplay: true,
                        arrows: false,
                        dots: true
                    });
                }
            }
            loadBannerSlider();
        });
    </script>
    <?php
    echo '</div>';
endif;
wp_reset_query();

?>

<?php if (is_admin()) {
    echo '</div>';
}; ?>
