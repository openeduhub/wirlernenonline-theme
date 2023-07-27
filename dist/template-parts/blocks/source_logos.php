<?php
$title = get_field( 'title' );
if( $title ){
    echo '<h1>'.$title.'</h1>';
}
?>
<div class="wloBlockWrapper">
    <div class="sourceLogosBlock">

        <?php
        $numberposts = get_field('numberposts');

        $partner_query = array(
            'post_type' => 'edusource',
            'numberposts' => $numberposts,
            'post_status'       => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'verfuegbar_mit_inhalten_group_verfuegbar_mit_inhalten',
                    'compare'		=> 'like',
                    'value'			=> true,
                )
            )
        );


        $posts = get_posts($partner_query);
        if($posts) {
            foreach($posts as $post) {
                $image = get_field('thumbnail', $post->ID);
                $image_alt = get_the_title($post->ID);
                if($image['alt']){
                    $image_alt = $image['alt'];
                }
                ?>

                <div class="partnerCell">
                    <a href="<?php echo get_field('url', $post->ID); ?>" target="_blank"><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo $image_alt; ?>"></a>
                </div>

            <?php }
        }
        ?>

    </div>

    <a class="wloButton" href="<?php echo the_permalink(4225); ?>" target="_blank" rel="noreferrer noopener">mehr Quellen</a>

</div>


<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.sourceLogosBlock').slick({
            slidesToShow: <?php echo get_field('carousel'); ?>,
            slidesToScroll: 1,
            centerMode: true,
            centerPadding: '60px',
            autoplay: true,
            autoplaySpeed: 2000,
        });
    });
</script>
