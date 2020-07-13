<?php
function add_portal(WP_REST_Request $request) {

    $collection_id = $request->get_param( 'collection_id' );
    $title = urldecode($request->get_param( 'title' ));
    $discipline = urldecode($request->get_param( 'discipline' ));
    $edu_context = urldecode($request->get_param( 'edu_context'));
    $intended_end_user_role = urldecode($request->get_param( 'intended_end_user_role'));

    if ( $template = get_page_by_path( 'themenportal-vorlage', OBJECT, 'portal' ) )
        $template_id = $template->ID;

    if($template_id)
        $content = get_post_field('post_content', $template_id);

    $portal_insert = array(
        'post_author'           => 'admin',
        'post_content'          => $content,
        'post_content_filtered' => '',
        'post_title'            => $title,
        'post_name'             => sanitize_title_with_dashes($title,'','save'),
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
    if(!empty($post_id) && is_numeric($post_id))
    {
        $collection_url = "https://redaktion.openeduhub.net/edu-sharing/components/collections?id=" . $collection_id;
        update_field( 'collection_url', $collection_url, $post_id );

        //Discipline
        $disciplineLastSlash = strrpos($discipline, "/");
        $disciplineIdNr = substr($discipline, $disciplineLastSlash + 1);
        update_field( 'discipline', intval($disciplineIdNr), $post_id );

        //Edu Context
        $eduConLastSlash = strrpos($edu_context, "/");
        $eduConId = substr($edu_context, $eduConLastSlash + 1);
        update_field( 'edu_context', $eduConId, $post_id );

        //Intended End User Role
        $euRoleLastSlash = strrpos($intended_end_user_role, "/");
        $euRoleId = substr($intended_end_user_role, $euRoleLastSlash + 1);
        update_field( 'intended_end_user_role', $euRoleId, $post_id );

        require_once ABSPATH . '/wp-admin/includes/post.php';
        $sample_permalink_obj = get_sample_permalink($post_id);
        return str_replace('%pagename%', $sample_permalink_obj[1], $sample_permalink_obj[0]);
    }
    else {
        return 0;
    }
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
            'discipline' => array(
                'validate_callback' => function($param, $request, $key) {
                    return !empty( $param );
                }
            ),
            'edu_context' => array(
                'validate_callback' => function($param, $request, $key) {
                    return !empty( $param );
                }
            ),
            'intended_end_user_role' => array(
                'validate_callback' => function($param, $request, $key) {
                    return !empty( $param );
                }
            ),
        ),
    ) );
} );