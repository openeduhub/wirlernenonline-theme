<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Widget-Inhalte für Themenseiten</div>';
};

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();

if (empty(get_field('authors', $postID))){
    $portalID = array(
        '120'   => 6526,    // deutsch
        '28002' => 6512,    // deutsch als zweitsprache
        '100'   => 6521,    // chemie
        '460'   => 6506,    // physik
        '320'   => 16743,   // informatik
        '060'   => 6515,    // kunst
        '380'   => 6955,    // mathe
        '900'   => 6463,     // medienbildung
        '12002'   => 20473     // darstellendes spiel
    );
    $educational_filter_values = get_educational_filter_values($postID);
    $authors = get_field('authors', $portalID[ $educational_filter_values['disciplines'][0] ]);
    update_field( 'authors', $authors, $postID );
}

if (empty(get_field('author_page_link', $postID))){
    update_field( 'author_page_link', 'themenportale@wirlernenonline.de)', $postID );
}

$GLOBALS['wlo_widget_duplicates'] = array();

$headline = 'Empfohlene Inhalte für dich  - von der Redaktion geprüft';
if (get_field('headline')){
    $headline = get_field('headline');
}

$widgetCtaText = 'Lass dich von den geprüften Inhalten inspirieren! Du kannst auch "Inhalte vorschlagen", die gut zum Thema passen.';
if (get_field('widgetCtaText')){
    $widgetCtaText = get_field('widgetCtaText');
}

$widgetCtaText_alt = 'Hilf uns, Inhalte zu sammeln! Du kannst sie direkt in die richtige Kategorie ablegen und unseren Redakteur:innen zur Prüfung schicken.';
if (get_field('widgetCtaText_alt')){
    $widgetCtaText_alt = get_field('widgetCtaText_alt');
}

$noContentHeadline = 'Wir sammeln und prüfen gerade Inhalte für das Thema "'.get_the_title($postID).'"';
if (get_field('noContentHeadline')){
    $noContentHeadline = get_field('noContentHeadline');
}

$noContentText = 'Unsere Mission ist es, freie Lehr- und Lerninhalte zu prüfen und zugänglich zu machen. Unterstütze uns dabei, klicke auf <b>"Inhalte vorschlagen"</b>';
if (get_field('noContentText')){
    $noContentText = get_field('noContentText');
}

?>

<div class="portal_block portal_widget_content">
    <div class="widget_content_header">
        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge.png">
        <h3><?php echo $headline; ?></h3>
    </div>
    <div>
        <p class="widget_cta"><?php echo $widgetCtaText; ?></p>

        <div class="portal_switcher">
            <button class="portal_switch_button active" id="portal-showSearch" onclick="showContent()">geprüfte Inhalte</button>
            <button class="portal_switch_button" id="portal-showQuality" onclick="showWidgets()">Inhalte vorschlagen</button>
        </div>

        <div class="no_widget_content">
            <img class="yellow_arrow" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/yellow_arrow.png">
            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/working.png">
            <h4><?php echo $noContentHeadline; ?></h4>
            <p><?php echo $noContentText; ?></b></p>
        </div>

        <div class="widget_content widget_content_count"></div>
        <div class="widgets <?php if (!is_admin()) echo 'portal_collection_grid'; ?>" >
            <InnerBlocks />
        </div>

    </div>

</div>

<div class="widget_search">

    <?php
    $search_query = '{
                      search(searchString: "' . esc_html( get_the_title($postID) ) . '") {
                        total {
                          relation
                          value
                        }
                      }
                    }';
    $response = callWloGraphApi($search_query);
    $widget_search_result = $response->data->search->total->value;
    ?>

    <?php if ($widget_search_result == 1){ ?>
        <h3><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.png"> 1 Ergebniss in unserer Suchmaschine</h3>
    <?php }else if ($widget_search_result > 1){ ?>
        <h3><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.png"> <?php echo $response->data->search->total->value; ?> Ergebnisse in unserer Suchmaschine</h3>
    <?php }else{ ?>
        <h3><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.png"> Leider keine Ergebnisse in unserer Suchmaschine</h3>
    <?php }?>
    <div class="portal_search_button">
        <a class="button primary small" href="<?php echo WLO_SEARCH; ?>de/search?q=<?php echo get_the_title($postID); ?>"
           target="_blank">Zur Suche
        </a>
    </div>
</div>

<script type="application/javascript">
    function showWidgets() {
        jQuery('.portal_widget').show();
        jQuery('.widget_content').hide();
        jQuery('.widget_cta').html('<?php echo $widgetCtaText_alt; ?>');
        jQuery('#portal-showQuality').addClass('active');
        jQuery('#portal-showSearch').removeClass('active');
        if (jQuery('.widget_content').length == 1){
            jQuery('.no_widget_content').hide();
        }
    }

    function showContent() {
        jQuery('.portal_widget').hide();
        jQuery('.widget_content').show();
        jQuery('.widget_cta').html('<?php echo $widgetCtaText; ?>');
        jQuery('#portal-showQuality').removeClass('active');
        jQuery('#portal-showSearch').addClass('active');
        if (jQuery('.widget_content').length == 1){
            jQuery('.no_widget_content').show();
        }
    }

    jQuery( document ).ready(function() {
        const widget_content_count = jQuery('.widget_content').length -1;
        if (widget_content_count == 1){
            jQuery('.widget_content_count').html('1 Inhalt');
        }else if (widget_content_count > 1){
            jQuery('.widget_content_count').html(widget_content_count + ' Inhalte');
        }else {
            jQuery('.no_widget_content').show();
            jQuery('.widget_cta').show();
        }
        <?php   if (is_admin()) {
                    echo "jQuery('.portal_widget').show();";
                } ?>
    });
</script>


<?php if (is_admin()) {
    echo '</div>';
}; ?>

