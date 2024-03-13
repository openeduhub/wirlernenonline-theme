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

//$elementId = uniqid('configurable-');
$widgetId = get_field('widget-id');

?>

<wlo-user-configurable id="the-content"></wlo-user-configurable>

<?php if (is_admin()) {
    echo '</div>';
} ?>

<script>
    jQuery(document).ready(() => {
        const content = document.getElementById('the-content')
        content.addEventListener("persistConfig", ($event) => {
            console.log('received event ', $event);
            console.log('THE ACF ', acf);
            console.log('has AFC-field: ', acf.getField('widget-id'));
            console.log('available AFC-fields: ', acf.findFields());
            console.log('AFC-field: ', acf.get('Widget-ID'));
            console.log('ACF-Field by key: ', acf.get('field_65eaeb33ec0d4'));
            if (acf.has('widget-id')) {
                acf.set('widget-id', $event.detail.value);
            }
        });
    })
</script>