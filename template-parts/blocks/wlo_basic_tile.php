<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: einfache Kachel</div>';
} ?>

<?php
$minHeight = '';
if (!empty(get_field('minHeight'))){
    $minHeight = 'min-height: '.get_field('minHeight').'px;';
}
$portalMargin = '';
if (get_post_type( get_the_ID() ) == 'portal'){
    $portalMargin = 'margin: 0.5em 1.5em;';
}
?>

<div class="wlo-basic-tile" style="<?php echo $portalMargin . $minHeight; ?>">
    <InnerBlocks />
</div>

<?php if (is_admin()) {
    echo '</div>';
} ?>
