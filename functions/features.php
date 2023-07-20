<?php
function admin_bar()
{
    if (is_user_logged_in()) {
        add_filter('show_admin_bar', '__return_true', 1000);
    }
}

add_action('init', 'admin_bar');

// %%%%%%%%%% Title Tag
//add_theme_support( 'title-tag' );

// %%%%%%%%%% Post Thumbnails
add_theme_support('post-thumbnails');

// %%%%%%%%%% Custom Excerpt Length %%%%%%%%%
function custom_excerpt_length($length)
{
    return 20;
}

add_filter('excerpt_length', 'custom_excerpt_length', 999);

function new_excerpt_more($more)
{
    return ' … <a class="read-more" aria-label="weiter lesen" href="' . get_permalink(get_the_ID()) . '">' . __(' <i class="fa fa-long-arrow-right"></i>') . '</a>';
}

add_filter('excerpt_more', 'new_excerpt_more');

// %%%%%%%%% Custom Post Archive Page

// function archive_add_custom_types( $query ) {
//   if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
//     $query->set( 'post_type', array(
//      'post', 'nav_menu_item', 'projekt'
//         ));
//       return $query;
//     }
// }
// add_filter( 'pre_get_posts', 'archive_add_custom_types' );

// %%%%%%%% SVG Support in Media
function svg_support($svg_mime)
{
    $svg_mime['svg'] = 'image/svg+xml';
    return $svg_mime;
}

add_filter('upload_mimes', 'svg_support');

// Disable admin bar in site view
show_admin_bar(false);


function get_field_values($str)
{
    $field = get_field($str);

    if (!is_array($field)) {
        return null;
    }

    $values = array_map(function ($v) {
        if ($v instanceof WP_Term) {
            return $v->term_id;
        }
        return $v['value'];
    }, $field);

    return implode(',', $values);
}

function change_acf_form_title($field)
{

    if (is_page_template('template_add_source.php')) {
        $field['label'] = "Titel der Quelle";
        //$field['instructions'] = "Changed Instruction";
    } elseif (is_page_template('template_add_tool.php')) {
        $field['label'] = "Titel des Tools";
    }

    if ($field) {
        return $field;
    } else {
        exit;
    }
}

add_filter('acf/prepare_field/name=_post_title', 'change_acf_form_title');

//send mail if content is added via the frontend
function acf_save_form($post_id)
{

    // bail early if editing in admin
    if (is_admin()) {
        return;
    }

    $title = get_the_title($post_id);
    $mail = get_field('email', $post_id);
    $newsletter = get_field('newsletter', $post_id);
    // email data
    $to = get_bloginfo('admin_email' . ', redaktion@wirlernenonline.de');
    $headers[] = 'From: wirlernenonline.de <redaktion@wirlernenonline.de>';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $subject = 'Neuer Eintrag hinzugefügt: ' . $title;
    $body = '<p>Es wurde ein neuer Eintrag für "' . $title . '" über das Hinzufügen-Formular auf wirlernenonline.de angelegt.</p>';
    $body .= '<p>Du kannst dir den Eintrag <a href="' . get_edit_post_link($post_id) . '">hier</a> anschauen und ggf. veröffentlichen.</p>';

    if ($mail) {
        $body .= '<p>Der Eintrag wurde von: ' . $mail . ' hinzugefügt.</p>';
    }
    if ($newsletter) {
        $body .= '<p>Eine Anmeldung zum Newsletter ist gewünscht.</p>';
    }

    $body .= '<hr>';
    $body .= '<p>Diese Mail wurde automatisch von wirlernenonline.de versendet</p>';

    // send email
    wp_mail($to, $subject, $body, $headers);
}

add_action('acf/save_post', 'acf_save_form');

//* Add edit_pages capability to contributors
function contributor_edit_pages()
{
    $contributor = get_role('contributor');
    $contributor->add_cap('edit_pages');
}

add_action('init', 'contributor_edit_pages');


