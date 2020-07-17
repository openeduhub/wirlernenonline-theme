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
    'meta_query' => array(
        array(
            'key' => 'collection_level',
            'value' => 0
        )
    )
);

$portal_disciplines = [];
$portals = new WP_Query($portals_args);
while ($portals->have_posts()) {
    $portals->the_post();
    $portalID = $portals->post->ID;
    $portalUrl = get_permalink($portals->post->ID);

    $portal_discipline = get_field('discipline', $portalID)[0];
    $portal_discipline['url'] = $portalUrl;

    $portal_disciplines[] = $portal_discipline;
}
$portals->reset_postdata();

?>
<div class="portal-select-container grid-x grid-margin-x">
    <div class="cell medium-4">
        <select class="portal_select select-discipline">
            <?php
            echo '<option value="">Fach auswählen</option>';
            foreach ($portal_disciplines as $key => $portal_discipline) {
                if (!empty($disciplines) && $disciplines[0] == $portal_discipline['value']) {
                    echo '<option value="' . $portal_discipline['url'] . '" selected>' . $portal_discipline['label'] . '</option>';
                } else {
                    echo '<option value="' . $portal_discipline['url'] . '">' . $portal_discipline['label'] . '</option>';
                }
            }
            ?>
        </select>
    </div>
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
        </select></div>
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
</div>

<?php if (is_admin()) {
    echo '</div>';
}; ?>
<script>
    jQuery('.select-discipline').on('change', function () {
        var url = window.location.href;
        var value = jQuery(this).attr('value');
        window.location.href = value;
    });
    jQuery('.select-educational-context').on('change', function () {
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
            if (url.indexOf("?") > -1) {
                window.location.href = url + "&educationalContext=" + value;
            } else {
                window.location.href = url + "?educationalContext=" + value;
            }
        }
    });
    jQuery('.select-intended-end-user-role').on('change', function () {

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
            if (url.indexOf("?") > -1) {
                window.location.href = url + "&intendedEndUserRole=" + value;
            } else {
                window.location.href = url + "?intendedEndUserRole=" + value;
            }
        }
    });
</script>
