<a href="<?php echo get_field('url', $post->ID); ?>" target="_blank">
  <div class="card">
    <?php
    $image = get_field('thumbnail', $post->ID);
    if( !empty( $image ) ): ?>
    <img class="edu-item__image margin-bottom-1"
      data-original="<?php echo esc_url($image['sizes']['three-columns-four-three']); ?>"
      alt="<?php echo esc_attr($image['alt']); ?>"
      height="240"
      width="360" />
  <?php endif; ?>
  <div class="card-section">
    <h2 class="edu-item__title"><?php echo get_the_title($post->ID); ?></h2>

  </div>
</div>
</a>
