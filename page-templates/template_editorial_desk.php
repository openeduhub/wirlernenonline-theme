<?php
/*
Template Name: WlO-Redaktionsumgebung
Template Post Type: page
 */

get_header('editorial-desk');
?>
<?php

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}


while (have_posts()) : the_post(); ?>

    <div class="wlo-editorial-page">
        <div class="wlo-editorial-page-header">
            <h1 class="wlo-editorial-page-title">
                <img class="editorial-menu-icon" src="<?php echo get_template_directory_uri(); ?>'/src/assets/img/menu_icon.svg'">
                <?php the_title(); ?>
                <img src="<?php echo get_template_directory_uri(); ?>'/src/assets/img/arrow_down_white.svg'">
            </h1>

            <div class="wlo-new-content">

                <button class="wlo-new-content-button">
                    <div>
                        <img src="<?php echo get_template_directory_uri(); ?>'/src/assets/img/plus.svg'">
                        <span>Neu</span>
                    </div>
                </button>
                <div class="wlo-new-content-dropdown">
                    <a href="<?php echo WLO_REPO.'components/upload?ticket='.$ticket; ?>" target="_blank">Dateiupload</a>
                    <a href="<?php echo WLO_REPO.'components/search?connector=H5P&ticket='.$ticket; ?>" target="_blank">H5P erstellen</a>
                    <a href="<?php echo WLO_REPO.'components/search?connector=ONLY_OFFICE&ticket='.$ticket; ?>" target="_blank">OnlyOffice erstellen</a>
                    <a href="<?php echo get_home_url().'/wp-admin/post-new.php?post_type=page'; ?>" target="_blank">Neue Wordpress-Seite erstellen</a>
                    <a href="<?php echo get_home_url().'/wp-admin/post-new.php'; ?>" target="_blank">Neuen Wordpress-Blogbeitrag erstellen</a>
                    <!--        <a href="--><?php //echo WLO_REPO.'components/workspace?connector=MOODLE&ticket='.$ticket; ?><!--" target="_blank">Moodle erstellen</a>-->
                </div>

            </div>

            <script>
                jQuery(document).ready(function(){

                    jQuery(".wlo-editorial-page-title").click( function (){
                        jQuery(".editorial-sidebar").toggleClass('hide-sidebar');
                        jQuery('#editorial-sidebar-blur').fadeToggle('fast');
                    });

                });
            </script>
        </div>

        <?php the_content(); ?>
    </div>

<?php
endwhile;
?>

</main><!-- .site-content -->


<?php wp_footer(); ?>
</body>
</html>