function home_hero_fill_subjectbuttons($response, $schoolType, $allowedSubjects)
{

    $return = '';
    $min = 1;
    $max = 11;
    $subjects = array();
    $allowedSubjects = array_column($allowedSubjects, 'label', 'value');

    foreach ($response->data->subjectsPortals->{$schoolType} as $fach) {
        $key = $fach->id;
        if ($allowedSubjects[$key] && $fach->doc_count >= $min) {
            $subjects[$key] = array(
                'name' => $allowedSubjects[$key],
                'count' => $fach->doc_count,
                'url' => $fach->url
            );
        }
    }

    $menu_subjects = array_slice($subjects, 0, $max);
    asort($menu_subjects);

    foreach ($menu_subjects as $fach => $value) {
        $return .= '<a class="subjectButton" href="' . $value['url'] . '" >' . $value['name'] . '<span class="filter_tag">' . $value['count'] . '</span></a>';
    }

    if (count($subjects) > 10) {
        $return .= '<button type="button" class="extraButton" id="extraButton_' . $schoolType . '" onclick="wloToggleMenu(\'' . $schoolType . '\')">' . 'weitere Fächer' . '</button>';

        $return .= '</div>';
        $return .= '<div class="subject_grid extraSubjects" id="extra_' . $schoolType . '" style="display: none;">';

        $menu_extra_subjects = array_slice($subjects, $max);
        asort($menu_extra_subjects);

        foreach ($menu_extra_subjects as $fach => $value) {
            $return .= '<a class="subjectButton" href="' . $value['url'] . '" >' . $value['name'] . '<span class="filter_tag">' . $value['count'] . '</span></a>';
        }
    }

    return $return;
}


function themenportal_block_categories($categories, $post)
{
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'themenportal',
                'title' => 'Fachportal',
                'icon' => 'list-view',
            ),
            array(
                'slug' => 'wlo',
                'title' => 'WLO',
                'icon' => 'marker',
            ),
        )
    );
}
add_filter('block_categories', 'themenportal_block_categories', 10, 2);

function acf_editor_post_id()
{
    if (is_admin() && function_exists('acf_maybe_get_POST')) :
        return intval(acf_maybe_get_POST('post_id'));
    else :
        global $post;
        return $post->ID;
    endif;
}

/**
 * checks if any of the to match values match inside the property values (from the node)
 * returns true if the item matches the matchValue (any of them)
 * If softMatch==true, items without a value set will always match any values
 * @param $propertyValue
 * @param $matchValue
 * @param bool $softMatch
 * @return bool
 */
function checkPropertyMatch($propertyValue, $matchValue, $softMatch = true)
{
    // soft match == if the item has the property not defined, always display it
    if ($softMatch && (empty($propertyValue))) {
        //error_log('softmatch!');
        return true;
    }

    if (is_array($matchValue)) {
        foreach ($matchValue as $m) {
            foreach ($propertyValue as $p) {
                if (basename($p) == $m) {
                    //error_log('match: '.basename($p).'-'.$m);
                    return true;
                }
            }
        }
    } else {
        foreach ($propertyValue as $p) {
            if (basename($p) == $matchValue) {
                //error_log('$matchValue: '.basename($p).'-'.$matchValue);
                return true;
            }
        }
    }
    return false;
}

function callWloGraphApi($search_query)
{
    $curl_post_data = array("query" => $search_query);
    $data_string = json_encode($curl_post_data);

    $restApiCacheObj = null;
    if ((get_transient($search_query)) === false) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        try {
            $curl = curl_init(WLO_SEARCH . 'relay/graphql');
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
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
                trigger_error(curl_error($curl), E_USER_WARNING);
                return false;
            }
        } catch (Exception $e) {
            echo 'curl error: ' . $e->getMessage();
            error_log('curl error: ' . $e->getMessage());
            trigger_error($e->getMessage(), E_USER_WARNING);
            return false;
        }
        curl_close($curl);

        $restApiCacheObj = json_decode($response);
        set_transient($search_query, $restApiCacheObj, 60);
    } else {
        $restApiCacheObj = get_transient($search_query);
    }

    return $restApiCacheObj;
}

