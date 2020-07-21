<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Themenbaum</div>';
}; ?>
<?php

if (!function_exists('helper_useLightColor')) {
    function helper_useLightColor($bgColor)
    {
        $color = ($bgColor{0} === '#') ? substr($bgColor, 1, 7) : $bgColor;
        $r = intval(substr($color, 0, 2), 16); // hexToR
        $g = intval(substr($color, 2, 4), 16); // hexToG
        $b = intval(substr($color, 4, 6), 16); // hexToB
        return ((($r * 0.299) + ($g * 0.587) + ($b * 0.114)) > 186) ?
            false : true;
    }

    ;
}

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

$url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/local/' . $matches[1][0] . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true&';

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

?>
<div class="portal-subject-tree">

    <?php
    if (!empty($response->collections)) {
        echo '<h3>' . ((!empty(get_field('headline'))) ? get_field('headline') : 'Sammlungen') . '</h3>';
        echo (!empty(get_field('text'))) ? '<p>' . get_field('text') . '</p>' : '';

        if (get_field('next_level')) {
            //Sub-Level
            ?>
            <div class="portal_subject_grid">
                <?php
                foreach ($response->collections as $collection) {
                    if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated') {
                        $prop = $collection->properties;

                        // Filter Discipline
                        $propDisciplines = $prop->{'ccm:taxonid'};
                        $propDisciplines = (!empty($propDisciplines)) ? array_filter($propDisciplines) : [];
                        $propDisciplines = (!empty($propDisciplines)) ? trim_https_http_from_array($propDisciplines) : [];

                        $disciplinesVocab = (!empty(array_filter($disciplines))) ? array_map("map_vocab_disciplines_value_only", $disciplines) : [];
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

                        $educationalContextsVocab = (!empty(array_filter($educationalContexts))) ? array_map("map_vocab_educationalContexts_value_only", $educationalContexts) : [];
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

                        $intendedEndUserRolesVocab = (!empty(array_filter($intendedEndUserRoles))) ? array_map("map_vocab_disciplines_value_only", $intendedEndUserRoles) : [];
                        $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? array_filter($intendedEndUserRolesVocab) : [];
                        $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? trim_https_http_from_array($intendedEndUserRolesVocab) : [];

                        $filterIntendedEndUserRole = (empty($intendedEndUserRolesVocab) || empty($propIntendedEndUserRoles)) ? false : empty(array_intersect($propIntendedEndUserRoles, $intendedEndUserRolesVocab));

                        if ($filterIntendedEndUserRole) {
                            continue;
                        }

                        $nodeId = $collection->ref->id;
                        ?>
                        <ul>
                            <li>
                                <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>"><h4><span
                                                class='material-icons'>expand_more</span><?php echo $collection->title; ?>
                                    </h4></a>


                                <?php

                                $url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/local/' . $nodeId . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true&';

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
                                ?>
                                <ul>
                                    <?php
                                    foreach ($response->collections as $collection) {
                                        if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated') {
                                            $prop = $collection->properties;

                                            // Filter Discipline
                                            $propDisciplines = $prop->{'ccm:taxonid'};
                                            $propDisciplines = (!empty($propDisciplines)) ? array_filter($propDisciplines) : [];
                                            $propDisciplines = (!empty($propDisciplines)) ? trim_https_http_from_array($propDisciplines) : [];

                                            $disciplinesVocab = (!empty(array_filter($disciplines))) ? array_map("map_vocab_disciplines_value_only", $disciplines) : [];
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

                                            $educationalContextsVocab = (!empty(array_filter($educationalContexts))) ? array_map("map_vocab_educationalContexts_value_only", $educationalContexts) : [];
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

                                            $intendedEndUserRolesVocab = (!empty(array_filter($intendedEndUserRoles))) ? array_map("map_vocab_disciplines_value_only", $intendedEndUserRoles) : [];
                                            $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? array_filter($intendedEndUserRolesVocab) : [];
                                            $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? trim_https_http_from_array($intendedEndUserRolesVocab) : [];

                                            $filterIntendedEndUserRole = (empty($intendedEndUserRolesVocab) || empty($propIntendedEndUserRoles)) ? false : empty(array_intersect($propIntendedEndUserRoles, $intendedEndUserRolesVocab));

                                            if ($filterIntendedEndUserRole) {
                                                continue;
                                            }
                                            ?>
                                            <li>
                                                <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>">
                                                    <h5>
                                                        <span class='material-icons'>chevron_right</span><?php echo $collection->title; ?>
                                                    </h5></a>
                                            </li>
                                        <?php }
                                    } ?>
                                </ul>
                            </li>
                        </ul>
                        <?php
                    }
                } ?>
            </div>
            <?php
        } else {
            //Top-Level
            ?>
            <div class="portal_subject_grid">
                <?php
                foreach ($response->collections as $collection) {
                    if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated') {
                        $prop = $collection->properties;

                        // Filter Discipline
                        $propDisciplines = $prop->{'ccm:taxonid'};
                        $propDisciplines = (!empty($propDisciplines)) ? array_filter($propDisciplines) : [];
                        $propDisciplines = (!empty($propDisciplines)) ? trim_https_http_from_array($propDisciplines) : [];

                        $disciplinesVocab = (!empty(array_filter($disciplines))) ? array_map("map_vocab_disciplines_value_only", $disciplines) : [];
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

                        $educationalContextsVocab = (!empty(array_filter($educationalContexts))) ? array_map("map_vocab_educationalContexts_value_only", $educationalContexts) : [];
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

                        $intendedEndUserRolesVocab = (!empty(array_filter($intendedEndUserRoles))) ? array_map("map_vocab_disciplines_value_only", $intendedEndUserRoles) : [];
                        $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? array_filter($intendedEndUserRolesVocab) : [];
                        $intendedEndUserRolesVocab = (!empty($intendedEndUserRolesVocab)) ? trim_https_http_from_array($intendedEndUserRolesVocab) : [];

                        $filterIntendedEndUserRole = (empty($intendedEndUserRolesVocab) || empty($propIntendedEndUserRoles)) ? false : empty(array_intersect($propIntendedEndUserRoles, $intendedEndUserRolesVocab));

                        if ($filterIntendedEndUserRole) {
                            continue;
                        }

                        $bgColor = $prop->{'ccm:collectioncolor'}[0];
                        $fontColor = (!empty($bgColor) && helper_useLightColor($bgColor)) ? "#000000" : "#ffffff";

                        ?>
                        <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>">
                            <div class="portal_tree_branch" style="
                                    background: <?php echo $bgColor ?>;
                                    color: <?php echo $fontColor ?>;
                                    ">
                                <img width="70" height="50" src="<?php echo $collection->preview->url; ?>">
                                <div class="portal_search_text">
                                    <h5><?php echo $collection->title; ?></h5>
                                </div>
                            </div>
                        </a>
                    <?php }
                } ?>
            </div>
            <?php
        }
    }
    ?>
</div>

<?php if (is_admin()) {
    echo '</div>';
}; ?>
