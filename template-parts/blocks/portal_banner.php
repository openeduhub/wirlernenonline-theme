<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Banner</div>';
};

if (get_field('active')){


if ( get_the_id() ){
    $postID = get_the_id();
}else{
    $postID = acf_editor_post_id();
}


    $count = 5;
    if (get_field('count')){
        $count = intval( get_field('count') );
    }

    $search_discipline = get_field('discipline', $postID)['value'];
    $search_edu_context = get_field('edu_context', $postID)['value'];
    $search_intended_end_user_role = get_field('intended_end_user_role', $postID)['value'];
    $search_oer = get_field('oer', $postID);
    if (get_field('settings_active')){
        $search_discipline = get_field('discipline')['value'];
        $search_edu_context = get_field('edu_context')['value'];
        $search_intended_end_user_role = get_field('intended_end_user_role')['value'];
        $search_oer = get_field('oer');
    }

    $category_id = get_cat_ID('Themenportal');


    $count = -1;
    if (get_field('count')){
        $count = get_field('count');
    }

    $args = array(
        'posts_per_page'=> $count,
        'cat' => $category_id,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'discipline',
                'value' => $search_discipline
            ),
            array(
                'key' => 'edu_context',
                'value' => $search_edu_context
            ),
            array(
                'key' => 'intended_end_user_role',
                'value' => $search_intended_end_user_role
            )
        )
    );
    $query = new WP_Query( $args );

    if ( $query->have_posts() ) :
    echo '<div class="portal_banner_slider">';
        while ($query->have_posts()) : $query->the_post();
            ?>
            <div>
                <div class="portal_banner_slider_content">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
                    <div class="portal_banner_slider_content_text">
                        <h3><?php the_title(); ?></h3>
                        <p><?php the_excerpt(); ?></p>
                        <div class="text-right">
                            <a href="<?php the_permalink(); ?>" class="button">Weiterlesen</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        endwhile;

        ?>

    <?php
    echo '</div>';
    endif;
    wp_reset_query();
}

?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.portal_banner_slider').slick({
            autoplay:true,
            arrows:false,
            dots: true
        });
    });
</script>
<?php if (is_admin()){echo '</div>';};?>
