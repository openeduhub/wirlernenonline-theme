<?php
get_header();
?>
<?php
while (have_posts()) : the_post(); ?>
    <!--<style type="text/css">
        .portal {
            background: <?php /*the_field('background_color'); */?>;
        }

        .portal h1, .portal h2,.portal h3,.portal h4,.portal h5,.portal h6, .portal a{
            color: <?php /*the_field('font_color'); */?>;
        }
    </style>-->
    <div class="portal">
        <div class="grid-container">
            <div class="grid-x grid-margin-x">
                <div class="medium-12 cell">
                    <div class="portal-wrapper">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="add-content-menu-container">
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
