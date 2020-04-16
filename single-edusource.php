<?php
get_header();
?><div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="medium-12 cell">
			<h1 class="page-title"><?php the_title(); ?></h1>
		</div>
		<?php
		while ( have_posts() ) : the_post(); ?>
		<div class="medium-8 cell">
			<?php
			$thumbnail = get_field('thumbnail');
			if( !empty( $thumbnail ) ): ?>
			<img class="margin-bottom-2 fullwidth" src="<?php echo esc_url($thumbnail['url']); ?>" alt="<?php echo esc_attr($thumbnail['alt']); ?>" />
		<?php endif; ?>
		<?php
		$value = get_field( "long_text" );
		if ( $value ) :?>
		<div class="long-text-container lead margin-bottom-2">
			<?php the_field('long_text'); ?>
		</div>
	<?php else : ?>
		<p class="lead margin-bottom-2"><?php the_field('short_text'); ?></p>
	<?php endif;
	?>
	<div class="grid-x grid-margin-x margin-bottom-2">
		<div class="cell medium-6">
			<hr>
			<p>Fachgebiete:</p>
			<?php
      $fachgebiete = get_field( 'fachgebiet' );
      if( $fachgebiete ):
        foreach ($fachgebiete as $fachgebiet):?>
        <span class="multi-select"><?php echo $fachgebiet[label]; ?></span>
      <?php endforeach;
      ?>
			<?php endif; ?>
		</div>
		<div class="cell medium-6">
			<hr>
			<p>Schulformen:</p>
			<?php
      $schulformen = get_field( 'schulform' );
      if( $schulformen ):
        foreach ($schulformen as $schulform):?>
        <span class="multi-select"><?php echo $schulform[label]; ?></span>
      <?php endforeach;
      ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="medium-4 cell">
	<?php get_template_part('template-parts/edusource/attributes'); ?>
	<a class="button" href="<?php the_field('url'); ?>" target="_blank">Zur Quelle</a>
</div>
<div class="medium-12 cell">
	<hr>
	<div class="grid-x grid-margin-x align-middle margin-bottom-2">
		<div class="cell shrink">
			<h3>Bewerten Sie diese Quelle</h3>
		</div>
		<div class="cell auto">
			<?php echo do_shortcode('[wp_ulike for="post" id="1" style="wpulike-heart"]'); ?>
		</div>
	</div>
</div>
<div class="medium-12 cell">
	<?php //comments_template(); ?>
</div>
<?php endwhile;
?>
</div>
</div>
</div>
<?php
get_footer();
