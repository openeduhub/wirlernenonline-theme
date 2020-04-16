<?php
// %%%%%%%%%% Title Tag
//add_theme_support( 'title-tag' );

// %%%%%%%%%% Custom Excerpt Length %%%%%%%%%
function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more( $more ) {
	return ' … <a class="read-more" href="' . get_permalink( get_the_ID() ) . '">' . __( ' <i class="fa fa-long-arrow-right"></i>' ) . '</a>';
}
add_filter( 'excerpt_more', 'new_excerpt_more' );

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
function svg_support ( $svg_mime ){
$svg_mime['svg'] = 'image/svg+xml';
return $svg_mime;
}
add_filter( 'upload_mimes', 'svg_support' );

// Disable admin bar in site view
show_admin_bar(false);
