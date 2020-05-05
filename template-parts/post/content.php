<article id="post-<?php the_ID(); ?>" <?php post_class('cell'); ?>>
    <a href="<?php the_permalink(); ?>" >
        <div class="card margin-bottom-3">
            <?php the_post_thumbnail('three-columns-four-three'); ?>
            <div class="card-section margin-top-1" tabindex="-1">
                <h2 class="font-size-h3" tabindex="-1"><?php //the_title(); ?>TEST</h2>
                <?php the_excerpt(); ?>
            </div>
        </div>
    </a>
</article>
