<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<?php get_template_part('head') ?>

<?php

if(!is_user_logged_in()) {
    wp_redirect( get_page_link(5807) );
    exit();
}

$user = wp_get_current_user();

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}


?>

<body <?php body_class(); ?>>

    <div class="editorial-sidebar hide-wlo-sidebar">
        <div class="editorial-sidebar-close">
            <img src="<?php echo get_template_directory_uri(); ?>'/src/assets/img/close.svg'">
        </div>
        <div class="editorial-user-box">
            <img class="editorial-user-image" src="<?php echo esc_url( get_avatar_url( $user->ID ) ); ?>">
            <div class="editorial-user-name"><?php echo $user->user_nicename; ?></div>


        </div>
        <nav>
            <?php wp_nav_menu( array('theme_location' => 'editorial-menu', 'container_class' => 'editorial-menu', 'walker' => new editorial_walker_nav_menu) ); ?>
        </nav>

        <div class="editorial-sidebar-footer">
            <a href="<?php echo get_home_url(); ?>">
                <img class="logo" src="<?php echo get_stylesheet_directory_uri() ?>/src/assets/img/wlo-logo.svg" alt="Link zur Startseite">
            </a>
        </div>

        <script>
            jQuery(document).ready(function(){

                if (jQuery(".current-menu-item").parent().hasClass('sub-menu')){
                    jQuery(".current-menu-item").parent().show();
                    jQuery(".editorial-menu").animate({ left: '-=300px'  });
                }

                jQuery(".toggle-button").click( function (event){
                    event.preventDefault();
                    jQuery(this).next().toggle("fast");
                    jQuery(".editorial-menu").animate({ left: '-=300px'  });
                    //jQuery(this).find('img').toggleClass('rotate');
                    //jQuery(this).find('img').css("transform", "rotate(180deg)");
                });

                jQuery(".menu-back-button").click( function (){
                    jQuery(".editorial-menu").animate({ left: '+=300px'  });
                    //jQuery(this).find('img').toggleClass('rotate');
                    jQuery(this).parent().parent().toggle("slow");
                    //jQuery(this).find('img').css("transform", "rotate(180deg)");
                });

                jQuery(".editorial-sidebar-close").click( function (){
                    jQuery(this).parent().toggleClass('hide-wlo-sidebar');
                    jQuery('#editorial-sidebar-blur').fadeToggle('fast');
                });

                jQuery("#editorial-sidebar-blur").click( function (){
                    jQuery('.editorial-sidebar').toggleClass('hide-wlo-sidebar');
                    jQuery('#editorial-sidebar-blur').fadeToggle('fast');
                });

            });
        </script>
    </div>

    <div id="editorial-sidebar-blur"></div>

	<main>
