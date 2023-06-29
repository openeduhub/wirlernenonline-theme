<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$portalTitle = get_the_title($postID);

$author_ids = get_field('authors', $postID);
if (empty($author_ids)) {
    $author_ids = array();
}
?>

<div class="fachportal-header-bar">
    <div class="fachportal-header-bar-wrapper">

        <div class="portal-breadcrumbs">
            <ul class="portal-breadcrumbs-list">
                <li class='portal-breadcrumbs-list-item'>
                    <a href='<?php echo get_page_link(55115); ?>'>Fachportale</a>
                    <span class='material-icons'>chevron_right</span>
                </li>
                <li class='portal-breadcrumbs-list-item'>
                    <a href='<?php echo get_page_link(); ?>'><?php echo $portalTitle; ?></a>
                    <span class='material-icons'>chevron_right</span>
                </li>
            </ul>
        </div>

        <div class="wlo-tile-team">
            <p>Redaktion <?php echo $portalTitle; ?>:</p>
            <?php foreach ($author_ids as $author_id) {
                echo $author_id['user_avatar'];
            } ?>
            <img class="wlo-team-bookmark" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/QS-Faehnchen.svg">
        </div>

    </div>
</div>