<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <div class="cell auto">
        <div class="margin-bottom-2 latest-header"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/eltern-icon-240x240.png" width="50" height="50" alt="Lehrer*innen"><h1>Neues für Eltern</h1></div>
    </div>
    <div class="cell shrink">
      <a class="button hollow" href="https://suche.wirlernenonline.de/de/search?filter=true&filters=%7B%22types%22:%5B%22SOURCE%22%5D,%22keywords%22:%5B%22Unterst%C3%BCtzung%20f%C3%BCr%20Eltern%22%5D%7D">Alle Einträge</a>
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
                'value' => '66',
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'role',
                'value' => 'parent',
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
