<?php
/* Template Name: EduTool Archive
*/
get_header();?>
<div class="grid-container">
	<div class="grid-x grid-margin-x align-bottom">
		<div class="cell medium-5">
			<h1 class="page-title"><?php the_title(); ?> finden</h1>
			<p><?php the_field('lernstoff_intro') ?></p>
		</div>
		<div class="medium-7 cell">
			<div class="new-source callout margin-top-2">
				<div class="card-section">
					<h4>Neue Quelle Hinzufügen</h4>
					<div class="grid-x align-justify align-middle">
						<p class="cell small-12 medium-auto margin-bottom-0"> Es fehlt noch was?<br>Hier kannst du selber Tools hinzufügen.</p>
						<div class="cell small-12 medium-shrink">
							<a class="button tiny margin-top-1" href="<?php echo get_page_link( 2703 ); ?>" target="_blank">Tool hinzufügen</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<hr>

<?php

// args
$args_filter = array(
    'posts_per_page'	=> -1,
    'post_type'		=> 'edutool',
    'post_status'       => 'publish',
);

$filter_query = new WP_Query( $args_filter );

if ( $filter_query->have_posts() ) {

    $fachgebiete = array();
    $schulformen = array();
    $roles = array();
    $tags = array();
    $categories = array();
    $licenses = array();

    while ( $filter_query->have_posts() ) {

        $filter_query->the_post();

        $role = get_field( 'role' );
        $schulform = get_field( 'schulform' );
        $category = get_field( 'category' );
        $tag_field = get_field('tags');

        if( $schulform ){
            foreach ($schulform as $tag){
                $schulformen[$tag['value']] = $tag['label'];
            }
        }
        if( $role ){
            foreach ($role as $tag){
                $roles[$tag['value']] = $tag['label'];
            }
        }
        if( $tag_field ){
            foreach ($tag_field as $tag){
                $tags[$tag->term_id] = $tag->name;
            }
        }
        if( $category ){
            foreach ($category as $tag){
                $categories[$tag['value']] = $tag['label'];
            }
        }
    }
}

function getSelectOptions($fieldName, $fieldChoices){
    $options = '';
    $selected = '';
    if( !empty( get_query_var( $fieldName ) ) ){
        $selected = get_query_var( $fieldName );
    }
    asort($fieldChoices);
    foreach ($fieldChoices as $value => $label){
        $current = '';
        if ($selected == $value){
            $current = 'selected';
            //$current = 'selected="selected"';
        }
        $options .= '<option value="'.$value.'" '.$current.' >'.$label.'</option>';
    }
    return $options;
}

?>

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
			<label> Zielgruppe
				<select class="edu-filter edu-filter__select" data-filter="roles" data-placeholder="...">
                    <?php echo getSelectOptions('role', $roles); ?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-4">
			<label> Schulform
				<select class="edu-filter edu-filter__select" data-filter="schooltype" data-placeholder="...">
                    <?php echo getSelectOptions('schulform', $schulformen); ?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-4">
			<label>Art der Seite
				<select class="edu-filter edu-filter__select" data-filter="sourcetype" data-placeholder="...">
                    <?php echo getSelectOptions('category', $categories); ?>
				</select>
			</label>
		</div>
		<div class="cell small-12 medium-4">
			<label>Schlagworte
				<select class="edu-filter edu-filter__select" data-filter="tags" data-placeholder="...">
                    <?php echo getSelectOptions('tags', $tags); ?>
				</select>
			</label>
		</div>
		<?php /*
		<div class="cell small-12 medium-12">
			<button class="edu-filter__reset button clear no-space">Reset</button>
		</div>
		*/ ?>

	</div>
    <?php

    ?>
	<div class="edu-list grid-x grid-margin-x small-up-1 medium-up-3 large-up-3 block-grid">
		<?php
		$posts = get_posts(array(
			'post_type' => array('edutool'),
			'numberposts' => -1,
		));

        //$posts = wp_ulike_get_most_liked_posts( -1, array( 'edusource' ), 'edusource', 'all', 'like' );

		if($posts)
		{
			foreach($posts as $post)
			{?>
				<div class="edu-item cell"
				data-roles="<?php echo get_field_values('role'); ?>"
				data-schooltype="<?php echo get_field_values('schulform'); ?>"
				data-sourcetype="<?php echo get_field_values('category'); ?>"
				data-tags="<?php echo get_field_values('tags'); ?>">
				<?php get_template_part('template-parts/edusource/edu-card') ?>
			</div>
		<?php }
	}
	?>
</div>
</div>
<?php get_footer(); ?>
