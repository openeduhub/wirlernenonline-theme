<?php
get_header();
?>
<?php
while (have_posts()) : the_post(); ?>

    <div class="portal">

        <div class="portal-wrapper">
            <?php the_content(); ?>
        </div>

    </div>

    </div>
<?php
endwhile;
get_footer();
