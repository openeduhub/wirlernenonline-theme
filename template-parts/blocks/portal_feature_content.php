<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Feature des Monats</div>';
};
?>

<?php
$headline = 'Material des Monats';
if(!empty(get_field('headline'))){
    $headline = get_field('headline');
}
$image = get_field('feature_image');
?>

<div class="portal_feature">
    <?php if (!empty($block['anchor'])) {
        echo '<a name="' . $block['anchor'] . '"></a>';
    } ?>
    <div class="headline"><?php echo $headline; ?></div>

    <div class="portal_feature_content">
        <div class="feature_image">
        <?php if( !empty( $image ) ): ?>
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
        <?php endif; ?>
        </div>
        <div class="feature_text">
            <h3><?php echo get_field('feature_headline'); ?></h3>
            <p><?php the_field('feature_text'); ?></p>
            <?php if(!empty(get_field('feature_link'))) : ?>
                <a class="portal_feature_button button" href="<?php echo get_field('feature_link'); ?>" target="_blank">
                    Mehr erfahren
                </a>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php if (is_admin()) {
    echo '</div>';
}; ?>

