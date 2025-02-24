<?php

require_once(get_template_directory() . '/functions/wlo-config.php');

function url_valid(&$url)
{
    stream_context_set_default(
        array(
            'http' => array(
                'method' => 'GET',
                'header' => "Content-type:application/json\r\n"
                    . "Accept: application/json"
            )
        )
    );

    $file_headers = @get_headers($url);

    if ($file_headers === false) return false; // when server not found
    foreach ($file_headers as $header) { // parse all headers:
        // corrects $url when 301/302 redirect(s) lead(s) to 200:
        if (preg_match("/^Location: (http.+)$/", $header, $m)) $url = $m[1];
        // grabs the last $header $code, in case of redirect(s):
        if (preg_match("/^HTTP.+\s(\d\d\d)\s/", $header, $m)) $code = $m[1];
    } // End foreach...
    if ($code == 200) return true; // $code 200 == all OK
    else return false; // All else has failed, so this must be a bad link
} // End function url_exists

function check_unique_collection_link($collection_url)
{
    $portal_args = array(
        'post_type' => 'portal',
        'posts_per_page' => 1, // only need to see if there is 1
        'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
        'meta_query' => array(
            array(
                'key' => 'collection_url',
                'value' => $collection_url
            )
        )
    );

    $query_portal = new WP_Query($portal_args);
    if (count($query_portal->posts)) {
        return false;
    }
    $query_portal->reset_postdata();

    return true;
}

