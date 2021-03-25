<?php
/*
Template Name: Partner
Template Post Type: post, page, portal
 */

get_header();
?>
<?php

while (have_posts()) : the_post(); ?>

    <div class="wlo-page">

        <div class="wlo-header">
            <div class="wlo-header-wrapper">
                <div class="wlo-header-content">
                    <h1><?php the_title(); ?></h1>
                    <?php the_excerpt(); ?>
                    <a class="wlo-button wlo-button-right" href="<?php echo get_page_link(8); ?>?anliegen=Kooperation-Partner">Sprecht uns an</a>
                </div>
                <div class="wlo-header-bottom"></div>
            </div>
        </div>

        <div class="wlo-wrapper">
            <?php the_content(); ?>

            <?php
            $current_query_args = array(
                'posts_per_page'	=> -1,
                'post_type' => 'partner',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key'     => 'supporter',
                        'value'   =>  '"5"',
                        'compare' => 'LIKE',
                    ),
                ),
            );
            $current_query = new WP_Query( $current_query_args );
            ?>

            <h3>Konsortialpartner</h3>
            <p>
                <?php if(!empty(get_field('textKonsortialPartner'))){
                    echo get_field('textKonsortialPartner');
                }?>
            </p>

            <?php
            if ($current_query->have_posts()) : ?>

                <?php
                while ($current_query->have_posts()) :
                    $current_query->the_post(); ?>

                    <div class="wlo-partner">
                        <div class="wlo-partner-image">
                            <?php if (!empty(get_field('logo'))){ ?>
                                <a href="<?php echo get_field('link'); ?>" target="_blank">
                                    <img src="<?php echo get_field('logo')['url']; ?>" alt="Logo: <?php echo get_the_title(); ?>">
                                </a>
                            <?php } ?>
                        </div>

                        <div class="wlo-partner-description">
                            <div class="wlo-partner-title"><?php echo get_the_title(); ?></div>
                            <div class="wlo-partner-text">
                                <p><?php echo get_field('description'); ?></p>
                            </div>
                        </div>

                        <?php if (strlen(get_field('description')) >= 250){ ?>
                            <button class="wlo-partner-button" onclick="toggleWloPartner(this)">
                                <div>
                                    <span>Mehr</span>
                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg" alt="">
                                </div>
                            </button>
                        <?php } ?>

                    </div>


                <?php endwhile; ?>


            <?php endif;
            wp_reset_query();
            ?>

            <hr class="fachportal-spacer">


            <?php
            $current_query_args = array(
                'posts_per_page'	=> -1,
                'post_type' => 'partner',
                'post_status' => 'publish',
                'orderby' => 'title',
                'order'   => 'ASC',
                'meta_query' => array(
                    'relation' => 'OR',
                        [
                            'key'     => 'supporter',
                            'value'   => '"1"',
                            'compare' => 'LIKE',
                        ],
                        [
                            'key'     => 'supporter',
                            'value'   => '"2"',
                            'compare' => 'LIKE',
                        ],
                        [
                            'key'     => 'supporter',
                            'value'   => '"3"',
                            'compare' => 'LIKE',
                        ],
                ),
            );
            $current_query = new WP_Query( $current_query_args );
            ?>

            <h3>Weitere Partner</h3>
            <p>
                <?php if(!empty(get_field('textOtherPartner'))){
                    echo get_field('textOtherPartner');
                }?>
            </p>

            <?php
            if ($current_query->have_posts()) : ?>

                <?php
                while ($current_query->have_posts()) :
                    $current_query->the_post(); ?>

                    <div class="wlo-partner">
                        <div class="wlo-partner-image">
                            <?php if (!empty(get_field('logo'))){ ?>
                                <a href="<?php echo get_field('link'); ?>" target="_blank">
                                    <img src="<?php echo get_field('logo')['url']; ?>" alt="Logo: <?php echo get_the_title(); ?>">
                                </a>
                            <?php } ?>
                        </div>

                        <div class="wlo-partner-description">
                            <div class="wlo-partner-title"><?php echo get_the_title(); ?></div>
                            <div class="wlo-partner-text">
                                <p><?php echo get_field('description'); ?></p>
                            </div>
                        </div>

                        <?php if (strlen(get_field('description')) >= 250){ ?>
                            <button class="wlo-partner-button" onclick="toggleWloPartner(this)">
                                <div>
                                    <span>Mehr</span>
                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg" alt="">
                                </div>
                            </button>
                        <?php } ?>

                    </div>


                <?php endwhile; ?>


            <?php endif;
            wp_reset_query();
            ?>


        </div>

    </div>

<script>
    function toggleWloPartner(button){
        if (jQuery(button).closest('.wlo-partner').find('.wlo-partner-text').height() <= '74'){
            jQuery(button).closest('.wlo-partner').find('.wlo-partner-text').css('max-height', 1000);
            jQuery(button).closest('.wlo-partner').find('.wlo-partner-button div img').css("transform", "rotate(180deg)");
            jQuery(button).closest('.wlo-partner').find('.wlo-partner-button div span').html("Weniger");
        }else {
            //jQuery(button).closest('.wlo-partner').find('.wlo-partner-text').height('74');
            jQuery(button).closest('.wlo-partner').find('.wlo-partner-text').css('max-height', '3.7em');
            jQuery(button).closest('.wlo-partner').find('.wlo-partner-button div img').css("transform", "rotate(0deg)");
            jQuery(button).closest('.wlo-partner').find('.wlo-partner-button div span').html("Mehr");
        }
    }
</script>

<?php
endwhile;
get_footer();
