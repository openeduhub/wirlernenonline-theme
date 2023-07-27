<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Sammlungsinhalte</div>';
} ?>
<?php

/* Filter Values prioritized GET/POST > Block-Setting > Portal-Setting */
/*
 * collectionUrl
 * disciplines
 * educationalContexts
 * intendedEndUserRoles
 * oer
 * objectTypes
 * learningResourceTypes
 * generalKeyword
 * oehWidgets
*/
/* ------------------------------------------------------------------- */
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

// echo '<pre style="background-color: lightgrey">' , var_dump($educational_filter_values) , '</pre>';
// echo '<script>console.log(' , json_encode($educational_filter_values) , ')</script>';

$collectionUrl = $educational_filter_values["collectionUrl"];
$disciplines = $educational_filter_values["disciplines"];
$educationalContexts = $educational_filter_values["educationalContexts"];
$intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
$oer = $educational_filter_values["oer"];
$objectTypes = $educational_filter_values["objectTypes"];
$learningResourceTypes = $educational_filter_values["learningResourceTypes"];
$generalKeywords = $educational_filter_values["generalKeyword"];
$oehWidgets = $educational_filter_values["oehWidgets"];

//$addContentPageID = 9614; //dev
$addContentPageID = 9933; //pre
//$addContentPageID = 9081; //local

/* ------------------------------------------------------------------- */

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);

$collectionID = $matches[1][0];
$collectionLevel = get_field('collection_level', $postID);

$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $matches[1][0] . '/children/references';
$response = callWloRestApi($url);

$mediaTypes = array(
    "file-image" => "Bild",
    "file-word" => "Word-Dokument",
    "file-excel" => "Tabelle",
    "file-powerpoint" => "Präsentation",
    "file-odt" => "OpenOffice Dokument",
    "file-ods" => "OpenOffice Tabelle",
    "file-odp" => "OpenOffice Präsentation",
    "file-zip" => "Zip-Datei",
    "file-script" => "Skript",
    "file-xml" => "XML-Datei",
    "file-audio" => "Audio",
    "file-txt" => "Textdatei",
    "file-video" => "Video",
    "file-qti" => "ONYX-Datei",
    "file-h5p" => "H5P-Datei",
    "file-pdf" => "PDF-Datei",
    "link" => "Website",
    "folder" => "Ordner",
    "file" => "Unbekannt",
    "collection" => "Sammlung",
    "saved_search" => "Suche"
);

$headline = ((!empty(get_field('headline'))) ? get_field('headline') : 'Materialien');

?>

<div class="portal_collection_content_block">
<?php if (!empty($block['anchor'])) {
    echo '<a name="' . $block['anchor'] . '"></a>';
}
echo '<div class="portal_collection_content_header">';
echo '<div class="headline big">' . $headline . '</div>';
if (!empty(get_field('text'))) {
    echo '<p>' . get_field('text') . '</p>';
}
echo '</div>';


$layout = get_field('layout');
$maxResults = get_field('maxResults');
$slidesToShow = 1;
$slidesToScroll = 1;
$countContent = 0;

