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


        <form id="zmf-search" target="_blank" action="https://materialkiste.kita.bayern/edu-sharing/components/search" method="GET">

            <div class="wlo-zmf-search">
                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/icon_search.svg" alt="">
                <input type="search" name="query" id="search" aria-label="Search" autocomplete="off" placeholder="Finde passende Inhalte, Tools & Quellen">
                <input type="submit" value="Suche">
            </div>

        </form>
    </div>
</header>
<main id="main-content" class="main-content">
