<?php
// Register Navigation Menus
function custom_navigation_menus() {

    $locations = array(
        'top' => __( 'Top', 'text_domain' ),
        'footer' => __( 'Footer', 'text_domain' ),
        'top-call-top-action' => __( 'Top Call To Action', 'text_domain' ),
        'add-content-button' => __( 'Add Content Button', 'text_domain' ),
    );
    register_nav_menus( $locations );

}
add_action( 'init', 'custom_navigation_menus' );

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
