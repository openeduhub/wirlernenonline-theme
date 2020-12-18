<?php


add_action( 'rest_api_init', function () {
    register_rest_route( 'wlo/v1', '/add/', array(
        'methods' => 'POST',
        'callback' => 'add_portal_page',
        'args' => array(
            'collectionId' => array(
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
            'educationalContext' => array(
                'validate_callback' => function($param, $request, $key) {
                    return true;
                }
            ),
            'intendedEndUserRole' => array(
                'validate_callback' => function($param, $request, $key) {
                    return true;
                }
            ),
        ),
        'permission_callback' => function () {
            return current_user_can( 'edit_others_posts' );
        }
    ) );
} );


function add_portal_page(WP_REST_Request $request)
{
    $collection_id = $request->get_param('collectionId');

}
