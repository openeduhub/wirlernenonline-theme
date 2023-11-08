</main><!-- .site-content -->

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="footer-wrapper">
        <div class="footer-main">
            <div class="footer-buttons">
                <a class="footer-btn" href="<?php echo get_page_link(5807); ?>">Anmelden</a>
                <a href="https://twitter.com/wirlernenonline" target="_blank" aria-label="Link zu X"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/x.svg" alt="Icon: X"></a>
                <a href="https://www.youtube.com/channel/UCPs3ndK7W1JDMHz02_AwdBg" target="_blank" aria-label="Link zu youtube"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/icon_youtube.svg" alt="Icon: youtube"></a>
            </div>

            <?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="dropdown menu footer-menu" data-dropdown-menu>%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
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
