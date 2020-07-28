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

try {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8'
        )
    );
    $response = curl_exec($curl);
    if ($response === false) {
        echo 'curl error';
        return false;
    }
} catch (Exception $e) {
    echo 'curl error: ' . $e->getMessage();
    return false;
}
curl_close($curl);

$response = json_decode($response);

$title = (!empty(get_field('headline'))) ? get_field('headline') : $response->collection->properties->{'cm:title'}[0];
$description = (!empty(get_field('description'))) ? get_field('description') : $response->collection->properties->{'cm:description'}[0];
$iconUrl = (!empty(get_field('icon'))) ? get_field('icon')['sizes']['thumbnail'] : $response->collection->preview->url;
$author_ids = (!empty(get_field('authors', $postID))) ? get_field('authors', $postID) : [];
$author_page_link = (!empty(get_field('author_page_link', $postID))) ? get_field('author_page_link', $postID) : '';

$headerId = uniqid('header-');

?>
<div class="portal_block">
    <div class="portal_header_top">
        <div class="portal_header_top_left">
            <p><?php echo ($collectionLevel == 0) ? "Fachportal" : "Themenportal" ?></p>
            <h1><?php echo $title ?></h1>
            <img src="<?php echo $iconUrl ?>"/>
        </div>
        <div class="portal_header_top_right">
            <div class="portal_header_top_right_author_img_container">

                <?php foreach ($author_ids as $author_id) {
                    $author = get_user_by('id', $author_id);
                    ?>

                    <a href="mailto:<?php echo $author->user_email ?>"
                       title="<?php echo $author->display_name ?>">
                        <img src="<?php echo um_get_user_avatar_url( $author_id, $size = '96' )?>" />
                    </a>

                <?php } ?>
            </div>
            <div class="portal_header_top_right_author_button_container">
                <a href="<?php echo $author_page_link ?>" class="button primary small">Schreib uns!</a>
            </div>
        </div>
    </div>

    <div class="portal_header_bottom">
        <p><?php echo $description ?></p>
    </div>
</div>
<?php if (is_admin()) {
    echo '</div>';
}; ?>

