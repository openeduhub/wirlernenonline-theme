<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Partner</div>';
} ?>

<?php
$title = get_field( 'title' );
$carousel = intval(get_field('carousel'));

if( $title ){
    echo '<h1>'.$title.'</h1>';
}
?>

<div class="partnerBlock">

    <?php
    $metaquery = array('relation' => 'AND');
    $meta = false;

    $partner = get_field('partner');
    if ($partner){
        $partner_query = array('relation' => 'OR');
        foreach ($partner as $supporter){
            $partner_query[] = array(
                'key' => 'supporter',
                'value' => '"'.$supporter['value'].'"',
                'compare' => 'LIKE'
            );
        }
        $metaquery[] = $partner_query;
        $meta = true;
    }

    $partner_query = array(
    'post_type' => 'partner',
    'numberposts' => -1,
    'post_status'       => 'publish',
    );

    if ($meta){
        $partner_query['meta_query'] = $metaquery;
    }

    $posts = get_posts($partner_query);
    if($posts) {
    foreach($posts as $post) {
        $image = get_field('logo', $post->ID);
        $image_alt = get_the_title($post->ID);
        if($image['alt']){
            $image_alt = $image['alt'];
        }
        ?>

    <div class="partnerCell">
        <div class="logoCell">
            <a href="<?php echo get_page_link(5262); ?>"><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo $image_alt; ?>"></a>
        </div>
    </div>

    <?php }
    }
    ?>


</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.partnerBlock').slick({
            slidesToShow: <?php echo $carousel; ?>,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 2000,
            focusOnChange: true,
            accessibility: false,
            responsive: [
                {
                    breakpoint: 950,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 750,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
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
    });
</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>
