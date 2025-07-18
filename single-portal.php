<?php
if (defined('PORTAL_REDIRECT_URL') && !empty(PORTAL_REDIRECT_URL)) {
    $collection_url = get_field('collection_url');
    $url_components = parse_url($collection_url);
    parse_str($url_components['query'], $params);
    $collectionID = $params['id'];
    $new_themenseite_url = 'https://repository.staging.openeduhub.net/search/de/template?collectionId=' . $collectionID;
    wp_redirect($new_themenseite_url);
    exit;
}
get_header();
?>

<?php while (have_posts()) : the_post(); ?>
    <div class="portal">
        <?php get_template_part('template-parts/fachportal/header-bar'); ?>
        <?php get_template_part('template-parts/fachportal/header'); ?>
        <div class="portal-wrapper portal-wrapper-fachseite">
            <div class="portal-wrapper-left">
                <?php the_content(); ?>
            </div>
            <?php get_template_part('template-parts/fachportal/portal-wrapper-right'); ?>
        </div>
    </div>
<?php endwhile;

get_footer();
