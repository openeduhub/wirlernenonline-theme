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
    ?>

    <div class="portal-select-container grid-x grid-margin-x">
        <div class="cell medium-4">
            <select class="portal_select">
                <?php
                $subject_field = get_field_object('subject',$postID);
                foreach ($subject_field['choices'] as $key => $value) {
                    if ($subject_field['value']['value'] == $key) {
                        echo '<option value="' . $key . '" selected>' . $value . '</option>';
                    } else {
                        echo '<option value="' . $key . '">' . $value . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="cell medium-4">
            <select class="portal_select">
                <?php
                $subject_field = get_field_object('school_type',$postID);
                foreach ($subject_field['choices'] as $key => $value) {
                    if ($subject_field['value']['value'] == $key) {
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
                $subject_field = get_field_object('role', $postID);
                foreach ($subject_field['choices'] as $key => $value) {
                    if ($subject_field['value']['value'] == $key) {
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