function callWloRestApi($url, $type = 'GET', $body = null)
{
    // error_log('Start ----------------------------------------------------------------------------');
    // error_log($type . ' ' . $url);
    // error_log($body);

    $cacheTime = 60;
    // cache source_table for 24h
    //if ($url == WLO_REPO . 'rest/search/v1/queries/-home-/mds_oeh/ngsearch/?maxItems=5000&skipCount=25&propertyFilter=-all-'){
    if (strpos($url, 'rest/search/v1/queries/-home-/mds_oeh/ngsearch/?maxItems=500') !== false) {
        $cacheTime = 86400;
    }

    $restApiCacheObj = null;
    if ((get_transient($url . $body)) === false) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $curl,
                CURLOPT_HTTPHEADER,
                array(
                    'Accept: application/json',
                    'Content-Type: application/json; charset=utf-8'
                )
            );
            if (!empty($body)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
            }
            // $start = microtime(true);
            $response = curl_exec($curl);
            // $time_elapsed = microtime(true) - $start;
            // error_log('took: '. $time_elapsed);
            if ($response === false) {
                error_log('Curl error when calling ' . $type . ' ' . $url . ': ' . curl_error($curl));
                if (!empty($body)) {
                    error_log('Request body: ' . $body);
                }
                echo 'curl error';
                return false;
            }
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode != 200) {
                error_log('Response code ' . $httpCode . ' when calling ' . $type . ' ' . $url . "\n" . $response);
                if (!empty($body)) {
                    error_log('Request body: ' . $body);
                }
                // error_log(print_r(debug_backtrace(), true));
                echo 'error ' . $httpCode;
                return false;
            }
        } catch (Exception $e) {
            echo 'curl error: ' . $e->getMessage();
            return false;
        }
        curl_close($curl);

        $restApiCacheObj = json_decode($response);
        set_transient($url . $body, $restApiCacheObj, $cacheTime);
    } else {
        $restApiCacheObj = get_transient($url . $body);
    }

    return $restApiCacheObj;
}

function register_query_vars($qvars)
{
    $qvars[] = 'discipline';
    $qvars[] = 'educationalContext';
    $qvars[] = 'intendedEndUserRole';
    $qvars[] = 'oer';
    return $qvars;
}

add_filter('query_vars', 'register_query_vars');

function getWloVocabs($type)
{
    $transient = 'vocab_' . $type;
    $vocab_json = null;
    if (false === ($value = get_transient($transient))) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/' . $type . '/index.json');
        $vocab_json = json_decode($json);
        set_transient($transient, $vocab_json, 60 * 60 * 12);
    } else {
        $vocab_json = get_transient($transient);
    }
    return $vocab_json;
}

/**
 * Returns an associative array of value-label pairs for the given vocabs. 
 */
function getWloVocabsValueLabelPairs($type, $lang = 'de') {
    $vocabs = getWloVocabs($type);
    $entries = $vocabs->hasTopConcept;
    $result = [];
    foreach($entries as &$entry) {
        $result[$entry->id] = $entry->prefLabel->$lang;
    }
    unset($entry);
    return $result;
}

function getNewLrtList($id)
{
    $transient = 'new_lrt_vocab_' . $id;
    $vocab_json = null;
    $new_lrt_list = array();
    if (false === ($value = get_transient($transient))) {
        // this code runs when there is no valid transient set
        // Get Select-Field Options from Vocab Scheme
        $json = file_get_contents('https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/new_lrt/' . $id . '.json');
        $vocab_json = json_decode($json);

        if (!empty($vocab_json)) {
            $new_lrt_list[] = array(
                'id' => $vocab_json->id,
                'label' => $vocab_json->prefLabel->de,
            );

            if (!empty($vocab_json->narrower)) {
                foreach ($vocab_json->narrower as $item) {
                    $new_lrt_list[] = array(
                        'id' => $item->id,
                        'label' => $item->prefLabel->de,
                    );
                }
            }
        }

        set_transient($transient, $new_lrt_list, 60 * 60 * 12);
    } else {
        $new_lrt_list = get_transient($transient);
    }
    return $new_lrt_list;
}

