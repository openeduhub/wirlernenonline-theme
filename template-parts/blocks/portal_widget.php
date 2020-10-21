<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Widget</div>';
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

$pageTitle = get_the_title($postID);
$pageDiscipline = get_field('discipline', $postID)[0]['label'];

/* ------------------------------------------------------------------- */

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);

$collectionID = $matches[1][0];

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

$headline = 'Materialien';
if(!empty(get_field('headline'))){
    $headline = get_field('headline');
}else if (!empty(get_field('oehWidgets')[0]['label'])){
    $headline = get_field('oehWidgets')[0]['label'];
}


$contentArray = array();
foreach ($response->references as $reference) {
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
    $propGeneralKeywords = $prop->{'cclom:general_keyword'};
    $propGeneralKeywords = (!empty($propGeneralKeywords)) ? array_filter($propGeneralKeywords) : [];

    $filterGeneralKeywords = (empty($propGeneralKeywords)) ? true : empty(@array_intersect($generalKeywords, $propGeneralKeywords));

    if (!empty($generalKeywords) && $filterGeneralKeywords) {
        //echo '<pre style="background-color: lightgrey">' , var_dump("Keyword") , '</pre>';
        continue;
    }

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

    $oerLicenses = array(
        'CC_0',
        'CC_BY',
        'CC_BY_SA',
        'PDM',
    );
    $nodeLicense = $prop->{'ccm:commonlicense_key'}[0];
    $isOER = false;
    foreach ($oerLicenses as $license){
        if( $nodeLicense == $license){
            $isOER = true;
        }
    }

    // check for duplicates
    if (!empty($GLOBALS['wlo_widget_duplicates'])){
        if (in_array($reference->ref->id, $GLOBALS['wlo_widget_duplicates'] )){
            continue;
        }
    }

    $GLOBALS['wlo_widget_duplicates'][] = $reference->ref->id;



    $contentArray[] = array(
            'id' => $reference->ref->id,
            'mediatype' => $mediaTypes[$reference->mediatype],
            'image_url' => $reference->preview->url,
            'content_url' => $reference->properties->{'ccm:wwwurl'}[0] ? $reference->properties->{'ccm:wwwurl'}[0] : $reference->content->url,
            'title' => $reference->properties->{'cclom:title'}[0] ? $reference->properties->{'cclom:title'}[0] : $reference->properties->{'cm:name'}[0],
            'description' => !empty($reference->properties->{'cclom:general_description'}) ? (implode("\n", $reference->properties->{'cclom:general_description'})) : '',
            'source' => !empty($reference->properties->{'ccm:metadatacontributer_creatorFN'}) ? (implode("\n", $reference->properties->{'cclom:general_description'})) : '',
            'oer' => $isOER
    );
} //end foreach


if (is_admin()) {
    echo '<h4>' . $headline . '</h4>';
}

if (!empty($contentArray)){
    foreach ($contentArray as $content) { ?>
        <div class="portal_content_branch widget_content" id="<?php echo $content['id']; ?>">
            <div class="media-type"><?php echo $headline; ?></div>
            <img class="widget-badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge.png">
            <?php if ($content['oer']){ ?>
                <img class="widget-badge oer" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/oer.png">
            <?php } ?>
            <?php if (!empty($content['image_url'])) { ?>
                <img src="<?php echo $content['image_url']; ?> alt="">
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
<?php }
} ?>

<div class="portal_widget">
    <div class="portal_content_branch portal_content_branch_empty">
        <div class="headline"><?php echo $headline; ?></div>
        <div class="portal_content_image">
            <a href="<?php echo get_page_link($addContentPageID) . '?collectionID=' . $collectionID . '&oehWidgets=' . $oehWidgets[0] . '&headline=' . $headline . '&pageTitle=' . $pageTitle .'&pageDiscipline=' . $pageDiscipline; ?>" target="_blank">
                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/plus-big.jpg" alt="">
            </a>
        </div>
        <div class="portal_content_info">
            <div class="portal_search_text">
                <h6>Du hast einen Inhalt, der gut hierzu passen würde?</h6>
            </div>
            <div class="portal_search_button">
                <a class="button primary small wlo-red"
                   href="<?php echo get_page_link($addContentPageID) . '?collectionID=' . $collectionID . '&oehWidgets=' . $oehWidgets[0] . '&headline=' . $headline . '&pageTitle=' . $pageTitle .'&pageDiscipline=' . $pageDiscipline; ?>"
                   target="_blank">
                    Schlag den ersten Inhalt vor
                </a>
            </div>
        </div>
    </div>
</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>
