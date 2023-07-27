<?php
/*
Template Name: WirLernenOnline-2021
Template Post Type: post, page, portal
 */

get_header();
?>
<?php
while (have_posts()) : the_post(); ?>

    <div class="wlo-page">

        <div class="wlo-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-header-content">
                    <h1><?php the_title(); ?></h1>
                    <?php the_excerpt(); ?>
                </div>
                <div class="wlo-header-bottom"></div>
            </div>
        </div>

        <div class="wlo-wrapper">
            <?php the_content(); ?>
        </div>

    </div>

<?php
endwhile;
get_footer();