function get_educational_filter_values($postID)
{

    $collection_level = map_educational_filter_values('collection_level', $postID);
    $collection_url = map_educational_filter_values('collection_url', $postID);
    $disciplines = map_educational_filter_values('discipline', $postID, true);
    $educationalContexts = map_educational_filter_values('educationalContext', $postID, true);
    $intendedEndUserRoles = map_educational_filter_values('intendedEndUserRole', $postID, true);
    $oer = map_educational_filter_values('oer', $postID);
    $objectTypes = map_educational_filter_values('objectTypes', $postID, true);
    $learningResourceTypes = map_educational_filter_values('learningResourceTypes', $postID, true);
    $generalKeywords = map_educational_filter_values('generalKeyword', $postID);
    $oehWidgets = map_educational_filter_values('oehWidgets', $postID, true);

    return [
        "collectionUrl" => (!empty($collection_url)) ? $collection_url : '',
        "collectionLevel" => (!empty($collection_level)) ? $collection_level : '',
        "disciplines" => (!empty($disciplines)) ? $disciplines : [],
        "educationalContexts" => (!empty($educationalContexts)) ? $educationalContexts : [],
        "intendedEndUserRoles" => (!empty($intendedEndUserRoles)) ? $intendedEndUserRoles : [],
        "oer" => boolval($oer),
        "objectTypes" => (!empty($objectTypes)) ? $objectTypes : '',
        "learningResourceTypes" => (!empty($learningResourceTypes)) ? $learningResourceTypes : '',
        "generalKeyword" => (!empty($generalKeywords)) ? $generalKeywords : '',
        "oehWidgets" => (!empty($oehWidgets)) ? $oehWidgets : '',
    ];
}

function map_educational_filter_values($filter, $postID, $array = false)
{

    if (!empty($_GET[$filter])) {
        return $_GET[$filter];
    } else if (!empty(get_field($filter))) {
        return $array ? array_column(get_field($filter), 'value') : get_field($filter);
    } else if (!empty(get_field($filter, $postID))) {
        return $array ? array_column(get_field($filter, $postID), 'value') : get_field($filter, $postID);
    }
    return '';
}


function map_vocab_disciplines($n)
{
    return "\"http://w3id.org/openeduhub/vocabs/discipline/" . strval($n) . "\"";
}
function map_vocab_educationalContexts($n)
{
    return "\"http://w3id.org/openeduhub/vocabs/educationalContext/" . strval($n) . "\"";
}
function map_vocab_intendedEndUserRoles($n)
{
    return "\"http://w3id.org/openeduhub/vocabs/intendedEndUserRole/" . strval($n) . "\"";
}
function map_vocab_learningResourceTypes($n)
{
    return "\"http://w3id.org/openeduhub/vocabs/learningResourceType/" . strval($n) . "\"";
}
function map_vocab_disciplines_value_only($n)
{
    return "http://w3id.org/openeduhub/vocabs/discipline/" . strval($n) . "";
}
function map_vocab_educationalContexts_value_only($n)
{
    return "http://w3id.org/openeduhub/vocabs/educationalContext/" . strval($n) . "";
}
function map_vocab_intendedEndUserRoles_value_only($n)
{
    return "http://w3id.org/openeduhub/vocabs/intendedEndUserRole/" . strval($n) . "";
}

function map_vocab_learning_resource_types_value_only($n)
{
    return "http://w3id.org/openeduhub/vocabs/learningResourceType/" . strval($n) . "";
}

function map_vocab_oeh_widgets_value_only($n)
{
    return "http://w3id.org/openeduhub/vocabs/widgets/" . strval($n) . "";
}

function map_vocab_value_to_quotes($n)
{
    return "\"" . strval($n) . "\"";
}

function trim_https_http_from_array($array)
{
    return str_replace(["https", "http"], '', $array);
}


