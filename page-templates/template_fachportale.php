<?php
/*
Template Name: Fachportale (Übersicht)
Template Post Type: page
 */

get_header();
?>
<?php

?>
    <div class="wlo-page">

        <div class="wlo-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-header-content">
                    <h1><?php the_title(); ?></h1>
                    <?php the_excerpt(); ?>

                </div>
                <!--                <div class="wlo-header-bottom"></div> -->

            </div>




        </div>

        <div class="wlo-portals-header-bottom">



        <div class="wlo-wrapper zmf-wrapper">
            <h3 class="wlo-portals-title">Klicke auf die Kacheln!</br>
                Thematisch sortiert nach Bildungs- und Erziehungsbereichen findest du hier Anleitungen, Expertisen und Praxisideen:</h3>

            <?php
            $portal_query_args = array(
                'posts_per_page'	=> -1,
                'post_type' => 'portal',
                'post_status' => 'publish',
                'order' => 'ASC',
                'orderby' => 'title',
                'meta_query' => array(
                    'relation' => 'AND', // both of below conditions must match
                    array(
                        'key' => 'collection_level',
                        'value' => '0',
                        'compare' 	=> '=',
                    ),
                    array(
                        'key' => 'type',
                        'value' => '2', //zmf
                        'compare'	=> '=',
                    ),
                ),
            );
            $portal_query = new WP_Query( $portal_query_args );

            if ($portal_query->have_posts()) : ?>

                <div class="wlo-portals-tiles">

                    <?php
                    while ($portal_query->have_posts()) :
                        $portal_query->the_post(); ?>

                        <a class="wlo-portals-tile <?php echo wloSubjectType(esc_html( get_the_title() ))['type']; ?>" href="<?php the_permalink(); ?>" aria-label="Zum-Fachportal: <?php echo esc_html( get_the_title() ); ?>"
                        style="border: 3px solid <?php echo get_field('background_color'); ?>">
                            <?php if (has_post_thumbnail()){ ?>
                                <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="Icon: <?php echo esc_html( get_the_title() ); ?>">
                            <?php } ?>
                            <!--                        <div class="wlo-portals-tile-type">--><?php //echo wloSubjectType(get_the_title())['type']; ?><!--</div>-->
                            <div class="wlo-portals-tile-title"><?php echo get_the_title(); ?></div>
                        </a>

                    <?php endwhile; ?>

                </div>


            <?php endif;
            wp_reset_query();
            ?>

            <?php the_content(); ?>
        </div>

    </div>


<?php
get_footer();
