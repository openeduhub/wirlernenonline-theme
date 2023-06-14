<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Swimlane</div>';
} ?>

<?php
// $postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
// $educational_filter_values = get_educational_filter_values($postID);
// $collectionUrl = $educational_filter_values["collectionUrl"];
// $url_components = parse_url($collectionUrl);
// parse_str($url_components['query'], $params);
// $collectionID = $params['id'];

$queryId = 'ngsearch';
$criteria = get_field('criteria');
?>


<p>Swimlane works</p>

<wlo-swimlane queryId="<?php echo $queryId; ?>" criteria='<?php echo $criteria; ?>'></wlo-swimlane>

<?php if (is_admin()) {
    echo '</div>';
} ?>