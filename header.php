<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<?php get_template_part('head') ?>

<body <?php body_class(); ?>>
<header id="site-header" class="site-header" role="banner">
    <a class="skip-link" href="#main-content" aria-label="skip-link">Zum Inhalt springen</a>
    <div class="top-bar" id="main-menu" data-animate="fade-in fade-out">
        <div class="zmf-logo">
            <a href="<?php echo get_home_url(); ?>">
                <img class="logo" src="<?php echo get_stylesheet_directory_uri() ?>/src/assets/img/kita-hub-logo-medien.svg" alt="Link zur Startseite">
            </a>
        </div>
    </div>
</header>
<main id="main-content" class="main-content">
