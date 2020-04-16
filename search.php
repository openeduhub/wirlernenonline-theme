<?php get_header(); ?>
<?php if ( have_posts() ) : ?>
	<div class="row white-background">
		<div class="medium-8 medium-centered columns">
			<h1 class="page-title space-top-medium"><?php printf( __( 'Suchergebnisse für: %s', 'twentyfifteen' ), get_search_query() ); ?></h1>
		</div>
	</div>
	<?php
	while ( have_posts() ) : the_post(); ?>
	<?php ?>
	<div class="row white-background">
		<div class="medium-8 medium-centered columns">
			<?php the_title( sprintf( '<h2><a href="%s" rel="bookmark"> → ', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		</div>
	</div>
<? endwhile;
else :
	get_template_part( 'content', 'none' );
endif;
?>
<?php get_footer(); ?>
