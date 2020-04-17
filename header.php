<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-91648560-4"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-91648560-4');
</script>
<?php get_template_part('head') ?>
<body <?php body_class(); ?>>
	<header id="site-header" class="site-header" role="banner">
		<div class="title-bar" data-responsive-toggle="main-menu" data-hide-for="medium">
			<button class="menu-icon" type="button" data-toggle></button>
			<a class="mobile-logo-container" href="<?php echo get_home_url(); ?>">
				<img class="logo" src="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/WLO-Logo-01.png" alt="Logo">
			</a>
		</div>
		<div class="top-bar" id="main-menu" data-animate="fade-in fade-out">
			<div class="top-bar-left position-relative">
				<div class="grid-container">
					<div class="grid-x grid-margin-x align-middle">
						<div class="auto cell show-for-medium">
							<a href="<?php echo get_home_url(); ?>">
								<img class="logo" src="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/WLO-Logo-01.png" alt="Logo">
							</a>
						</div>
						<div class="medium-shrink small-12 cell">
							<?php wp_nav_menu( array( 'theme_location' => 'top', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="vertical medium-horizontal menu top-menu" data-responsive-menu="drilldown medium-dropdown">%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
						</div>
						<div class="cell small-12 medium-auto">
							<?php wp_nav_menu( array( 'theme_location' => 'top-call-top-action', 'container' => false, 'depth' => 0, 'items_wrap' => '<ul class="menu top-call-to-action-menu align-right">%3$s</ul>', 'walker' => new insertcart_walker( array( 'in_top_bar' => true, 'item_type' => 'li', 'menu_type' => 'main-menu' ) ), ) ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<main id="main-content" class="main-content">
