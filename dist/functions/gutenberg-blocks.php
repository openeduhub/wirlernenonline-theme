<?php
// Custom Block Categories

function custom_block_category($categories, $post)
{
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'custom-blocks',
                'title' => __('Custom Blocks', 'custom-blocks'),
            ),
        )
    );
}

add_filter('block_categories', 'custom_block_category', 10, 2);

// Register Custom Blocks
// For Icons go to: https://developer.wordpress.org/resource/dashicons/ – But leafe the "dashicons-" Prefix

function register_acf_block_types()
{
    acf_register_block_type(['name' => 'Home Hero Field',
        'title' => __('Home Hero Field'),
        'description' => __('Big Field with Search input and checkboxes'),
        'render_template' => 'template-parts/blocks/home-hero.php',
        'category' => 'custom-blocks',
        'icon' => 'megaphone',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'Home Badge',
        'title' => __('Home Badge'),
        'description' => __('Badge for high attraction.'),
        'render_template' => 'template-parts/blocks/home-badge.php',
        'category' => 'custom-blocks',
        'icon' => 'megaphone',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'neuste_quellen',
        'title' => __('Neuste Quellen'),
        'description' => __('Zeigt aktuelle Quellen an'),
        'render_template' => 'template-parts/blocks/latest-sources.php',
        'category' => 'custom-blocks',
        'icon' => 'format-gallery',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'Quellenerschliesung',
        'title' => __('Quellenerschliesung'),
        'description' => __('Zeigt eine Auflistung der Quellenerschließung'),
        'render_template' => 'template-parts/blocks/source_development.php',
        'category' => 'custom-blocks',
        'icon' => 'tag',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'Quellentabelle',
        'title' => __('Quellentabelle'),
        'description' => __('Zeigt eine Auflistung der Quellenerschließung'),
        'render_template' => 'template-parts/blocks/source_table.php',
        'category' => 'custom-blocks',
        'icon' => 'tag',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'Letzte Blog Posts',
        'title' => __('Letzte Blog Posts'),
        'description' => __('Letzte Blog Posts'),
        'render_template' => 'template-parts/blocks/latest-posts.php',
        'category' => 'widgets',
        'icon' => 'excerpt-view',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'partner',
        'title' => __('WLO: Partner'),
        'description' => __('Zeigt die Logos der Partner'),
        'render_template' => 'template-parts/blocks/wlo_partner.php',
        'category' => 'custom-blocks',
        'icon' => 'groups',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'source_logos',
        'title' => __('Erschlossene Quellen'),
        'description' => __('Zeigt die Logos der erschlossenen Quellen'),
        'render_template' => 'template-parts/blocks/source_logos.php',
        'category' => 'custom-blocks',
        'icon' => 'format-gallery',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'press',
        'title' => __('Pressespiegel'),
        'description' => __('Presseübersicht'),
        'render_template' => 'template-parts/blocks/pressespiegel.php',
        'category' => 'custom-blocks',
        'icon' => 'media-document',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'wlo-job',
        'title' => __('WLO-Job'),
        'description' => __('Block mit Job-link'),
        'render_template' => 'template-parts/blocks/wlo_job.php',
        'category' => 'custom-blocks',
        'icon' => 'id-alt',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'portal_breadcrumbs',
        'title' => __('Breadcrumbs'),
        'description' => __('Zeigt den Pfad der übergeordneten Seiten an.'),
        'render_template' => 'template-parts/blocks/portal_breadcrumbs.php',
        'category' => 'themenportal',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<g>
	<polygon class="st0" points="8.4,12.3 5.9,8.7 0.4,8.7 3,12.3 0.4,16 5.9,16 	"/>
	<polygon class="st0" points="16.5,12.3 13.9,8.7 8.5,8.7 11.1,12.3 8.5,16 13.9,16 	"/>
	<polygon class="st0" points="24,12.3 21.4,8.7 16,8.7 18.6,12.3 16,16 21.4,16 	"/>
</g>
</svg>',
        'keywords' => [],
    ]);

    acf_register_block_type(['name' => 'portal_select',
        'title' => __('Navigation'),
        'description' => __('Zeigt eine Navigation zwischen den verschiedenen Themenportalen, Bildungsstufen und Zielgruppen.'),
        'render_template' => 'template-parts/blocks/portal_select.php',
        'category' => 'themenportal',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<path class="st0" d="M1,9.9v3.9c0,1.1,0.9,1.9,1.9,1.9h18.1c1.1,0,1.9-0.9,1.9-1.9V9.9C23,8.9,22.1,8,21.1,8H2.9C1.9,8,1,8.9,1,9.9z
	 M16.5,9.3h4.5c0.4,0,0.6,0.3,0.6,0.6v3.9c0,0.4-0.3,0.6-0.6,0.6h-4.5V9.3z M2.3,9.9c0-0.4,0.3-0.6,0.6-0.6h12.3v5.2H2.9
	c-0.4,0-0.6-0.3-0.6-0.6V9.9z M19.6,12.7c0.3,0.3,0.7,0.3,0.9,0c0.3-0.3,0.3-0.7,0-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9,0l-1,1
	c-0.3,0.3-0.3,0.7,0,0.9c0.3,0.3,0.7,0.3,0.9,0l0.5-0.5L19.6,12.7z M4.2,12.5h5.8c0.4,0,0.6-0.3,0.6-0.6c0-0.4-0.3-0.6-0.6-0.6H4.2
	c-0.4,0-0.6,0.3-0.6,0.6C3.6,12.2,3.9,12.5,4.2,12.5z"/>
</svg>
',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'portal_current',
        'title' => __('Inhaltesuche'),
        'description' => __('Zeigt die (aktuellsten) Materialien und Inhalte zum Thema aus der Suche an.'),
        'render_template' => 'template-parts/blocks/portal_current.php',
        'category' => 'themenportal',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<g>
	<path class="st0" d="M20.1,4.5h-4V1.6c0-0.3-0.3-0.6-0.6-0.6H3.3C2.1,1,1,2.1,1,3.3v16.7C1,21.7,2.3,23,3.9,23h16.2
		c1.6,0,2.9-1.3,2.9-2.9V7.4C23,5.8,21.7,4.5,20.1,4.5z M3.3,2.2h11.6v2.2H3.3C2.7,4.5,2.2,4,2.2,3.3S2.7,2.2,3.3,2.2z M21.8,20.1
		c0,0.9-0.8,1.7-1.7,1.7H3.9c-0.9,0-1.7-0.8-1.7-1.7V5.4c0.3,0.2,0.7,0.3,1.1,0.3h16.7c0.9,0,1.7,0.8,1.7,1.7V20.1z"/>
	<path class="st0" d="M20.1,13.7H3.9c-0.3,0-0.6,0.3-0.6,0.6s0.3,0.6,0.6,0.6h16.2c0.3,0,0.6-0.3,0.6-0.6S20.4,13.7,20.1,13.7z"/>
	<path class="st0" d="M20.1,16.5H3.9c-0.3,0-0.6,0.3-0.6,0.6c0,0.3,0.3,0.6,0.6,0.6h16.2c0.3,0,0.6-0.3,0.6-0.6
		C20.7,16.8,20.4,16.5,20.1,16.5z"/>
	<path class="st0" d="M20.1,19.4H3.9c-0.3,0-0.6,0.3-0.6,0.6c0,0.3,0.3,0.6,0.6,0.6h16.2c0.3,0,0.6-0.3,0.6-0.6S20.4,19.4,20.1,19.4
		z"/>
	<polygon class="st0" points="4.3,9.4 6.2,11.8 7,11.8 7,7.9 6.2,7.9 6.2,10.4 4.3,7.9 3.5,7.9 3.5,11.8 4.3,11.8 	"/>
	<polygon class="st0" points="10.8,11 8.8,11 8.8,10.2 10.6,10.2 10.6,9.5 8.8,9.5 8.8,8.7 10.8,8.7 10.8,7.9 8,7.9 8,11.8 
		10.8,11.8 	"/>
	<polygon class="st0" points="13.1,11.8 13.9,9.1 14.8,11.8 15.4,11.8 16.8,7.9 15.8,7.9 15.1,10.2 14.4,7.9 13.5,7.9 12.8,10.2 
		12,7.9 11.1,7.9 12.5,11.8 	"/>
	<path class="st0" d="M18.6,11.1c-0.4,0-0.7-0.2-1.2-0.5l-0.5,0.6c0.5,0.5,1,0.7,1.7,0.7c0.8,0,1.4-0.5,1.4-1.2c0-0.7-0.4-1-1.2-1.2
		C18.3,9.4,18,9.3,18,9c0-0.2,0.1-0.4,0.5-0.4c0.4,0,0.7,0.1,1,0.4l0.4-0.6c-0.4-0.3-0.9-0.5-1.5-0.5c-0.9,0-1.4,0.4-1.4,1.1
		c0,0.7,0.4,1,1.3,1.2c0.4,0.1,0.7,0.2,0.7,0.5C19.1,11,18.8,11.1,18.6,11.1z"/>
</g>
</svg>',
        'keywords' => [],
        'supports' => ['anchor' => true],
    ]);
    acf_register_block_type(['name' => 'portal_header',
        'title' => __('Header'),
        'description' => __('Zeigt einen Kopfbereich für das Themenportal an: Titel, Sammlungsbeschreibung, Autoren, Autoren-Seite, Icon.'),
        'render_template' => 'template-parts/blocks/portal_header.php',
        'category' => 'themenportal',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"></path><path fill="#a2a2a2" d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"></path></svg>',
        'keywords' => [],
    ]);
    acf_register_block_type(['name' => 'portal_banner',
        'title' => __('Banner'),
        'description' => __('Zeigt in einem Laufbanner die aktuellen Blogbeiträge (Kategorie = Themenportal; Fach, Bildungsstufe, Zielgruppe entsprechend Voreinstellung oder Portaleinstellung.)'),
        'render_template' => 'template-parts/blocks/portal_banner.php',
        'category' => 'themenportal',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<path class="st0" d="M1,6.4h4.4v12.1H1V6.4z M6.5,20.8h11V4.3h-11V20.8z M8.7,6.4h6.6v12.1H8.7V6.4z M18.6,6.4H23v12.1h-4.4V6.4z"/>
</svg>',
        'keywords' => [],
        'supports' => ['anchor' => true],
    ]);
    acf_register_block_type(['name' => 'portal_latest_posts',
        'title' => __('Neueste Blogbeiträge'),
        'description' => __('Zeigt aktuelle Blogbeiträge im aktuellen Blog. Nicht fachspezifisch.'),
        'render_template' => 'template-parts/blocks/portal_latest_posts.php',
        'category' => 'themenportal',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<circle class="st0" cx="4.8" cy="20.2" r="2.8"/>
<path class="st0" d="M2,2.9v3.6c9,0,16.4,7.4,16.4,16.4H22C22,11.8,13.1,2.9,2,2.9z M2,10.1v3.6c5,0,9.2,4.1,9.2,9.2h3.6
	C14.8,15.9,9,10.1,2,10.1z"/>
</svg>',
        'keywords' => [],
        'supports' => ['anchor' => true],
    ]);
    acf_register_block_type(['name' => 'portal_search',
        'title' => __('WLO Suchanfrage'),
        'description' => __('Springt direkt zur Suche, reicht Anfrage weiter.'),
        'render_template' => 'template-parts/blocks/portal_search.php',
        'category' => 'themenportal',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<path class="st0" d="M16.7,14.8h-1l-0.4-0.4c1.3-1.4,2-3.3,2-5.3c0-4.5-3.6-8.2-8.2-8.2S1,4.6,1,9.2s3.6,8.2,8.2,8.2
	c2,0,3.9-0.8,5.3-2l0.4,0.4v1l6.3,6.3l1.9-1.9L16.7,14.8z M9.2,14.8c-3.1,0-5.7-2.5-5.7-5.7S6,3.5,9.2,3.5s5.7,2.5,5.7,5.7
	S12.3,14.8,9.2,14.8z"/>
</svg>',
        'keywords' => [],
        'supports' => ['anchor' => true],
    ]);
    acf_register_block_type(['name' => 'portal_search_grouped',
        'title' => __('WLO Agreggierte Suchanfrage'),
        'description' => __('Zeigt aggregierte Suchergebnisse nach Typ an'),
        'render_template' => 'template-parts/blocks/portal_search_grouped.php',
        'category' => 'themenportal',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<path class="st0" d="M16.7,14.8h-1l-0.4-0.4c1.3-1.4,2-3.3,2-5.3c0-4.5-3.6-8.2-8.2-8.2S1,4.6,1,9.2s3.6,8.2,8.2,8.2
	c2,0,3.9-0.8,5.3-2l0.4,0.4v1l6.3,6.3l1.9-1.9L16.7,14.8z M9.2,14.8c-3.1,0-5.7-2.5-5.7-5.7S6,3.5,9.2,3.5s5.7,2.5,5.7,5.7
	S12.3,14.8,9.2,14.8z"/>
</svg>',
        'keywords' => [],
        'supports' => ['anchor' => true],
    ]);
    acf_register_block_type(['name' => 'portal_subject_tree',
        'title' => __('Themenbaum'),
        'description' => __('Zeigt die Sammlungs-Struktur der übergebenen Sammlung.'),
        'render_template' => 'template-parts/blocks/portal_subject_tree.php',
        'category' => 'themenportal',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 88.6 73.7" style="enable-background:new 0 0 88.6 73.7;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<g>
	<g>
		<g>
			<ellipse class="st0" cx="44.2" cy="7.6" rx="7" ry="7"/>
			<ellipse class="st0" cx="26.1" cy="36.9" rx="7" ry="7"/>
			<ellipse class="st0" cx="62.2" cy="36.9" rx="7" ry="7"/>
			<path class="st0" d="M27.5,37.1c-0.4,0-0.8-0.1-1.2-0.4c-0.9-0.7-1.2-1.9-0.5-2.9L42.4,9.4c0.4-0.6,1-0.9,1.7-0.9
				c0.7,0,1.3,0.3,1.7,0.8l18,24.5c0.7,0.9,0.5,2.2-0.4,2.9c-0.9,0.7-2.2,0.5-2.9-0.4L44.2,14.2l-15,22
				C28.8,36.8,28.2,37.1,27.5,37.1z"/>
		</g>
		<g>
			<ellipse class="st0" cx="7.2" cy="64.4" rx="7" ry="7"/>
			<ellipse class="st0" cx="43.3" cy="66.6" rx="7" ry="7"/>
			<path class="st0" d="M8.6,63.5c-0.5,0-1.1-0.2-1.5-0.6c-0.8-0.8-0.8-2.1,0-2.9l17.5-17.5c0.4-0.4,0.9-0.6,1.5-0.6c0,0,0,0,0,0
				c0.5,0,1.1,0.2,1.5,0.6l17.1,17.5c0.8,0.8,0.8,2.1,0,2.9s-2.1,0.8-2.9,0l-15.7-16l-16,16C9.7,63.3,9.2,63.5,8.6,63.5z"/>
		</g>
		<g>
			<ellipse class="st0" cx="81.2" cy="63.4" rx="7" ry="7"/>
			<path class="st0" d="M80.3,63.5c-0.6,0-1.2-0.3-1.6-0.8L63,42.2c-0.7-0.9-0.5-2.2,0.4-2.9c0.9-0.7,2.2-0.5,2.9,0.4l15.6,20.5
				c0.7,0.9,0.5,2.2-0.4,2.9C81.1,63.3,80.7,63.5,80.3,63.5z"/>
		</g>
	</g>
</g>
</svg>',
        'keywords' => [],
        'supports' => ['anchor' => true],
    ]);
    acf_register_block_type(['name' => 'portal_collection_content_browser',
        'title' => __('Sammlungsinhalte'),
        'description' => __('Zeigt den Inhalt/Materialien innerhalb der übergebenen Sammlung.'),
        'render_template' => 'template-parts/blocks/portal_collection_content_browser.php',
        'category' => 'themenportal',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24" 
viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" 
fill="none"></path>
<path style="fill: #a2a2a2" d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z">
</path></svg>',
        'keywords' => [],
        'supports' => ['anchor' => true],
    ]);
acf_register_block_type(['name' => 'portal_collection_group_content',
        'title' => __('Sammlungsinhalte (Unterseiten)'),
        'description' => __('Zeigt den Inhalt/Materialien innerhalb der übergebenen Sammlung (für Unterseiten).'),
        'render_template' => 'template-parts/blocks/portal_collection_group_content.php',
        'category' => 'themenportal',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24" 
viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" 
fill="none"></path>
<path style="fill: #a2a2a2" d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z">
</path></svg>',
        'keywords' => [],
        'supports' => ['anchor' => true],
]);
acf_register_block_type(['name' => 'portal_feature_content',
        'title' => __('Feature des Monats'),
        'description' => __('Hebt einen Inhalt besonders hervor.'),
        'render_template' => 'template-parts/blocks/portal_feature_content.php',
        'category' => 'themenportal',
        'icon' => 'id',
        'keywords' => [],
        'supports' => ['anchor' => true],
]);
acf_register_block_type(['name' => 'edusharing_repo_button',
    'title'				=> __('Button zu edu-sharing'),
    'description'		=> __('Springt zum ES-Repo mit Login.'),
    'render_template'	=> 'template-parts/blocks/edusharing_repo_button.php',
    'category'			=> 'custom-blocks',
    'icon'				=> 'admin-links',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_toggle_content',
    'title'				=> __('Themenportal: Ergebnis Auswahl'),
    'description'		=> __('Wechselt zwischen Qualitätsgesicherten Inhalten und Suchergebnissen'),
    'render_template'	=> 'template-parts/blocks/portal_toggle_content.php',
    'category'			=> 'themenportal',
    'icon'				=> 'admin-settings',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_collection_group',
    'title'				=> __('Sammlungsinhalte Gruppe'),
    'description'		=> __('Zeigt ein Grid, das Sammlungsinhalte-Blöcke enthält.'),
    'render_template'	=> 'template-parts/blocks/portal_collection_group.php',
    'category'			=> 'themenportal',
    'icon'				=> 'admin-settings',
    'supports'          => array(
        'mode' => false,
        '__experimental_jsx' => true
    ),
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_widget_content',
    'title'				=> __('Widget-Inhalte für Themenseiten'),
    'description'		=> __('Zeigt die Inhalte aus den Widgets an.'),
    'render_template'	=> 'template-parts/blocks/portal_widget_content.php',
    'category'			=> 'themenportal',
    'icon'				=> 'images-alt2',
    'supports'          => array(
        'mode' => false,
        '__experimental_jsx' => true
    ),
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'portal_widget',
    'title'				=> __('Themenportal: Widget'),
    'description'		=> __('Zeigt ein Widgets an.'),
    'render_template'	=> 'template-parts/blocks/portal_widget.php',
    'category'			=> 'themenportal',
    'icon'				=> 'format-image',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'fachportal_content_block',
    'title'				=> __('Fachportal: Inhaltsblock'),
    'description'		=> __('Zeigt Sammlungs-Inhalte an.'),
    'render_template'	=> 'template-parts/blocks/fachportal_content_block.php',
    'category'			=> 'themenportal',
    'icon'				=> 'format-image',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'fachportal_blog_content_block',
    'title'				=> __('Fachportal: Blogbeiträge'),
    'description'		=> __('Zeigt Blog-Inhalte an.'),
    'render_template'	=> 'template-parts/blocks/fachportal_blog_content_block.php',
    'category'			=> 'themenportal',
    'icon'				=> 'format-image',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'fachportal_header_block',
    'title'				=> __('Themenseite: Header'),
    'description'		=> __('Zeigt den Header an.'),
    'render_template'	=> 'template-parts/blocks/fachportal_header_block.php',
    'category'			=> 'themenportal',
    'icon'				=> 'format-image',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'fachportal_footer_block',
    'title'				=> __('Themenseite: Footer'),
    'description'		=> __('Zeigt den Footer an.'),
    'render_template'	=> 'template-parts/blocks/fachportal_footer_block.php',
    'category'			=> 'themenportal',
    'icon'				=> 'format-image',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'fachseite_header_block',
    'title'				=> __('Fachseite: Header'),
    'description'		=> __('Zeigt den Header an.'),
    'render_template'	=> 'template-parts/blocks/fachseite_header_block.php',
    'category'			=> 'themenportal',
    'icon'				=> 'format-image',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'fachseite_feature_block',
    'title'				=> __('Fachseite: Inhalt des Monats'),
    'description'		=> __('Featured einen WLO-Inhalt'),
    'render_template'	=> 'template-parts/blocks/fachseite_feature_block.php',
    'category'			=> 'themenportal',
    'icon'				=> 'format-image',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'fachportal_team_block',
    'title'				=> __('Fachseite: Redaktion'),
    'description'		=> __('Zeigt das Redaktions-Team'),
    'render_template'	=> 'template-parts/blocks/fachportal_team_block.php',
    'category'			=> 'themenportal',
    'icon'				=> 'groups',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'fachportal_spacer',
    'title'				=> __('Fachportal: Trennlinie'),
    'description'		=> __('Trennt Inhalte horizontal'),
    'render_template'	=> 'template-parts/blocks/fachportal_spacer.php',
    'category'			=> 'themenportal',
    'icon'				=> 'minus',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'wlo_news',
    'title'				=> __('WLO News'),
    'description'		=> __('Zeigt News-Beiträge'),
    'render_template'	=> 'template-parts/blocks/wlo_news.php',
    'category'			=> 'wlo',
    'icon'				=> 'media-text',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'wlo_newsletter',
    'title'				=> __('WLO Newsletter'),
    'description'		=> __('Anmeldeformular zum WLO-Newsletter'),
    'render_template'	=> 'template-parts/blocks/wlo_newsletter.php',
    'category'			=> 'wlo',
    'icon'				=> 'email',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'wlo_button',
    'title'				=> __('WLO Button'),
    'description'		=> __('/blau oder gelb)'),
    'render_template'	=> 'template-parts/blocks/wlo_button.php',
    'category'			=> 'wlo',
    'icon'				=> 'admin-links',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'wlo_tile',
    'title'				=> __('WLO Kachel'),
    'description'		=> __('Inhalt mit Bild und Button'),
    'render_template'	=> 'template-parts/blocks/wlo_tile.php',
    'category'			=> 'wlo',
    'icon'				=> 'format-image',
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'wlo_basic_tile',
    'title'				=> __('WLO einfache Kachel'),
    'description'		=> __('Für die Startseite'),
    'render_template'	=> 'template-parts/blocks/wlo_basic_tile.php',
    'category'			=> 'wlo',
    'icon'				=> 'format-image',
    'supports'          => array(
        'mode' => false,
        '__experimental_jsx' => true
    ),
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'wlo_oer_menu',
    'title'				=> __('WLO Untermenü für OER-Seiten'),
    'description'		=> __('Nur für die OER-Seiten'),
    'render_template'	=> 'template-parts/blocks/wlo_oer_menu.php',
    'category'			=> 'wlo',
    'icon'				=> 'category',
    'supports'          => array(
        'mode' => false,
        '__experimental_jsx' => true
    ),
    'keywords'			=> [ ],
]);
acf_register_block_type(['name' => 'wlo_accordion',
    'title'				=> __('WLO Registerkarten'),
    'description'		=> __('Ausklappbare Inhalte'),
    'render_template'	=> 'template-parts/blocks/wlo_accordion.php',
    'category'			=> 'wlo',
    'icon'				=> '<svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100.047 182.767C145.732 182.767 182.767 145.732 182.767 100.047C182.767 54.3617 145.732 17.3267 100.047 17.3267C54.3615 17.3267 17.3265 54.3617 17.3265 100.047C17.3265 145.732 54.3615 182.767 100.047 182.767Z" fill="#1B3E76"/>
                            <path d="M100.047 200C93.276 200 86.5047 199.317 79.9208 197.969C73.4956 196.655 67.175 194.692 61.1351 192.137C55.2089 189.631 49.4943 186.529 44.1499 182.919C38.8607 179.346 33.8878 175.243 29.3693 170.724C24.8507 166.205 20.7478 161.233 17.1743 155.943C13.5635 150.599 10.4622 144.884 7.9558 138.958C5.40113 132.918 3.43929 126.598 2.12404 120.172C0.776391 113.589 0.0932617 106.817 0.0932617 100.047C0.0932617 93.276 0.776391 86.5047 2.12404 79.9209C3.4386 73.4956 5.40113 67.1751 7.9558 61.1352C10.4622 55.209 13.5642 49.4944 17.1743 44.15C20.7478 38.8607 24.8507 33.8879 29.3693 29.3693C33.8878 24.8507 38.8607 20.7478 44.1499 17.1743C49.4943 13.5636 55.2089 10.4623 61.1351 7.95586C67.175 5.40119 73.4956 3.43935 79.9208 2.1241C86.5047 0.776452 93.276 0.0933228 100.047 0.0933228C106.817 0.0933228 113.589 0.776452 120.172 2.1241C126.598 3.43866 132.918 5.40119 138.958 7.95586C144.884 10.4623 150.599 13.5643 155.943 17.1743C161.233 20.7478 166.205 24.8507 170.724 29.3693C175.243 33.8879 179.345 38.8607 182.919 44.15C186.53 49.4944 189.631 55.209 192.137 61.1352C194.692 67.1751 196.654 73.4956 197.969 79.9209C199.317 86.5047 200 93.276 200 100.047C200 106.817 199.317 113.589 197.969 120.172C196.655 126.598 194.692 132.918 192.137 138.958C189.631 144.884 186.529 150.599 182.919 155.943C179.345 161.233 175.243 166.205 170.724 170.724C166.205 175.243 161.233 179.346 155.943 182.919C150.599 186.53 144.884 189.631 138.958 192.137C132.918 194.692 126.598 196.654 120.172 197.969C113.589 199.317 106.817 200 100.047 200ZM100.047 34.56C63.9372 34.56 34.5599 63.9373 34.5599 100.047C34.5599 136.156 63.9372 165.533 100.047 165.533C136.156 165.533 165.533 136.156 165.533 100.047C165.533 63.9373 136.156 34.56 100.047 34.56Z" fill="#EA4B71"/>
                            </svg>',
    'supports'          => array(
        'mode' => false,
        '__experimental_jsx' => true
    ),
    'keywords'			=> [ ],
]);
}
if (function_exists('acf_register_block_type')) {
    add_action('acf/init', 'register_acf_block_types');
}

