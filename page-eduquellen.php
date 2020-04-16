<?php
/* Template Name: EduSource Archive
*/
get_header();?>
<div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="cell medium-12">
			<h1 class="page-title"><?php the_title(); ?> finden</h1>
		</div>
	</div>
</div>
<hr>
<div class="grid-container">
	<div class="grid-x grid-margin-x align-middle filter-container">
		<div class="cell shrink">
			<h4>Filter:</h4>
		</div>
		<div class="cell auto">
			<label>
      	<input type="search" placeholder="Suche">
      </label>
		</div>
		<div class="cell auto">
			<label>
      	<input type="search" placeholder="Fachgebiete">
      </label>
		</div>
		<div class="cell auto">
			<label>
      	<input type="search" placeholder="Lizenz">
      </label>
		</div>
	</div>
	<div class="grid-x grid-margin-x small-up-1 medium-up-3 large-up-3 block-grid">
		<?php
		// WP_Query arguments
		$args = array(
			'post_type'              => array( 'edusource' ),
			'nonpaging'		=> false,

		);

		// The Query
		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post(); ?>
				<div class="cell">
					<a href="<?php the_permalink(); ?>">
						<div class="card">
							<?php
							$image = get_field('thumbnail');
							if( !empty( $image ) ): ?>
							<img class="margin-bottom-1" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
						<?php endif; ?>
						<div class="card-section">
							<h2><?php the_title(); ?></h2>
							<?php if( get_field( 'short_text' ) ) : ?>
								<p><?php the_field( 'short_text' ); ?></p>
							<?php endif; ?>
							<a class="button tiny success" href="#">License Field… coming</a>
						</div>
					</div>
				</a>
			</div>

		<?php }
	} else {
		// no posts found
	}

	// Restore original Post Data
	wp_reset_postdata();
	?>
</div>
</div>
<?php get_footer(); ?>
