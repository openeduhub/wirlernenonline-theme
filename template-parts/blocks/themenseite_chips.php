<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenseite: Chips</div>';
} ?>
<style>
    wlo-collection-chips {
        display: flex;
        padding: 0 15px;
    }
</style>
<?php

require_once(get_template_directory() . '/functions/wlo-config.php');

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);
$collectionUrl = $educational_filter_values["collectionUrl"];
$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];
?>

<wlo-collection-chips collection="<?php echo $collectionID; ?>"></wlo-collection-chips>

<?php if (is_admin()) {
    echo '</div>';
} ?>