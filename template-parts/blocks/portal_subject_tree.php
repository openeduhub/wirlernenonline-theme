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

$backgroundColor = '#003E82';
if (get_field('background_color', $postID)) {
    $backgroundColor = get_field('background_color', $postID);
}

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

$url = WLO_REPO . 'rest/collection/v1/collections/local/' . $matches[1][0] . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true&fetchCounts=false';
$response = callWloRestApi($url);

?>
<div class="portal_block">
    <?php
    if (!empty($block['anchor'])) {
        echo '<a name="' . $block['anchor'] . '"></a>';
    }
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
                        if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated' || true) {
                            $prop = $collection->properties;
                            $ccm_location = str_replace('dev.wirlernenonline.de', 'wirlernenonline.de', $collection->properties->{'cclom:location'}[0]);

                            // Filter Disciplines
                            if (wlo_edu_filter($prop->{'ccm:taxonid'}, $disciplines, "map_vocab_disciplines_value_only")) {
                                continue;
                            }
                            // Filter EducationalContext
                            if (wlo_edu_filter($prop->{'ccm:educationalcontext'}, $educationalContexts, "map_vocab_educationalContexts_value_only")) {
                                continue;
                            }
                            // Filter IntendedEndUserRole
                            if (wlo_edu_filter($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, "map_vocab_intendedEndUserRoles_value_only")) {
                                continue;
                            }

                            $nodeId = $collection->ref->id;
                            $title = $collection->title;
                            if (!empty($prop->{'ccm:collectionshorttitle'}[0])){
                                $title = $prop->{'ccm:collectionshorttitle'}[0];
                            }
                            ?>

                                    <div class="portal_menu" style="background: <?php echo $backgroundColor ?>;">
                                        <a href="<?php echo $ccm_location; ?>">
                                            <h5><?php echo $title; ?></h5>
                                        </a>

                                        <?php
                                        $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $nodeId . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true&fetchCounts=false&';
                                        $response = callWloRestApi($url);

                                        if (!empty($response->collections)) :?>
                                        <div class="portal_menu_dropdown_button">
                                            <div class="portal_menu_icon">▼</div>
                                            <div class="portal_menu_dropdown" style="background: <?php echo $backgroundColor ?>;">
                                                <?php foreach ($response->collections as $collection) {
                                                    if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated') {
                                                        $prop = $collection->properties;
                                                        $ccm_location = str_replace('dev.wirlernenonline.de', 'wirlernenonline.de', $collection->properties->{'cclom:location'}[0]);

                                                        // Filter Disciplines
                                                        if (wlo_edu_filter($prop->{'ccm:taxonid'}, $disciplines, "map_vocab_disciplines_value_only")) {
                                                            continue;
                                                        }
                                                        // Filter EducationalContext
                                                        if (wlo_edu_filter($prop->{'ccm:educationalcontext'}, $educationalContexts, "map_vocab_educationalContexts_value_only")) {
                                                            continue;
                                                        }
                                                        // Filter IntendedEndUserRole
                                                        if (wlo_edu_filter($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, "map_vocab_intendedEndUserRoles_value_only")) {
                                                            continue;
                                                        }

                                                        $title = $collection->title;
                                                        if (!empty($prop->{'ccm:collectionshorttitle'}[0])){
                                                            $title = $prop->{'ccm:collectionshorttitle'}[0];
                                                        }
                                                        ?>
                                                        <a href="<?php echo $ccm_location; ?>">
                                                            <h6><?php echo $title; ?></h6>
                                                        </a>

                                                    <?php }
                                                } ?>

                                            </div>
                                        </div>

                                        <?php endif; ?>
                                    </div>

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
                        $ccm_location = str_replace('dev.wirlernenonline.de', 'wirlernenonline.de', $collection->properties->{'cclom:location'}[0]);
                        if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated') {
                            $prop = $collection->properties;

                            // Filter Disciplines
                            if (wlo_edu_filter($prop->{'ccm:taxonid'}, $disciplines, "map_vocab_disciplines_value_only")) {
                                continue;
                            }
                            // Filter EducationalContext
                            if (wlo_edu_filter($prop->{'ccm:educationalcontext'}, $educationalContexts, "map_vocab_educationalContexts_value_only")) {
                                continue;
                            }
                            // Filter IntendedEndUserRole
                            if (wlo_edu_filter($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, "map_vocab_intendedEndUserRoles_value_only")) {
                                continue;
                            }

                            $title = $collection->title;
                            if (!empty($prop->{'ccm:collectionshorttitle'}[0])){
                                $title = $prop->{'ccm:collectionshorttitle'}[0];
                            }

                            $bgColor = $prop->{'ccm:collectioncolor'}[0];
                            $fontColor = (!empty($bgColor) && helper_useLightColor($bgColor)) ? "#000000" : "#ffffff";

                            ?>
                            <a href="<?php echo $ccm_location; ?>">
                                <div class="portal_tree_branch" style="background: <?php echo $backgroundColor ?>;">
                                    <?php if (get_field('collection_icon')): ?>
                                        <img width="70" height="50" src="<?php echo $collection->preview->url; ?>">
                                    <?php endif; ?>
                                    <div class="portal_search_text">
                                        <h5 style="color: <?php echo $fontColor ?> !important;">
                                            <?php echo $title; ?>
                                        </h5>
                                    </div>
                                </div>
                            </a>
                        <?php }
                    } ?>
                </div>
                <?php
            }
        } else if (false){
            ?>
            <p class="primary">Leider gibt es in dieser Sammlung noch keine weiteren Sammlungen. <a
                        href="<?php echo get_permalink(get_page_by_path('tool-hinzufuegen')) ?>">Hilf uns dabei</a>,
                hier mehr Informationen und Materialien zusammenzutragen.</p>
            <?php
        }
        ?>
    </div>
</div>

<?php if (is_admin()) {
    echo '</div>';
}; ?>
