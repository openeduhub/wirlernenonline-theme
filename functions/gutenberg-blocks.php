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
	acf_register_block_type(['name' => 'Accordion',
	'title'				=> __('Accordion'),
	'description'		=> __('An accordion'),
	'render_template'	=> 'template-parts/blocks/accordion.php',
	'category'			=> 'custom-blocks',
	'icon'				=> 'slides',
	'keywords'			=> [ ],
]);
// acf_register_block_type(['name' => 'Gallery Swiper',
// 	'title'				=> __('Gallery Swiper'),
// 	'description'		=> __('An image gallery with swiper'),
// 	'render_template'	=> 'template-parts/blocks/swiper-gallery.php',
// 	'category'			=> 'widgets',
// 	'icon'				=> 'slides',
// 	'keywords'			=> [ ],
// ]);
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

// Add Styleing to Editor
/**
 * Registers support for editor styles & Enqueue it.
 */
function add_editor_styles() {
	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Enqueue editor styles.
	add_editor_style( 'dist/assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'add_editor_styles' );
