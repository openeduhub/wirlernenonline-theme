<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <div class="cell auto">
      <h1 class="margin-bottom-2"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/schueler-icon-150x150.png" width="70"> Neues für Schüler*innen</h1>
    </div>
    <div class="cell shrink">
      <a class="button hollow" href="<?php get_site_url(); ?>/lernstoff/?roles=learner">Alle Einträge</a>
    </div>
  </div>
  <div class="grid-x grid-margin-x small-up-1 medium-up-2 large-up-3 block-grid">
    <?php
    $posts = get_posts(array(
        'post_type' => array('edusource'),
        'numberposts' => 3,
        'post_status'       => 'publish',
        'meta_query' => array(
            array(
                'key' => 'role',
                'value' => 'learner',
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