function add_portal(WP_REST_Request $request)
{

    error_log('###ADD-PORTAL####');


    $collection_id = $request->get_param('collectionId');

    $type = 'wlo';

    //check for zmf
    $parents_url = WLO_REPO . 'rest/node/v1/nodes/-home-/' . $collection_id . '/parents?propertyFilter=-all-&fullPath=false';
    $parents = callWloRestApi($parents_url)->nodes;
    $portal = $parents[count($parents) - 2];
    error_log(print_r($portal, true));
    if ($portal->title === 'Kita digital') {
        $type = 'zmf';
    }

    $requestDisciplines = explode(",", urldecode($request->get_param('discipline')));

    $disciplines = getWloVocaps('discipline')->hasTopConcept;

    $disciplinesMapped = [];
    foreach ($requestDisciplines as $currentDiscipline) {
        foreach ($disciplines as $discipline) {
            if ($discipline->id == $currentDiscipline) {
                $disciplinesMapped[] = $discipline->prefLabel->de;
            }
        }
    }

    $topic = urldecode($request->get_param('title'));

    $edu_contexts = explode(",", urldecode($request->get_param('educationalContext')));
    if(!$edu_contexts) {
        $edu_contexts = [];
    }
    $intended_end_user_roles = explode(",", urldecode($request->get_param('intendedEndUserRole')));

    $collection_url = WLO_REPO . "components/collections?id=" . $collection_id;

    $check_url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collection_id;
    $check_url_ret = $check_url;

    //Check if Collection was not already added, Check if Collection exists
    // if (check_unique_collection_link($collection_url) && url_valid($check_url)) {
    if (true) {

        //Get Level of Collection
        $parents_api_url = WLO_REPO . '/rest/node/v1/nodes/-home-/' . $collection_id . '/parents?propertyFilter=-all-&fullPath=false';

        try {
            $curl = curl_init($parents_api_url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $curl,
                CURLOPT_HTTPHEADER,
                array(
                    'Accept: application/json',
                    'Content-Type: application/json; charset=utf-8'
                )
            );
            $response = curl_exec($curl);
            if ($response === false) {
                echo 'curl error';
                return false;
            }
        } catch (Exception $e) {
            echo 'curl error: ' . $e->getMessage();
            return false;
        }
        curl_close($curl);

        $response = json_decode($response);


        $nodes = array();
        if(isset($response->nodes)) {
            foreach ($response->nodes as $node) {
                $nodes[] = [$node->title, $node->properties->{'cclom:location'}[0]];
            }
        }
        //Delete highest Level
        array_pop($nodes);
        $nodes = array_reverse($nodes);

        $collection_level = sizeof($nodes) - 1;
        // we only have templates until the first level is reached, then we'll use the same templates
        if ($collection_level > 1) {
            $collection_level = 1;
        }

        //Copy Themenportal-Vorlage Content
        if ($template = get_page_by_path('themenportal-vorlage-' . $collection_level, OBJECT, 'portal')) {
            $template_id = $template->ID;
        }
        if ($type == 'zmf') {
            $template_id = '64494';
        }

        if ($template_id) {
            $content = get_post_field('post_content', $template_id);
        }

        $slug = '';
        if ($type == 'zmf') {
            $slug = 'kita-' . $topic;
        } else {
            foreach ($disciplinesMapped as $discipline) {
                $slug .= $discipline . '-';
            }
            $slug .= $topic;
        }

        $post_status = 'draft';
        if ($collection_level == 1) {
            $post_status = 'publish';
        }


        $portal_insert = array(
            'post_author' => 'admin',
            'post_content' => $content,
            'post_content_filtered' => '',
            'post_title' => $topic,
            //'post_name' => sanitize_title_with_dashes($topic.'-'.$node->properties->{'ccm:taxonid_DISPLAYNAME'}[0], '', 'save'),
            'post_name' => sanitize_title_with_dashes($slug, '', 'save'),
            'post_excerpt' => '',
            'post_status' => 'publish',
            'post_type' => 'portal',
            'comment_status' => '',
            'ping_status' => '',
            'post_password' => '',
            'to_ping' => '',
            'pinged' => '',
            'post_parent' => 0,
            'menu_order' => 0,
            'guid' => '',
            'import_id' => 0,
            'context' => '',
        );


        // Insert the post into the database
        $post_id = wp_insert_post($portal_insert, true);

        if (!empty($post_id) && is_numeric($post_id)) {

            update_post_meta($post_id, '_wp_page_template', 'page-templates/template_themenseite.php');

            update_field('collection_url', $collection_url, $post_id);
            update_field('collection_level', $collection_level, $post_id);
            update_field('topic', $topic, $post_id);

            //Discipline
            if (!function_exists("clean_discipline")) {
                function clean_discipline($n)
                {
                    $disciplineLastSlash = strrpos($n, "/");
                    $disciplineIdNr = substr($n, $disciplineLastSlash + 1);
                    return $disciplineIdNr;
                }
            }
            update_field('discipline', array_map("clean_discipline", $requestDisciplines), $post_id);


            //Edu Context
            if (!function_exists("clean_edu_context")) {
                function clean_edu_context($n)
                {
                    $eduConLastSlash = strrpos($n, "/");
                    $eduConId = substr($n, $eduConLastSlash + 1);
                    return $eduConId;
                }
            }
            update_field('educationalContext', array_map("clean_edu_context", $edu_contexts), $post_id);

            //Intended End User Role
            if (!function_exists("clean_intended_end_user_role")) {
                function clean_intended_end_user_role($n)
                {
                    $euRoleLastSlash = strrpos($n, "/");
                    $euRoleId = substr($n, $euRoleLastSlash + 1);
                    return $euRoleId;
                }
            }

            update_field('intendedEndUserRole', array_map("clean_intended_end_user_role", $intended_end_user_roles), $post_id);

            if ($type == 'zmf') {
                //set typ zmf for page
                update_field('field_617963f992a7b', 2, $post_id);
            }

            /*
          //Copy Template Blog Posts
            $post_args = array(
                'post_type' => 'post',
                'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
                'category_name' => 'Themenportal',
                'tag' => 'Vorlage-' . $collection_level
            );

            $query_post = new WP_Query($post_args);
            $template_posts_count = count($query_post->posts);
            $while_count = 0;
            $category_id = get_cat_ID('Themenportal');

            while ( $query_post->have_posts() ) {
                $query_post->the_post();
                $original_post_id = $query_post->post->ID;
                $original_title = str_replace('%portal_name%',$topic,$query_post->post->post_title);

                $post_insert = array(
                    'post_author' => 'admin',
                    'post_content' => $query_post->post->post_content,
                    'post_content_filtered' => '',
                    'post_title' => $original_title,
                    'post_name' => sanitize_title_with_dashes($original_title, '', 'save'),
                    'post_excerpt' => '',
                    'post_status' => 'publish',
                    'post_type' => 'post',
                    'post_category' => array($category_id)
                );
                $duplicate_post_id = wp_insert_post($post_insert, true);

                update_field('discipline', array_map("clean_discipline", $disciplines), $duplicate_post_id);
                update_field('educationalContext', array_map("clean_edu_context", $edu_contexts), $duplicate_post_id);
                update_field('intendedEndUserRole', array_map("clean_intended_end_user_role", $intended_end_user_roles), $duplicate_post_id);
                update_field('collection_level', $collection_level, $duplicate_post_id);

                set_post_thumbnail( $duplicate_post_id, get_post_thumbnail_id($original_post_id) );

                if($while_count == $template_posts_count)
                {
                    header("Content-Type: application/json");
                    $rtn = array("Error", "Too much loops.");
                    print json_encode($rtn);
                    http_response_code(404);
                    die();
                }
                else {
                    $while_count++;
                }
            }
            $query_post->reset_postdata();
          */

            require_once ABSPATH . '/wp-admin/includes/post.php';
            $slug = get_post_field('post_name', $post_id);

            http_response_code(200);

            if ($type == 'zmf') {
                print('https://medien.kita.bayern/portal/' . $slug);
            } else {
                print(get_site_url() . '/portal/' . $slug);
            }



            return;
        } else {
            header("Content-Type: application/json");
            $rtn = array("Error", "Couldn\'t create Portal Page.", $check_url_ret);
            print json_encode($rtn);
            http_response_code(404);
            die();
        }
    } else {
        header("Content-Type: application/json");
        $rtn = array("Error", "Collection not available or already added.", $check_url_ret);
        print json_encode($rtn);
        http_response_code(404);
        die();
    }
}

