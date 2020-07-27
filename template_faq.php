<?php /* Template Name: FAQ */
get_header();?>
<div class="gutenberg-layout-container">
    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php get_footer(); ?>
