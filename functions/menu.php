<?php
// Register Navigation Menus
function custom_navigation_menus() {

    $locations = array(
        'top' => __( 'Top', 'text_domain' ),
        'footer' => __( 'Footer', 'text_domain' ),
        'top-call-top-action' => __( 'Top Call To Action', 'text_domain' ),
        'add-content-button' => __( 'Add Content Button', 'text_domain' ),
        'editorial-menu' => __( 'Redaktionsumgebung', 'text_domain' ),
    );
    register_nav_menus( $locations );

}
add_action( 'init', 'custom_navigation_menus' );

class editorial_walker_nav_menu extends Walker_Nav_Menu {

    function start_lvl(&$output, $depth = 0, $args = array()) {
        $output .= '<ul class="sub-menu">';
        $parent = $depth>0 ? 'zurück' : '<h2><img src="'.get_template_directory_uri().'/src/assets/img/redaktions-tools.svg" alt=""> Redaktionsumgebung</h2>';
        $output .= '<div class="menu-back"><button class="menu-back-button"><img src="'.get_template_directory_uri().'/src/assets/img/arrow_down_white.svg" alt=""> </button>'.$parent.'</div>';
    }

    function start_el(&$output, $item, $depth=0, $args=[], $id=0) {

        if ( '-' === $item->title )        {
            // you may remove the <hr> here and use plain CSS.
            $output .= '<li class="wlo-menu-separator"><hr>';
        }
        else{
            $output .= "<li class='" .  implode(" ", $item->classes) . "'>";


            $has_children = array_search ( 'menu-item-has-children' , $item->classes );
            if ($has_children != false) {
                $output .= '<a class="toggle-button" href="#"><span>' . apply_filters( 'the_title', $item->title, $item->ID ).'</span>';
                $output .= '<img class="toggle-arrow" src="'.get_template_directory_uri().'/src/assets/img/arrow_down.svg" alt="">';
                $output .= '</a>';
            }else{
                $linkUrl = $item->url;
                if (str_contains($linkUrl, '--username--')) {
                    $user    = wp_get_current_user();
                    $linkUrl = str_replace('--username--', $user->user_login, $linkUrl);
                }
                if (strpos($linkUrl, WLO_REPO) !== false && is_user_logged_in() ){
                    if (function_exists('get_repo_ticket')){
                        $ticket = get_repo_ticket();
                    }else{
                        $ticket = '';
                    }

                    if (strpos($linkUrl, '?') !== false){
                        $linkUrl .= '&ticket='.$ticket;
                    }else{
                        $linkUrl .= '?ticket='.$ticket;
                    }
                }
                $output .= '<a href="' . $linkUrl . '" target="'.$item->target.'"><span>' . apply_filters( 'the_title', $item->title, $item->ID ).'</span></a>';

            }

            $output .= '</a>';
        }

    }
}

// %%%%%%%%%% Custom Walker for foundation 6 menu %%%%%%%%%%
class insertcart_walker extends Walker_Nav_Menu
{
    /*
     * Add vertical menu class and submenu data attribute to sub menus
     */

    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"vertical menu\" data-submenu>\n";
    }
}

// %%%%%%%%%% Custom Walker for foundation 6 menu %%%%%%%%%%
class cta_walker extends Walker_Nav_Menu
{
    /*
     * Add vertical menu class and submenu data attribute to sub menus
     */

    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '<ul class="cta-dropdown-content">';
    }

    function start_el(&$output, $item, $depth=0, $args=[], $id=0) {

        $has_children = array_search ( 'menu-item-has-children' , $item->classes );
        if ($has_children != false) {
            $output .= "<li class='" .  implode(" ", $item->classes) . "'>";

            $output .= '<a href="' . $item->url . '" target="'.$item->target.'"><span>' . apply_filters( 'the_title', $item->title, $item->ID ).'</span></a>';
        }else{
            $output .= "<li class='" .  implode(" ", $item->classes) . "'>";

            $output .= '<a href="' . $item->url . '" target="'.$item->target.'"><span>' . apply_filters( 'the_title', $item->title, $item->ID ).'</span></a>';
        }



    }
}
//Optional fallback
function f6_topbar_menu_fallback($args)
{
    /*
     * Instantiate new Page Walker class instead of applying a filter to the
     * "wp_page_menu" function in the event there are multiple active menus in theme.
     */

    $walker_page = new Walker_Page();
    $fallback = $walker_page->walk(get_pages(), 0);
    $fallback = str_replace("<ul class='children'>", '<ul class="children submenu menu vertical" data-submenu>', $fallback);

    echo '<ul class="dropdown menu data-dropdown-menu">'.$fallback.'</ul>';
}

// %%%%%%%%%% Add menu Slug as Class %%%%%%%%
function add_slug_class_to_menu_item($output){
 $ps = get_option('permalink_structure');
 if(!empty($ps)){
     $idstr = preg_match_all('/<li id="menu-item-(\d+)/', $output, $matches);
     foreach($matches[1] as $mid){
         $id = get_post_meta($mid, '_menu_item_object_id', true);
         $slug = basename(get_permalink($id));
         $output = preg_replace('/menu-item-'.$mid.'">/', 'menu-item-'.$mid.' menu-item-'.$slug.'">', $output, 1);
     }
 }
 return $output;
}
add_filter('wp_nav_menu', 'add_slug_class_to_menu_item');

add_filter( 'wp_get_nav_menu_items','nav_items', 11, 3 );

function nav_items( $items, $menu, $args )
{
    if($menu->slug == "add-content-button-menu")
    {
        $postID = get_the_ID();
        if(!is_null($postID)){
            $disciplines = (!empty(get_post_meta($postID, 'discipline', false)[0])) ? get_post_meta($postID, 'discipline', false)[0] : null;

            if(!empty($disciplines)){
                foreach ($items as $key => $item){
                    $item->url = add_query_arg( 'discipline', implode(',',$disciplines), $item->url );
                }
            }
        }
    }

    return $items;
}

add_action('admin_bar_menu', 'add_toolbar_items', 100);
function add_toolbar_items($admin_bar){
    // TODO: refactor this and the same code in login-redirect.php into the edu-sharing plugin
    if (function_exists('get_repo_ticket')) {
        $ticket = get_repo_ticket();
    } else {
        $ticket = '';
    }
    $admin_bar->add_menu( array(
        'id'    => 'redaktion',
        'title' => 'Redaktionsumgebung',
        'href'  => WLO_REPO . "/components/start?ticket=" . $ticket,
    ));
}
