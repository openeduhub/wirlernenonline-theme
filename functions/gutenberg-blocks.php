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
        'title'				=> __('Breadcrumbs'),
        'description'		=> __('Zeigt den Pfad der übergeordneten Seiten an.'),
        'render_template'	=> 'template-parts/blocks/portal_breadcrumbs.php',
        'category'			=> 'themenportal',
        'icon'				=> 'media-document',
        'keywords'			=> [ ],
    ]);

    acf_register_block_type(['name' => 'portal_select',
        'title'				=> __('Navigation'),
        'description'		=> __('Zeigt eine Navigation zwischen den verschiedenen Themenportalen, Bildungsstufen und Zielgruppen.'),
        'render_template'	=> 'template-parts/blocks/portal_select.php',
        'category'			=> 'themenportal',
        'icon'				=> 'media-document',
        'keywords'			=> [ ],
    ]);
acf_register_block_type(['name' => 'portal_current',
    'title'				=> __('Inhaltesuche'),
    'description'		=> __('Zeigt die (aktuellsten) Materialien und Inhalte zum Thema aus der Suche an.'),
    'render_template'	=> 'template-parts/blocks/portal_current.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_banner',
    'title'				=> __('Banner'),
    'description'		=> __('Zeigt in einem Laufbanner die aktuellen Blogbeiträge (Kategorie = Themenportal; Fach, Bildungsstufe, Zielgruppe entsprechend Voreinstellung oder Portaleinstellung.)'),
    'render_template'	=> 'template-parts/blocks/portal_banner.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_tip',
    'title'				=> __('Neueste Blogbeiträge'),
    'description'		=> __('Zeigt aktuelle Blogbeiträge im aktuellen Blog. Nicht fachspezifisch.'),
    'render_template'	=> 'template-parts/blocks/portal_tip.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_search',
    'title'				=> __('WLO Suchanfrage'),
    'description'		=> __('Springt direkt zur Suche, reicht Anfrage weiter.'),
    'render_template'	=> 'template-parts/blocks/portal_search.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_subject_tree',
    'title'				=> __('Themenbaum'),
    'description'		=> __('Zeigt die Sammlungs-Struktur der übergebenen Sammlung.'),
    'render_template'	=> 'template-parts/blocks/portal_subject_tree.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_collection_content_browser',
    'title'				=> __('Sammlungsinhalte'),
    'description'		=> __('Zeigt den Inhalt/Materialien innerhalb der übergebenen Sammlung.'),
    'render_template'	=> 'template-parts/blocks/portal_collection_content_browser.php',
    'category'			=> 'themenportal',
    'icon'				=> 'media-document',
    'keywords'			=> [ ],
]);
//acf_register_block_type(array(
//    'name'              => 'portal-edusharing-grid',
//    'title'             => 'EduSharing Grid',
//    'description'       => 'Zeigt ein Container-Block, der EduSharing-Blöcke enthält.',
//    'category'          => 'themenportal',
//    'mode'              => 'preview',
//    'icon'				=> 'media-document',
//    'enqueue_script'    => get_template_directory_uri() . '/template-parts/blocks/portal_edusharing_grid.js'
//));
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

function register_edusharing_blocks() {

    wp_register_script(
        'portal-edusharing-grid-script',get_template_directory_uri() . '/template-parts/blocks/portal_edusharing_grid.js'
    );

    register_block_type( 'acf/portal-edusharing-grid', array(
        'name'              => 'portal-edusharing-grid',
        'title'             => 'EduSharing Grid',
        'description'       => 'Zeigt ein Grid, das EduSharing-Blöcke enthält.',
        'category'          => 'themenportal',
        'mode'              => 'preview',
        'icon'				=> 'media-document',
        'editor_script' => 'portal-edusharing-grid-script',
    ) );

    wp_register_script(
        'portal-edusharing-list-script',get_template_directory_uri() . '/template-parts/blocks/portal_edusharing_list.js'
    );

    register_block_type( 'acf/portal-edusharing-list', array(
        'name'              => 'portal-edusharing-list',
        'title'             => 'EduSharing Liste',
        'description'       => 'Zeigt eine Liste, die EduSharing-Blöcke enthält.',
        'category'          => 'themenportal',
        'mode'              => 'preview',
        'icon'				=> 'media-document',
        'editor_script' => 'portal-edusharing-list-script',
    ) );

}
add_action( 'init', 'register_edusharing_blocks' );