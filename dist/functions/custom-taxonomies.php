<?php
function custom_taxonomies() {
    register_taxonomy('locations', 'event', array(
        'labels' => array(
            'name' => __('Locations', 'gtf-theme'),
            'singular_name' => __('Location', 'gtf-theme'),
            'all_items' => __('All Locations', 'gtf-theme'),
            'edit_item' => __('Edit Location', 'gtf-theme'), 
            'view_item' => __('View Location', 'gtf-theme'), 
            'update_item' => __('Update Location', 'gtf-theme'), 
            'add_new_item' => __('Add New Location', 'gtf-theme'), 
            'new_item_name' => __('New Location Name', 'gtf-theme'), 
        ),
        'public' => false, 
    ));

    register_taxonomy('speakers', 'event', array(
        'labels' => array(
            'name' => __('Speakers', 'gtf-theme'),
            'singular_name' => __('Speaker', 'gtf-theme'),
            'all_items' => __('All Speakers', 'gtf-theme'),
            'edit_item' => __('Edit Speaker', 'gtf-theme'), 
            'view_item' => __('View Speaker', 'gtf-theme'), 
            'update_item' => __('Update Speaker', 'gtf-theme'), 
            'add_new_item' => __('Add New Speaker', 'gtf-theme'), 
            'new_item_name' => __('New Speaker Name', 'gtf-theme'), 
        ),
        'public' => false, 
    ));

    register_taxonomy('event_types', 'event', array(
        'labels' => array(
            'name' => __('Event Types', 'gtf-theme'),
            'singular_name' => __('Event Type', 'gtf-theme'),
            'all_items' => __('All Event Types', 'gtf-theme'),
            'edit_item' => __('Edit Event Type', 'gtf-theme'), 
            'view_item' => __('View Event Type', 'gtf-theme'), 
            'update_item' => __('Update Event Type', 'gtf-theme'), 
            'add_new_item' => __('Add New Event Type', 'gtf-theme'), 
            'new_item_name' => __('New Event Type Name', 'gtf-theme'), 
        ),
        'public' => false, 
    ));
}
add_action( 'init', 'custom_taxonomies', 0 );