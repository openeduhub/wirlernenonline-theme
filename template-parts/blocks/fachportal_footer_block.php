<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenseite: Footer</div>';
} ?>

<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();

$educational_filter_values = get_educational_filter_values($postID);
$collectionUrl = $educational_filter_values["collectionUrl"];

$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

$pageTitle = get_the_title($postID);
$pageDiscipline = get_the_title($postID);

$authors = get_field('authors', $postID);

$showAuthors = get_field('showAuthors');
$author_ids = (!empty(get_field('authors', $postID))) ? get_field('authors', $postID) : [];
$author_page_link = (!empty(get_field('author_page_link', $postID))) ? get_field('author_page_link', $postID) : '';

?>

<div class="fachportal-footer">

    <div class="fachportal-footer-search">

        <?php
        $searchTotal = $GLOBALS['wlo_themenseiten_searchTotal'];
        ?>

        <?php if ($searchTotal == 1){ ?>
            <p><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.svg" alt="Icon: Roboter"> 1 weiteres (ungeprüftes) Ergebnis in unserer Suchmaschine</p>
        <?php }else if ($searchTotal > 1){ ?>
            <p><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.svg" alt="Icon: Roboter"> <?php echo $searchTotal; ?> weitere (ungeprüfte) Ergebnisse in unserer Suchmaschine</p>
        <?php }else{ ?>
            <p><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.svg" alt="Icon: Roboter"> Leider keine Ergebnisse in unserer Suchmaschine</p>
        <?php }?>
        <a class="fachportal-footer-searchbutton" href="<?php echo WLO_SEARCH; ?>de/search?q=<?php echo get_the_title($postID); ?>" target="_blank">
            Zu den Ergebnissen
        </a>
    </div>

    <a class="fachportal-add-content-button" href="<?php echo get_page_link(ADD_CONTENT_PAGE_ID) . '?collectionID=' . $collectionID . '&headline=' . $pageTitle .'&pageDiscipline=' . $pageDiscipline; ?>" target="_blank">
        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/plus.svg" alt="Icon: Plus"> Inhalte vorschlagen
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
            und überprüft vorgeschlagene Inhalte. Mach mit und werde Teil unserer Community!
        </p>
    </div>

    <a class="fachportal-add-content-button" href="<?php echo get_page_link(97); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/plus.svg" alt="Icon: Plus"> Mitmachen
    </a>
</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>

