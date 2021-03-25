<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Regristierkarte</div>';
} ?>


<?php
$accordionID = uniqid();
$headline = 'Überschrift';
if (get_field('headline')) {
    $headline = get_field('headline');
}
$accordionStyle = '';
$color = '';
if (get_field('background')) {
    $accordionStyle = 'background: '.get_field('background').';';
}
if (get_field('color')) {
    $accordionStyle .= 'color: '.get_field('color').' !important;';
    $color = 'color: '.get_field('color').' !important;';
}
$bottomColor = '';
if (get_field('bottomColor')) {
    $bottomColor = 'background: '.get_field('bottomColor').';';
}

if ( is_page_template( 'template_jobs.php' ) || is_page_template('dist/template_jobs.php') ) {
    $accordionStyle .= 'width: calc(100% + 4em); margin: 0 -2em;';
}
?>

<div class="wlo-accordion-wrapper" style="<?php echo $accordionStyle; ?>">
    <button class="wlo-accordion" id="wlo-accordion-<?php echo $accordionID; ?>">
        <h2 style="<?php echo $color; ?>"><?php echo $headline; ?></h2>
        <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Inhalte ein odder ausklappen">
    </button>

    <div class="wlo-accordion-content" id="wlo-accordion-content-<?php echo $accordionID; ?>">
        <InnerBlocks />
    </div>
    <div class="wlo-accordion-bottom" style="<?php echo $bottomColor; ?>"></div>
</div>



<script>
    jQuery('#wlo-accordion-<?php echo $accordionID; ?>').click(function(){
        jQuery(this).find("img").toggleClass("wlo-accordion-icon-active");
        jQuery('#wlo-accordion-content-<?php echo $accordionID; ?>').slideToggle();
    });
</script>



<?php if (is_admin()) {
    echo '</div>';
} ?>
