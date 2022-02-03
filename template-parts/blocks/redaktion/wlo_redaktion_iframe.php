<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: iFrame mit Ticket</div>';
} ?>

<?php

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}

$url = get_field('url');

$height = '500px';
if (!empty(get_field('height'))){
    $height = get_field('height');
}

$width = '100%';
if (!empty(get_field('width'))){
    $width = get_field('width');
}

?>

<iframe width="<?php echo $width; ?>" height="<?php echo $height; ?>" src="<?php echo $url.'?ticket='.$ticket; ?>"></iframe>


<?php if (is_admin()) { ?>
    <?php echo '</div>';
} ?>
