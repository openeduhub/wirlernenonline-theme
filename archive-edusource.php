<?php
/* Template Name: EduSource Archive
*/
get_header();?>
<div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="cell medium-8">
			<h1 class="page-title"><?php the_title(); ?> finden</h1>
		</div>
		<div class="medium-4 cell">
			<a href="<?php echo get_page_link( 1748 ); ?>">
				<div class="callout margin-top-2">
					<div class="card-section">
						<h4>Neue Quelle Hinzufügen</h4>
						<p> Es fehlt noch was?<br>Hier kannst du selber Quellen hinzufügen.</p>
						<a class="button tiny" href="<?php echo get_page_link( 1748 ); ?>" target="_blank">Quelle hinzufügen</a>
					</div>
				</div>
			</a>
		</div>
	</div>
</div>
<hr>
<div class="grid-container">
	<div class="grid-x grid-margin-x">
		<div class="cell medium-6">
			<label> <strong>Suche</strong>
				<input class="edu-filter edu-filter__search" data-filter="search" type="search" placeholder="Suchbegriff eingeben">
			</label>
		</div>
		<div class="medium-12 cell">
			<h4>Filter:</h4>
		</div>
	</div>

	<div class="grid-x grid-margin-x align-middle margin-bottom-3">
		<div class="cell small-12 medium-4">
			<label> Fachbereiche
				<select class="edu-filter edu-filter__select" data-filter="fields" data-placeholder="–"> <!-- field from JS -->
					<?php
					$choices = get_field_object('field_5e8746d0dc6b6')['choices']; /* IS get ID from frontend */
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-4">
			<label> Zielgruppe
				<select class="edu-filter edu-filter__select" data-filter="roles" data-placeholder="–">
					<?php
					$choices = get_field_object('field_5e8de14ae422c')['choices'];
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-4">
			<label> Lizenz
				<select class="edu-filter edu-filter__select" data-filter="licenses" data-placeholder="–">
					<?php
					$choices = get_field_object('field_5e8f3d8c0ea6d')['choices'];
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-4">
			<label> Schulform
				<select class="edu-filter edu-filter__select" data-filter="schooltype" data-placeholder="–">
					<?php
					$choices = get_field_object('field_5e8747a3dc6b7')['choices'];
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-4">
			<label>Art der Seite
				<select class="edu-filter edu-filter__select" data-filter="sourcetype" data-placeholder="–">
					<?php
					$choices = get_field_object('field_5e874809dc6b9')['choices'];
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-4">
			<label>Schlagworte
				<select class="edu-filter edu-filter__select" data-filter="tags" data-placeholder="–">
					<?php /* @LG is Taxonomie …
					$choices = get_field_object('field_5e87482fdc6ba')['choices'];
					foreach($choices as $key => $value) {
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					*/ ?>
				</select>
			</label>
		</div>
		<?php /*
		<div class="cell small-12 medium-12">
			<button class="edu-filter__reset button clear no-space">Reset</button>
		</div>
		*/ ?>

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
				data-licenses="<?php echo get_field_values('licence'); ?>"
				data-schooltype="<?php echo get_field_values('schulform'); ?>"
				data-sourcetype="<?php echo get_field_values('lernresourcentyp'); ?>">
				<?php get_template_part('template-parts/edusource/edu-card') ?>
			</div>
		<?php }
	}
	?>
</div>
</div>
<?php get_footer(); ?>
