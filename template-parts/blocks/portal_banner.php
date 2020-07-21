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
            'value' => $discipline,
            'compare' => 'LIKE'
        );
    }
    $meta_query_args[] = $meta_query_args_disciplines;
}


if (!empty($educationalContexts)) {
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
    echo '<div class="portal_banner_slider">';
    while ($query->have_posts()) : $query->the_post();
        ?>
        <div>
            <div class="portal_banner_slider_content">
                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                <div class="portal_banner_slider_content_text">
                    <h3><?php the_title(); ?></h3>
                    <p><?php the_excerpt(); ?></p>
                    <div class="text-right">
                        <a href="<?php the_permalink(); ?>" class="button">Weiterlesen</a>
                    </div>
                </div>
            </div>
        </div>

    <?php
    endwhile;

    ?>

    <?php
    echo '</div>';
endif;
wp_reset_query();

?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.portal_banner_slider').slick({
            autoplay: true,
            arrows: false,
            dots: true
        });
    });
</script>
<?php if (is_admin()) {
    echo '</div>';
}; ?>
