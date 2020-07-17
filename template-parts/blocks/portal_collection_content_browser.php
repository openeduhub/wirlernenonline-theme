<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Inhalt</div>';
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

if (!empty($response->references)) { ?>
    <div class="portal-collection-content-browser">
        <?php
            echo '<h3>' . ((!empty(get_field('headline'))) ? get_field('headline') : 'Materialien') . '</h3>';
            echo (!empty(get_field('text'))) ? '<p>' . get_field('text') . '</p>' : '';
        ?>
        <div class="portal_content_grid">
            <?php
            foreach ($response->references as $reference) {
                $prop = $reference->properties;

                // Filter Discipline
                $propDisciplines = $prop->{'ccm:taxonid'};
                $propDisciplines = (!empty($propDisciplines)) ? array_filter($propDisciplines) : [];
                $propDisciplines = (!empty($propDisciplines)) ? trim_https_http_from_array($propDisciplines) : [];

                $disciplinesVocab = (!empty($disciplines)) ? array_map("map_vocab_disciplines_value_only", $disciplines) : [];
                $disciplinesVocab = (!empty($disciplinesVocab)) ? array_filter($disciplinesVocab) : [];
                $disciplinesVocab = (!empty($disciplinesVocab)) ? trim_https_http_from_array($disciplinesVocab) : [];

                $filterDiscipline = (empty($disciplinesVocab) || empty($propDisciplines)) ? false : empty(array_intersect($propDisciplines, $disciplinesVocab));

                if ($filterDiscipline) {
                    continue;
                }

                // Filter EducationalContext
                $propEducationalContexts = $prop->{'ccm:educationalcontext'};
                $propEducationalContexts = (!empty($propEducationalContexts)) ? array_filter($propEducationalContexts) : [];
                $propEducationalContexts = (!empty($propEducationalContexts)) ? trim_https_http_from_array($propEducationalContexts) : [];

                $educationalContextsVocab = (!empty($educationalContexts)) ? array_map("map_vocab_educationalContexts_value_only", $educationalContexts) : [];
                $educationalContextsVocab = (!empty($educationalContextsVocab)) ? array_filter($educationalContextsVocab) : [];
                $educationalContextsVocab = (!empty($educationalContextsVocab)) ? trim_https_http_from_array($educationalContextsVocab) : [];

                $filterEducationalContext = (empty($educationalContextsVocab) || empty($propEducationalContexts)) ? false : empty(array_intersect($propEducationalContexts, $educationalContextsVocab));

                if ($filterEducationalContext) {
                    continue;
                }

                // Filter IntendedEndUserRole
                $propIntendedEndUserRoles = $prop->{'ccm:educationalintendedenduserrole'};
                $propIntendedEndUserRoles = (!empty($propIntendedEndUserRoles)) ? array_filter($propIntendedEndUserRoles) : [];
                $propIntendedEndUserRoles = (!empty($propIntendedEndUserRoles)) ? trim_https_http_from_array($propIntendedEndUserRoles) : [];

                $intendedEndUserRolesVocab = (!empty($intendedEndUserRoles)) ? array_map("map_vocab_disciplines_value_only", $intendedEndUserRoles) : [];
                $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? array_filter($intendedEndUserRolesVocab) : [];
                $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? trim_https_http_from_array($intendedEndUserRolesVocab) : [];

                $filterIntendedEndUserRole = (empty($intendedEndUserRolesVocab) || empty($propIntendedEndUserRoles)) ? false : empty(array_intersect($propIntendedEndUserRoles, $intendedEndUserRolesVocab));

                if ($filterIntendedEndUserRole) {
                    continue;
                }
                ?>
                <a href="<?php echo $reference->content->url; ?>" target="_blank">
                    <div class="portal_content_branch">
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
}
?>
<?php if (is_admin()) {
    echo '</div>';
}; ?>
