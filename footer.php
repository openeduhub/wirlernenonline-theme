</main><!-- .site-content -->
<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="grid-container">
		<div class="grid-x grid-margin-x align-middle">
			<div class="cell medium-12">
				<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="dropdown menu footer-menu" data-dropdown-menu>%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
