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
    acf_register_block_type(['name' => 'Home Badge',
        'title'				=> __('Home Badge'),
        'description'		=> __('Badge for high attraction.'),
        'render_template'	=> 'template-parts/blocks/home-badge.php',
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
acf_register_block_type(['name' => 'Partner',
    'title'				=> __('Partner'),
    'description'		=> __('Zeigt die Logos der Partner'),
    'render_template'	=> 'template-parts/blocks/partner.php',
    'category'			=> 'custom-blocks',
    'icon'				=> 'groups',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'source_logos',
    'title'				=> __('Erschlossene Quellen'),
    'description'		=> __('Zeigt die Logos der erschlossenen Quellen'),
    'render_template'	=> 'template-parts/blocks/source_logos.php',
    'category'			=> 'custom-blocks',
    'icon'				=> 'format-gallery',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'press',
    'title'				=> __('Pressespiegel'),
    'description'		=> __('Presseübersicht'),
    'render_template'	=> 'template-parts/blocks/pressespiegel.php',
    'category'			=> 'custom-blocks',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
    acf_register_block_type(['name' => 'portal_breadcrumbs',
        'title'				=> __('Themenportal: Breadcrumbs'),
        'description'		=> __('Erzeugt die Struktur der überliegenden Seiten/Portale...'),
        'render_template'	=> 'template-parts/blocks/portal_breadcrumbs.php',
        'category'			=> 'themenportal',
        'icon'				=> 'media-document',
        'keywords'			=> [ ],
    ]);

    acf_register_block_type(['name' => 'portal_select',
        'title'				=> __('Themenportal: Auswahl'),
        'description'		=> __('Erzeugt die aktuelle Themenauswahl...'),
        'render_template'	=> 'template-parts/blocks/portal_select.php',
        'category'			=> 'themenportal',
        'icon'				=> 'media-document',
        'keywords'			=> [ ],
    ]);
acf_register_block_type(['name' => 'portal_current',
    'title'				=> __('Themenportal: Neueste Suchinhalte'),
    'description'		=> __('Zeigt die neusten Treffer aus der Suche'),
    'render_template'	=> 'template-parts/blocks/portal_current.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_banner',
    'title'				=> __('Themenportal: Banner'),
    'description'		=> __('Zeigt aktuelle Blogbeiträge'),
    'render_template'	=> 'template-parts/blocks/portal_banner.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_tip',
    'title'				=> __('Themenportal: Neueste Blogbeiträge'),
    'description'		=> __('Zeigt aktuelle Blogbeiträge'),
    'render_template'	=> 'template-parts/blocks/portal_tip.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_search',
    'title'				=> __('Themenportal: Edu-Sharing Suche'),
    'description'		=> __('Springt zur Suche...'),
    'render_template'	=> 'template-parts/blocks/portal_search.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_subject_tree',
    'title'				=> __('Themenportal: Themenbaum'),
    'description'		=> __('Zeigt den Themenbaum'),
    'render_template'	=> 'template-parts/blocks/portal_subject_tree.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
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
