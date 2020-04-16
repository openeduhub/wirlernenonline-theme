<?php
function customTMCE($settings) {
    $style_formats = array(
        array(
            'title' => 'Überschriften',
            'items' => array(
                array(
                    'title' => 'Default',
                    'items' => array(
                        array(
                            'title' => 'Heading 1',
                            'block' => 'h1',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 2',
                            'block' => 'h2',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 3',
                            'block' => 'h3',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 4',
                            'block' => 'h4',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 5',
                            'block' => 'h5',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 6',
                            'block' => 'h6',
                            'remove' => 'all',
                            'exact' => true
                        ),
                    ),
                ),
                array(
                    'title' => 'GTF MarkPro',
                    'items' => array(
                        array(
                            'title' => 'Heading 1',
                            'block' => 'h1',
                            'classes' => 'gtf-markpro',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 2',
                            'block' => 'h2',
                            'classes' => 'gtf-markpro',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 3',
                            'block' => 'h3',
                            'classes' => 'gtf-markpro',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 4',
                            'block' => 'h4',
                            'classes' => 'gtf-markpro',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 5',
                            'block' => 'h5',
                            'classes' => 'gtf-markpro',
                            'remove' => 'all',
                            'exact' => true
                        ),
                        array(
                            'title' => 'Heading 6',
                            'block' => 'h6',
                            'classes' => 'gtf-markpro',
                            'remove' => 'all',
                            'exact' => true
                        ),
                    )
                )
            )
        ),
        array(
            'title' => '60px Bold',
            'block' => 'p',
            'classes' => 'bold-60',
            'remove' => 'all',
            'exact' => true
        ),
        array(
            'title' => 'Lead',
            'block' => 'p',
            'classes' => 'lead',
            'remove' => 'all',
            'exact' => true
        ),
        array(
            'title' => 'Paragraph',
            'block' => 'p',
            'remove' => 'all',
            'exact' => true
        ),
    );

    $custom_colors = '
        "000000", "Black",
        "ffffff", "White",
        "33FF99", "Green",
        "FF7171", "Red",
        "0000FF", "Blue",
        "000033", "Purple",
    ';

    $settings['style_formats'] = json_encode( $style_formats);
    $settings['textcolor_map'] = '['.$custom_colors.']';
    $settings['textcolor_rows'] = 1;
    $settings['textcolor_cols'] = 9;
    $settings['toolbar1'] = 'styleselect, forecolor, bold, italic, underline, alignleft, aligncenter, alignright, numlist, link, unlink, removeformat';
    $settings['toolbar2'] = '';
    return $settings;
}

function noCustomColorTMCE( $plugins ) {
    // $remove = array('colorpicker');
    // array_diff($plugin, $remove);
    // return $plugins;
    foreach ( $plugins as $key => $plugin_name ) {
        if ( 'colorpicker' === $plugin_name ) {
            unset( $plugins[ $key ] );
            return $plugins;
        }
    }
    return $plugins;
}

function acfTinyMCE($toolbars) {
    $toolbars['GTF'] = array();
    $toolbars['GTF'][1] = array('styleselect', 'forecolor', 'bold', 'italic', 'underline', 'alignleft', 'aligncenter', 'alignright', 'numlist', 'link', 'unlink', 'removeformat');
    return $toolbars;
}

add_filter('tiny_mce_before_init', 'customTMCE');
add_filter( 'tiny_mce_plugins', 'noCustomColorTMCE' );
add_filter( 'acf/fields/wysiwyg/toolbars', 'acfTinyMCE');