function register_edusharing_blocks()
{

    wp_register_script(
        'portal-edusharing-grid-script', get_template_directory_uri() . '/template-parts/blocks/portal_edusharing_grid.js'
    );

    register_block_type('acf/portal-edusharing-grid', array(
        'name' => 'portal-edusharing-grid',
        'title' => 'EduSharing Grid',
        'description' => 'Zeigt ein Grid, das EduSharing-Blöcke enthält.',
        'category' => 'themenportal',
        'mode' => 'preview',
        'icon' => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 19.2 13.2" style="enable-background:new 0 0 19.2 13.2;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<path class="st0" d="M1.1,6.2h5v-6h-5V6.2z M1.1,13.2h5v-6h-5V13.2z M7.1,13.2h5v-6h-5V13.2z M13.1,13.2h5v-6h-5V13.2z M7.1,6.2h5
	v-6h-5V6.2z M13.1,0.2v6h5v-6H13.1z"/>
</svg>',
        'editor_script' => 'portal-edusharing-grid-script',
    ));

    wp_register_script(
        'portal-edusharing-list-script', get_template_directory_uri() . '/template-parts/blocks/portal_edusharing_list.js'
    );

    register_block_type('acf/portal-edusharing-list', array(
        'name' => 'portal-edusharing-list',
        'title' => 'EduSharing Liste',
        'description' => 'Zeigt eine Liste, die EduSharing-Blöcke enthält.',
        'category' => 'themenportal',
        'mode' => 'preview',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path fill="none" d="M0 0h24v24H0z"></path><path style="fill: #A2A2A2" d="M3 15h18v-2H3v2zm0 4h18v-2H3v2zm0-8h18V9H3v2zm0-6v2h18V5H3z"></path></svg>',
        'editor_script' => 'portal-edusharing-list-script',
    ));

}

add_action('init', 'register_edusharing_blocks');
