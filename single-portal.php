<?php
get_header();
?>
<?php
while (have_posts()) : the_post(); ?>
    <style type="text/css">
        .portal {
            background: <?php the_field('background_color'); ?>;
        }

        .portal h1, .portal h2,.portal h3,.portal h4,.portal h5,.portal h6, .portal a{
            color: <?php the_field('font_color'); ?>;
        }
    </style>
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
