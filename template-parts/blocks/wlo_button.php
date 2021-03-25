<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Button</div>';
} ?>

<?php
$buttonText = 'Zum Inhalt';
if (!empty(get_field('buttonText'))){
    $buttonText = get_field('buttonText');
}
$buttonIcon = get_template_directory_uri().'/src/assets/img/plus.svg';
if (!empty(get_field('buttonIcon'))){
    $buttonIcon = get_field('buttonIcon')['url'];
}
$yellowButton = '';
if (get_field('yellowButton')){
    $yellowButton = 'wlo-block-button-yellow';
}
$buttonIconAlign = 'flex-direction: row;';
if (get_field('buttonIconAlign') == 'right'){
    $buttonIconAlign = 'flex-direction: row-reverse;';
}
$externalLink = '';
if (get_field('external')){
    $externalLink = 'target="_blank"';
}
$buttonAlign = '';
if (!empty(get_field('align'))){
    $buttonAlign = 'float: ' . get_field('align')['label'].';';
}
$buttonMargin = '';
if ( is_page_template( 'page-templates/template_redaktion.php' ) ) {
    $buttonMargin = 'margin-right: 0.5em';
}

?>

<a class="wlo-block-button <?php echo $yellowButton; ?>" href="<?php echo get_field('url'); ?>" <?php echo $externalLink; ?>
   style="<?php echo $buttonAlign . $buttonIconAlign . $buttonMargin; ?>">
    <?php if (!empty(get_field('buttonIcon'))){ ?>
        <img src="<?php echo get_field('buttonIcon')['url']; ?>" alt="">
    <?php } ?>
    <span><?php echo $buttonText; ?></span>
</a>

<?php if (is_admin()) {
    echo '</div>';
} ?>