function wlo_edu_filter($collectionData, $wpData, $filter)
{
    $propDisciplines = (!empty($collectionData)) ? $collectionData : [];
    $wpData = (!empty($wpData)) ? $wpData : [];

    $propDisciplines = (!empty($propDisciplines)) ? array_filter($propDisciplines) : [];
    $propDisciplines = (!empty($propDisciplines)) ? trim_https_http_from_array($propDisciplines) : [];

    $disciplinesVocab = (!empty(array_filter($wpData))) ? array_map($filter, $wpData) : [];
    $disciplinesVocab = (!empty($disciplinesVocab)) ? array_filter($disciplinesVocab) : [];
    $disciplinesVocab = (!empty($disciplinesVocab)) ? trim_https_http_from_array($disciplinesVocab) : [];

    $filterDiscipline = (empty($disciplinesVocab) || empty($propDisciplines)) ? false : empty(array_intersect($propDisciplines, $disciplinesVocab));

    return $filterDiscipline;
}

function getSearchFilterValues($field, $postID)
{
    $field_values = (!empty(get_field($field, $postID))) ? get_field($field, $postID) : [];
    if (!empty(get_field($field))) {
        $field_values = get_field($field);
    };
    $search_filter = '';
    if (!empty($field_values)) {
        if ($field == 'learningResourceTypes') {
            $field = 'learningResourceType';
        }
        $search_filter .= '"' . $field . '":[';
        $i = 0;
        $len = count($field_values);
        foreach ($field_values as $value) {
            if ($i == $len - 1) {
                $search_filter .= '"' . $value['label'] . '"';
            } else {
                $search_filter .= '"' . $value['label'] . '",';
            }
            $i++;
        }
        $search_filter .= '],';
    }
    return $search_filter;
}


//add_action( 'user_register', 'wlo_registration_save', 10, 1 );

function wlo_registration_save($user_id)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, KEYCLOAK_URL . "protocol/openid-connect/token");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        "username=" . KEYCLOAK_USER . "&password=" . KEYCLOAK_PW . "&client_id=wlo_wordpress&grant_type=password"
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = json_decode(curl_exec($ch));
    curl_close($ch);


    if ($server_output->access_token) {

        $user = get_userdata($user_id);

        $curl_post_data = array(
            "username" => $user->user_login,
            "firstName" => $user->first_name,
            "lastName" => $user->last_name,
            "email" => $user->user_email,
            "enabled" => 'true',
        );
        $data_string = json_encode($curl_post_data);
        $url = KEYCLOAK_URL . 'users';

        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt(
                $curl,
                CURLOPT_HTTPHEADER,
                array(
                    'Accept: application/json',
                    'Content-Type: application/json; charset=utf-8',
                    'Authorization: Bearer ' . $server_output->access_token
                )
            );
            $response = curl_exec($curl);
            if ($response === false) {
                error_log('curl error' . curl_error($curl));
                return false;
            }
        } catch (Exception $e) {
            error_log('curl error: ' . $e->getMessage());
            return false;
        }
        curl_close($curl);

        $response = json_decode($response);

        error_log('response: ' . print_r($response, true));

        return true;
    }
}

function restrict_redaktionsumgebung()
{
    if (is_page(9935) && !is_user_logged_in()) {
        $loginUrl = home_url('/login/');
        wp_redirect($loginUrl);
        exit();
    }
}
add_action('template_redirect', 'restrict_redaktionsumgebung');


function wlo_update_custom_roles()
{
    if (get_option('custom_roles_version') < 5) {
        add_role('portal_redakteur', 'Themenportal Redakteur', get_role('editor')->capabilities);
        add_role('community_redakteur', 'Community Redakteur', get_role('subscriber')->capabilities);
        add_role('uploadtahon_user', 'Uploadtahon', get_role('editor')->capabilities);
        update_option('custom_roles_version', 5);
    }
}
add_action('init', 'wlo_update_custom_roles');

/**
 * Calls the Google Geocoding API to resolve an address string provided by the user to geo
 * coordinates.
 * 
 * @return array results as described here: https://developers.google.com/maps/documentation/geocoding/requests-geocoding#results
 */
