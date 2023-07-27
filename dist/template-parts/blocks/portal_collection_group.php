<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Sammlungsinhalte Gruppe</div>';
};

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();

$headline = 'Überschrift';
if (get_field('headline')){
    $headline = get_field('headline');
}
?>

<div class="portal_block portal_collection_group">
    <h3><?php echo $headline; ?></h3>
    <hr>

    <div <?php if (!is_admin()) echo 'class="portal_collection_grid"'; ?>>
        <InnerBlocks />
    </div>
</div>


<?php if (is_admin()) {
    echo '</div>';
}; ?>

