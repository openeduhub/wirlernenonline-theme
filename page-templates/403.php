<?php
/*
Template Name: WLO-Error-403
Template Post Type: page
 */
get_header(); ?>
<div class="wlo-page">
    <div class="wlo-header">
    </div>

    <div class="wlo-error error-403">
        <div class="wlo-error-wrapper">
            <h1 class="wlo-error-message">
                Upps...<br>
                Auf diese Seite hast du keinen Zugriff...
            </h1>
            <a class="wlo-error-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                zur Startseite
                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward.svg">
            </a>
        </div>
    </div>
</div>
<?php get_footer(); ?>
