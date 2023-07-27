<?php /* Template Name: TESTING! */
get_header();?>
<div class="gutenberg-layout-container">
    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>

    <?php

    // args
    $args = array(
        'numberposts'	=> -1,
        'posts_per_page' => -1,
        'post_status' => 'any',
        'post_type'		=> 'source',
        'meta_query'	=> array(
            'relation'		=> 'OR',
            array(
                'key'		=> 'long_text',
                'compare' => 'EXISTS'
            )
        )
    );


    // query
    $the_query = new WP_Query( $args );
    $i = 1;

    ?>
    <?php if( $the_query->have_posts() ): ?>
    <h3>Quellen mit long_text</h3>
        <ul>
            <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>
            <?php





            ?>
                <li>
                    <a href="<?php the_permalink(); ?>">
                        <?php echo$i.' - '; the_title(); ?>: <?php echo get_the_ID().' - '; ?>
                    </a>
                </li>

                <?php
                    //update_field('discipline', array('080'), get_the_ID());
                    $i++;
                 ?>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <?php wp_reset_query();	 // Restore global post data stomped by the_post(). ?>

</div>

<?php get_footer(); ?>
