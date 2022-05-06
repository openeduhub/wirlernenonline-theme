<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Inhaltsblock</div>';
} ?>
<?php

require_once(get_template_directory().'/functions/wlo-config.php');

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];

$collectionLevel = get_field('collection_level', $postID);

$pageTitle = get_the_title($postID);
$pageDiscipline = get_the_title($postID);

/* ------------------------------------------------------------------- */

$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

$fachportalContentId = uniqid('fachportalContentId-');

$contentCount = get_field('content_count');
$contentType = get_field('contentType');
$blockIcon = '';
if (isset(get_field('blockIcon')['url'])){
    $blockIcon = get_field('blockIcon')['url'];
}
$softmatch = get_field('softmatch');
$sorting = get_field('sorting');
$descrText = base64_encode(get_field('descrText'));

$headline = '';
if ($collectionLevel >= 1){
    if (isset(get_field('contentType')['label'])){
        $headline = get_field('contentType')['label'];
    }
}
if(!empty(get_field('headline'))){
    $headline = get_field('headline');
}

$slidesToShow = 4;
$slidesToScroll = 4;
if (get_field('slidesToShow')) {
    $slidesToShow = get_field('slidesToShow');
}
if (get_field('slidesToScroll')) {
    $slidesToScroll = get_field('slidesToScroll');
}
$showSliderDots = 'true';
?>

<div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
    <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
</div>

<script type="text/javascript" >
    jQuery(document).ready(function($) {

        var data = {
            'action': 'fachportal_content_block',
            'postID': '<?php echo $postID; ?>',
            'collectionID': '<?php echo $collectionID; ?>',
            'headline': '<?php echo $headline; ?>',
            'descrText': '<?php echo $descrText; ?>',
            'collectionLevel': '<?php echo $collectionLevel; ?>',
            'blockIcon': '<?php echo $blockIcon; ?>',
            'softmatch': '<?php echo $softmatch; ?>',
            'sorting': '<?php echo $sorting; ?>',
            'slidesToShow': '<?php echo $slidesToShow; ?>',
            'slidesToScroll': '<?php echo $slidesToScroll; ?>',
            'contentCount': '<?php echo $contentCount; ?>',
            'contentType': '<?php echo htmlentities(json_encode($contentType)); ?>',
            'educational_filter_values': '<?php echo htmlentities(json_encode($educational_filter_values)); ?>',
        };

        jQuery.post(ajaxurl, data, function(response) {
            jQuery('#<?php echo $fachportalContentId; ?>').html(response);
        });

    });

</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>