$contentArray = array();
foreach ($response->references as $reference) {
    //var_dump($reference);
    $prop = $reference->properties;

    //check if deleted
    if($reference->originalId == null){
        continue;
    }

    if (!checkPropertyMatch($prop->{'ccm:taxonid'}, $disciplines, true)) {
        continue;
    }
    if (!checkPropertyMatch($prop->{'ccm:educationalcontext'}, $educationalContexts, true)) {
        continue;
    }
    if (!checkPropertyMatch($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, true)) {
        continue;
    }

    // Filter ObjectType
    $propObjectType = $prop->{'ccm:objectType'};
    if ($propObjectType &&
        !empty($propObjectType) &&
        !empty($objectTypes) &&
        !in_array($propObjectType, $objectTypes)) {

        //echo '<pre style="background-color: lightgrey">' , var_dump("OType") , '</pre>';
        continue;
    }

    $displayObjectType = (!empty($objectTypes)) ? array_intersect($propObjectType, $objectTypes)[0] : $propObjectType[0];


    // Filter LearningResourceType
    $propLearningResourceTypes = $prop->{'ccm:educationallearningresourcetype'};
    $propLearningResourceTypes = (!empty($propLearningResourceTypes)) ? array_filter($propLearningResourceTypes) : [];
    $propLearningResourceTypes = (!empty($propLearningResourceTypes)) ? trim_https_http_from_array($propLearningResourceTypes) : [];

    $learningResourceTypesVocab = (!empty($learningResourceTypes) && !empty(array_filter($learningResourceTypes))) ? array_map("map_vocab_learning_resource_types_value_only", $learningResourceTypes) : [];
    $learningResourceTypesVocab = (!empty($learningResourceTypesVocab)) ? array_filter($learningResourceTypesVocab) : [];
    $learningResourceTypesVocab = (!empty($learningResourceTypesVocab)) ? trim_https_http_from_array($learningResourceTypesVocab) : [];

    $intersectLearningResourceType = array_intersect($propLearningResourceTypes, $learningResourceTypesVocab);
    $filterLearningResourceTypes = (empty($propLearningResourceTypes)) ? true : empty($intersectLearningResourceType);
    if (!empty($learningResourceTypesVocab) && $filterLearningResourceTypes) {
        //echo '<pre style="background-color: lightgrey">' , var_dump("LRT") , '</pre>';
        continue;
    }

    $displayLearningResourceType = (!empty($intersectLearningResourceType)) ? $intersectLearningResourceType[0] : $propLearningResourceTypes[0];
    $displayLearningResourceType = (!empty($displayLearningResourceType)) ? $displayLearningResourceType : 'Inhalt';

    // Filter General Keyword
    /*$propGeneralKeywords = $prop->{'cclom:general_keyword'};
    $propGeneralKeywords = (!empty($propGeneralKeywords)) ? array_filter($propGeneralKeywords) : [];

    error_log('$generalKeywords: '.print_r($generalKeywords, true));
    error_log('$propGeneralKeywords: '.print_r($propGeneralKeywords, true));
    $filterGeneralKeywords = (empty($propGeneralKeywords)) ? true : empty(@array_intersect($generalKeywords, $propGeneralKeywords));

    if (!empty($generalKeywords) && $filterGeneralKeywords) {
        //echo '<pre style="background-color: lightgrey">' , var_dump("Keyword") , '</pre>';
        continue;
    }*/

    // Filter LearningResourceType
    $propOehWidgets = $prop->{'ccm:oeh_widgets'};
    $propOehWidgets = (!empty($propOehWidgets)) ? array_filter($propOehWidgets) : [];
    $propOehWidgets = (!empty($propOehWidgets)) ? trim_https_http_from_array($propOehWidgets) : [];

    $oehWidgetsVocab = (!empty($oehWidgets) && !empty(array_filter($oehWidgets))) ? array_map("map_vocab_oeh_widgets_value_only", $oehWidgets) : [];
    $oehWidgetsVocab = (!empty($oehWidgetsVocab)) ? array_filter($oehWidgetsVocab) : [];
    $oehWidgetsVocab = (!empty($oehWidgetsVocab)) ? trim_https_http_from_array($oehWidgetsVocab) : [];

    $intersectOehWidgets = array_intersect($propOehWidgets, $oehWidgetsVocab);
    $filterOehWidgets = (empty($propOehWidgets)) ? true : empty($intersectOehWidgets);

    if (!empty($oehWidgetsVocab) && $filterOehWidgets) {
        //echo '<pre style="background-color: lightgrey">' , var_dump("LRT") , '</pre>';
        continue;
    }

    $displayOehWidgets = (!empty($intersectOehWidgets)) ? $intersectOehWidgets[0] : $propOehWidgets[0];
    $displayOehWidgets = (!empty($displayOehWidgets)) ? $displayOehWidgets : 'Inhalt';

    $contentArray[] = array(
        'mediatype' => $mediaTypes[$reference->mediatype],
        'image_url' => $reference->preview->url,
        //'content_url' => $reference->content->url,
        'content_url' => $reference->properties->{'ccm:wwwurl'}[0] ? $reference->properties->{'ccm:wwwurl'}[0] : $reference->content->url,
        'title' => $reference->properties->{'cclom:title'}[0] ? $reference->properties->{'cclom:title'}[0] : $reference->properties->{'cm:name'}[0],
        'description' => !empty($reference->properties->{'cclom:general_description'}) ? (implode("\n", $reference->properties->{'cclom:general_description'})) : '',
        'source' => !empty($reference->properties->{'ccm:metadatacontributer_creatorFN'}) ? (implode("\n", $reference->properties->{'cclom:general_description'})) : '',
    );
} //end foreach


