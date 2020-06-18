<?php
function create_eduSource() {

    register_post_type( 'edusource',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'eduQuellen' ),
                'singular_name' => __( 'eduQuelle' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'eduQuellen'),
            'show_in_rest' => true,
            'supports' => array('title','editor','author','excerpt','comments','revisions'),
            'taxonomies' => array('post_tag'),
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_eduSource' );


function create_eduTool() {

    register_post_type( 'edutool',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'eduTools' ),
                'singular_name' => __( 'eduTool' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'eduTool'),
            'show_in_rest' => true,
            'supports' => array('title','editor','author','excerpt','comments','revisions'),
            'taxonomies' => array('post_tag'),
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_eduTool' );


function create_wloPartner() {

    register_post_type( 'partner',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Partner' ),
                'singular_name' => __( 'Partner' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'partner'),
            'show_in_rest' => true,
            'supports' => array('title','editor','author','excerpt','comments','revisions'),
            'menu_icon' => 'dashicons-groups',
            'taxonomies' => array('post_tag'),
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_wloPartner' );


function create_wloPresse() {

    register_post_type( 'presse',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Presse' ),
                'singular_name' => __( 'Presse' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'presse'),
            'show_in_rest' => true,
            'supports' => array('title','editor','author','excerpt','comments','revisions'),
            'menu_icon' => 'dashicons-media-document',
            'taxonomies' => array('post_tag'),
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_wloPresse' );

function prefix_disable_gutenberg($current_status, $post_type){
    // Use your post type key instead of 'product'
    if ($post_type === 'edusource' || $post_type === 'edutool' || $post_type === 'partner' || $post_type === 'presse'){
        return false;
    }
    return $current_status;
}
add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
