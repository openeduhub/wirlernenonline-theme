<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<?php get_template_part('head') ?>
<body <?php body_class(); ?>>
	<header id="site-header" class="site-header" role="banner">
		<div class="title-bar" data-responsive-toggle="main-menu" data-hide-for="medium">
			<button class="menu-icon" type="button" data-toggle></button>
			<div class="title-bar-title">Menu</div>
			<img class="logo float-right" src="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/Bananenbiegerei-Logo-White.png" alt="Logo">
		</div>
		<div class="top-bar" id="main-menu" data-animate="fade-in fade-out">
			<div class="top-bar-left">
				<?php //wp_nav_menu( array( 'theme_location' => 'top', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="dropdown menu top-menu"  data-dropdown-menu>%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
			</div>
			<div class="top-bar-right">
				<img class="logo show-for-medium" src="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/Bananenbiegerei-Logo.png" alt="Logo">
			</div>
		</div>
	</header>
	<main id="main-content" class="main-content">
