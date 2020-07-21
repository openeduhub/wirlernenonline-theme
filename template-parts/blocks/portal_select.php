<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Auswahl</div>';
};


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

?>
<div class="portal-select-container grid-x grid-margin-x">
    <div class="cell medium-4">
        <select class="portal_select select-discipline">
            <?php
            echo '<option value="">Fach auswählen</option>';
            foreach ($portals as $key => $portal) {
                if ($portal['level'] > 0)
                    continue;

                if(!empty($portal['educationalContexts']) || !empty($portal['intendedEndUserRoles']))
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
    <?php
    $portal_level = get_field('collection_level', $postID);


    if (intval($portal_level) == 0) {
        ?>
        <div class="cell medium-4">
            <select class="portal_select select-educational-context">
                <?php
                $subject_field = get_field_object('educationalContext', $postID);
                $educationalContextsChoices = $subject_field['choices'];

                echo '<option value="">Alle Bildungsstufen</option>';
                foreach ($educationalContextsChoices as $value => $label) {

                    $currentDiscipline = $disciplines[0];
                    $currentEducationalContext = $value;

                    // If Portal with given discipline/educationalContext combination exists, link to that one
                    $portalsWithEduContext = array_filter($portals, function ($portal) use ($currentDiscipline, $currentEducationalContext) {
                        $isToplevel = $portal['level'] == 0;
                        $hasDiscipline = (!empty($portal['discipline'])) && ($portal['discipline']['value'] == $currentDiscipline);
                        $hasContext = (!empty($portal['educationalContexts'])) && in_array($currentEducationalContext, array_column($portal['educationalContexts'],'value'));
                        return $isToplevel && $hasDiscipline && $hasContext;
                    });

                    $portalsWithEduContextNoRole = array_filter($portalsWithEduContext, function ($portal) use ($currentDiscipline, $currentEducationalContext) {
                        return empty($portal['intendedEndUserRoles']);
                    });


                    $dataUrl = '';
                    if (!empty($portalsWithEduContextNoRole)){
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

                echo '<option value="">Alle Zielgruppen</option>';
                foreach ($intendedEndUserRolesChoices as $value => $label) {

                    $currentDiscipline = $disciplines[0];
                    $currentEducationalContext = $educationalContexts[0];
                    $currentIntendedEndUserRole = $value;

                    // If Portal with given discipline/educationalContext combination exists, link to that one
                    $dataUrl = '';
                    $portalsWithRole = array_filter($portals, function ($portal) use ($currentDiscipline, $currentEducationalContext, $currentIntendedEndUserRole) {
                        $isToplevel = $portal['level'] == 0;
                        $hasDiscipline = (!empty($portal['discipline'])) && ($portal['discipline']['value'] == $currentDiscipline);
                        $hasContext = (!empty($portal['educationalContexts'])) &&  in_array($currentEducationalContext, array_column($portal['educationalContexts'],'value'));
                        $hasRole = (!empty($portal['intendedEndUserRoles'])) && in_array($currentIntendedEndUserRole, array_column($portal['intendedEndUserRoles'],'value'));

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

    <?php } else { ?>
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
    <?php }; ?>
</div>
<?php if (is_admin()) {
    echo '</div>';
}; ?>
<script>
    jQuery('.select-discipline').on('change', function () {
        var newUrl = jQuery(this).children('option:selected').data('url');
        window.location.href = newUrl;
    });
    jQuery('.select-educational-context').on('change', function () {
        var newUrl = jQuery(this).children('option:selected').data('url');
        if ((newUrl !== undefined) &&
            newUrl) {
            window.location.href = newUrl;
        } else {
            var url = window.location.href;
            var value = jQuery(this).attr('value');
            if (url.indexOf("educationalContext") > -1) {
                var regEx = /([?&]educationalContext)=([^#&]*)/g;

                if (value) {
                    var newUrl = url.replace(regEx, '$1=' + value);
                } else {
                    var newUrl = url.replace(regEx, '');
                }

                window.location.href = newUrl;
            } else {
                if (value) {
                    if (url.indexOf("?") > -1) {
                        window.location.href = url + "&educationalContext=" + value;
                    } else {
                        window.location.href = url + "?educationalContext=" + value;
                    }
                }
            }
        }

    })
    ;
    jQuery('.select-intended-end-user-role').on('change', function () {
        var newUrl = jQuery(this).children('option:selected').data('url');
        if ((newUrl !== undefined) &&
            newUrl) {
            window.location.href = newUrl;
        } else {
            var url = window.location.href;
            var value = jQuery(this).attr('value');

            if (url.indexOf("intendedEndUserRole") > -1) {
                var regEx = /([?&]intendedEndUserRole)=([^#&]*)/g;

                if (value) {
                    var newUrl = url.replace(regEx, '$1=' + value);
                } else {
                    var newUrl = url.replace(regEx, '');
                }

                window.location.href = newUrl;
            } else {
                if (value) {
                    if (url.indexOf("?") > -1) {
                        window.location.href = url + "&intendedEndUserRole=" + value;
                    } else {
                        window.location.href = url + "?intendedEndUserRole=" + value;
                    }
                }
            }
        }
    })
    ;
</script>
