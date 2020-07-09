<?php
get_header();
?>
<?php
while (have_posts()) : the_post(); ?>
    <div class="portal">
        <div class="grid-container">
            <div class="grid-x grid-margin-x">
                <div class="medium-12 cell">
                    <div class="portal-wrapper">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
endwhile;
get_footer();
