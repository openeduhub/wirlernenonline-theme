<?php
/*
Template Name: WlO-Lennart-Spezial
Template Post Type: page
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<?php get_template_part('head'); ?>

<body <?php body_class(); ?>>

    <main>

        <div class="wlo-embed">
            <?php
            while (have_posts()) : the_post(); ?>

                <?php the_content(); ?>

            <?php
            endwhile;
            ?>
        </div>

    </main><!-- .site-content -->


<?php wp_footer(); ?>
</body>
</html>
