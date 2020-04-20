<?php
function site_scripts() {
    //JS
    wp_enqueue_script( 'site-js', get_template_directory_uri() . '/dist/assets/js/app.js', array( 'jquery' ), '', true );
    if (is_page_template('archive-edusource.php') || is_front_page()) {
        wp_enqueue_script( 'select2-js', get_template_directory_uri() . '/dist/assets/js/select2.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( 'isotope-js', get_template_directory_uri() . '/dist/assets/js/isotope.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( 'imagesloaded-js', get_template_directory_uri() . '/dist/assets/js/imagesloaded.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( 'lazyload-js', get_template_directory_uri() . '/dist/assets/js/lazyload.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( 'lernstoff-js', get_template_directory_uri() . '/dist/assets/js/lernstoff.js', array( 'jquery', 'isotope-js', 'select2-js'), '', true );
    }

    // CSS
    wp_enqueue_style( 'select2-css', get_template_directory_uri() . '/dist/assets/scss/select2.css', array(), '', 'all' );
    wp_enqueue_style( 'site-css', get_template_directory_uri() . '/dist/assets/scss/app.css', array(), '', 'all' );
    wp_enqueue_style( 'style-css', get_template_directory_uri() . '/style.css', array(), '', 'all' );
}
add_action('wp_enqueue_scripts', 'site_scripts', 999);
