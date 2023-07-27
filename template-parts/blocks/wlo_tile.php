<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Kachel</div>';
} ?>

<?php
$title = 'Überschrift';
if (!empty(get_field('title'))){
    $title = get_field('title');
}
$minHeight = '';
if (!empty(get_field('minHeight'))){
    $minHeight = 'min-height: '.get_field('minHeight').'px;';
}
$centerContent = '';
if (get_field('centerContent')){
    $centerContent = 'text-align: center;';
}
$buttonText = 'Zum Inhalt';
if (!empty(get_field('buttonText'))){
    $buttonText = get_field('buttonText');
}
$buttonIcon = get_template_directory_uri().'/src/assets/img/plus.svg';
if (!empty(get_field('buttonIcon'))){
    $buttonIcon = get_field('buttonIcon')['url'];
}
$buttonAlign = '';
if (!empty(get_field('align'))){
    $buttonAlign = 'float: ' . get_field('align')['label'].';';
}
$buttonIconAlign = 'flex-direction: row;';
if (get_field('buttonIconAlign') == 'right'){
    $buttonIconAlign = 'flex-direction: row-reverse;';
}
$yellowButton = '';
if (get_field('yellowButton')){
    $yellowButton = 'wlo-tile-button-yellow';
}
$externalLink = '';
if (get_field('external')){
    $externalLink = 'target="_blank"';
}
$icon = '';
if (get_field('icon')){
    $icon = 'wlo-tile-icon';
}
?>
<div class="fachportal-content-block">

    <div class="wlo-tile content">
        <div class="widget-content">
            <?php if (!empty(get_field('url'))){ ?>
                <a href="<?php echo get_field('url'); ?>" <?php echo $externalLink; ?>>
            <?php } ?>

                    <?php if (!empty(get_field('image'))) { ?>
                        <img class="main-image <?php echo $icon; ?>" src="<?php echo get_field('image')['url']; ?>" alt="Cover: <?php echo $title; ?>">
                    <?php } ?>
                    <div class="content-info" style="<?php echo $minHeight . $centerContent; ?>">
                        <div class="content-title content-title-bold"><?php echo $title; ?></div>
                        <p class="content-description"><?php echo get_field('description'); ?></p>
                        <?php if (!empty(get_field('url'))){ ?>
                            <a class="wlo-tile-button <?php echo $yellowButton; ?>" href="<?php echo get_field('url'); ?>" <?php echo $externalLink; ?>
                               style="<?php echo $buttonIconAlign . $buttonAlign; ?>" aria-label="Zum-Inhalt: <?php echo $title; ?>">
                                <img src="<?php echo $buttonIcon; ?>" alt="">
                                <span><?php echo $buttonText; ?></span>
                            </a>
                        <?php } ?>
                    </div>

            <?php if (!empty(get_field('url'))){ ?>
                </a>
            <?php } ?>

        </div>
    </div>

</div>

<?php if (is_admin()) {
    echo '</div>';
} ?>
