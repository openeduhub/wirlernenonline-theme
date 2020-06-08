<?php /* Template Name: Add Source */

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
                    'post_type'     => 'edusource',
                    'post_status'   => 'pending'
                ),
                'fields' => array('url', 'short_text', 'fachgebiet', 'email', 'newsletter'),
                'submit_value'  => 'Neue Quelle anlegen',
                'html_updated_message' => '<div id="message" class="updated">
                                                <p>Vielen Dank. Deine Quelle wurde gespeichert.<br>Sie wird vor Veröffentlichung von der Redaktion geprüft.</p>
                                                <a class="button" href="'.get_page_link(4225).'">Zur Quellen-Übersicht</a>
                                            </div>',
            )); ?>

        </div>

        <?php endwhile;
        ?>
    </div>
    </div>
    </div>
<?php
get_footer();
