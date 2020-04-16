<?php
get_header();
?><div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="medium-12 cell">
			<h1 class="page-title"><?php the_title(); ?></h1>
		</div>
		<div class="medium-8 cell">
			<?php
			while ( have_posts() ) : the_post();?>
				<?php the_content(); ?>
			<?php endwhile;
			?>
		</div>
		<div class="medium-4 cell">
			<?php the_post_thumbnail(); ?>
		</div>
	</div>
</div>
<?php
get_footer();
