<a href="<?php the_permalink($post->ID); ?>">
  <div class="card">
    <?php
    $image = get_field('thumbnail', $post->ID);
    if( !empty( $image ) ): ?>
    <img class="margin-bottom-1" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
  <?php endif; ?>
  <div class="card-section">
    <h2 class="edu-item__title"><?php echo get_the_title($post->ID); ?></h2>
    <?php if( get_field( 'short_text', $post->ID ) ) : ?>
      <p class="edu-item__content"><?php the_field( 'short_text', $post->ID ); ?></p>
    <?php endif; ?>
    <div class="button-group tiny">
    <?php
    $licences = get_field( 'licence', $post->ID ); ?>
    <?php if( $licences ):
      foreach ($licences as $licence):?>
        <div class="label success margin-right-1"><?php echo $licence['label']; ?></div>
    <?php endforeach;
    ?>
  <?php endif; ?>
  </div>
      <div style="float: right">
          <?php if(function_exists('wp_ulike')) wp_ulike('get'); ?>
      </div>
      <a class="button" href="<?php the_field('url'); ?>" target="_blank">Zur Quelle</a>
  </div>
</div>
</a>
