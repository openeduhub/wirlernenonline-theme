<?php
get_header();
?>

    <div class="wlo-page">

    <div class="wlo-header">
        <div class="wlo-header-wrapper">
            <div class="wlo-header-filler"></div>
            <div class="wlo-header-bottom"></div>
        </div>
    </div>

    <div class="wlo-wrapper">
        <div class="wlo-single-blog">
            <?php
            while ( have_posts() ) : the_post();?>
                <div class="wlo-single-blog-content">
                    <h1><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </div>
                <div class="wlo-single-blog-img">
                    <?php
                    the_post_thumbnail();
                    if (!empty(get_post(get_post_thumbnail_id())->post_excerpt)){
                        echo '<p>'.get_post(get_post_thumbnail_id())->post_excerpt.'</p>';
                    }
                    ?>

                </div>
            <?php endwhile;
            ?>
        </div>
        <a class="wlo-single-blog-button" href="<?php echo esc_url( get_permalink(7) ); ?>">Zur Übersicht</a>

    </div>


<?php
get_footer();
