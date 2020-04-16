<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<?php get_template_part('head') ?>
<body <?php body_class(); ?>>
	<header id="site-header" class="site-header" role="banner">
		<div class="title-bar" data-responsive-toggle="main-menu" data-hide-for="medium">
			<button class="menu-icon" type="button" data-toggle></button>
			<div class="title-bar-title">Menu</div>
			<img class="logo float-right" src="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/wir-lernen-online-logo.png" alt="Logo">
		</div>
		<div class="top-bar" id="main-menu" data-animate="fade-in fade-out">
			<div class="top-bar-left position-relative">
				<div class="grid-container">
					<div class="grid-x grid-margin-x align-middle">
						<div class="auto cell">
							<a href="<?php echo get_home_url(); ?>">
								<img class="logo show-for-medium" src="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/wir-lernen-online-logo.png" alt="Logo">
							</a>
						</div>
						<div class="shrink cell">
							<?php wp_nav_menu( array( 'theme_location' => 'top', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="dropdown menu top-menu align-center"  data-dropdown-menu>%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
						</div>
						<div class="auto cell">
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<main id="main-content" class="main-content">
