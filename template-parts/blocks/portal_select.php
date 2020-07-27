<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Navigation</div>';
};


/* Filter Values prioritized GET/POST > Block-Setting > Portal-Setting */
/*
 * collectionLevel
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

$collectionLevel = intval($educational_filter_values["collectionLevel"]);
$collectionUrl = $educational_filter_values["collectionUrl"];
$disciplines = $educational_filter_values["disciplines"];
$educationalContexts = $educational_filter_values["educationalContexts"];
$intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
$oer = $educational_filter_values["oer"];
/* ------------------------------------------------------------------- */


$portals_args = array(
    'post_type' => 'portal',
    'post_status' => array('publish'),
    'posts_per_page' => -1
//    'meta_query' => array(
//        array(
//            'key' => 'collection_level',
//            'value' => 0
//        )
//    )
);

$portals = [];
$portals_result = new WP_Query($portals_args);
while ($portals_result->have_posts()) {
    $portals_result->the_post();
    $portalID = $portals_result->post->ID;
    $portalUrl = get_permalink($portalID);

    $portal['discipline'] = get_field('discipline', $portalID)[0];
    $portal['educationalContexts'] = get_field('educationalContext', $portalID);
    $portal['intendedEndUserRoles'] = get_field('intendedEndUserRole', $portalID);
    $portal['url'] = $portalUrl;
    $portal['level'] = intval(get_field('collection_level', $portalID));

    $portals[] = $portal;
}
$portals_result->reset_postdata();

$navigationId = uniqid('navigation-');

