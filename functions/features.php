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
    # email data
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
                'title' => 'Themenportal',
                'icon' => 'list-view',
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

function callWloGraphApi($search_query)
{
    $curl_post_data = array("query" => $search_query);
    $data_string = json_encode($curl_post_data);
    //$url = 'https://suche.wirlernenonline.de/relay/graphql';
    $url = 'https://staging.wirlernenonline.de/relay/graphql';

    try {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
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
        trigger_error($e->getMessage(), E_USER_WARNING);
        return false;
    }
    curl_close($curl);

    return json_decode($response);
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

function get_educational_filter_values($postID)
{

    //Collection URL
    $collection_url = (!empty(get_field('collection_url'))) ? get_field('collection_url') : get_field('collection_url', $postID);
    $collection_url = (!empty($collection_url)) ? $collection_url : get_post_meta($postID, 'collection_url', true);

    //Disciplines
    $query_var_disciplines = (!empty(get_query_var('discipline', null))) ? explode(";", get_query_var('discipline', null)) : [];
    $block_var_disciplines = (!empty(get_field('discipline'))) ? get_field('discipline') : [];
    $portal_var_disciplines = (!empty(get_field('discipline', $postID))) ? get_field('discipline', $postID) : [];

    $disciplines = (!empty($portal_var_disciplines)) ? $portal_var_disciplines : [];
    $disciplines = (!empty($block_var_disciplines)) ? $block_var_disciplines : $disciplines;
    $disciplines = (!empty($disciplines)) ? array_column($disciplines, 'value') : [];
    $disciplines = (!empty($query_var_disciplines)) ? $query_var_disciplines : $disciplines;

    // Preview
    $disciplines = (!empty($disciplines)) ? $disciplines : get_post_meta($postID, 'discipline', false)[0];


    //EducationalContext
    $query_var_educationalContexts = (!empty(get_query_var('educationalContext', null))) ? explode(";", get_query_var('educationalContext', null)) : [];
    $block_var_educationalContexts = (!empty(get_field('educationalContext'))) ? get_field('educationalContext') : [];
    $portal_var_educationalContexts = (!empty(get_field('educationalContext', $postID))) ? get_field('educationalContext', $postID) : [];


    $educationalContexts = (!empty($portal_var_educationalContexts)) ? $portal_var_educationalContexts : [];
    $educationalContexts = (!empty($block_var_educationalContexts)) ? $block_var_educationalContexts : $educationalContexts;
    $educationalContexts = (!empty($educationalContexts)) ? array_column($educationalContexts, 'value') : [];
    $educationalContexts = (!empty($query_var_educationalContexts)) ? $query_var_educationalContexts : $educationalContexts;

    // Preview
    $educationalContexts = (!empty($educationalContexts)) ? $educationalContexts : get_post_meta($postID, 'educationalContext', false)[0];


    //intendedEndUserRole
    $query_var_intendedEndUserRoles = (!empty(get_query_var('intendedEndUserRole', null))) ? explode(";", get_query_var('intendedEndUserRole', null)) : [];
    $block_var_intendedEndUserRoles = (!empty(get_field('intendedEndUserRole'))) ? get_field('intendedEndUserRole') : [];
    $portal_var_intendedEndUserRoles = (!empty(get_field('intendedEndUserRole', $postID))) ? get_field('intendedEndUserRole', $postID) : [];

    $intendedEndUserRoles = (!empty($portal_var_intendedEndUserRoles)) ? $portal_var_intendedEndUserRoles : [];
    $intendedEndUserRoles = (!empty($block_var_intendedEndUserRoles)) ? $block_var_intendedEndUserRoles : $intendedEndUserRoles;
    $intendedEndUserRoles = (!empty($intendedEndUserRoles)) ? array_column($intendedEndUserRoles, 'value') : [];
    $intendedEndUserRoles = (!empty($query_var_intendedEndUserRoles)) ? $query_var_intendedEndUserRoles : $intendedEndUserRoles;

    // Preview
    $intendedEndUserRoles = (!empty($intendedEndUserRoles)) ? $intendedEndUserRoles : get_post_meta($postID, 'intendedEndUserRole', false)[0];


    //OER
    $query_var_oer = get_query_var('oer', false);
    $block_var_oer = get_field('oer');
    $portal_var_oer = (!empty(get_field('oer', $postID))) ? get_field('oer', $postID) : [];

    $oer = (!empty($portal_var_oer)) ? $portal_var_oer : false;
    $oer = (!empty($block_var_oer)) ? $block_var_oer : $oer;
    $oer = (!empty($query_var_oer)) ? $query_var_oer : $oer;


    // Preview
    $oer = (!empty($oer)) ? $oer : get_post_meta($postID, 'oer', false)[0];

    return [
        "collectionUrl" => (!empty($collection_url)) ? $collection_url : '',
        "disciplines" => (!empty($disciplines)) ? $disciplines : [],
        "educationalContexts" => (!empty($educationalContexts)) ? $educationalContexts : [],
        "intendedEndUserRoles" => (!empty($intendedEndUserRoles)) ? $intendedEndUserRoles : [],
        "oer" => boolval($oer)
    ];
}

function map_vocab_disciplines($n)
{
    return "\"https://w3id.org/openeduhub/vocabs/discipline/" . strval($n) . "\"";
}
function map_vocab_educationalContexts($n)
{
    return "\"https://w3id.org/openeduhub/vocabs/educationalContext/" . strval($n) . "\"";
}
function map_vocab_intendedEndUserRoles($n)
{
    return "\"https://w3id.org/openeduhub/vocabs/intendedEndUserRole/" . strval($n) . "\"";
}
function map_vocab_learningResourceTypes($n)
{
    return "\"http://w3id.org/openeduhub/vocabs/learningResourceType/" . strval($n) . "\"";
}
function map_vocab_disciplines_value_only($n)
{
    return "https://w3id.org/openeduhub/vocabs/discipline/" . strval($n) . "";
}
function map_vocab_educationalContexts_value_only($n)
{
    return "https://w3id.org/openeduhub/vocabs/educationalContext/" . strval($n) . "";
}
function map_vocab_intendedEndUserRoles_value_only($n)
{
    return "https://w3id.org/openeduhub/vocabs/intendedEndUserRole/" . strval($n) . "";
}

function map_vocab_learning_resource_types_value_only($n)
{
    return "http://w3id.org/openeduhub/vocabs/learningResourceType/" . strval($n) . "";
}

function map_vocab_value_to_quotes($n)
{
    return "\"" . strval($n) . "\"";
}

function trim_https_http_from_array($array){
    return str_replace(["https", "http"],'', $array);
}


