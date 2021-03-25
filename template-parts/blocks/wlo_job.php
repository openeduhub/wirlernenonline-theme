<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Job-Block</div>';
} ?>

<div class="wlo-job-block">
    <?php
    $image = get_field('image');
    $headline = 'Job';
    if (!empty(get_field('headline'))){
        $headline = get_field('headline');
    }
    if( !empty( $image ) ): ?>
        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
    <?php endif; ?>
    <div class="wlo-job-block-content">
        <h3><?php echo $headline; ?></h3>
        <p><?php the_field('text'); ?></p>
        <a class="wlo-job-block-button" href="<?php the_field('url'); ?>">zur Ausschreibung</a>
    </div>
</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>
