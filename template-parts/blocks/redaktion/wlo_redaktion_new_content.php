<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Neue Inhalte</div>';
} ?>

<?php
if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}
?>

<div class="wlo-new-content">

    <button class="wlo-new-content-button">
        <div>
            <img src="<?php echo get_template_directory_uri(); ?>'/src/assets/img/plus_white.svg'">
            <span>Neuen Inhalt anlegen</span>
        </div>
    </button>
    <div class="wlo-new-content-dropdown">
        <a href="<?php echo WLO_REPO.'components/upload?ticket='.$ticket; ?>" target="_blank">Dateiupload</a>
        <a href="<?php echo WLO_REPO.'components/search?connector=H5P&ticket='.$ticket; ?>" target="_blank">H5P erstellen</a>
        <a href="<?php echo WLO_REPO.'components/search?connector=ONLY_OFFICE&ticket='.$ticket; ?>" target="_blank">OnlyOffice erstellen</a>
<!--        <a href="--><?php //echo WLO_REPO.'components/workspace?connector=MOODLE&ticket='.$ticket; ?><!--" target="_blank">Moodle erstellen</a>-->
    </div>

</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>
