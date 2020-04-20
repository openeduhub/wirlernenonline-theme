<?php
// %%%%%%%%%% Title Tag
//add_theme_support( 'title-tag' );

// %%%%%%%%%% Post Thumbnails
add_theme_support( 'post-thumbnails' );

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


function get_field_values($str) {
  $field = get_field($str);

  if (!is_array($field)) {
    return null;
  }

  $values = array_map(function($v) {
    if ($v instanceof WP_Term) {
      return $v->term_id;
    }
    return $v['value'];
  }, $field);

  return implode(',', $values);
}

function change_acf_form_title( $field ) {

    if ( is_page_template('template_add_source.php') ) {
        $field['label'] = "Titel der Quelle";
        //$field['instructions'] = "Changed Instruction";
    }elseif (is_page_template('template_add_tool.php')){
        $field['label'] = "Titel des Tools";
    }

    if ( $field ) {
        return $field;
    } else {
        exit;
    }
}
add_filter('acf/prepare_field/name=_post_title', 'change_acf_form_title');
