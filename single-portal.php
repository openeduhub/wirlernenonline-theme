<?php
get_header();
?>
<?php
while ( have_posts() ) : the_post();?>
<div class="portal-wrapper">
    <div class="portal-side-menu">
        <h3>Themenportal</h3>
        <select class="portal_select">
            <?php
            $subject_field = get_field_object( 'subject' );
            foreach ($subject_field['choices'] as $key => $value){
                if ($subject_field['value']['value'] == $key){
                    echo '<option value="'.$key.'" selected>'.$value.'</option>';
                }else{
                    echo '<option value="'.$key.'">'.$value.'</option>';
                }
            }
            ?>
        </select>

        <select class="portal_select">
            <?php
            $subject_field = get_field_object( 'school_type' );
            foreach ($subject_field['choices'] as $key => $value){
                if ($subject_field['value']['value'] == $key){
                    echo '<option value="'.$key.'" selected>'.$value.'</option>';
                }else{
                    echo '<option value="'.$key.'">'.$value.'</option>';
                }
            }
            ?>
        </select>

        <select class="portal_select">
            <?php
            $subject_field = get_field_object( 'role' );
            foreach ($subject_field['choices'] as $key => $value){
                if ($subject_field['value']['value'] == $key){
                    echo '<option value="'.$key.'" selected>'.$value.'</option>';
                }else{
                    echo '<option value="'.$key.'">'.$value.'</option>';
                }
            }
            ?>
        </select>
    </div>

    <div class="portal-content-wrapper">

        <?php the_content(); ?>

    </div>

</div>

<?php
endwhile;
get_footer();
