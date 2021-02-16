<?php
get_header();
?>
<?php
while (have_posts()) : the_post(); ?>

    <div class="portal">

        <?php if( have_rows('menu') && get_field('sidemenu_active')): ?>
            <ul class="portal-sidemenu">
                <?php while( have_rows('menu') ): the_row(); ?>
                    <li>
                        <a href="#<?php echo get_sub_field('link'); ?>"><?php echo get_sub_field('name'); ?></a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>

        <div class="portal-wrapper">
            <?php the_content(); ?>
        </div>

    </div>
    <div class="add-content-menu-container" role="navigation" aria-label="Hinzufügen-Button">
        <?php
        $defaults = array(
            'menu'            => '',
            'container'       => 'li',
            'container_class' => '',
            'container_id'    => '',
            'menu_class'      => 'add-content-menu-list',
            'menu_id'         => '',
            'echo'            => true,
            'fallback_cb'     => 'wp_page_menu',
            'before'          => '',
            'after'           => '',
            'link_before'     => '',
            'link_after'      => '',
            'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'item_spacing'    => 'preserve',
            'depth'           => 0,
            'walker'          => '',
            'theme_location'  => 'add-content-button',
        );

        wp_nav_menu($defaults);
        ?>
        <span class="material-icons add-content-menu-button" onclick="jQuery('.add-content-menu-list').toggle()">
            add
        </span>
    </div>
<?php
endwhile;
get_footer();
