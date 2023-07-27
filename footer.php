</main><!-- .site-content -->

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="footer-wrapper">
        <div class="footer-main">

            <?php wp_nav_menu( array( 'theme_location' => 'zmf-footer', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="dropdown menu footer-menu" data-dropdown-menu>%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
        </div>
        <div class="footer-right">
            <div class="footer-bmbf">
                <a href="https://www.bmbf.de/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/BMBF.png" alt="Gefördert vom Bundesministerium für Bildung und Forschung"></a>
            </div>
            <div class="footer-license">
                <p>
                    Unsere Inhalte stehen unter der Lizenz <a href="<?php echo get_page_link(2); ?>/#license">CC BY 4.0</a>.<br>
                    Für Inhalte von Partnern achten Sie bitte auf die Lizenzbedingungen der verlinkten Webseiten.
                </p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
