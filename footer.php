</main><!-- .site-content -->
<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="grid-container">
		<div class="grid-x grid-margin-x align-middle">
			<div class="cell medium-auto">
				<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="dropdown menu footer-menu" data-dropdown-menu>%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
			</div>
			<div class="cell shrink">
				<button class="button small no-space" data-open="newsletterModal">Newsletter Anmeldung</button>
				<div class="reveal" id="newsletterModal" data-reveal>
				  <?php get_template_part('template-parts/partials/newsletter-signup-form') ?>
				</div>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
