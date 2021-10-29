<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<?php get_template_part('head') ?>

<?php
$user = wp_get_current_user();
$mainSubject = get_the_author_meta( 'mainSubject', $user->ID );

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}

$apiUrl = 'rest/iam/v1/people/-home-/-me-/memberships?maxItems=100&skipCount=0';
//$apiUrl = 'rest/iam/v1/people/-home-/'.$username.'/memberships?maxItems=100&skipCount=0';
$memberships = callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET', $ticket);

$groups = array();
foreach ($memberships['groups'] as $group){
    if ($group['properties']['ccm:groupType'][0] == 'EDITORIAL'){
        $groups[] = $group['properties']['cm:authorityDisplayName'][0];
    }
}

if (empty($groups)){
    $groups[] = 'Keine EDITORIAL Gruppen';
}

if (isset($_GET["subject"])){
    $GLOBALS['wlo_redaktion'] = array(
        'subject' => $_GET["subject"],
    );
}else if (!empty($mainSubject)){
    $GLOBALS['wlo_redaktion'] = array(
        'subject' => $mainSubject,
    );
}else{
    $GLOBALS['wlo_redaktion'] = array(
        'subject' => str_replace('WLO-', '', $groups[0]),
    );
}
?>

<body <?php body_class(); ?>>

    <div class="editorial-sidebar hide-sidebar">
        <div class="editorial-sidebar-close">
            <img src="<?php echo get_template_directory_uri(); ?>'/src/assets/img/close.svg'">
        </div>
        <div class="editorial-user-box">
            <img class="editorial-user-image" src="<?php echo esc_url( get_avatar_url( $user->ID ) ); ?>">
            <div class="editorial-user-name"><?php echo $user->user_nicename; ?></div>
            <div class="editorial-user-settings">
                <label for="subject">Fach:</label>
                <select name="subject" id="portal" onchange="document.location.href = '?subject=' + this.value">
                    <?php
                    foreach ($groups as $subject){
                        $subject = str_replace('WLO-', '', $subject);

                        if ($subject == $GLOBALS['wlo_redaktion']['subject'] ){
                            echo '<option selected="selected" value="'.$subject.'">'.$subject.'</option>';
                        }else{
                            echo '<option value="'.$subject.'">'.$subject.'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

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
                    jQuery(this).parent().toggle("slow");
                    //jQuery(this).find('img').css("transform", "rotate(180deg)");
                });

                jQuery(".editorial-sidebar-close").click( function (){
                    jQuery(this).parent().toggleClass('hide-sidebar');
                    jQuery('#editorial-sidebar-blur').fadeToggle('fast');
                });

                jQuery("#editorial-sidebar-blur").click( function (){
                    jQuery('.editorial-sidebar').toggleClass('hide-sidebar');
                    jQuery('#editorial-sidebar-blur').fadeToggle('fast');
                });

            });
        </script>
    </div>

    <div id="editorial-sidebar-blur"></div>

	<main>
