<?php
// Custom Block Categories

function custom_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'custom-blocks',
				'title' => __( 'Custom Blocks', 'custom-blocks' ),
			),
		)
	);
}
add_filter( 'block_categories', 'custom_block_category', 10, 2);

// Register Custom Blocks
// For Icons go to: https://developer.wordpress.org/resource/dashicons/ – But leafe the "dashicons-" Prefix

function register_acf_block_types(){
	acf_register_block_type(['name' => 'Home Hero Field',
	'title'				=> __('Home Hero Field'),
	'description'		=> __('Big Field with Search input and checkboxes'),
	'render_template'	=> 'template-parts/blocks/home-hero.php',
	'category'			=> 'custom-blocks',
	'icon'				=> 'megaphone',
	'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'neuste_quellen',
    'title'				=> __('Neuste Quellen'),
    'description'		=> __('Zeigt aktuelle Quellen an'),
    'render_template'	=> 'template-parts/blocks/latest-sources.php',
    'category'			=> 'custom-blocks',
    'icon'				=> 'format-gallery',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'Quellenerschliesung',
    'title'				=> __('Quellenerschliesung'),
    'description'		=> __('Zeigt eine Auflistung der Quellenerschließung'),
    'render_template'	=> 'template-parts/blocks/source_development.php',
    'category'			=> 'custom-blocks',
    'icon'				=> 'tag',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'Letzte Blog Posts',
    'title'				=> __('Letzte Blog Posts'),
    'description'		=> __('Letzte Blog Posts'),
    'render_template'	=> 'template-parts/blocks/latest-posts.php',
    'category'			=> 'widgets',
    'icon'				=> 'excerpt-view',
    'keywords'			=> [ ],
]);
// acf_register_block_type(['name' => 'Responsive Table',
// 	'title'				=> __('Responsive Table'),
// 	'description'		=> __('Table block optimized for mobile'),
// 	'render_template'	=> 'template-parts/blocks/responsive-table.php',
// 	'category'			=> 'widgets',
// 	'icon'				=> 'excerpt-view',
// 	'keywords'			=> [ ],
// ]);
}
if (function_exists('acf_register_block_type')) {
	add_action('acf/init', 'register_acf_block_types');
}
