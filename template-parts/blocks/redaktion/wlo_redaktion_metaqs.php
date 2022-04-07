<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: MetaQS-Block</div>';
} ?>

<?php


switch ($GLOBALS['wlo_redaktion']['subject']) {
    case 'DaZ':
        $GLOBALS['wlo_redaktion']['subject'] = 'Deutsch als Zweitsprache';
        break;
    case 'Deutsch':
        //$GLOBALS['wlo_redaktion']['subject'] = 'Deutsch - Musterfachseite'; // nur für dev!
        break;
    case 'Berufsorientierung':
        $GLOBALS['wlo_redaktion']['subject'] = 'Zukunfts- und Berufsorientierung';
        break;
}

if (is_admin() &&  empty($GLOBALS['wlo_redaktion']['subject'])){
    $GLOBALS['wlo_redaktion']['subject'] = 'Deutsch als Zweitsprache';
}

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}

$collectionID = wlo_getPortalIDbyName($GLOBALS['wlo_redaktion']['subject']);

$metaQsMode = get_field('mode')['value'];
$metaQsHeight = get_field('height');
if (empty($metaQsHeight)){
    $metaQsHeight = 400;
}

$ng_dir =  get_template_directory_uri() . '/src/assets/js/angular/';

?>

<div class="wlo-redaktion-metaqs">
    <div class="metaqs-badge"><?php echo $GLOBALS['wlo_redaktion']['subject']; ?></div>
    <app-meta-widget collectionid="<?php echo $collectionID; ?>" ticket="<?php echo $ticket; ?>" mode="<?php echo $metaQsMode; ?>" style="display:flex; height: <?php echo $metaQsHeight; ?>px"></app-meta-widget>
</div>


<?php if (is_admin()) { ?>
    <link rel="stylesheet" href="<?php echo $ng_dir; ?>styles.metaqs.css" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="<?php echo $ng_dir; ?>styles.metaqs.css"></noscript>
    <script src="<?php echo $ng_dir; ?>runtime.metaqs.js" defer></script>
    <script src="<?php echo $ng_dir; ?>polyfills.metaqs.js" defer></script>
    <script src="<?php echo $ng_dir; ?>main.metaqs.js" defer></script>
    <?php echo '</div>';
} ?>