function callGeocodingApi(string $address)
{
    $url = 'https://maps.googleapis.com/maps/api/geocode/json'
        . '?address=' . urlencode($address)
        . '&language=de'
        . '&region=de' // bias results to be located in Germany, does not fully restrict results
        . '&key=' . GOOGLE_MAPS_API_KEY;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    try {
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    } catch (Exception $e) {
        error_log('Exception when requesting ' . $url . ': ' . $e->getMessage());
        return false;
    }
    curl_close($curl);
    if ($httpCode != 200) {
        error_log('Response code ' . $httpCode . ' when requesting ' . $url . ': ' . $response);
        return false;
    }
    // error_log($response);
    $response = json_decode($response, true);
    if ($response['status'] != 'OK') {
        error_log('Status ' . $response['status'] . ' when requesting ' . $url . ': ' . $response['error_message']);
        return false;
    }
    return $response['results'];
}

function callRepoApi($restUrl, $data = null, $contentType = 'Content-Type: application/json', $mode = 'POST', $ticket = null)
{
    $apiUrl = WLO_REPO . $restUrl;
    $login = WLO_REPO_LOGIN;
    $password = WLO_REPO_PW;

    $curl = curl_init($apiUrl);
    if ($mode == 'PUT') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    } else if ($mode == 'POST') {
        curl_setopt($curl, CURLOPT_POST, 1);
    }
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if (empty($ticket)) {
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "$login:$password");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            $contentType,
        ));
    } else {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: EDU-TICKET ' . $ticket,
            'Accept: application/json',
            $contentType,
        ));
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // EXECUTE:
    try {
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($result === false) {
            echo 'Result empty: ' . curl_error($curl) . '<br>';
            return false;
        }
    } catch (Exception $e) {
        echo 'Exception: ' . $e->getMessage() . '<br>';
        return false;
    }
    //error_log(print_r(curl_getinfo($curl), true));
    curl_close($curl);

    if ($httpcode != 200) {
        error_log($mode . ' request to ' . $apiUrl . ' failed with response code ' . $httpcode);
        error_log(print_r($result, true));
        echo "Connection Failure (http-code: " . $httpcode . ", mode: " . $mode . ")<br>";
        return false;
    }

    //error_log(print_r($result, true));
    return json_decode($result, true);
}

function callMailcowAPI($endpoint, $mode = 'GET', $data = NULL)
{
    $ch = curl_init();

    $url = MAILCOW_URL . $endpoint;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    if ($mode == 'POST') {
        curl_setopt($ch, CURLOPT_POST, TRUE);
    }

    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "X-API-Key: " . MAILCOW_APIKEY
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    //var_dump($response);
    return $response;
}


if (!function_exists('post_meta_request_params')) :
    function post_meta_request_params($args, $request)
    {
        $args += array(
            'meta_key'   => $request['meta_key'],
            'meta_value' => $request['meta_value'],
            'meta_query' => $request['meta_query'],
            'meta_compare' => $request['meta_compare']
        );

        return $args;
    }
    //add_filter( 'rest_post_query', 'post_meta_request_params', 99, 2 );
    // add_filter( 'rest_page_query', 'post_meta_request_params', 99, 2 ); // Add support for `page`
    add_filter('rest_portal_query', 'post_meta_request_params', 99, 2); // Add support for `my-custom-post`
endif;


/**
 * Updates the `cclom:location` property of the collection referenced by a topics page to point to
 * the topics page when the topics page's URL changed.
 */
function updateCCMlocation($post_ID, $post_after, $post_before)
{
    $template = get_page_template_slug($post_ID);
    if (
        'portal' == get_post_type($post_ID)
        && $template == 'page-templates/template_themenseite.php'
        && $post_before->post_name != $post_after->post_name
    ) {
        $educational_filter_values = get_educational_filter_values($post_ID);
        $collectionUrl = $educational_filter_values["collectionUrl"];

        $pattern = '/http.*\?id=(.*)(&|$)/';
        preg_match_all($pattern, $collectionUrl, $matches);

        $url = 'rest/node/v1/nodes/-home-/' . $matches[1][0] . '/metadata?versionComment=change%20cclom%3Alocation';
        $data = '{"cclom:location":["' . get_permalink($post_ID) . '"]}';

        $result = callRepoApi($url, $data);
        if ($result) {
            error_log('changed slug');
        } else {
            error_log('curl error');
        }
    }
}
add_action('post_updated', 'updateCCMlocation', 10, 3);


