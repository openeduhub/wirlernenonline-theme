<?php
/*
Template Name: WLO: Inhalt hinzugefügt
Template Post Type: post, page
 */

get_header();
?>
<?php
while (have_posts()) : the_post();

    global $post;

    $title = get_the_title();
    if($_GET['type'] == 'source'){
        $title = 'Quelle hinzugefügt';
    }
    if($_GET['type'] == 'tool'){
        $title = 'Tool hinzugefügt';
    }
?>

    <div class="wlo-page">

        <div class="wlo-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-header-content">
                    <h1><?php echo $title; ?></h1>
                    <?php the_excerpt(); ?>
                </div>
                <div class="wlo-header-bottom"></div>
            </div>
        </div>

        <div class="wlo-wrapper">
            <?php the_content(); ?>
        </div>

    </div>

<?php
endwhile;
get_footer();
