<?php
function add_portal(WP_REST_Request $request) {

    $collection_id = $request->get_param( 'collection_id' );
    $title = $request->get_param( 'title' );
    $subject = $request->get_param( 'subject' );
    $school_type = $request->get_param( 'school_type');
    $role = $request->get_param( 'role');

    if ( $template = get_page_by_path( 'themenportal-vorlage', OBJECT, 'portal' ) )
        $template_id = $template->ID;

    if($template_id)
        $content = get_post_field('post_content', $template_id);

    $portal_insert = array(
        'post_author'           => 'admin',
        'post_content'          => $content,
        'post_content_filtered' => '',
        'post_title'            => $title,
        'post_excerpt'          => '',
        'post_status'           => 'draft',
        'post_type'             => 'portal',
        'comment_status'        => '',
        'ping_status'           => '',
        'post_password'         => '',
        'to_ping'               => '',
        'pinged'                => '',
        'post_parent'           => 0,
        'menu_order'            => 0,
        'guid'                  => '',
        'import_id'             => 0,
        'context'               => '',
    );

    // Insert the post into the database
    $post_id = wp_insert_post( $portal_insert , true);

    $collection_url = "https://redaktion.openeduhub.net/edu-sharing/components/collections?id=" . $collection_id;
    update_field( 'collection_url', $collection_url, $post_id );

    update_field( 'subject', $subject, $post_id );
    update_field( 'school_type', $school_type, $post_id );
    update_field( 'role', $role, $post_id );

    return get_permalink( $post_id);
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'portal/v1', '/add/', array(
        'methods' => 'GET',
        'callback' => 'add_portal',
        'args' => array(
            'collection_id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return !empty( $param );
                }
            ),
            'title' => array(
                'validate_callback' => function($param, $request, $key) {
                    return !empty( $param );
                }
            ),
            'subject' => array(
                'validate_callback' => function($param, $request, $key) {
                    return !empty( $param ) && is_numeric($param);
                }
            ),
            'school_type' => array(
                'validate_callback' => function($param, $request, $key) {
                    return !empty( $param );
                }
            ),
            'role' => array(
                'validate_callback' => function($param, $request, $key) {
                    return !empty( $param );
                }
            ),
        ),
    ) );
} );