function restGetBreadcrumbs(WP_REST_Request $request): WP_REST_Response | WP_ERROR
{
    $collectionId = $request->get_param('collectionId');
    $parents = getCollectionAncestors($collectionId);
    if (empty($parents)) {
        return new WP_REST_Response(
            array(
                'status' => 400,
                'response' => 'Could not get collection ancestors for ' . $collectionId,
            )
        );
    }
    $breadcrumbs = getBreadcrumbs($parents);
    return rest_ensure_response($breadcrumbs);
}

function restChat(WP_REST_Request $request): WP_REST_Response | WP_ERROR
{
    $body = $request->get_body();
    $url = WLO_AI_PROMPT_SERVICE_URL . "/chat";
    $headers = array(
        'ai-prompt-token' => WLO_AI_PROMPT_SERVICE_TOKEN,
        'Content-Type' => 'application/json',
    );
    $response = wp_remote_post($url, array(
        'headers' => $headers,
        'timeout' => 60,
        'body' => $body,
    ));
    if (is_wp_error($response) || $response['response']['code'] != 200) {
        return new WP_REST_Response(
            array(
                'status' => 500,
                'response' => 'Failed to get response from ChatGPT',
            )
        );
    }
    $apiBody = json_decode(wp_remote_retrieve_body($response));
    return rest_ensure_response($apiBody);
}

add_action('rest_api_init', function () {
    register_rest_route('portal/v1', '/add/', array(
        'methods' => 'GET',
        'callback' => 'add_portal',
        'permission_callback' => '__return_true',
        'args' => array(
            'collectionId' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                }
            ),
            'title' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                }
            ),
            'discipline' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                }
            ),
            'educationalContext' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return true;
                }
            ),
            'intendedEndUserRole' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return true;
                }
            ),
        ),
    ));
    register_rest_route('portal/v1', '/breadcrumbs/', [
        'methods' => 'GET',
        'callback' => 'restGetBreadcrumbs',
        'permission_callback' => '__return_true',
        'args' => [
            'collectionId' => ['required' => true]
        ],
    ]);
    register_rest_route('portal/v1', '/chat/', [
        'methods' => 'POST',
        'callback' => 'restChat',
        'permission_callback' => '__return_true',
    ]);
});
