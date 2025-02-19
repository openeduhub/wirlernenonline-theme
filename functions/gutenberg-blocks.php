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
add_filter('block_categories_all', 'custom_block_category', 10, 2);

// Register Custom Blocks
// For Icons go to: https://developer.wordpress.org/resource/dashicons/ – But leafe the "dashicons-" Prefix

function register_acf_block_types()
{
    acf_register_block_type([
        'name'              => 'Quellentabelle',
        'title'             => __('WLO: Quellentabelle'),
        'description'       => __('Zeigt eine Auflistung der Quellenerschließung'),
        'render_template'   => 'template-parts/blocks/source_table.php',
        'category'          => 'custom-blocks',
        'icon'              => 'tag',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo-job',
        'title'             => __('WLO-Job'),
        'description'       => __('Block mit Job-link'),
        'render_template'   => 'template-parts/blocks/wlo_job.php',
        'category'          => 'custom-blocks',
        'icon'              => 'id-alt',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name' => 'edusharing_repo_button',
        'title'             => __('Button zu edu-sharing'),
        'description'       => __('Springt zum ES-Repo mit Login.'),
        'render_template'   => 'template-parts/blocks/edusharing_repo_button.php',
        'category'          => 'custom-blocks',
        'icon'              => 'admin-links',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name' => 'fachportal_content_block',
        'title'             => __('Fachportal: Inhaltsblock'),
        'description'       => __('Zeigt Sammlungs-Inhalte an.'),
        'render_template'   => 'template-parts/blocks/fachportal_content_block.php',
        'category'          => 'themenportal',
        'icon'              => 'format-image',
        'keywords'          => [],
        'enqueue_assets'    => 'wlo_themenseite_detail_view_assets',
    ]);
    acf_register_block_type([
        'name'              => 'themenseite_content_block',
        'title'             => __('Themenseite: Inhaltsblock'),
        'description'       => __('Zeigt Sammlungs-Inhalte an.'),
        'render_template'   => 'template-parts/blocks/themenseite_content_block.php',
        'category'          => 'themenportal',
        'icon'              => 'format-image',
        'keywords'          => [],
        'enqueue_assets'    => 'wlo_themenseite_detail_view_assets',
    ]);
    acf_register_block_type([
        'name'              => 'themenseite_themenbaum',
        'title'             => __('Themenseite: Themenbaum'),
        'description'       => __('Eine Spaltenansicht des Themenbaums'),
        'render_template'   => 'template-parts/blocks/themenseite_themenbaum.php',
        'category'          => 'themenportal',
        'icon'              => 'columns',
        'keywords'          => [],
        'enqueue_assets'    => 'wlo_themenseite_ng_widgets_assets',
    ]);
    acf_register_block_type([
        'name'              => 'fachportal_blog_content_block',
        'title'             => __('Fachportal: Blogbeiträge'),
        'description'       => __('Zeigt Blog-Inhalte an.'),
        'render_template'   => 'template-parts/blocks/fachportal_blog_content_block.php',
        'category'          => 'themenportal',
        'icon'              => 'format-image',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'fachportal_header_block',
        'title'             => __('Themenseite: Header'),
        'description'       => __('Zeigt den Header an.'),
        'render_template'   => 'template-parts/blocks/fachportal_header_block.php',
        'category'          => 'themenportal',
        'icon'              => 'format-image',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'fachportal_footer_block',
        'title'             => __('Themenseite: Footer'),
        'description'       => __('Zeigt den Footer an.'),
        'render_template'   => 'template-parts/blocks/fachportal_footer_block.php',
        'category'          => 'themenportal',
        'icon'              => 'format-image',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'fachseite_header_block',
        'title'             => __('Fachseite: Header'),
        'description'       => __('Zeigt den Header an.'),
        'render_template'   => 'template-parts/blocks/fachseite_header_block.php',
        'category'          => 'themenportal',
        'icon'              => 'format-image',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'fachseite_feature_block',
        'title'             => __('Fachseite: Inhalt des Monats'),
        'description'       => __('Featured einen WLO-Inhalt'),
        'render_template'   => 'template-parts/blocks/fachseite_feature_block.php',
        'category'          => 'themenportal',
        'icon'              => 'format-image',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'fachseite_newest_content',
        'title'             => __('Fachseite: Neueste Inhalte'),
        'description'       => __('Featured die neuesten geprüften Inhalte'),
        'render_template'   => 'template-parts/blocks/fachseite_newest_content.php',
        'category'          => 'themenportal',
        'icon'              => 'slides',
        'keywords'          => [],
    ]);
    acf_register_block_type(['name' => 'fachportal_newest_content',
        'title'             => __('Fachportal: Neueste Inhalte'),
        'description'       => __('Featured die neuesten geprüften Inhalte'),
        'render_template'   => 'template-parts/blocks/fachportal_newest_content.php',
        'category'          => 'themenportal',
        'icon'              => 'slides',
        'keywords'          => [ ],
    ]);
    acf_register_block_type([
        'name'              => 'fachportal_team_block',
        'title'             => __('Fachseite: Redaktion'),
        'description'       => __('Zeigt das Redaktions-Team'),
        'render_template'   => 'template-parts/blocks/fachportal_team_block.php',
        'category'          => 'themenportal',
        'icon'              => 'groups',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'fachportal_spacer',
        'title'             => __('Fachportal: Trennlinie'),
        'description'       => __('Trennt Inhalte horizontal'),
        'render_template'   => 'template-parts/blocks/fachportal_spacer.php',
        'category'          => 'themenportal',
        'icon'              => 'minus',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'fachportal_swimlane',
        'title'             => __('Fachportal: Swimlane'),
        'description'       => __('Swimlane für gefilterte Sammlungsinhalte aus der Redaktionsumgebung'),
        'render_template'   => 'template-parts/blocks/fachportal_swimlane.php',
        'category'          => 'themenportal',
        'icon'              => 'slides',
        'keywords'          => [],
        'enqueue_assets' => function () {
            wp_enqueue_style('wlo-pages', get_template_directory_uri() . '/vendor/wlo-pages/styles.css', array(), '');
            wp_enqueue_script('wlo-pages-polyfills', get_template_directory_uri() . '/vendor/wlo-pages/polyfills.js', array(), '', true);
            wp_enqueue_script('wlo-pages-runtime', get_template_directory_uri() . '/vendor/wlo-pages/runtime.js', array(), '', true);
            wp_enqueue_script('wlo-pages-main', get_template_directory_uri() . '/vendor/wlo-pages/main.js', array(), '', true);
        },
    ]);
    acf_register_block_type([
        'name'              => 'themenseite_chips',
        'title'             => __('Themeinseite: Chips'),
        'description'       => __('Unterseiten der Sammlung als Chips/Badges'),
        'render_template'   => 'template-parts/blocks/themenseite_chips.php',
        'category'          => 'themenportal',
        'icon'              => 'slides',
        'keywords'          => [],
        'enqueue_assets' => function () {
            wp_enqueue_style('wlo-pages', get_template_directory_uri() . '/vendor/wlo-pages/styles.css', array(), '');
            wp_enqueue_script('wlo-pages-polyfills', get_template_directory_uri() . '/vendor/wlo-pages/polyfills.js', array(), '', true);
            wp_enqueue_script('wlo-pages-runtime', get_template_directory_uri() . '/vendor/wlo-pages/runtime.js', array(), '', true);
            wp_enqueue_script('wlo-pages-main', get_template_directory_uri() . '/vendor/wlo-pages/main.js', array(), '', true);
            wp_enqueue_script('wlo-pages-vendor', get_template_directory_uri() . '/vendor/wlo-pages/vendor.js', array(), '', true);
        },
    ]);
    acf_register_block_type([
        'name'              => 'fachportal_configurable_widget',
        'title'             => __('Fachportal: Inhalte-Widget'),
        'description'       => __('Widget für gefilterte Sammlungsinhalte aus der Redaktionsumgebung'),
        'render_template'   => 'template-parts/blocks/fachportal_configurable_widget.php',
        'category'          => 'themenportal',
        'icon'              => 'slides',
        'keywords'          => [],
        'enqueue_assets' => function () {
            wp_enqueue_style('wlo-pages', get_template_directory_uri() . '/vendor/wlo-pages/styles.css', array(), '');
            wp_enqueue_script('wlo-pages-polyfills', get_template_directory_uri() . '/vendor/wlo-pages/polyfills.js', array(), '', true);
            wp_enqueue_script('wlo-pages-runtime', get_template_directory_uri() . '/vendor/wlo-pages/runtime.js', array(), '', true);
            wp_enqueue_script('wlo-pages-main', get_template_directory_uri() . '/vendor/wlo-pages/main.js', array(), '', true);
        },
    ]);
    acf_register_block_type([
        'name'              => 'career_events_map',
        'title'             => __('Berufsseite: Veranstaltungs-Karte'),
        'description'       => __('Kartenansicht berufsbezogener Veranstaltungen'),
        'render_template'   => 'template-parts/blocks/career_events_map.php',
        'category'          => 'themenportal',
        'icon'              => 'location-alt',
        'keywords'          => [],
        'enqueue_assets' => function () {
            wp_enqueue_style('leaflet', get_template_directory_uri() . '/vendor/leaflet/dist/leaflet.css', array(), '1.9.4');
            wp_enqueue_script('leaflet', get_template_directory_uri() . '/vendor/leaflet/dist/leaflet.js', array(), '1.9.4', true);
        },
    ]);
    acf_register_block_type([
        'name'              => 'wlo_news',
        'title'             => __('WLO News'),
        'description'       => __('Zeigt News-Beiträge'),
        'render_template'   => 'template-parts/blocks/wlo_news.php',
        'category'          => 'wlo',
        'icon'              => 'media-text',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_newsletter',
        'title'             => __('WLO Newsletter'),
        'description'       => __('Anmeldeformular zum WLO-Newsletter'),
        'render_template'   => 'template-parts/blocks/wlo_newsletter.php',
        'category'          => 'wlo',
        'icon'              => 'email',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'partner',
        'title'             => __('WLO: Partner'),
        'description'       => __('Zeigt die Logos der Partner'),
        'render_template'   => 'template-parts/blocks/wlo_partner.php',
        'category'          => 'custom-blocks',
        'icon'              => 'groups',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_button',
        'title'             => __('WLO Button'),
        'description'       => __('/blau oder gelb)'),
        'render_template'   => 'template-parts/blocks/wlo_button.php',
        'category'          => 'wlo',
        'icon'              => 'admin-links',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_tile',
        'title'             => __('WLO Kachel'),
        'description'       => __('Inhalt mit Bild und Button'),
        'render_template'   => 'template-parts/blocks/wlo_tile.php',
        'category'          => 'wlo',
        'icon'              => 'format-image',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_basic_tile',
        'title'             => __('WLO einfache Kachel'),
        'description'       => __('Für die Startseite'),
        'render_template'   => 'template-parts/blocks/wlo_basic_tile.php',
        'category'          => 'wlo',
        'icon'              => 'format-image',
        'supports'          => array(
            'mode' => false,
            '__experimental_jsx' => true,
        ),
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_collapsable_text',
        'title'             => __('WLO einklappbarer Text'),
        'description'       => __('z.B. für die Beirats-Seite'),
        'render_template'   => 'template-parts/blocks/wlo_collapsable_text.php',
        'category'          => 'wlo',
        'icon'              => 'editor-alignleft',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_oer_menu',
        'title'             => __('WLO Untermenü für OER-Seiten'),
        'description'       => __('Nur für die OER-Seiten'),
        'render_template'   => 'template-parts/blocks/wlo_oer_menu.php',
        'category'          => 'wlo',
        'icon'              => 'category',
        'supports'          => array(
            'mode' => false,
            '__experimental_jsx' => true
        ),
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_redaktion_menu',
        'title'             => __('WLO Nav für Redaktionsseiten'),
        'description'       => __('Nur für Redaktionsseiten'),
        'render_template'   => 'template-parts/blocks/redaktion/wlo_redaktion_menu.php',
        'category'          => 'wlo',
        'icon'              => 'admin-links',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_redaktion_tool',
        'title'             => __('Qualitätskontrolle für die Themenseiten'),
        'description'       => __('Nur für Redaktionsseiten'),
        'render_template'   => 'template-parts/blocks/redaktion/wlo_redaktion_tool.php',
        'category'          => 'wlo',
        'icon'              => 'admin-settings',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_redaktion_new_content',
        'title'             => __('Neue Inhalte zu WLO hinzufügen'),
        'description'       => __('Nur für Redaktionsseiten'),
        'render_template'   => 'template-parts/blocks/redaktion/wlo_redaktion_new_content.php',
        'category'          => 'wlo',
        'icon'              => 'plus-alt',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_redaktion_check_new_content',
        'title'             => __('WLO-Redaktion: Inhalte prüfen'),
        'description'       => __('Nur für Redaktionsseiten'),
        'render_template'   => 'template-parts/blocks/redaktion/wlo_redaktion_check_new_content.php',
        'category'          => 'wlo',
        'icon'              => 'plus-alt',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_redaktion_iframe',
        'title'             => __('iFrame mit Ticket'),
        'description'       => __('Für die Redaktionsumgebung'),
        'render_template'   => 'template-parts/blocks/redaktion/wlo_redaktion_iframe.php',
        'category'          => 'wlo',
        'icon'              => 'welcome-widgets-menus',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_testimonials',
        'title'             => __('WLO Testimonials'),
        'description'       => __('Für die Startseite'),
        'render_template'   => 'template-parts/blocks/wlo_testimonials.php',
        'category'          => 'wlo',
        'icon'              => 'format-quote',
        'keywords'          => [],
    ]);
    acf_register_block_type([
        'name'              => 'wlo_accordion',
        'title'             => __('WLO Registerkarten'),
        'description'       => __('Ausklappbare Inhalte'),
        'render_template'   => 'template-parts/blocks/wlo_accordion.php',
        'category'          => 'wlo',
        'icon'              => '<svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100.047 182.767C145.732 182.767 182.767 145.732 182.767 100.047C182.767 54.3617 145.732 17.3267 100.047 17.3267C54.3615 17.3267 17.3265 54.3617 17.3265 100.047C17.3265 145.732 54.3615 182.767 100.047 182.767Z" fill="#1B3E76"/>
                                <path d="M100.047 200C93.276 200 86.5047 199.317 79.9208 197.969C73.4956 196.655 67.175 194.692 61.1351 192.137C55.2089 189.631 49.4943 186.529 44.1499 182.919C38.8607 179.346 33.8878 175.243 29.3693 170.724C24.8507 166.205 20.7478 161.233 17.1743 155.943C13.5635 150.599 10.4622 144.884 7.9558 138.958C5.40113 132.918 3.43929 126.598 2.12404 120.172C0.776391 113.589 0.0932617 106.817 0.0932617 100.047C0.0932617 93.276 0.776391 86.5047 2.12404 79.9209C3.4386 73.4956 5.40113 67.1751 7.9558 61.1352C10.4622 55.209 13.5642 49.4944 17.1743 44.15C20.7478 38.8607 24.8507 33.8879 29.3693 29.3693C33.8878 24.8507 38.8607 20.7478 44.1499 17.1743C49.4943 13.5636 55.2089 10.4623 61.1351 7.95586C67.175 5.40119 73.4956 3.43935 79.9208 2.1241C86.5047 0.776452 93.276 0.0933228 100.047 0.0933228C106.817 0.0933228 113.589 0.776452 120.172 2.1241C126.598 3.43866 132.918 5.40119 138.958 7.95586C144.884 10.4623 150.599 13.5643 155.943 17.1743C161.233 20.7478 166.205 24.8507 170.724 29.3693C175.243 33.8879 179.345 38.8607 182.919 44.15C186.53 49.4944 189.631 55.209 192.137 61.1352C194.692 67.1751 196.654 73.4956 197.969 79.9209C199.317 86.5047 200 93.276 200 100.047C200 106.817 199.317 113.589 197.969 120.172C196.655 126.598 194.692 132.918 192.137 138.958C189.631 144.884 186.529 150.599 182.919 155.943C179.345 161.233 175.243 166.205 170.724 170.724C166.205 175.243 161.233 179.346 155.943 182.919C150.599 186.53 144.884 189.631 138.958 192.137C132.918 194.692 126.598 196.654 120.172 197.969C113.589 199.317 106.817 200 100.047 200ZM100.047 34.56C63.9372 34.56 34.5599 63.9373 34.5599 100.047C34.5599 136.156 63.9372 165.533 100.047 165.533C136.156 165.533 165.533 136.156 165.533 100.047C165.533 63.9373 136.156 34.56 100.047 34.56Z" fill="#EA4B71"/>
                                </svg>',
        'supports'          => array(
            'mode' => false,
            '__experimental_jsx' => true,
        ),
        'keywords'          => [],
    ]);
    register_block_type(__DIR__ . '/../blocks/build/chat-gpt-prompt');
    add_action('admin_enqueue_scripts', function () {
        wp_add_inline_script(
            'wirlernenonline-theme-chat-gpt-prompt-editor-script',
            'window.chatGptPromptConfig = ' . json_encode(array(
                'eduSharingUrl' => WLO_REPO,
            )),
            'before'
        );
    });
    register_block_type(__DIR__ . '/../blocks/build/chat-gpt-chat');
}
if (function_exists('acf_register_block_type')) {
    add_action('acf/init', 'register_acf_block_types');
}

