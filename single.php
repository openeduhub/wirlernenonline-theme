<?php
get_header();
?><div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="medium-12 cell">
			<?php
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/post/content');
			endwhile;
			?>
		</div>
	</div>
</div>
<?php
get_footer();
