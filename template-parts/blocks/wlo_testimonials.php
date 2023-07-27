<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Testimonials</div>';
} ?>

<?php
$sliderId = uniqid('slider-');
?>


<div class="wlo-testimonials" id="<?php echo $sliderId; ?>">

    <?php
    $testimonials_query = array(
    'post_type' => 'testimonials',
    'posts_per_page' => -1,
    'post_status'       => 'publish',
    );

    $posts = get_posts($testimonials_query);

    if($posts) {
        foreach($posts as $post) {
            $image = get_field('picture', $post->ID);
            ?>

            <div class="wlo-testimonial">
                <div class="wlo-testimonial-wrapper">
                    <?php if( !empty( $image ) ): ?>
                        <img src="<?php echo esc_url($image['url']); ?>" alt="Picture: <?php echo get_the_title($post->ID); ?>">
                    <?php endif; ?>
                    <div class="wlo-testimonial-name"><?php echo get_the_title($post->ID); ?></div>
                    <div class="wlo-testimonial-position"><?php echo get_field('position', $post->ID); ?></div>
                    <div class="wlo-testimonial-text">»<?php echo get_field('text', $post->ID); ?>«</div>
                </div>
            </div>

        <?php }
    }
    ?>


</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        // Handler for .ready() called. Put the Slick Slider etc. init code here.
        function loadWLOtestimonialsSlider() {
            if (typeof jQuery().slick === "function") {
                jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    arrows: true,
                    dots:  false,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    zIndex: 0,
                    responsive: [
                        {
                            breakpoint: 900,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        },
                    ]
                });
            }
        }

        loadWLOtestimonialsSlider();
    });
</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>
