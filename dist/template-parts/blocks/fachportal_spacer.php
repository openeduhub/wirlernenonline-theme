<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Trennlinie</div>';
} ?>

<?php
$height = 'margin: 1em auto;';
if (!empty(get_field('height'))){
    $height = 'margin: '.intval(get_field('height')/2).'px auto;';
}
?>

    <hr class="wlo-spacer" style="<?php echo $height; ?>">

<?php if (is_admin()) {
    echo '</div>';
} ?>
