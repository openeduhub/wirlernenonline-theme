<?php
/**
* The template for displaying 404 pages (not found)
*
* @package WordPress
* @subpackage Twenty_Fifteen
* @since Twenty Fifteen 1.0
*/
get_header(); ?>
<div class="wlo-page">
    <div class="wlo-header">
    </div>

    <div class="wlo-error error-404">
        <div class="wlo-error-wrapper">
            <h1 class="wlo-error-message">
                Upps...<br>
                Diese Seite haben wir nicht gefunden...
            </h1>
            <a class="wlo-error-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                zur Startseite
                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward.svg">
            </a>
        </div>
    </div>
</div>
<?php get_footer(); ?>
