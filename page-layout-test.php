<?php
get_header();?>
<div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="cell medium-12">
			<h1 class="page-title"><?php the_title(); ?></h1>
		</div>
		<div class="medium-6 cell">
			<h1>6 columns</h1>
		</div>
		<div class="medium-6 cell">
			<h1>6 columns</h1>
		</div>
		<div class="medium-8 cell">
			<h1>8 columns</h1>
		</div>
		<div class="medium-4 cell">
			<h1>4 columns</h1>
		</div>
	</div>
</div>
<div class="gutenberg-layout-container">
	<?php
	while (have_posts()) : the_post();
		the_content();
	endwhile;
	?>
</div>
<?php get_footer(); ?>
