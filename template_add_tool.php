<?php /* Template Name: Add Tool */

acf_form_head();
get_header();
?>

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

        <?php
        while ( have_posts() ) : the_post(); ?>

            <?php
            the_content();

            acf_form(array(
                'post_id'       => 'new_post',
                'post_title' => true,
                'new_post'      => array(
                    'post_type'     => 'edutool',
                    'post_status'   => 'pending'
                ),
                'fields' => array('url', 'short_text', 'email', 'newsletter'),
                'submit_value'  => 'Neues Tool anlegen',
                'html_updated_message' => '<div id="message" class="updated">
                                                <p>Vielen Dank. Dein Tool wurde gespeichert.<br>Es wird vor Veröffentlichung von der Redaktion geprüft.</p>
                                            </div>',
            )); ?>



        <?php endwhile;
        ?>

        </div>

    </div>

<?php
get_footer();
