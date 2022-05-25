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

$wloMetaQsId = uniqid('wlo-metaqs-');

$metaQsMode = get_field('mode')['value'];
$metaQsHeight = get_field('height');
if (empty($metaQsHeight)){
    $metaQsHeight = 400;
}

$ng_dir =  get_template_directory_uri() . '/src/assets/js/angular/';

?>

<div class="wlo-redaktion-metaqs" id="<?php echo $wloMetaQsId; ?>">
    <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
</div>

<script type="text/javascript" >
    jQuery(document).ready(function($) {

        var data = {
            'action': 'wlo_metaQs',
            'subject': '<?php echo $GLOBALS['wlo_redaktion']['subject']; ?>',
            'metaQsMode': '<?php echo $metaQsMode; ?>',
            'metaQsHeight': '<?php echo $metaQsHeight; ?>',
            'collectionID': '<?php echo $collectionID; ?>',
            'ticket': '<?php echo $ticket; ?>',
            'ng_dir': '<?php echo $ng_dir; ?>',
            'admin': '<?php echo is_admin(); ?>',
        };

        jQuery.post(ajaxurl, data, function(response) {
            jQuery('#<?php echo $wloMetaQsId; ?>').html(response);
        });

    });

</script>
