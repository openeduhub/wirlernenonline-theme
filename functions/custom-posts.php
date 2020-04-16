<?php
function custom_post_type()
{
	register_post_type('projects',
	array(
		'labels'      => array(
			'name'          => __('Projects'),
			'singular_name' => __('Project'),
		),
		'public'      => true,
		'has_archive' => true,
	)
);
}
add_action('init', 'custom_post_type');
