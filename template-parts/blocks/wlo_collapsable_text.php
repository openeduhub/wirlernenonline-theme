<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: einklappbarer Text</div>';
} ?>

<div class="wlo-collapsable-text">
    <div class="wlo-partner-text">
        <p><?php echo get_field('text'); ?></p>
    </div>

    <?php if (strlen(get_field('text')) >= 250){ ?>
        <button class="wlo-partner-button" onclick="toggleWloPartner(this)">
            <div>
                <span>Mehr</span>
                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg" alt="">
            </div>
        </button>
    <?php } ?>
</div>

<script>
    function toggleWloPartner(button){
        if (jQuery(button).closest('.wlo-collapsable-text').find('.wlo-partner-text').height() <= '201'){
            jQuery(button).closest('.wlo-collapsable-text').find('.wlo-partner-text').css('max-height', 1000);
            jQuery(button).closest('.wlo-collapsable-text').find('.wlo-partner-button div img').css("transform", "rotate(180deg)");
            jQuery(button).closest('.wlo-collapsable-text').find('.wlo-partner-button div span').html("Weniger");
        }else {
            //jQuery(button).closest('.wlo-partner').find('.wlo-partner-text').height('74');
            jQuery(button).closest('.wlo-collapsable-text').find('.wlo-partner-text').css('max-height', '6.94em');
            jQuery(button).closest('.wlo-collapsable-text').find('.wlo-partner-button div img').css("transform", "rotate(0deg)");
            jQuery(button).closest('.wlo-collapsable-text').find('.wlo-partner-button div span').html("Mehr");
        }
    }
</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>
