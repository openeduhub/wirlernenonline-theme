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
		<div class="cell small-12 medium-shrink">
			<h4>Filter:</h4>
		</div>
		<div class="cell small-12 medium-auto">
			<label>
				<input type="search" placeholder="Suche">
			</label>
		</div>
		<div class="cell small-12 medium-auto">
			<label>
				<select>
					<option value="husker">Fachgebiete</option>
					<option value="starbuck">Starbuck</option>
					<option value="hotdog">Hot Dog</option>
					<option value="apollo">Apollo</option>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-auto">
			<label>
				<input type="search" placeholder="Lizenz">
			</label>
		</div>
	</div>
	<div class="grid-x grid-margin-x small-up-1 medium-up-3 large-up-3 block-grid">

		<?php

    $posts = get_posts(array(
      'post_type' => array('edusource'),
      'numberposts' => -1,
    ));

    if($posts)
    {
      foreach($posts as $post)
      {?>
        <div class="cell">
					<?php get_template_part('template-parts/edusource/edu-card') ?>
        </div>

      <?php }
    }

    ?>
</div>
</div>
<?php get_footer(); ?>
