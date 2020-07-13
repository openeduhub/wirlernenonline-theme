<?php
function url_valid(&$url) {
    $file_headers = @get_headers($url);
    if ($file_headers === false) return false; // when server not found
    foreach($file_headers as $header) { // parse all headers:
        // corrects $url when 301/302 redirect(s) lead(s) to 200:
        if(preg_match("/^Location: (http.+)$/",$header,$m)) $url=$m[1];
        // grabs the last $header $code, in case of redirect(s):
        if(preg_match("/^HTTP.+\s(\d\d\d)\s/",$header,$m)) $code=$m[1];
    } // End foreach...
    if($code==200) return true; // $code 200 == all OK
    else return false; // All else has failed, so this must be a bad link
} // End function url_exists

function check_unique_collection_link($collection_url) {
    $args = array(
        'post_type' => 'portal',
        'posts_per_page' => 1, // only need to see if there is 1
        'post_status' => 'publish, draft, trash',
        'meta_query' => array(
            array(
                'key' => 'collection_url',
                'value' => $collection_url
            )
        )
    );

    $query = new WP_Query($args);
    if (count($query->posts)){
        return false;
    }
    return true;
}

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

        $check_url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/-home-/' . $collection_id ;
        $check_url_ret = $check_url;

        if(check_unique_collection_link($collection_url) && url_valid($check_url)) {

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

            http_response_code(200);
            print(str_replace('%pagename%', $sample_permalink_obj[1], $sample_permalink_obj[0]));

            return;
        }
        else {
            header("Content-Type: application/json");
            $rtn = array("Error", "Collection not available or already added.", $check_url_ret);
            print json_encode($rtn);
            http_response_code(404);
            die();
        }
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