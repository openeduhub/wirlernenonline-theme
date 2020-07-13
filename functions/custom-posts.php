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
            'has_archive' => false,
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


function create_wloUxIdeas() {

    register_post_type( 'uxideas',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'UX-Ideen' ),
                'singular_name' => __( 'UX-Idee' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'uxideas'),
            'show_in_rest' => true,
            'supports' => array('title','editor','author','excerpt','comments','revisions'),
            'menu_icon' => 'dashicons-sticky',
            'taxonomies' => array('post_tag'),
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_wloUxIdeas' );


function create_subjectPortal() {

    register_post_type( 'portal',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Themenportal' ),
                'singular_name' => __( 'Themenportal' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'portal'),
            'show_in_rest' => true,
            'supports' => array('title','editor','author','excerpt','comments','revisions','custom-fields'),
            'menu_icon' => 'dashicons-list-view',
            'taxonomies' => array('post_tag')
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_subjectPortal' );

function prefix_disable_gutenberg($current_status, $post_type){
    // Use your post type key instead of 'product'
    if ($post_type === 'edusource' || $post_type === 'edutool' || $post_type === 'partner' || $post_type === 'presse' || $post_type === 'uxideas'){
        return false;
    }
    return $current_status;
}
add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
