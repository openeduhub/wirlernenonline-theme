<?php
/*
Template Name: WLO-Adaptive-Themenseite
Template Post Type: post, page, portal
 */

get_header();

// Get collection ID.
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);
$collectionUrl = $educational_filter_values["collectionUrl"];
$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionId = $params['id'];

?>

<div class="portal">
    <?php get_template_part(
        'template-parts/fachportal/header-bar',
        args: [
            'collectionId' => $collectionId,
            'appendPostElement' => true,
        ]
    ); ?>
    <?php get_template_part('template-parts/fachportal/header'); ?>
    <?php get_template_part('template-parts/adaptive-themenseite/page-variables-bar'); ?>
    <div class="portal-wrapper portal-wrapper-fachseite">
        <div class="portal-wrapper-left">
            <?php while (have_posts()) : the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; ?>
        </div>
        <?php get_template_part('template-parts/fachportal/portal-wrapper-right'); ?>
    </div>
</div>
<?php get_footer();