?>
<div class="portal_block">
    <div class="portal-navigation grid-x grid-margin-x <?php echo $navigationId ?>">
        <?php
        /* ----------------------------------------------------------------------------------------------------------
        * Level 0 - Fachportal
        */
        if (intval($collectionLevel) == 0) {
            ?>
            <div class="cell medium-4">
                <select class="portal_select select-discipline">
                    <?php
                    echo '<option value="">Fach auswählen</option>';
                    foreach ($portals as $key => $portal) {
                        if ($portal['level'] > 0)
                            continue;

                        if (!empty($portal['educationalContexts']) || !empty($portal['intendedEndUserRoles']))
                            continue;

                        if (!empty($disciplines) && $disciplines[0] == $portal['discipline']['value']) {
                            echo '<option data-url="' . $portal['url'] . '" value="' . $portal['discipline']['value'] . '" selected>' . $portal['discipline']['label'] . '</option>';
                        } else {
                            echo '<option data-url="' . $portal['url'] . '" value="' . $portal['discipline']['value'] . '">' . $portal['discipline']['label'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="cell medium-4">
                <select class="portal_select select-educational-context">
                    <?php
                    $subject_field = get_field_object('educationalContext', $postID);
                    $educationalContextsChoices = $subject_field['choices'];

                    $defaultValueField = '<option data-url="" value="">Alle Bildungsstufen</option>';
                    $currentDiscipline = $disciplines[0];

                    // Set default page
                    $portalsWithNoEduContext = array_filter($portals, function ($portal) use ($currentDiscipline) {
                        $isToplevel = $portal['level'] == 0;
                        $hasDiscipline = (!empty($portal['discipline'])) && ($portal['discipline']['value'] == $currentDiscipline);
                        $hasNoContext = empty($portal['educationalContexts']);
                        return $isToplevel && $hasDiscipline && $hasNoContext;
                    });

                    if (!empty($portalsWithNoEduContext)) {
                        $dataUrl = $portalsWithNoEduContext[array_key_first($portalsWithNoEduContext)]['url'];
                        $defaultValueField = '<option data-url="' . $dataUrl . '" value="">Alle Bildungsstufen</option>';
                    }
                    echo $defaultValueField;


                    foreach ($educationalContextsChoices as $value => $label) {

                        $currentDiscipline = $disciplines[0];
                        $currentEducationalContext = $value;

                        // If Portal with given discipline/educationalContext combination exists, link to that one
                        $portalsWithEduContext = array_filter($portals, function ($portal) use ($currentDiscipline, $currentEducationalContext) {
                            $isToplevel = $portal['level'] == 0;
                            $hasDiscipline = (!empty($portal['discipline'])) && ($portal['discipline']['value'] == $currentDiscipline);
                            $hasContext = (!empty($portal['educationalContexts'])) && in_array($currentEducationalContext, array_column($portal['educationalContexts'], 'value'));
                            return $isToplevel && $hasDiscipline && $hasContext;
                        });

                        $portalsWithEduContextNoRole = array_filter($portalsWithEduContext, function ($portal) use ($currentDiscipline, $currentEducationalContext) {
                            return empty($portal['intendedEndUserRoles']);
                        });


                        $dataUrl = '';
                        if (!empty($portalsWithEduContextNoRole)) {
                            $dataUrl = $portalsWithEduContextNoRole[array_key_first($portalsWithEduContextNoRole)]['url'];
                        }

                        if (!empty($portalsWithEduContext)) {
                            $dataUrl = $portalsWithEduContext[array_key_first($portalsWithEduContext)]['url'];
                        }

                        if (!empty($educationalContexts) && $educationalContexts[0] == $currentEducationalContext) {
                            echo '<option data-url="' . $dataUrl . '" value="' . $value . '" selected>' . $label . '</option>';
                        } else {
                            echo '<option data-url="' . $dataUrl . '" value="' . $value . '">' . $label . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="cell medium-4">
                <select class="portal_select select-intended-end-user-role">
                    <?php
                    $subject_field = get_field_object('intendedEndUserRole', $postID);
                    $intendedEndUserRolesChoices = $subject_field['choices'];

                    $defaultValueField = '<option data-url="" value="">Alle Zielgruppen</option>';
                    foreach ($educationalContextsChoices as $value => $label) {
                        $currentDiscipline = $disciplines[0];
                        $currentEducationalContext = $value;

                        // If Portal with given discipline/educationalContext combination exists, link to that one
                        $portalsWithNoRole = array_filter($portals, function ($portal) use ($currentDiscipline, $currentEducationalContext) {
                            $isToplevel = $portal['level'] == 0;
                            $hasDiscipline = (!empty($portal['discipline'])) && ($portal['discipline']['value'] == $currentDiscipline);
                            $hasContext = (!empty($portal['educationalContexts'])) && in_array($currentEducationalContext, array_column($portal['educationalContexts'], 'value'));
                            $hasNoRole = empty($portal['intendedEndUserRoles']);
                            return $isToplevel && $hasDiscipline && $hasContext && $hasNoRole;
                        });

                        if (!empty($portalsWithNoRole)) {
                            $dataUrl = $portalsWithNoRole[array_key_first($portalsWithNoRole)]['url'];
                            $defaultValueField = '<option data-url="' . $dataUrl . '" value="">Alle Zielgruppen</option>';
                            break;
                        }
                    }
                    echo $defaultValueField;

                    foreach ($intendedEndUserRolesChoices as $value => $label) {

                        $currentDiscipline = $disciplines[0];
                        $currentEducationalContext = $educationalContexts[0];
                        $currentIntendedEndUserRole = $value;

                        // If Portal with given discipline/educationalContext combination exists, link to that one
                        $dataUrl = '';
                        $portalsWithRole = array_filter($portals, function ($portal) use ($currentDiscipline, $currentEducationalContext, $currentIntendedEndUserRole) {
                            $isToplevel = $portal['level'] == 0;
                            $hasDiscipline = (!empty($portal['discipline'])) && ($portal['discipline']['value'] == $currentDiscipline);
                            $hasContext = (!empty($portal['educationalContexts'])) && in_array($currentEducationalContext, array_column($portal['educationalContexts'], 'value'));
                            $hasRole = (!empty($portal['intendedEndUserRoles'])) && in_array($currentIntendedEndUserRole, array_column($portal['intendedEndUserRoles'], 'value'));

                            return $isToplevel && $hasDiscipline && $hasContext && $hasRole;
                        });

                        $dataUrl = '';
                        if (!empty($portalsWithRole)) {
                            $dataUrl = $portalsWithRole[array_key_first($portalsWithRole)]['url'];
                        }
                        if (!empty($intendedEndUserRoles) && $intendedEndUserRoles[0] == $value) {
                            echo '<option data-url="' . $dataUrl . '" value="' . $value . '" selected>' . $label . '</option>';
                        } else {
                            echo '<option data-url="' . $dataUrl . '" value="' . $value . '">' . $label . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

        <?php }
        else {
            /* ----------------------------------------------------------------------------------------------------------
             * Level 1 - Themenportal
            */
            ?>
            <div class="cell medium-4">
                <select class="portal_select select-educational-context">
                    <?php
                    $subject_field = get_field_object('educationalContext', $postID);

                    echo '<option value="">Alle Bildungsstufen</option>';
                    foreach ($subject_field['choices'] as $key => $value) {
                        if (!empty($educationalContexts) && $educationalContexts[0] == $key) {
                            echo '<option value="' . $key . '" selected>' . $value . '</option>';
                        } else {
                            echo '<option value="' . $key . '">' . $value . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="cell medium-4">
                <select class="portal_select select-intended-end-user-role">
                    <?php
                    $subject_field = get_field_object('intendedEndUserRole', $postID);

                    echo '<option value="">Alle Zielgruppen</option>';
                    foreach ($subject_field['choices'] as $key => $value) {
                        if (!empty($intendedEndUserRoles) && $intendedEndUserRoles[0] == $key) {
                            echo '<option value="' . $key . '" selected>' . $value . '</option>';
                        } else {
                            echo '<option value="' . $key . '">' . $value . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="cell medium-4">
                <div class="portal_checkbox_container cb-oer">
                    <label for="<?php echo $navigationId ?>-cb-oer">OER:</label>
                    <input type="checkbox" id="<?php echo $navigationId ?>-cb-oer" value="oer" <?php echo ($oer == true) ? 'checked' : '' ?>/>
                </div>
            </div>
        <?php }; ?>
    </div>
    <script>
        jQuery('.<?php echo $navigationId ?> .select-discipline').on('change', function () {
            var newUrl = jQuery(this).children('option:selected').data('url');
            window.location.href = newUrl;
        });
        jQuery('.<?php echo $navigationId ?> .select-educational-context').on('change', function () {
            var newUrl = jQuery(this).children('option:selected').data('url');
            if ((newUrl !== undefined) &&
                newUrl) {
                window.location.href = newUrl;
            } else {
                var value = jQuery(this).attr('value');
                var urlParams = new URLSearchParams(window.location.search);
                if(value){
                    urlParams.set("educationalContext",value.toString());
                }
                else{
                    urlParams.delete("educationalContext");
                }
            }
            window.location.href = "?" + urlParams.toString();
        })
        ;
        jQuery('.<?php echo $navigationId ?> .select-intended-end-user-role').on('change', function () {
            var newUrl = jQuery(this).children('option:selected').data('url');
            if ((newUrl !== undefined) &&
                newUrl) {
                window.location.href = newUrl;
            } else {
                var value = jQuery(this).attr('value');
                var urlParams = new URLSearchParams(window.location.search);
                if(value){
                    urlParams.set("intendedEndUserRole",value.toString());
                }
                else{
                    urlParams.delete("intendedEndUserRole");
                }
                window.location.href = "?" + urlParams.toString();
            }
        });
        jQuery('#<?php echo $navigationId ?>-cb-oer').change(function() {
            var enableOER = jQuery(this).is(":checked");
            var urlParams = new URLSearchParams(window.location.search);
            if(enableOER){
                urlParams.set("oer","1");
            }
            else{
                urlParams.delete("oer");
            }
            window.location.href = "?" + urlParams.toString();
        });
    </script>
</div>
<?php if (is_admin()) {
    echo '</div>';
}; ?>

