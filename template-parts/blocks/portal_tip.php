<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Neueste Blogbeiträge</div>';
};

if (get_field('active')){

    $slider_tags = get_field('tag');
    if(!empty($slider_tags)){
        $tag_list = '';
        foreach ($slider_tags as $tag){
            $tag_list .= $tag->name.',';
        }
    }

    $slider_cats = get_field('category');
    $cat_list = [];
    if(!empty($slider_cats)){
        foreach ($slider_cats as $cat){
            array_push($cat_list, $cat->term_id);
        }
    }

    $count = -1;
    if (get_field('count')){
        $count = get_field('count');
    }

    $args = array(
        'posts_per_page'=> $count,
        'tag' => $tag_list,
        'category__in' => $cat_list,
        'post_status' => 'publish'
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) :


        echo '<div class="portal_block">';
            if(!empty(get_field('headline')))
                echo '<h3>' . get_field('headline') . '</h3>';
            else
                echo '<h3>Blogbeiträge</h3>';

            echo '<div class="portal_latest_posts_block">';
                echo '<div class="portal_latest_posts_slider">';
                while ($query->have_posts()) : $query->the_post();
                    ?>
                    <div>
                        <div class="portal_latest_posts_slider_content">
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo get_the_post_thumbnail_url();?>">
                            </a>
                            <div class="portal_latest_posts_slider_content_text">
                                <a href="<?php the_permalink(); ?>"><h5><?php the_title(); ?></h5></a>
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                echo '</div>';
            echo '</div>';
            ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery('.portal_latest_posts_slider').slick({
                    infinite: true,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    zIndex: 0
                });
            });
        </script>
        <?php
        echo '</div>';

    endif;
    wp_reset_query();
}

?>


<?php if (is_admin()){echo '</div>';};?>