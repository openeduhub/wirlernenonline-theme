<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Blogbeiträge</div>';
};

if (get_field('active')){

    $slider_tags = get_field('tag');
    if(!empty($slider_tags)){
        $tag_list = '';
        foreach ($slider_tags as $tag){
            $tag_list .= $tag->name.',';
        }
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


        echo '<div class="portal_block">';
            echo '<h3>'.get_field('headline').'</h3>';
            echo '<div class="portal_tip_slider">';
            while ($query->have_posts()) : $query->the_post();
                ?>
                <div>
                    <div class="portal_block_slider_content">
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php echo get_the_post_thumbnail_url();?>">
                        </a>
                        <div class="portal_block_slider_content_text">
                            <a href="<?php the_permalink(); ?>"><h5><?php the_title(); ?></h5></a>
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                </div>
                <?php
            endwhile;
            echo '</div>';
        echo '</div>';

    endif;
    wp_reset_query();
}

?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.portal_tip_slider').slick({
            infinite: true,
            slidesToShow: 2,
            slidesToScroll: 1,
            zIndex: 0
        });
    });
</script>
<?php if (is_admin()){echo '</div>';};?>