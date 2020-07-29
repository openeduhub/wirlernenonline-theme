<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Sammlungsinhalte</div>';
}; ?>
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

/* ------------------------------------------------------------------- */

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);

$url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/-home-/' . $matches[1][0] . '/children/references';
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
?>
<div class="portal_block">

    <?php
    echo '<h4>' . ((!empty(get_field('headline'))) ? get_field('headline') : 'Materialien') . '</h4>';
    echo (!empty(get_field('text'))) ? '<p>' . get_field('text') . '</p>' : '';

    if (!empty($response->references)) { ?>
        <div class="portal-collection-content-browser">
                <?php
                if (get_field('layout') == 'list') {
                    ?>
                    <div class="portal_content_list">
                    <?php
                }
                else if (get_field('layout') == 'grid') {
                    ?>
                    <div class="portal_content_grid <?php echo (get_field('horizontal_scroll') == true) ? 'x-scroll' : ''?>">
                    <?php
                }

                ?>
                <?php
                foreach ($response->references as $reference) {
                    $prop = $reference->properties;

                    // Filter Discipline
                    $propDisciplines = $prop->{'ccm:taxonid'};
                    $propDisciplines = (!empty($propDisciplines)) ? array_filter($propDisciplines) : [];
                    $propDisciplines = (!empty($propDisciplines)) ? trim_https_http_from_array($propDisciplines) : [];

                    $disciplinesVocab = (!empty(array_filter($disciplines))) ? array_map("map_vocab_disciplines_value_only", $disciplines) : [];
                    $disciplinesVocab = (!empty($disciplinesVocab)) ? array_filter($disciplinesVocab) : [];
                    $disciplinesVocab = (!empty($disciplinesVocab)) ? trim_https_http_from_array($disciplinesVocab) : [];

                    $filterDiscipline = (empty($propDisciplines)) ? true : empty(array_intersect($propDisciplines, $disciplinesVocab));

                    if (!empty($disciplinesVocab) && $filterDiscipline) {

                        //echo '<pre style="background-color: lightgrey">' , var_dump("Discipline") , '</pre>';
                        continue;
                    }

                    // Filter EducationalContext
                    $propEducationalContexts = $prop->{'ccm:educationalcontext'};
                    $propEducationalContexts = (!empty($propEducationalContexts)) ? array_filter($propEducationalContexts) : [];
                    $propEducationalContexts = (!empty($propEducationalContexts)) ? trim_https_http_from_array($propEducationalContexts) : [];

                    $educationalContextsVocab = (!empty(array_filter($educationalContexts))) ? array_map("map_vocab_educationalContexts_value_only", $educationalContexts) : [];
                    $educationalContextsVocab = (!empty($educationalContextsVocab)) ? array_filter($educationalContextsVocab) : [];
                    $educationalContextsVocab = (!empty($educationalContextsVocab)) ? trim_https_http_from_array($educationalContextsVocab) : [];

                    $filterEducationalContext = (empty($propEducationalContexts)) ? true : empty(array_intersect($propEducationalContexts, $educationalContextsVocab));

                    if (!empty($educationalContextsVocab) && $filterEducationalContext) {

                        //echo '<pre style="background-color: lightgrey">' , var_dump("EduContext") , '</pre>';
                        continue;
                    }

                    // Filter IntendedEndUserRole
                    $propIntendedEndUserRoles = $prop->{'ccm:educationalintendedenduserrole'};
                    $propIntendedEndUserRoles = (!empty($propIntendedEndUserRoles)) ? array_filter($propIntendedEndUserRoles) : [];
                    $propIntendedEndUserRoles = (!empty($propIntendedEndUserRoles)) ? trim_https_http_from_array($propIntendedEndUserRoles) : [];

                    $intendedEndUserRolesVocab = (!empty(array_filter($intendedEndUserRoles))) ? array_map("map_vocab_disciplines_value_only", $intendedEndUserRoles) : [];
                    $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? array_filter($intendedEndUserRolesVocab) : [];
                    $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? trim_https_http_from_array($intendedEndUserRolesVocab) : [];

                    $filterIntendedEndUserRole = (empty($propIntendedEndUserRoles)) ? true : empty(array_intersect($propIntendedEndUserRoles, $intendedEndUserRolesVocab));

                    if (!empty($intendedEndUserRolesVocab) && $filterIntendedEndUserRole) {

                        //echo '<pre style="background-color: lightgrey">' , var_dump("Role") , '</pre>';
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

                    $filterGeneralKeywords = (empty($propGeneralKeywords)) ? true : empty(array_intersect($generalKeywords, $propGeneralKeywords));

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

                    ?>
                    <div class="portal_content_branch">
                        <h5 class="media-type"><?php echo $mediaTypes[$reference->mediatype] ?></h5>
                        <?php if (!empty($reference->preview->url)) { ?><img
                            src="<?php echo $reference->preview->url; ?>"><?php }; ?>
                        <div class="portal_search_text">
                            <a href="<?php echo $reference->content->url; ?>" target="_blank"><h6><?php echo ($reference->properties->{'cclom:title'}[0]) ? $reference->properties->{'cclom:title'}[0] : $reference->properties->{'cm:name'}[0]; ?></h6></a>&nbsp;&nbsp;
                            <h6 class="media-type"><?php echo $mediaTypes[$reference->mediatype] ?></h6>
                            <p><?php echo (!empty($reference->properties->{'cclom:general_description'})) ? (implode("\n", $reference->properties->{'cclom:general_description'})) : '' ?></p>
                        </div>
                        <div class="portal_search_button">
                            <a class="button primary small" href="<?php echo $reference->content->url; ?>" target="_blank"><?php echo $mediaTypes[$reference->mediatype]?> öffnen</a>
                            <div class="portal_search_source">Quelle: <?php echo (!empty($reference->properties->{'ccm:metadatacontributer_creatorFN'})) ? (implode(", ", $reference->properties->{'ccm:metadatacontributer_creatorFN'})) : '';?></div>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
        <?php
    } else {
        ?>
        <p class="primary">Leider gibt es in dieser Sammlung noch keine Materialien. <a
                    href="<?php echo get_permalink(get_page_by_path('tool-hinzufuegen')) ?>">Hilf uns dabei</a>, hier
            mehr Informationen und Materialien zusammenzutragen.</p>
        <?php
    }
    ?>
</div>
<?php if (is_admin()) {
    echo '</div>';
}; ?>
