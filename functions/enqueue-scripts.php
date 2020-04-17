<?php
function site_scripts() {
    //JS
    wp_enqueue_script( 'select2-js', get_template_directory_uri() . '/src/assets/js/select2.full.min.js', array( 'jquery' ), '', true );
    if (is_page_template('templates/archive-edusource.php')) {
        wp_enqueue_script( 'portfolio-js', get_template_directory_uri() . '/dist/assets/js/portfolio.js', array( 'jquery' ), '', true );
    }
    wp_enqueue_script( 'site-js', get_template_directory_uri() . '/dist/assets/js/app.js', array( 'jquery' ), '', true );

    // CSS
    wp_enqueue_style( 'select2-css', get_template_directory_uri() . '/dist/assets/scss/select2.css', array(), '', 'all' );
    wp_enqueue_style( 'site-css', get_template_directory_uri() . '/dist/assets/scss/app.css', array(), '', 'all' );
    wp_enqueue_style( 'style-css', get_template_directory_uri() . '/style.css', array(), '', 'all' );
}
add_action('wp_enqueue_scripts', 'site_scripts', 999);
