<?php
get_header();?>
<section class="page-title-container padding-top-2 padding-bottom-4">
	<div class="grid-container">
		<div class="grid-x grid-margin-x">
			<div class="medium-12 cell">
				<h1><?php //echo get_the_title('7') ?></h1>
			</div>
		</div>
	</div>
</section>
<section class="blog-loop-container blog-posts">
	<div class="grid-container">
		<div class="grid-x grid-margin-x small-up-1 medium-up-2 large-up-4">
      <?php
  		if ( have_posts() ) {

  			// Load posts loop.
  			while ( have_posts() ) {
  				the_post();
  				get_template_part( 'template-parts/post/content' );
  			}

  			// Previous/next page navigation.

  		} else {

  			// If no content, include the "No posts found" template.
  			get_template_part( 'template-parts/content/content', 'none' );

  		}
  		?>
			<div class="medium-12 cell">

			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>
