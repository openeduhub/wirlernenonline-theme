<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <div class="cell auto">

      <h1 class="margin-bottom-2"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/lehrer-icon-150x150.png" width="50"> Neues für Lehrer*innen</h1>
    </div>
    <div class="cell shrink">
      <a class="button hollow" href="<?php get_site_url(); ?>/lernstoff/?roles=teacher">Alle Einträge</a>
    </div>
  </div>
  <div class="grid-x grid-margin-x small-up-1 medium-up-2 large-up-3 block-grid">
    <?php
    $posts = get_posts(array(
      'post_type' => array('edusource'),
      'numberposts' => 3,
        'post_status'       => 'publish',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'tags',
                'value' => '48',
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'role',
                'value' => 'teacher',
                'compare' => 'LIKE'
            )
        )
    ));
    if($posts)
    {
      foreach($posts as $post)
      {?>
        <div class="cell">
          <?php include(locate_template('template-parts/edusource/edu-card.php'));  ?>
        </div>
      <?php }
    }
    ?>
  </div>
</div>
