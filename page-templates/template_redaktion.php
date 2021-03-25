<?php
/*
Template Name: WirLernenOnline-Redaktion
Template Post Type: page
 */

get_header();
?>
<?php
while (have_posts()) : the_post(); ?>

    <div class="wlo-page">

        <div class="wlo-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-header-filler"></div>
                <div class="wlo-header-bottom"></div>
            </div>
        </div>

        <div class="wlo-wrapper">
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </div>

    </div>

<?php
endwhile;
get_footer();
