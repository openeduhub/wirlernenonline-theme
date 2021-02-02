<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Footer</div>';
} ?>

<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();

$educational_filter_values = get_educational_filter_values($postID);
$collectionUrl = $educational_filter_values["collectionUrl"];
$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);
$collectionID = $matches[1][0];

$pageTitle = get_the_title($postID);
$pageDiscipline = get_field('discipline', $postID)[0]['label'];

$showAuthors = get_field('showAuthors');
$author_ids = (!empty(get_field('authors', $postID))) ? get_field('authors', $postID) : [];
$author_page_link = (!empty(get_field('author_page_link', $postID))) ? get_field('author_page_link', $postID) : '';

//$addContentPageID = 9614; //dev
$addContentPageID = 9933; //pre
//$addContentPageID = 9081; //local

if (empty(get_field('authors', $postID))){
    $portalID = array(
        '120'       => 6526,    // deutsch
        '28002'     => 6512,    // deutsch als zweitsprache
        '100'       => 6521,    // chemie
        '460'       => 6506,    // physik
        '320'       => 16743,   // informatik
        '060'       => 6515,    // kunst
        '380'       => 6955,    // mathe
        '900'       => 6463,     // medienbildung
        '12002'     => 20473,     // darstellendes spiel
        '080'       => 20627,     // biologie
        '64018'     => 21313,     // nachhaltigkeit
        '20007'     => 20503,     // spanisch
        '20008'     => 21153,     // türkisch
        '20001'     => 20931,     // englisch
        '520'       => 21348,     // religion
        '480'       => 20266     // politik
    );
    $educational_filter_values = get_educational_filter_values($postID);
    $authors = get_field('authors', $portalID[ $educational_filter_values['disciplines'][0] ]);
    update_field( 'authors', $authors, $postID );
}

?>

<div class="fachportal-footer">

    <div class="fachportal-footer-search">

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
            <p><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.svg"> 1 weiteres (ungeprüftes) Ergebniss in unserer Suchmaschine</p>
        <?php }else if ($widget_search_result > 1){ ?>
            <p><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.svg"> <?php echo $response->data->search->total->value; ?> weitere (ungeprüfte) Ergebnisse in unserer Suchmaschine</p>
        <?php }else{ ?>
            <p><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.svg"> Leider keine Ergebnisse in unserer Suchmaschine</p>
        <?php }?>
        <a class="fachportal-footer-searchbutton" href="<?php echo WLO_SEARCH; ?>de/search?q=<?php echo get_the_title($postID); ?>" target="_blank">
            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"> Zur den Ergebnissen
        </a>
    </div>

    <a class="fachportal-add-content-button" href="<?php echo get_page_link($addContentPageID) . '?collectionID=' . $collectionID . '&headline=' . $pageTitle .'&pageDiscipline=' . $pageDiscipline; ?>" target="_blank">
        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/plus.svg"> Inhalte vorschlagen
    </a>

    <div class="fachportal-footer-team">
        <?php foreach ($author_ids as $author_id) {
            echo $author_id['user_avatar'];
        } ?>
    </div>
    <div class="fachportal-footer-text">
        <h3>Redaktion <?php echo $GLOBALS['wlo_fachportal']['title']; ?></h3>
        <p>
            Unser <?php echo $GLOBALS['wlo_fachportal']['title']; ?>-Team trägt die besten Inhalte und Materialien zusammen
            und überprüft vorgeschlagene Inhalte. Mach mit uns werde Teil unserer Community!
        </p>
    </div>

    <a class="fachportal-add-content-button" href="<?php echo get_page_link(97); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/plus.svg"> Mitmachen
    </a>
</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>

