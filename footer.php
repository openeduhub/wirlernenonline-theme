</main><!-- .site-content -->
<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="grid-container">
		<div class="grid-x grid-margin-x align-middle">
			<div class="cell medium-auto">
				<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="dropdown menu footer-menu" data-dropdown-menu>%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
			</div>
			<div class="cell shrink">
				<button class="button small no-space" data-open="newsletterModal">Newsletter</button>
				<div class="reveal" id="newsletterModal" data-reveal>
				  <?php get_template_part('template-parts/partials/newsletter-signup-form') ?>
				</div>
			</div>
            <div class="cell shrink">
                <a href="<?php echo get_page_link(6); ?>"><img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/BMBF.png" width="175" alt="Gefördert vom Bundesministerium für Bildung und Forschung"></a>
            </div>
		</div>
        <div class="footer-license">
            <p>Unsere Inhalte stehen unter der Lizenz <a href="<?php echo get_page_link(2); ?>/#license">CC BY 4.0</a>. Für Inhalte von Partnern achten Sie bitte auf die Lizenzbedingungen der verlinkten Webseiten.</p>
        </div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
