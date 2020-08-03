<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Header</div>';
};


/* Filter Values prioritized GET/POST > Block-Setting > Portal-Setting */
/*
 * collectionLevel
 * collectionUrl
 * disciplines
 * educationalContexts
 * intendedEndUserRoles
 * oer
*/
/* ------------------------------------------------------------------- */
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

// echo '<pre style="background-color: lightgrey">' , var_dump($educational_filter_values) , '</pre>';
// echo '<script>console.log(' , json_encode($educational_filter_values) , ')</script>';

$collectionLevel = intval($educational_filter_values["collectionLevel"]);
$collectionUrl = $educational_filter_values["collectionUrl"];
$disciplines = $educational_filter_values["disciplines"];
$educationalContexts = $educational_filter_values["educationalContexts"];
$intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
$oer = $educational_filter_values["oer"];
/* ------------------------------------------------------------------- */

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);

$url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/-home-/' . $matches[1][0];
$response = callWloRestApi($url);

$title = (!empty(get_field('headline'))) ? get_field('headline') : $response->collection->properties->{'cm:title'}[0];
$description = (!empty(get_field('description'))) ? get_field('description') : $response->collection->properties->{'cm:description'}[0];
$showIcon = get_field('showIcon');
$iconUrl = (!empty(get_field('icon'))) ? get_field('icon')['sizes']['thumbnail'] : $response->collection->preview->url;
$showAuthors = get_field('showAuthors');
$author_ids = (!empty(get_field('authors', $postID))) ? get_field('authors', $postID) : [];
$author_page_link = (!empty(get_field('author_page_link', $postID))) ? get_field('author_page_link', $postID) : '';

$headerId = uniqid('header-');

?>
<div class="portal_block no-margin">
    <div class="portal_header_top">
        <div class="portal_header_top_left">
            <div class="portal_header_top_left_title_container">
                <h1 class="portal_header_title"><?php echo $title ?></h1>
            </div>
            <div class="portal_header_icon">
                <?php if($showIcon){?>
                    <img src="<?php echo $iconUrl ?>"/>
                <?php }?>
            </div>
<!--            <h6>--><?php //echo ($collectionLevel == 0) ? "Fachportal" : "Themenportal" ?><!--</h6>-->
        </div>
        <div class="portal_header_top_right">
            <?php if($showAuthors){?>
            <div class="portal_header_top_right_author_img_container">
                <?php
                    foreach ($author_ids as $author_id) {
                        //var_dump($author_id);
                        $author = get_user_by('id', $author_id['ID']);
                        um_fetch_user( $author_id['ID'] );
                        ?>

                        <a href="mailto:<?php echo $author_id['user_email'] ?>"
                           title="<?php echo $author_id['nickname'] ?>">
                            <?php echo $author_id['user_avatar'] ?>
                        </a>

                        <?php um_reset_user();
                    } ?>
            </div>
            <div class="portal_header_top_right_author_button_container">
                <a href="<?php echo $author_page_link ?>" class="button primary small">Schreib uns!</a>
            </div>
            <?php }?>
        </div>
    </div>

    <div class="portal_header_bottom">
        <div>
            <p><?php echo $description ?></p>
        </div>
    </div>
</div>
<?php if (is_admin()) {
    echo '</div>';
}; ?>

