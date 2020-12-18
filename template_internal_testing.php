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
        'post_type'		=> 'portal',
        'meta_query'	=> array(
            'relation'		=> 'OR',
            array(
                'key'		=> 'discipline',
                'value'		=> '80',
                'compare'	=> 'LIKE'
            )
        )
    );


    // query
    $the_query = new WP_Query( $args );

    ?>
    <?php if( $the_query->have_posts() ): ?>
    <h3>Bio mit 80</h3>
        <ul>
            <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>
            <?php
                $discipline = get_field('discipline');

                if ($discipline[0]['value'] == '80') :


            ?>
                <li>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>: <?php echo get_the_ID().' - '; print_r( $discipline[0] ); ?>
                    </a>
                </li>

                <?php
                    //update_field('discipline', array('080'), get_the_ID());

                endif; ?>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <?php wp_reset_query();	 // Restore global post data stomped by the_post(). ?>

</div>

<?php get_footer(); ?>
