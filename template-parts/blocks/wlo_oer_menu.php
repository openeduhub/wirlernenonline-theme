<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: einfache Kachel</div>';
} ?>

<?php
$minHeight = '';
if (!empty(get_field('minHeight'))){
    $minHeight = 'min-height: '.get_field('minHeight').'px;';
}
?>

<div class="wlo-oer-menu">
    <div class="wlo-oer-menu-header">
        <div class="wlo-oer-menu-tabs">

            <?php
            // Check rows exists.
            if( have_rows('menuTab') ):

                // Loop through rows.
                while( have_rows('menuTab') ) : the_row();

                    // Load sub field value.
                    $sub_value = get_sub_field('sub_field');
                    // Do something...
                    ?>
                    <div class="wlo-oer-menu-tab <?php if (get_sub_field('active')){echo 'wlo-oer-menu-tab-active';}?>">
                        <a href="<?php echo get_sub_field('link');?>"><?php echo get_sub_field('name');?></a>
                    </div>
                    <?php

                    // End loop.
                endwhile;

            endif;
            ?>

        </div>
    </div>

    <div class="wlo-oer-menu-content">
        <InnerBlocks />
    </div>

    <div class="wlo-oer-menu-footer"></div>
</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>
