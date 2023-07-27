<article id="post-<?php the_ID(); ?>" <?php post_class('cell blog-post'); ?>>
	<a href="<?php the_permalink(); ?>">
        <div class="card margin-bottom-3">
          <?php the_post_thumbnail('three-columns-four-three'); ?>
          <div class="card-section margin-top-1">
                <h2 class="font-size-h3"><?php the_title(); ?></h2>
                <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>
          </div>
        </div>
	</a>
</article>
