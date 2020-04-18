<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <div class="cell auto">

      <h1 class="margin-bottom-2"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/lehrer-icon-150x150.png" width="70"> Neues für Lehrer</h1>
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
      //'post_type' => 'event',
      //'meta_key' => 'location',
      //'meta_value' => 'melbourne'
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
