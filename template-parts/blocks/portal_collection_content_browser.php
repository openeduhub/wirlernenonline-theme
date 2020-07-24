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
/* ------------------------------------------------------------------- */

$block_var_objectTypes = (!empty(get_field('objectTypes'))) ? get_field('objectTypes') : [];
$objectTypes = (!empty($block_var_objectTypes)) ? array_column($block_var_objectTypes, 'value') : [];
$objectTypes = (!empty($objectTypes)) ? $objectTypes : get_post_meta($postID, 'objectTypes', false)[0];

$block_var_learningResourceTypes = (!empty(get_field('learningResourceTypes'))) ? get_field('learningResourceTypes') : [];
$learningResourceTypes = (!empty($block_var_learningResourceTypes)) ? array_column($block_var_learningResourceTypes, 'value') : [];
$learningResourceTypes = (!empty($learningResourceTypes)) ? $learningResourceTypes : get_post_meta($postID, 'learningResourceTypes', false)[0];

$block_var_generalKeywords = get_field('generalKeyword');
$generalKeywords = (!empty($block_var_generalKeywords)) ? $block_var_generalKeywords : get_post_meta($postID, 'generalKeyword', false)[0];
$generalKeywords = (!empty($generalKeywords)) ? explode(",", $generalKeywords) : [];

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);

$url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/-home-/' . $matches[1][0] . '/children/references';

try {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8'
        )
    );
    $response = curl_exec($curl);
    if ($response === false) {
        echo 'curl error';
        return false;
    }
} catch (Exception $e) {
    echo 'curl error: ' . $e->getMessage();
    return false;
}
curl_close($curl);

$response = json_decode($response);

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
    echo '<h3>' . ((!empty(get_field('headline'))) ? get_field('headline') : 'Materialien') . '</h3>';
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
                    <div class="portal_content_grid">
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
                    $propObjectType = $prop->{'ccm:objecttype'};
                    if ($propObjectType &&
                        !empty($propObjectType) &&
                        !empty($objectTypes) &&
                        !in_array($propObjectType, $objectTypes)) {

                        //echo '<pre style="background-color: lightgrey">' , var_dump("OType") , '</pre>';
                        continue;
                    }

                    // Filter LearningResourceType
                    $propLearningResourceTypes = $prop->{'ccm:educationallearningresourcetype'};
                    $propLearningResourceTypes = (!empty($propLearningResourceTypes)) ? array_filter($propLearningResourceTypes) : [];
                    $propLearningResourceTypes = (!empty($propLearningResourceTypes)) ? trim_https_http_from_array($propLearningResourceTypes) : [];

                    $learningResourceTypesVocab = (!empty($learningResourceTypes) && !empty(array_filter($learningResourceTypes))) ? array_map("map_vocab_learning_resource_types_value_only", $learningResourceTypes) : [];
                    $learningResourceTypesVocab = (!empty($learningResourceTypesVocab)) ? array_filter($learningResourceTypesVocab) : [];
                    $learningResourceTypesVocab = (!empty($learningResourceTypesVocab)) ? trim_https_http_from_array($learningResourceTypesVocab) : [];

                    $filterLearningResourceTypes = (empty($propLearningResourceTypes)) ? true : empty(array_intersect($propLearningResourceTypes, $learningResourceTypesVocab));

                    if (!empty($learningResourceTypesVocab) && $filterLearningResourceTypes) {
                        //echo '<pre style="background-color: lightgrey">' , var_dump("LRT") , '</pre>';
                        continue;
                    }

                    // Filter General Keyword
                    $propGeneralKeywords = $prop->{'cclom:general_keyword'};
                    $propGeneralKeywords = (!empty($propGeneralKeywords)) ? array_filter($propGeneralKeywords) : [];

                    $filterGeneralKeywords = (empty($propGeneralKeywords)) ? true : empty(array_intersect($generalKeywords, $propGeneralKeywords));

                    if (!empty($generalKeywords) && $filterGeneralKeywords) {
                        //echo '<pre style="background-color: lightgrey">' , var_dump("Keyword") , '</pre>';
                        continue;
                    }

                    ?>
                    <a href="<?php echo $reference->content->url; ?>" target="_blank">
                        <div class="portal_content_branch">
                            <h5 class="media-type"><?php echo $mediaTypes[$reference->mediatype] ?></h5>
                            <?php if (!empty($reference->preview->url)) { ?><img
                                src="<?php echo $reference->preview->url; ?>"><?php }; ?>
                            <div class="portal_search_text">
                                <h5><?php echo ($reference->properties->{'cclom:title'}[0]) ? $reference->properties->{'cclom:title'}[0] : $reference->properties->{'cm:name'}[0]; ?></h5>&nbsp;&nbsp;
                                <h5 class="media-type"><?php echo $mediaTypes[$reference->mediatype] ?></h5>
                            </div>
                        </div>
                    </a>
                    <?php
                } ?>
            </div>
        </div>
        <?php
    } else {
        ?>
        <h6 class="primary">Leider gibt es in dieser Sammlung noch keine Materialien. <a
                    href="<?php echo get_permalink(get_page_by_path('tool-hinzufuegen')) ?>">Hilf' uns dabei</a>, hier
            mehr Informationen und Materialien zusammenzutragen.</h6>
        <?php
    }
    ?>
</div>
<?php if (is_admin()) {
    echo '</div>';
}; ?>
