<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Auswahl</div>';
};

if ( get_the_id() ){
    $postID = get_the_id();
}else{
    $postID = acf_editor_post_id();
}


//Disciplines
$query_var_disciplines = (!empty(get_query_var('discipline', null))) ? explode(";", get_query_var('discipline', null)) : [];
$block_var_disciplines = (!empty(get_field('discipline'))) ? get_field('discipline') : [];
$portal_var_disciplines = (!empty(get_field('discipline', $postID))) ? get_field('discipline', $postID) : [];

$disciplines = (!empty($portal_var_disciplines)) ? $portal_var_disciplines : [];
$disciplines = (!empty($block_var_disciplines)) ? $block_var_disciplines : $disciplines;
$disciplines = (!empty($disciplines)) ? array_column($disciplines, 'value') : [];
$disciplines = (!empty($query_var_disciplines)) ? $query_var_disciplines : $disciplines;

// Preview
$disciplines = (!empty($disciplines)) ? $disciplines : get_post_meta($postID, 'discipline', false)[0];


//EducationalContext
$query_var_educationalContexts = (!empty(get_query_var('educationalContext', null))) ? explode(";", get_query_var('educationalContext', null)) : [];
$block_var_educationalContexts = (!empty(get_field('educationalContext'))) ? get_field('educationalContext') : [];
$portal_var_educationalContexts = (!empty(get_field('educationalContext', $postID))) ? get_field('educationalContext', $postID) : [];


$educationalContexts = (!empty($portal_var_educationalContexts)) ? $portal_var_educationalContexts : [];
$educationalContexts = (!empty($block_var_educationalContexts)) ? $block_var_educationalContexts : $educationalContexts;
$educationalContexts = (!empty($educationalContexts)) ? array_column($educationalContexts, 'value') : [];
$educationalContexts = (!empty($query_var_educationalContexts)) ? $query_var_educationalContexts : $educationalContexts;

// Preview
$educationalContexts = (!empty($educationalContexts)) ? $educationalContexts : get_post_meta($postID, 'educationalContext', false)[0];


//intendedEndUserRole
$query_var_intendedEndUserRoles = (!empty(get_query_var('intendedEndUserRole', null))) ? explode(";", get_query_var('intendedEndUserRole', null)) : [];
$block_var_intendedEndUserRoles = (!empty(get_field('intendedEndUserRole'))) ? get_field('intendedEndUserRole') : [];
$portal_var_intendedEndUserRoles = (!empty(get_field('intendedEndUserRole', $postID))) ? get_field('intendedEndUserRole', $postID) : [];

$intendedEndUserRoles = (!empty($portal_var_intendedEndUserRoles)) ? $portal_var_intendedEndUserRoles : [];
$intendedEndUserRoles = (!empty($block_var_intendedEndUserRoles)) ? $block_var_intendedEndUserRoles : $intendedEndUserRoles;
$intendedEndUserRoles = (!empty($intendedEndUserRoles)) ? array_column($intendedEndUserRoles, 'value') : [];
$intendedEndUserRoles = (!empty($query_var_intendedEndUserRoles)) ? $query_var_intendedEndUserRoles : $intendedEndUserRoles;

// Preview
$intendedEndUserRoles = (!empty($intendedEndUserRoles)) ? $intendedEndUserRoles : get_post_meta($postID, 'intendedEndUserRole', false)[0];


//OER
$query_var_oer = get_query_var('oer', false);
$block_var_oer = get_field('oer');
$portal_var_oer = (!empty(get_field('oer', $postID))) ? get_field('oer', $postID) : [];


$oer = (!empty($portal_var_oer)) ? $portal_var_oer : false;
$oer = (!empty($block_var_oer)) ? $block_var_oer : $oer;
$oer = (!empty($query_var_oer)) ? $query_var_oer : $oer;

// Preview
$oer = (!empty($oer)) ? $oer : get_post_meta($postID, 'oer', false)[0];
    ?>
    <div class="portal-select-container grid-x grid-margin-x">
        <div class="cell medium-4">
            <select class="portal_select">
                <?php
                $subject_field = get_field_object('discipline',$postID);

                foreach ($subject_field['choices'] as $key => $value) {
                    if ($disciplines[0] == $key) {
                        echo '<option value="' . $key . '" selected>' . $value . '</option>';
                    } else {
                        echo '<option value="' . $key . '">' . $value. '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="cell medium-4">
            <select class="portal_select">
                <?php
                $subject_field = get_field_object('educationalContext',$postID);

                foreach ($subject_field['choices'] as $key => $value) {
                    if ($educationalContexts[0] == $key) {
                        echo '<option value="' . $key . '" selected>' . $value . '</option>';
                    } else {
                        echo '<option value="' . $key . '">' . $value . '</option>';
                    }
                }
                ?>
            </select></div>
        <div class="cell medium-4">
            <select class="portal_select">
                <?php
                $subject_field = get_field_object('intendedEndUserRole', $postID);
                foreach ($subject_field['choices'] as $key => $value) {
                    if ($intendedEndUserRoles[0] == $key) {
                        echo '<option value="' . $key . '" selected>' . $value . '</option>';
                    } else {
                        echo '<option value="' . $key . '">' . $value . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

<?php if (is_admin()){echo '</div>';};?>