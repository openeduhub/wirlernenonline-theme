<?php /* Template Name: Add Tool */

acf_form_head();
get_header();
?><div class="grid-container">
    <div class="grid-x grid-margin-x">
        <div class="medium-12 cell">
            <h1 class="page-title"><?php the_title(); ?></h1>
        </div>
        <?php
        while ( have_posts() ) : the_post(); ?>

        <div class="medium-8 cell">
            <?php
            the_content();

            acf_form(array(
                'post_id'       => 'new_post',
                'post_title' => true,
                'new_post'      => array(
                    'post_type'     => 'edutool',
                    'post_status'   => 'pending'
                ),
                'submit_value'  => 'Neues Tool anlegen'
            )); ?>

        </div>

        <?php endwhile;
        ?>
    </div>
    </div>
    </div>
<?php
get_footer();
