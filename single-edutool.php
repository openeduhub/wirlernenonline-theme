<?php
get_header();
?><div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="medium-12 cell">
			<a class="backlink" href="<?php the_permalink(47) ?>">Zurück zur Übersicht</a>
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
            <?php
            $category = get_field( 'category' );
            if( $category ):
                echo '<div>';
                foreach ($category as $cat):?>
                    <h4 class="multi-select"><?php echo $cat['label']; ?></h4>
                <?php endforeach;
                echo '</div>'; ?>
            <?php endif; ?>

	<div class="grid-x grid-margin-x margin-bottom-2">

		<div class="cell medium-6">
			<hr>
			<p>Bildungsebene:</p>
			<?php
      $schulformen = get_field( 'schulform' );
      if( $schulformen ):
        foreach ($schulformen as $schulform):?>
        <span class="multi-select"><?php echo $schulform['label']; ?></span>
      <?php endforeach;
      ?>
			<?php endif; ?>
		</div>
        <?php
        $tags = get_field( 'tags' );
        if( $tags ):?>
        <div class="cell medium-6">
            <hr>
            <p>Schlagworte:</p>

           <?php foreach ($tags as $tag):?>
                <span class="multi-select"><?php echo $tag->name; ?></span>
            <?php endforeach;
            ?>
        </div>
        <?php endif; ?>
	</div>
</div>
<div class="medium-4 cell">
    <a class="button" href="<?php the_field('url'); ?>" target="_blank">Zur Quelle</a>
	<?php get_template_part('template-parts/edutool/attributes'); ?>
</div>
<div class="medium-12 cell">
	<hr>
	<div class="grid-x grid-margin-x align-middle margin-bottom-2">
		<div class="cell shrink">
			<h3>Bewerten Sie diese Quelle</h3>
		</div>
		<div class="cell auto">
            <?php //if(function_exists('wp_ulike')) wp_ulike('get'); ?>
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
