<?php
/**
* The template for displaying 404 pages (not found)
*
* @package WordPress
* @subpackage Twenty_Fifteen
* @since Twenty Fifteen 1.0
*/
get_header(); ?>
<section class="row error-404 not-found">
	<div class="small-8 columns small-centered text-center">
		<h1 class="page-title">Diese Seite konnte nicht gefunden werden</h1>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Weiter zur Homepage →</a></p>
	</div>
</section>
<?php get_footer(); ?>