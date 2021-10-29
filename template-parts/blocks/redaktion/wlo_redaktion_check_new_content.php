<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO-Redaktion: Inhalte prüfen</div>';
} ?>

<?php
if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}
?>
<div class="wlo-basic-tile">

    <h3>Neue Inhalte prüfen</h3>
    <p>Hier findest du Links zu Inhalten, die von deiner Fachredaktion geprüft werden müssen.</p>

    <div class="wlo-check-new-content">

        <div class="wlo-check-new-content-button-box">
            <a class="wlo-block-button" href="<?php echo WLO_REPO . 'components/editorial-desk?mode=collection_proposals&ticket=' . $ticket; ?>"
               target="_blank" style="flex-direction: row-reverse;" title="<?php the_field('tooltip_button_1'); ?>" >
                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg" alt="">
                <span><?php the_field('text_button_1'); ?></span>
            </a>
            <?php if( get_field('img_1') ): ?>
                <img src="<?php the_field('img_1'); ?>" />
            <?php endif; ?>
        </div>

        <div class="wlo-check-new-content-button-box">
            <a class="wlo-block-button" href="<?php echo WLO_REPO . 'components/editorial-desk?mode=tasks&ticket=' . $ticket; ?>"
               target="_blank" style="flex-direction: row-reverse;" title="<?php the_field('tooltip_button_2'); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg" alt="">
                <span><?php the_field('text_button_2'); ?></span>
            </a>
            <?php if( get_field('img_2') ): ?>
                <img src="<?php the_field('img_2'); ?>" />
            <?php endif; ?>
        </div>

        <div class="wlo-check-new-content-source-box">
            <?php
            if( have_rows('current_sources') ): ?>
                <div class="wlo-check-new-content-sources">
                <?php
                while( have_rows('current_sources') ) : the_row(); ?>
                    <a class="wlo-check-new-content-source" href="<?php echo get_sub_field('source_link'); ?>">
                        <?php echo get_sub_field('source'); ?>
                    </a>
                <?php
                endwhile; ?>
                </div>
                <p>Diese Quellen wurden frisch gecrawlt und können überprüft werden!</p>
            <?php endif; ?>

            <div class="wlo-check-new-content-new-sources">
                <?php the_field('new_sources'); ?>
            </div>

        </div>


    </div>



</div>

<?php if (is_admin()) {
    echo '</div>';
} ?>
