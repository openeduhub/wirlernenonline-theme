<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Redaktion</div>';
} ?>

<?php
$title = 'Redaktion '.$GLOBALS['wlo_fachportal']['title'];
if (!empty(get_field('title'))){
    $title = get_field('title');
}
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$author_ids = (!empty(get_field('authors', $postID))) ? get_field('authors', $postID) : [];

if (!empty(get_field('customUser'))){
    $author_ids = get_field('customUser');
}
?>
<div class="fachportal-content-block">
    <div class="wlo-tile content">
        <div class="widget-content">
            <div class="wlo-tile-team">
                <?php foreach ($author_ids as $author_id) {
                    echo $author_id['user_avatar'];
                } ?>
            </div>
            <div class="content-info">
                <div class="content-title"><?php echo $title; ?></div>
                <p class="content-description"><?php echo get_field('description'); ?></p>
                    <a class="wlo-tile-button wlo-tile-button-yellow" href="<?php echo get_page_link(97); ?>">Mitmachen</a>
            </div>
        </div>
    </div>
</div>

<?php if (is_admin()) {
    echo '</div>';
} ?>
