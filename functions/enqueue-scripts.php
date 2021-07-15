<?php



function site_scripts() {
    $versionDate = '20200821';
    //JS
    wp_enqueue_script( 'site-js', get_template_directory_uri() . '/dist/assets/js/app.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'slick', get_template_directory_uri() . '/dist/assets/js/slick.min.js', array( 'jquery' ), '', true );

    if (is_page_template('archive-edusource.php') || is_page_template('archive-edutool.php') || is_front_page() || true) {
        wp_enqueue_script( 'select2-js', get_template_directory_uri() . '/dist/assets/js/select2.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( 'isotope-js', get_template_directory_uri() . '/dist/assets/js/isotope.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( 'imagesloaded-js', get_template_directory_uri() . '/dist/assets/js/imagesloaded.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( 'lazyload-js', get_template_directory_uri() . '/dist/assets/js/lazyload.min.js', array( 'jquery' ), '', true );
        wp_enqueue_script( 'lernstoff-js', get_template_directory_uri() . '/dist/assets/js/lernstoff.js', array( 'jquery', 'isotope-js', 'imagesloaded-js', 'select2-js', 'lazyload-js' ), '', true );
    }

    if ( is_singular( 'portal' ) ) {
        wp_enqueue_script( 'chart-js', get_template_directory_uri() . '/dist/assets/js/Chart.min.js', array( 'jquery' ), '', false );
        wp_enqueue_style( 'chart-css', get_template_directory_uri() . '/dist/assets/scss/Chart.css', array(), $versionDate, 'all' );
    }

    // source_table
    if ( is_page('4225' ) ) {
        wp_enqueue_script( 'chart-js', get_template_directory_uri() . '/src/assets/js/datatables/datatables.min.js', array( 'jquery' ), '', false );
        wp_enqueue_style( 'chart-css', get_template_directory_uri() . '/src/assets/js/datatables/datatables.min.css', array(), $versionDate, 'all' );
    }


    // CSS
    wp_enqueue_style( 'select2-css', get_template_directory_uri() . '/dist/assets/scss/select2.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'slick-css', get_template_directory_uri() . '/dist/assets/scss/slick.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'slick-theme-css', get_template_directory_uri() . '/dist/assets/scss/slick-theme.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'site-css', get_template_directory_uri() . '/dist/assets/scss/app.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'portal-css', get_template_directory_uri() . '/dist/assets/scss/portal.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'wlo-css', get_template_directory_uri() . '/dist/assets/scss/wlo.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'style-css', get_template_directory_uri() . '/style.css', array(), $versionDate, 'all' );
}
add_action('wp_enqueue_scripts', 'site_scripts', 999);


function admin_scripts() {
    $versionDate = '20200821';
    //JS
    //wp_enqueue_script( 'site-js', get_template_directory_uri() . '/dist/assets/js/app.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'slick', get_template_directory_uri() . '/dist/assets/js/slick.min.js', array( 'jquery' ), '', true );

    wp_enqueue_script( 'chart-js', get_template_directory_uri() . '/dist/assets/js/Chart.min.js', array( 'jquery' ), '', true );
    wp_enqueue_style( 'chart-css', get_template_directory_uri() . '/dist/assets/scss/Chart.css', array(), $versionDate, 'all' );

    // source_table
    if ( is_page('4225' ) ) {
        wp_enqueue_script( 'chart-js', get_template_directory_uri() . '/src/assets/js/datatables/datatables.min.js', array( 'jquery' ), '', false );
        wp_enqueue_style( 'chart-css', get_template_directory_uri() . '/src/assets/js/datatables/datatables.min.css', array(), $versionDate, 'all' );
    }


    // CSS
    wp_enqueue_style( 'slick-css', get_template_directory_uri() . '/dist/assets/scss/slick.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'slick-theme-css', get_template_directory_uri() . '/dist/assets/scss/slick-theme.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'editor-css', get_template_directory_uri() . '/dist/assets/scss/editor.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'portal-css', get_template_directory_uri() . '/dist/assets/scss/portal.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'wlo-css', get_template_directory_uri() . '/dist/assets/scss/wlo.css', array(), $versionDate, 'all' );
    wp_enqueue_style( 'style-css', get_template_directory_uri() . '/style.css', array(), $versionDate, 'all' );
}
add_action( 'admin_enqueue_scripts', 'admin_scripts' );