function hex2rgb($hex)
{
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb = array($r, $g, $b);
    return implode(",", $rgb); // returns the rgb values separated by commas
    //return $rgb; // returns an array with the rgb values
}

add_action("wpcf7_before_send_mail", "registerNewsletter");
function registerNewsletter($cf7)
{
    // get the contact form object
    $wpcf = WPCF7_ContactForm::get_current();
    $submission = WPCF7_Submission::get_instance();
    //Below statement will return all data submitted by form.
    $data = $submission->get_posted_data();

    if ($data['newsletter']) {
        $data = array(
            'fields[email]' => $data['your-email'],
            'ml-submit' => 1
        );
        $url = 'https://static.mailerlite.com/webforms/submit/c6v7a9';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: */*',
        ]);
        $response = curl_exec($ch);
        //$info = curl_getinfo($ch);
        curl_close($ch);
        //error_log('response: ' . $response);
    }

    return $wpcf;
}

function wloSubjectType($subject)
{
    switch ($subject) {
        case 'Biologie':
        case 'Mathematik':
        case 'Physik':
        case 'Chemie':
        case 'Informatik':
            return array(
                'color' => '#003B7C',
                'type' => 'MINT',
            );

        case 'Politik':
        case 'Politische Bildung':
        case 'Geschichte':
        case 'Wirtschaft':
        case 'Pädagogik':
        case 'Geographie':
            return array(
                'color' => '#3DA6EE',
                'type' => 'Gesellschaftswissenschaften',
            );


        case 'Religion':
        case 'Philosophie & Ethik':
        case 'Philosophie &#038; Ethik':
        case 'Ethik':
            return array(
                'color' => '#7F6FEE',
                'type' => 'Religion',
            );

        case 'Musik':
        case 'Kunst':
        case 'Darstellendes Spiel':
            return array(
                'color' => '#E73445',
                'type' => 'Musische-Fächer',
            );

        case 'Deutsch':
        case 'Deutsch als Zweitsprache':
            return array(
                'color' => '#EC4A70',
                'type' => 'Deutsch',
            );

        case 'Englisch':
        case 'Türkisch':
        case 'Spanisch':
        case 'Französisch':
            return array(
                'color' => '#EF809A',
                'type' => 'Fremdsprachen',
            );

        case 'Sport':
            return array(
                'color' => '#B4DA1C',
                'type' => 'Sport',
            );

        case 'Medienbildung':
        case 'Nachhaltigkeit':
        case 'Zeitgemäßse Bildung':
        case 'Berufsorientierung':
        case 'Lernen lernen':
        case 'Gewaltprävention':
        case 'Sozialpädagogik':
        default:
            return array(
                'color' => '#FFB930',
                'type' => 'Querschnittsthemen',
            );
    }
}


function pagination_bar()
{
    global $wp_query;

    $total_pages = $wp_query->max_num_pages;

    if ($total_pages > 1) {
        $current_page = max(1, get_query_var('paged'));

        echo paginate_links(array(
            'base' => get_pagenum_link(1) . '%_%',
            'format' => '/page/%#%',
            'current' => $current_page,
            'total' => $total_pages,
        ));
    }
}

/**
 * Ultimate Member 2.0 - Customization
 * Description: Allow everyone to upload profile registration pages.
 */
add_filter("um_user_pre_updating_files_array", "um_custom_user_pre_updating_files_array", 10, 1);
function um_custom_user_pre_updating_files_array($arr_files)
{

    if (is_array($arr_files)) {
        foreach ($arr_files as $key => $details) {
            if ($key == "reg_profile_img") {
                unset($arr_files[$key]);
                $arr_files["profile_photo"] = $details;
            }
        }
    }

    return $arr_files;
}

add_filter("um_allow_frontend_image_uploads", "um_custom_allow_frontend_image_uploads", 10, 3);
function um_custom_allow_frontend_image_uploads($allowed, $user_id, $key)
{

    if ($key == "profile_photo") {
        return true;
    }

    return $allowed; // false
}


add_action('show_user_profile', 'wlo_show_extra_profile_fields');
add_action('edit_user_profile', 'wlo_show_extra_profile_fields');
function wlo_show_extra_profile_fields($user)
{
    $mainSubject = get_the_author_meta('mainSubject', $user->ID);
?>
    <h3><?php esc_html_e('Fachredaktion', 'crf'); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="mainSubject"><?php esc_html_e('Hautptfach', 'wlo'); ?></label></th>
            <td>
                <select id="mainSubject" name="mainSubject">
                    <?php
                    $subjects = getWloVocabs('discipline')->hasTopConcept;

                    foreach ($subjects as $subject) {
                        if ($subject->prefLabel->de == $mainSubject) {
                            echo '<option value="' . $subject->prefLabel->de . '" selected="selected">' . $subject->prefLabel->de . '</option>';
                        } else {
                            echo '<option value="' . $subject->prefLabel->de . '">' . $subject->prefLabel->de . '</option>';
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
<?php
}

function new_contactmethods($contactmethods)
{
    $contactmethods['wloEmail'] = 'WLO E-Mail';
    unset($contactmethods['facebook']);
    unset($contactmethods['instagram']);
    unset($contactmethods['linkedin']);
    unset($contactmethods['myspace']);
    unset($contactmethods['pinterest']);
    unset($contactmethods['soundcloud']);
    unset($contactmethods['tumblr']);
    unset($contactmethods['twitter']);
    unset($contactmethods['youtube']);
    unset($contactmethods['wikipedia']);
    return $contactmethods;
}
add_filter('user_contactmethods', 'new_contactmethods', 10, 1);

add_action('personal_options_update', 'wlo_update_profile_fields');
add_action('edit_user_profile_update', 'wlo_update_profile_fields');
function wlo_update_profile_fields($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (!empty($_POST['mainSubject'])) {
        update_user_meta($user_id, 'mainSubject',  $_POST['mainSubject']);
    }
}

/**
 * Replaces the origin of $url with $origin.
 * 
 * Example:
 *  replaceOrigin('https://example.com/foo?q=bar', 'http://localhost:8000')
 *      returns 'http://localhost:8000/foo?q=bar'
 */
function replaceOrigin(string $url, string $origin): string
{
    $parsedUrl = parse_url($url);
    $currentOrigin =
        $parsedUrl['scheme'] . '://' .
        $parsedUrl['host'] .
        (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '');
    return str_replace($currentOrigin, $origin, $url);
}

function wlo_convert_dev_url(?string $url): ?string
{
    if (isset($url) && get_site_url() != 'https://wirlernenonline.de') {
        return replaceOrigin($url, get_site_url());
    }
    return $url;
}

function wlo_getPortalPostId(string $name): int
{
    switch ($name) {
        case 'DaZ':
            $name = 'Deutsch als Zweitsprache';
            break;
        case 'Deutsch':
            //$name = 'Deutsch - Musterfachseite'; // nur für dev!
            break;
        case 'Berufsorientierung':
            $name = 'Zukunfts- und Berufsorientierung';
            break;
        case 'Philosophie':
            $name = 'Philosophie & Ethik';
            break;
        case 'OER':
            return 'a87c092d-e3b5-43ef-81db-757ab1967646';
    }

    $query_args = array(
        'posts_per_page' => 1,
        'post_type' => 'portal',
        'post_status' => 'publish',
        'title' => $name,
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'collection_level',
                'value' => 0
            )
        )
    );
    $query_result = new WP_Query($query_args);

    return $query_result->posts[0];
}

function wlo_getPortalIDbyName($name)
{
    $postId = wlo_getPortalPostId($name);
    return wlo_getPortalIdByPostId($postId);
}

function wlo_getPortalIdByPostId($postId)
{
    $collectionUrl = get_field('collection_url', $postId);
    $url_components = parse_url($collectionUrl);
    parse_str($url_components['query'], $params);
    return $params['id']; // collectionID
}
