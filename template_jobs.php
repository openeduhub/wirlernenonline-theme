<?php
/*
Template Name: Jobs
Template Post Type: post, page, portal
 */

get_header();
?>
<?php
while (have_posts()) : the_post(); ?>

    <div class="jobs-page">

        <div class="jobs-header">
            <div class="jobs-header-wrapper">
                <div class="jobs-header-content">
                    <h1><?php the_title(); ?></h1>
                    <?php the_excerpt(); ?>
                </div>
                <div class="jobs-header-bottom"></div>
            </div>


        </div>

        <div class="jobs-wrapper">



            <?php the_content(); ?>
        </div>

    </div>

<?php
endwhile;
get_footer();
