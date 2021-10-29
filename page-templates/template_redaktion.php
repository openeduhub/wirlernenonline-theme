<?php
/*
Template Name: WirLernenOnline-Redaktion
Template Post Type: page
 */

get_header();
?>
<?php

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}

$user = wp_get_current_user();
//$username = wp_get_current_user()->user_login;

$mainSubject = get_the_author_meta( 'mainSubject', $user->ID );

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

while (have_posts()) : the_post(); ?>

    <div class="wlo-page">

        <div class="wlo-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-header-filler"></div>
                <div class="wlo-header-bottom"></div>
            </div>
        </div>

        <div class="wlo-wrapper">
            <div class="wlo-redaktion-header">
                <h1><?php the_title(); ?></h1>



                <div class="wlo-redaktion-header-buttons">

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

                    <a href="<?php echo get_site_url(); ?>/login"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/Account.png" title="Mein WLO-Konto"></a>
                    <a href="https://issues.edu-sharing.net/jira/servicedesk/customer/portal/21"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/ServiceDesk.png" title="zum ServiceDesk"></a>
                    <a href="<?php echo get_site_url(); ?>/wp-login.php?action=logout"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/LogOut.png" title="Abmelden"></a>
                </div>
            </div>

            <?php the_content(); ?>
        </div>

    </div>

<?php
endwhile;
get_footer();
