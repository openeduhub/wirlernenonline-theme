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

    //echo '<pre style="background-color: lightgrey">' , var_dump($educational_filter_values) , '</pre>';

    $collectionUrl = $educational_filter_values["collectionUrl"];
    $disciplines = $educational_filter_values["disciplines"];
    $educationalContexts = $educational_filter_values["educationalContexts"];
    $intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
    $oer = $educational_filter_values["oer"];
    /* ------------------------------------------------------------------- */

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

                echo '<option value="">Bildungsstufe</option>';
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

                echo '<option value="">Zielgruppe</option>';
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