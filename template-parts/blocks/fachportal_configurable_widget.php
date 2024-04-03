<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Inhalte-Widget</div>';
} ?>

<?php
// $postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
// $educational_filter_values = get_educational_filter_values($postID);
// $collectionUrl = $educational_filter_values["collectionUrl"];
// $url_components = parse_url($collectionUrl);
// parse_str($url_components['query'], $params);
// $collectionID = $params['id'];

$widgetId = get_field('widget-id');
$eduTicket = get_repo_ticket();

?>

<wlo-user-configurable node-id="<?php echo $widgetId ?>" edu-ticket="<?php echo $eduTicket ?>" id="the-content"></wlo-user-configurable>

<?php if (is_admin()) {
    echo '</div>';
} ?>

<script>
    jQuery(document).ready(() => {
        const content = document.getElementById('the-content')
        content.addEventListener("persistConfigEvent", ($event) => {
            console.log('received event ', $event);
        });
        content.addEventListener("itemClickedEvent", ($event) => {
            showContentPopup($event.detail.ref.id);
        });
    })
</script>