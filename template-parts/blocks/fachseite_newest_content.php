<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachseite: Neueste Inhalte</div>';
} ?>

<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];

$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

$rgbBackgroundColor = $GLOBALS['wlo_fachportal']['rgbBackgroundColor'];
$accordionID = uniqid();
$sliderId = uniqid('slider-');
$slidesToShow = 3;
$slidesToScroll = 3;

// newest contents
$url = WLO_REPO . 'rest/search/v1/queries/-home-/mds_oeh/wlo_collection?contentType=FILES&maxItems=8&skipCount=0&sortProperties=cm%3Amodified&sortAscending=false&propertyFilter=-all-';
$body = '{
  "criteria": [
    {
      "property": "collection",
      "values": [
        "' . $collectionID . '"
      ]
    }
  ],
  "facets": [
  ]
}';
$subCollectionsElements = callWloRestApi($url, 'POST', $body);

$contentArray = array();
if (!empty($subCollectionsElements->nodes)) {
    foreach ($subCollectionsElements->nodes as $reference) {
        $prop = $reference->properties;

        //check if deleted
        //TODO: is this still relevant?
        //if (empty($reference->originalId)) {
        //    continue;
        //}

        $title = $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0];
        foreach ($contentArray as $content) {
            if ($content['title'] == $title) {
                continue 2;
            }
        }

        $contentArray[] = processEduSharingNode($reference);
    } //end foreach
}

?>

<div class="wlo-accordion-wrapper" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
    <?php
    if (!empty($contentArray)) { ?>
        <button class="wlo-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
            <h2>Die neusten geprüften Inhalte im gesamten Themenbereich <?php echo get_the_title($postID); ?></h2>
            <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg" alt="Inhalte ein odder ausklappen">
        </button>

        <div class="wlo-accordion-content" id="<?php echo $sliderId; ?>">

            <?php
            foreach (array_slice($contentArray, 0, get_field('content_count')) as $content) {
                printWloCard($content);
            }
            ?>
        </div>
    <?php } ?>

    <div class="wlo-accordion-bottom"></div>
</div>

<?php initSlick($sliderId, $slidesToShow, $slidesToScroll,  count($contentArray) + 1) ?>

<script>
    jQuery(window).on('resize', function() {
        jQuery('#<?php echo $sliderId ?>').slick('refresh');
    });

    jQuery('#fachportal-accordion-<?php echo $accordionID; ?>').click(function() {
        jQuery(this).find("img").toggleClass("fachportal-accordion-icon-active");
        jQuery('#<?php echo $sliderId; ?>').slideToggle('slow');
        jQuery('#<?php echo $sliderId ?>').slick('refresh');
    });
</script>