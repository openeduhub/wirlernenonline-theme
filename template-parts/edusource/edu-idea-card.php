<?php global $post; ?>
<a href="<?php the_permalink($post->ID); ?>">
  <div class="card">
    <?php
    $images = get_field('gallery', $post->ID);
    if( !empty( $images ) ):
        $image = $images[0];
        ?>
    <img class="edu-item__image margin-bottom-1"
      data-original="<?php echo esc_url($image['sizes']['three-columns-four-three']); ?>"
      alt="<?php echo esc_attr($image['alt']); ?>"
      height="240"
      width="360" />
  <?php endif; ?>
  <div class="card-section">
    <h2 class="edu-item__title"><?php echo get_the_title($post->ID); ?></h2>
    <?php if( get_field( 'short_text', $post->ID ) ) : ?>
      <p class="edu-item__content"><?php the_field( 'short_text', $post->ID ); ?></p>
    <?php endif; ?>
  </div>
</div>
</a>
