<article id="post-<?php the_ID(); ?>" <?php post_class('cell'); ?>>
	<a href="<?php the_permalink(); ?>">
	<div class="card">
	  <?php the_post_thumbnail(); ?>
	  <div class="card-section">
			<h2><?php the_title(); ?></h2>
			<?php the_excerpt(); ?>
	  </div>
	</div>
	</a>
</article>
