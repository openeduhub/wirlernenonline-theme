<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: MetaQS-Block</div>';
} ?>

<?php

if ($GLOBALS['wlo_redaktion']['subject'] == 'DaZ'){
    $GLOBALS['wlo_redaktion']['subject'] = 'Deutsch als Zweitsprache';
}

if (is_admin() &&  empty($GLOBALS['wlo_redaktion']['subject'])){
    $GLOBALS['wlo_redaktion']['subject'] = 'Deutsch';
}

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}

$query_args = array(
    'posts_per_page' => 10,
    'post_type' => 'portal',
    'post_status' => 'publish',
    'title' => $GLOBALS['wlo_redaktion']['subject'],
    //'title' => 'deutsch',
    'fields' => 'ids',
    'meta_query' => array(
        array(
            'key' => 'collection_level',
            'value' => 0
        )
    )
);

$query_result = new WP_Query($query_args);
$poratlID = $query_result->posts[0];
$collectionUrl = get_field('collection_url', $poratlID);
$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);
$collectionID = $matches[1][0];

$metaQsMode = get_field('mode')['value'];
$metaQsHeight = get_field('height');
if (empty($metaQsHeight)){
    $metaQsHeight = 400;
}

$ng_dir =  get_template_directory_uri() . '/src/assets/js/angular/';

?>

<div class="wlo-redaktion-metaqs">
    <app-meta-widget collectionid="<?php echo $collectionID; ?>" ticket="<?php echo $ticket; ?>" mode="<?php echo $metaQsMode; ?>" style="display:flex; height: <?php echo $metaQsHeight; ?>px"></app-meta-widget>
</div>


<?php if (is_admin()) { ?>
    <link rel="stylesheet" href="<?php echo $ng_dir; ?>styles.metaqs.css" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="<?php echo $ng_dir; ?>styles.metaqs.css"></noscript>
    <script src="<?php echo $ng_dir; ?>runtime.metaqs.js" defer></script>
    <script src="<?php echo $ng_dir; ?>polyfills.metaqs.js" defer></script>
    <script src="<?php echo $ng_dir; ?>main.metaqs.js" defer></script>
    <?php echo '</div>';
} ?>
