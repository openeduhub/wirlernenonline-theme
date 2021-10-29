<?php
/*
Template Name: Fachportale (Ãœbersicht)
Template Post Type: page
 */

get_header();
?>
<?php

$accordionID = uniqid();

$current_query_args = array(
    'posts_per_page'	=> 8,
    'post_type' => 'portal',
    'post_status' => 'publish',
    'orderby'     => 'modified',
    'order'       => 'DESC',
    'meta_query' => array(
        'relation' => 'AND', // both of below conditions must match
        array(
            'key' => 'collection_level',
            'value' => '0',
            'compare' 	=> '=',
        ),
        array(
            'key' => 'type',
            'value' => '1',
            'compare'	=> '=',
        ),
    ),
);
$current_query = new WP_Query( $current_query_args );

$sliderId = uniqid('slider-');
$slidesToShow = 6;
$slidesToScroll = 6;
$showSliderDots = 'false';

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
            <div class="wlo-accordion-wrapper" style="background: #D9E2EB;">
                <button class="wlo-accordion" id="wlo-accordion-<?php echo $accordionID; ?>">
                    <h2>Folgende Fachportale haben wieder neue Inhalte für dich</h2>
                    <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Inhalte ein odder ausklappen">
                </button>

                <div class="wlo-accordion-content" id="wlo-accordion-content-<?php echo $accordionID; ?>">

                    <div class="wlo-portals-accordion-slider" id="<?php echo $sliderId; ?>" style="opacity: 0;">
                        <?php
                        if ($current_query->have_posts()) : ?>

                            <?php
                            while ($current_query->have_posts()) :
                                $current_query->the_post(); ?>

                                <div class="wlo-portals-slider-tile">
                                    <a class="wlo-portals-tile" href="<?php the_permalink(); ?>" aria-label="Zum-Fachportal: <?php echo get_the_title(); ?>">
                                        <?php if (has_post_thumbnail()){ ?>
                                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="Icon: <?php echo get_the_title(); ?>">
                                        <?php } ?>
                                        <!--                                    <div class="wlo-portals-tile-type">--><?php //echo wloSubjectType(get_the_title())['type']; ?><!--</div>-->
                                        <div class="wlo-portals-tile-title"><?php echo get_the_title(); ?></div>
                                    </a>
                                </div>


                            <?php endwhile; ?>


                        <?php endif;
                        wp_reset_query();
                        ?>
                    </div>
                </div>
                <div class="wlo-accordion-bottom"></div>
            </div>
        </div>


        <div class="wlo-wrapper">
            <h3 class="wlo-portals-title">Fachportale</h3>

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
                        'value' => '1',
                        'compare'	=> '=',
                    ),
                ),
            );
            $portal_query = new WP_Query( $portal_query_args );

            if ($portal_query->have_posts()) : ?>
                <div class="wlo-portals-filter">
                    <p>Wähle hier aus, welche Fächergruppen Du angezeigt bekommen möchtest.</p>
                    <div class="wlo-portals-filter-tags">
                        <button onclick="onClickFilterButton(this, 'MINT')">
                            <div class="wlo-portals-filter-tag">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                MINT
                            </div>
                        </button>
                        <button onclick="onClickFilterButton(this, 'Gesellschaftswissenschaften')">
                            <div class="wlo-portals-filter-tag">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Gesellschaftswissenschaften
                            </div>
                        </button>
                        <button onclick="onClickFilterButton(this, 'Deutsch')">
                            <div class="wlo-portals-filter-tag">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Deutsch
                            </div>
                        </button>
                        <button onclick="onClickFilterButton(this, 'Fremdsprachen')">
                            <div class="wlo-portals-filter-tag">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Fremdsprachen
                            </div>
                        </button>
                        <button onclick="onClickFilterButton(this, 'Musische-Fächer')">
                            <div class="wlo-portals-filter-tag">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Musische Fächer
                            </div>
                        </button>
                        <button onclick="onClickFilterButton(this, 'Querschnittsthemen')">
                            <div class="wlo-portals-filter-tag">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Querschnittsthemen
                            </div>
                        </button>
                        <button onclick="onClickFilterButton(this, 'Religion')">
                            <div class="wlo-portals-filter-tag">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Religion
                            </div>
                        </button>
                        <button onclick="onClickFilterButton(this, 'Sport')">
                            <div class="wlo-portals-filter-tag">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Sport
                            </div>
                        </button>
                    </div>
                </div>

                <div class="wlo-portals-tiles">

                    <?php
                    while ($portal_query->have_posts()) :
                        $portal_query->the_post(); ?>

                        <a class="wlo-portals-tile <?php echo wloSubjectType(get_the_title())['type']; ?>" href="<?php the_permalink(); ?>" aria-label="Zum-Fachportal: <?php echo get_the_title(); ?>">
                            <?php if (has_post_thumbnail()){ ?>
                                <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="Icon: <?php echo get_the_title(); ?>">
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

    <script>
        jQuery('#wlo-accordion-<?php echo $accordionID; ?>').click(function(){
            jQuery(this).find("img").toggleClass("wlo-accordion-icon-active");
            jQuery('#wlo-accordion-content-<?php echo $accordionID; ?>').slideToggle();
        });

        onClickFilterButton = ( () => {
            let activeFilters = [];
            function updateTiles() {
                if (activeFilters.length === 0) {
                    jQuery('.wlo-portals-tiles .wlo-portals-tile').show('fast');
                } else {
                    jQuery('.wlo-portals-tiles .wlo-portals-tile').hide();
                    activeFilters.forEach((filter) => {
                        jQuery('.wlo-portals-tiles  .'+filter).show('fast');
                    });
                }
            }
            function setActiveState(button, isActive) {
                if (isActive){
                    console.log(button);
                    jQuery(button.querySelector('.wlo-portals-filter-tag')).addClass('active-btn');
                }else {
                    jQuery(button.querySelector('.wlo-portals-filter-tag')).removeClass('active-btn');
                }
            }
            function toggleFilter(button, filter) {
                if (activeFilters.includes(filter)) {
                    activeFilters.splice(activeFilters.indexOf(filter), 1);
                    setActiveState(button, false);
                } else {
                    activeFilters.push(filter);
                    setActiveState(button, true);
                }
                updateTiles();
            }
            return toggleFilter;
        })()

        jQuery(function () {
            // Handler for .ready() called. Put the Slick Slider etc. init code here.
            function loadWLOSlider() {
                if (typeof jQuery().slick === "function") {
                    jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                        infinite: false,
                        slidesToShow: <?php echo $slidesToShow; ?>,
                        slidesToScroll: <?php echo $slidesToScroll; ?>,
                        arrows: true,
                        dots:  <?php echo $showSliderDots; ?>,
                        zIndex: 0,
                        responsive: [
                            {
                                breakpoint: 900,
                                settings: {
                                    slidesToShow: 4,
                                    slidesToScroll: 4
                                }
                            },
                            {
                                breakpoint: 700,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 3
                                }
                            },
                            {
                                breakpoint: 600,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 2
                                }
                            },
                            {
                                breakpoint: 500,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                }
            }

            loadWLOSlider();

            jQuery('#<?php echo $sliderId?>').css('opacity', '1');

            jQuery(window).on('resize', function(){
                jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
            });
        });
    </script>

<?php
get_footer();
