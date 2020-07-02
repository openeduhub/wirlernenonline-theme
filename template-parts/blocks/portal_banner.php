



<?php

if (is_admin()){
    echo '<div class="portal_backend_hint">Banner Block</div>';
}

if (get_field('active')){

    $slider_tags = get_field('tag');
    $tag_list = '';
    foreach ($slider_tags as $tag){
        $tag_list .= $tag->name.',';
    }

    $count = -1;
    if (get_field('count')){
        $count = get_field('count');
    }

    $args = array(
        'posts_per_page'=> $count,
        'tag' => $tag_list,
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) :
    echo '<div class="portal_slider">';
        while ($query->have_posts()) : $query->the_post();
            ?>
            <div>
                <div class="portal_slider_content">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
                    <div class="portal_block_slider_content_text">
                        <h3><?php the_title(); ?></h3>
                        <p><?php the_excerpt(); ?></p>
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
        jQuery('.portal_slider').slick();
    });
</script>
