<?php
/*
Template Name: WirLernenOnline-Redaktion
Template Post Type: page
 */

get_header();
?>
<?php
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
