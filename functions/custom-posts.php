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


function prefix_disable_gutenberg($current_status, $post_type){
    // Use your post type key instead of 'product'
    if ($post_type === 'edusource' || $post_type === 'edutool'){
        return false;
    }
    return $current_status;
}
add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