function wlo_redaktion_metaqs_assets()
{
    if (!is_admin()) {
        wp_enqueue_script('runtime-metaqs-js', WLO_METAQS_NG_URL . '/runtime.js', array('jquery'), '', true);
        wp_enqueue_script('polyfills-metaqs-js', WLO_METAQS_NG_URL . '/polyfills.js', array('jquery'), '', true);
        wp_enqueue_script('main-metaqs-js', WLO_METAQS_NG_URL . '/main.js', array('jquery'), '', true);
        wp_enqueue_style('styles-metaqs-css', WLO_METAQS_NG_URL . '/styles.css', array(), '', 'all');
    }
}

function wlo_themenseite_detail_view_assets()
{
    if (!is_admin()) {
        wp_enqueue_script('runtime-detailView-js', get_template_directory_uri() . '/src/assets/js/angular/detail_view/runtime.js', array('jquery'), '', true);
        wp_enqueue_script('polyfills-detailView-js', get_template_directory_uri() . '/src/assets/js/angular/detail_view/polyfills.js', array('jquery'), '', true);
        wp_enqueue_script('main-detailView-js', get_template_directory_uri() . '/src/assets/js/angular/detail_view/main.js', array('jquery'), '', true);
        wp_enqueue_style('styles-detailView-css', get_template_directory_uri() . '/src/assets/js/angular/detail_view/styles.css', array(), '', 'all');
    }
}

