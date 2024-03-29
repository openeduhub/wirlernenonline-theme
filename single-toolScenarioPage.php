<?php
get_header();
?><div class="grid-container">
    <div class="grid-x grid-margin-x">
        <div class="medium-12 cell">
            <h1 class="page-title"><?php the_title(); ?></h1>
        </div>
        <div>
            <?php
            while ( have_posts() ) : the_post();?>
                <div class="gutenberg-layout-container">
                    <?php the_content(); ?>
                </div>
            <?php endwhile;
            ?>
        </div>
    </div>
    </div>
<?php
get_footer();
