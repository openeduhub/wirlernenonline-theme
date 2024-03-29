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
$linkUrl = get_field('url');
if (strpos($linkUrl, 'redaktion.openeduhub.net') && is_user_logged_in() ){
    if (function_exists('get_repo_ticket')){
        $ticket = get_repo_ticket();
    }else{
        $ticket = '';
    }

    if (strpos($linkUrl, '?') !== false){
        $linkUrl .= '&ticket='.$ticket;
    }else{
        $linkUrl .= '?ticket='.$ticket;
    }
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

<a class="wlo-block-button <?php echo $yellowButton; ?>" href="<?php echo $linkUrl; ?>" <?php echo $externalLink; ?>
   title="<?php the_field('tooltipText'); ?>" style="<?php echo $buttonAlign . $buttonIconAlign . $buttonMargin; ?>">
    <?php if (!empty(get_field('buttonIcon'))){ ?>
        <img src="<?php echo get_field('buttonIcon')['url']; ?>" alt="">
    <?php } ?>
    <span><?php echo $buttonText; ?></span>
</a>

<?php if (is_admin()) {
    echo '</div>';
} ?>