function wlo_themenseite_ng_widgets_assets()
{
?>
    <script>
        window.EDU_SHARING_API_URL = '<?php echo WLO_REPO; ?>rest';
        window.WORDPRESS_URL = '<?php echo get_site_url(); ?>';
    </script>
<?php
    wp_enqueue_script('runtime-ngWidgets-js', get_template_directory_uri() . '/src/assets/js/angular/ng-widgets/runtime.js', array('jquery'), '', true);
    wp_enqueue_script('polyfills-ngWidgets-js', get_template_directory_uri() . '/src/assets/js/angular/ng-widgets/polyfills.js', array('jquery'), '', true);
    wp_enqueue_script('main-ngWidgets-js', get_template_directory_uri() . '/src/assets/js/angular/ng-widgets/main.js', array('jquery'), '', true);
    wp_enqueue_style('styles-ngWidgets-css', get_template_directory_uri() . '/src/assets/js/angular/ng-widgets/styles.css', array(), '', 'all');
}

function register_edusharing_blocks()
{

    wp_register_script(
        'portal-edusharing-grid-script',
        get_template_directory_uri() . '/template-parts/blocks/portal_edusharing_grid.js'
    );

    register_block_type('acf/portal-edusharing-grid', array(
        'name'              => 'portal-edusharing-grid',
        'title'             => 'EduSharing Grid',
        'description'       => 'Zeigt ein Grid, das EduSharing-Blöcke enthält.',
        'category'          => 'themenportal',
        'mode'              => 'preview',
        'icon'              => '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 19.2 13.2" style="enable-background:new 0 0 19.2 13.2;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#A2A2A2;}
</style>
<path class="st0" d="M1.1,6.2h5v-6h-5V6.2z M1.1,13.2h5v-6h-5V13.2z M7.1,13.2h5v-6h-5V13.2z M13.1,13.2h5v-6h-5V13.2z M7.1,6.2h5
	v-6h-5V6.2z M13.1,0.2v6h5v-6H13.1z"/>
</svg>',
        'editor_script'     => 'portal-edusharing-grid-script',
    ));

    wp_register_script(
        'portal-edusharing-list-script',
        get_template_directory_uri() . '/template-parts/blocks/portal_edusharing_list.js'
    );

    register_block_type('acf/portal-edusharing-list', array(
        'name'              => 'portal-edusharing-list',
        'title'             => 'EduSharing Liste',
        'description'       => 'Zeigt eine Liste, die EduSharing-Blöcke enthält.',
        'category'          => 'themenportal',
        'mode'              => 'preview',
        'icon'              => '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path fill="none" d="M0 0h24v24H0z"></path><path style="fill: #A2A2A2" d="M3 15h18v-2H3v2zm0 4h18v-2H3v2zm0-8h18V9H3v2zm0-6v2h18V5H3z"></path></svg>',
        'editor_script'     => 'portal-edusharing-list-script',
    ));
    wp_enqueue_script('fix-lodash-conflicts', get_template_directory_uri() . '/src/assets/js/fixLodashConflict.js', array('jquery'), '', true);
}
add_action('init', 'register_edusharing_blocks');