if (!empty($maxResults)){
    $contentArray = array_slice($contentArray, 0, $maxResults );
}
if (!empty($contentArray)){
    $contentCount = count($contentArray);
?>
<div class="portal-collection-content-browser">
    <?php if ($layout == 'list') { ?>
        <div class="portal_content_list">
    <?php }else if ($layout == 'grid') { ?>
        <div class="portal_content_grid <?php echo (get_field('horizontal_scroll') == true) ? 'x-scroll' : '' ?>">
    <?php }else if ($layout == 'slider'){
        $sliderId = uniqid('slider-');
        if (get_field('slidesToShow')) {
            $slidesToShow = get_field('slidesToShow');
        }
        if (get_field('slidesToScroll')) {
            $slidesToScroll = get_field('slidesToScroll');
        } ?>
        <div class="portal_content_slider" id="<?php echo $sliderId; ?>">
    <?php }

        foreach ($contentArray as $content) { ?>

            <div class="portal_content_branch">
                <div class="media-type"><?php echo $content['mediatype'] ?></div>
                <?php if (!empty($content['image_url'])) { ?>
                    <img src="<?php echo $content['image_url']; ?>&crop=true&maxWidth=400&maxHeight=400" alt="<?php echo $content['title']; ?>">
                <?php } ?>
                <div class="portal_content_info">
                    <div class="portal_search_text">
                        <a href="<?php echo $content['content_url']; ?>" target="_blank">
                            <h6><?php echo $content['title']; ?></h6></a>
                        <h6 class="media-type"><?php echo $content['mediatype'] ?></h6>
                        <p><?php echo $content['description'] ?></p>
                    </div>
                    <div class="portal_search_button">
                        <a class="button primary small" href="<?php echo $content['content_url']; ?>" target="_blank">
                            <?php echo $content['mediatype']; ?> öffnen
                            <span class="hidden_context">für <?php echo $content['title']; ?></span>
                        </a>
                    </div>
                </div>
            </div>


        <?php } ?>

        </div>
    </div>

        <script type="text/javascript">
            jQuery(function () {
                // Handler for .ready() called. Put the Slick Slider etc. init code here.
                function loadSearchSlider() {
                    if (typeof jQuery().slick === "function") {
                        jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                            infinite: false,
                            slidesToShow: <?php echo $slidesToShow; ?>,
                            slidesToScroll: <?php echo $slidesToScroll; ?>,
                            arrows: true,
                            dots: true,
                            zIndex: 0,
                            responsive: [
                                {
                                    breakpoint: 950,
                                    settings: {
                                        slidesToShow: 2,
                                        slidesToScroll: 2
                                    }
                                },
                                {
                                    breakpoint: 750,
                                    settings: {
                                        slidesToShow: 1,
                                        slidesToScroll: 1
                                    }
                                }
                            ]
                        });
                    }
                }

                loadSearchSlider();

                jQuery(window).on('resize', function(){
                    jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
                });
            });
        </script>


    <?php } ?>
</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>
