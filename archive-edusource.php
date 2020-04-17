<?php
/* Template Name: EduSource Archive
*/
get_header();?>
<div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="cell medium-8">
			<h1 class="page-title"><?php the_title(); ?> finden</h1>
		</div>
		<?php /*
		<div class="cell medium-4">
			<a href="<?php echo get_page_link( 1748 ); ?>">
				<div class="callout">
					<img class="margin-bottom-1" src="<?php echo get_stylesheet_directory_uri(); ?>/src/assets/img/add_source.png" alt="Quelle hinzufügen" />
					<div class="card-section">
						<h4>Neue Quelle Hinzufügen</h4>
						<p> Es fehlt noch was?<br>Hier kannst du selber Quellen hinzufügen.</p>
						<a class="button" href="<?php echo get_page_link( 1748 ); ?>" target="_blank">Quelle hinzufügen</a>
					</div>
				</div>
			</a>
		</div>
		*/?>
	</div>
</div>
<hr>
<div class="grid-container filter-container">
	<div class="grid-x grid-margin-x align-middle">
		<div class="cell small-12 medium-shrink">
			<h4>Filter:</h4>
		</div>
		<div class="cell small-12 medium-auto">
			<label> Suche
				<input class="edu-filter__search" type="search" placeholder="Suche">
			</label>
		</div>
	</div>
	<div class="grid-x grid-margin-x align-middle margin-bottom-3">
		<div class="cell small-12 medium-auto">
			<label> Fachbereiche
				<select class="edu-filter" data-filter="fields" data-placeholder="Fachbereiche">
					<?php
					$choices = get_field_object('field_5e8746d0dc6b6')['choices'];
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-auto">
			<label> Rollen
				<select class="edu-filter" data-filter="roles" data-placeholder="Rollen">
					<?php
					$choices = get_field_object('field_5e8de14ae422c')['choices'];
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-auto">
			<label> Lizenz
				<select class="edu-filter" data-filter="licenses" data-placeholder="Lizenz">
					<?php
					$choices = get_field_object('field_5e8f3d8c0ea6d')['choices'];
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-shrink">
			<button class="edu-filter__reset button clear no-space">Reset</button>
		</div>
	</div>
	<div class="edu-list grid-x grid-margin-x small-up-1 medium-up-3 large-up-3 block-grid">
		<?php
		$posts = get_posts(array(
			'post_type' => array('edusource'),
			'numberposts' => -1,
		));
		if($posts)
		{
			foreach($posts as $post)
			{?>
				<div class="edu-item cell"
				data-fields="<?php echo get_field_values('fachgebiet'); ?>"
				data-roles="<?php echo get_field_values('role'); ?>"
				data-licenses="<?php echo get_field_values('licence'); ?>">
				<?php get_template_part('template-parts/edusource/edu-card') ?>
			</div>
		<?php }
	}
	?>
</div>
</div>
<?php get_footer(); ?>
