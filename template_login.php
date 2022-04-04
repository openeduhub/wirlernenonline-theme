<?php /* Template Name: Login */
get_header();?>
<div class="gutenberg-layout-container">
    <div class="grid-x grid-margin-x">
        <div class="medium-12 cell">
            <h1><?php the_title(); ?></h1>
        </div>
    </div>
    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;


    if (is_user_logged_in()) { ?>
        <div class="wp-block-buttons aligncenter wlo-redaktion-btn">
            <div class="wp-block-button"><a class="wp-block-button__link has-text-color has-very-light-gray-color has-background has-very-dark-gray-background-color" href="https://wirlernenonline.de/intranet-aktuelles-lernen/">Zur Redaktionsumgebung</a></div>
        </div>
    <?php };
    ?>
</div>

<?php get_footer(); ?>
