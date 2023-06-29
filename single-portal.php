<?php
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
