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
                'singular_name' => __( 'Themenportal' ),
                'edit_item' => __( 'Themenportal bearbeiten'),
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

add_filter('manage_portal_posts_columns' , 'add_portal_columns');

function add_portal_columns( $columns ) {

    $columns = array(
        'cb'           => '<input type="checkbox" />',
        'title'        => 'Title',
        'collection_level' => 'Portalebene',
        'collection_url' => 'Sammlungs-URL',
        'discipline'  => 'Fach',
        'educationalContext'     => 'Bildungsstufe',
        'intendedEndUserRole'     => 'Zielgruppe',
        'categories'   => 'Categories',
        'tags'   => 'Tags',
        'author'   => 'Autor',
        'date'         =>  'Date',
    );
    return $columns;
}

add_action( 'manage_portal_posts_custom_column', 'portal_columns', 10, 2 );

function portal_columns( $column ) {

    global $post;

    switch ( $column ) {
        case 'collection_level':
            if(intval(get_field( "collection_level", $post->ID )) == 0)
                echo "Fachportal";
            else
                echo "Themenportal";
            break;
        case 'collection_url':
            if(!empty(get_field( "collection_url", $post->ID ))){
                ?>
                <a href="<?php echo get_field( "collection_url", $post->ID )?>">Zur Sammlung</a>
                <?php
            }
            break;
        case 'discipline':
            if(!empty(get_field( "discipline", $post->ID )))
                echo implode(", ", array_column(get_field( "discipline", $post->ID ), "label"));
            break;

        case 'educationalContext':
            if(!empty(get_field( "educationalContext", $post->ID )))
                echo implode(", ", array_column(get_field( "educationalContext", $post->ID ), "label"));
            break;

        case 'intendedEndUserRole':
            if(!empty(get_field( "intendedEndUserRole", $post->ID )))
                echo implode(", ", array_column(get_field( "intendedEndUserRole", $post->ID ), "label"));
            break;
    }

}


function portal_sortable_columns( $columns ) {
    $columns['discipline'] = 'discipline';
    return $columns;
}
add_filter( 'manage_edit-portal_sortable_columns', 'portal_sortable_columns');

function wlo_portal_orderby( $query ) {
    if( ! is_admin() ) {
        return;
    }

    $orderby = $query->get( 'orderby');
    if ( 'discipline' == $orderby ) {
        $query->set('meta_key',$orderby);
        $query->set('orderby','meta_value');
    }
}
add_filter( 'pre_get_posts','wlo_portal_orderby');



function prefix_disable_gutenberg($current_status, $post_type){
    // Use your post type key instead of 'product'
    if ($post_type === 'edusource' || $post_type === 'edutool' || $post_type === 'partner' || $post_type === 'presse' || $post_type === 'uxideas'){
        return false;
    }
    return $current_status;
}
add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);


/**
 * Add a select dropdown filter with meta values.
 */
function wlo_portal_dropdown() {
    $scr = get_current_screen();
    if ( $scr->base !== 'edit' && $scr->post_type !== 'portal') return;

    $selected = filter_input(INPUT_GET, 'discipline_filter', FILTER_SANITIZE_STRING );

    $choices = [
        '720' => 'Allgemein',
        '80' => 'Biologie',
        '100' => 'Chemie',
        '12002' => 'Darstellen & Gestalten',
        '120' => 'Deutsch',
        '240' => 'Geschichte',
        '320' => 'Informatik',
        '60' => 'Kunst',
        '380' => 'Mathematik',
        '900' => 'Medienbildung',
        '400' => 'Mediendidaktik',
        '460' => 'Physik ',
    ];

    echo'<select name="discipline_filter">';
    echo '<option value="all" '. (( $selected == 'all' ) ? 'selected="selected"' : "") . '>' . 'Alle Fächer' . '</option>';
    foreach( $choices as $key => $value ) {
        echo '<option value="' . $key . '" '. (( $selected == $key ) ? 'selected="selected"' : "") . '>' . $value . '</option>';
    }
    echo'</select>';
}

add_action('restrict_manage_posts', 'wlo_portal_dropdown');


function wlo_portal_discipline_filter($query) {
    if ( is_admin() && $query->is_main_query() ) {
        $scr = get_current_screen();
        if ( $scr->base !== 'edit' && $scr->post_type !== 'portal' ) return;

        if (isset($_GET['discipline_filter']) && $_GET['discipline_filter'] != 'all') {
            $query->set('meta_query', array( array(
                'key' => 'discipline',
                'value' => '"'.$_GET['discipline_filter'].'"',
                'compare' => 'LIKE',
            ) ) );
        }
    }
}

add_action('pre_get_posts','wlo_portal_discipline_filter');


function only_blog_posts( $query ) {
    if ( $query->is_home() && $query->is_main_query() ) {
        $query->set( 'cat', '1');
    }
}
add_action( 'pre_get_posts', 'only_blog_posts' );
