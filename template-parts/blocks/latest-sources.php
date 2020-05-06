<?php
$title = get_field( 'title' );
if( !$title ){
    $title = 'Neuste Quellen';
}
$image = get_field('icon');

?>

<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <div class="cell auto">
        <div class="margin-bottom-2 latest-header"><img src="<?php echo $image['url']; ?>" width="50" height="50" alt="<?php echo $image['alt']; ?>"><h1><?php echo $title; ?></h1></div>
    </div>
      <?php
      $link = get_field( 'link' );
      if( $link ){ ?>
          <div class="cell shrink">
              <a class="button hollow" href="<?php echo $link; ?>">Alle Einträge</a>
          </div>
      <?php }
      ?>

  </div>
  <div class="grid-x grid-margin-x small-up-1 medium-up-2 large-up-3 block-grid">
    <?php
    $numberposts = get_field('numberposts');

    $type = get_field('typ');
    if ($type){
        $type_query = array();
        foreach ($type as $t){
            $type_query[] = $t['value'];
        }
    }

    $metaquery = array('relation' => 'AND');
    $meta = false;

    $tags = get_field('tags');
    if ($tags){
        $tags_query = array('relation' => 'OR');
        foreach ($tags as $tag){
            $tags_query[] = array(
                'key' => 'tags',
                'value' => $tag->term_id,
                'compare' => 'LIKE'
            );
        }
        $metaquery[] = $tags_query;
        $meta = true;
    }

    $roles = get_field('role');
    if ($roles){
        $roles_query = array('relation' => 'OR');
        foreach ($roles as $role){
            $roles_query[] = array(
                'key' => 'role',
                'value' => $role['value'],
                'compare' => 'LIKE'
            );
        }
        $metaquery[] = $roles_query;
        $meta = true;
    }

    $source_query = array(
        'post_type' => $type_query,
        'numberposts' => $numberposts,
        'post_status'       => 'publish',
    );

    if ($meta){
        $source_query['meta_query'] = $metaquery;
    }

    $posts = get_posts($source_query);
    if($posts) {
      foreach($posts as $post) {?>
        <div class="cell">
          <?php include(locate_template('template-parts/edusource/edu-card.php'));  ?>
        </div>
      <?php }
    }
    ?>
  </div>
</div>
