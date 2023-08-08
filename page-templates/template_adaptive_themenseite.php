<?php
/*
Template Name: WLO-Adaptive-Themenseite
Template Post Type: post, page, portal
 */

get_header();
?>

<div class="portal">
    <?php get_template_part('template-parts/fachportal/header-bar'); ?>
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
